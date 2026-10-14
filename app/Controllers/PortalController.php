<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Cache;
use App\Core\DB;
use App\Core\Validator;
use App\Core\View;
use App\Models\Article;

final class PortalController
{
    public function home(): void
    {
        $data = Cache::remember('home', fn () => ['featured' => Article::featured(), 'result' => Article::listing([], 1, 9), 'popular' => Article::popular()]);
        View::render('portal/home', $data + ['title' => 'Pulso · Lo que pasa, lo que importa']);
    }

    public function listing(string $kind = '', string $slug = ''): void
    {
        $filters = [];
        $title = 'Todas las noticias';
        $path = 'noticias';
        $q = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 150) : '';
        if ($kind === 'buscar') {
            $title = 'Resultados de búsqueda';
            $filters['q'] = $q;
            $path = 'buscar';
        } elseif ($kind) {
            $table = $kind === 'categoria' ? 'categories' : 'tags';
            $item = DB::query('SELECT * FROM ' . $table . ' WHERE slug=?', [$slug])->fetch();
            if (!$item) {
                View::error(404, 'Esta sección no existe. Explora las categorías de la portada.');
                return;
            }
            $filters[$kind === 'categoria' ? 'category_id' : 'tag_id'] = $item['id'];
            $title = $item['name'];
            $path = $kind . '/' . $slug;
        }
        $result = $kind === 'buscar' && !$q ? ['items' => [], 'total' => 0, 'pages' => 1, 'page' => 1] : Article::listing($filters, $_GET['page'] ?? 1);
        View::render('portal/listing', compact('title', 'result', 'path', 'q', 'kind'));
    }

    public function article(string $slug, array $errors = [], array $data = []): void
    {
        $article = Article::published($slug);
        if (!$article) {
            View::error(404, 'No encontramos esta noticia. Puede haber sido retirada; busca otra desde la portada.');
            return;
        }
        if (!isset($_SESSION['read'][$article['id']])) {
            DB::query('UPDATE articles SET views=views+1 WHERE id=?', [$article['id']]);
            $_SESSION['read'][$article['id']] = true;
        }
        $comments = DB::query("SELECT c.*, u.name FROM comments c JOIN users u ON u.id=c.user_id WHERE article_id=? AND status='aprobado' ORDER BY c.id DESC LIMIT 50", [$article['id']])->fetchAll();
        $tags = DB::query('SELECT t.* FROM tags t JOIN article_tag atg ON atg.tag_id=t.id WHERE atg.article_id=?', [$article['id']])->fetchAll();
        $related = array_slice(array_filter(Article::listing(['category_id' => $article['category_id']], 1, 4)['items'], fn ($item) => $item['id'] !== $article['id']), 0, 3);
        View::render('portal/article', compact('article', 'comments', 'tags', 'related', 'errors', 'data') + ['title' => $article['title']]);
    }

    public function comment(string $slug): void
    {
        $user = Auth::user();
        $article = Article::published($slug);
        if (!$article) {
            View::error(404, 'La noticia ya no está disponible.');
            return;
        }
        $content = $_POST['content'] ?? '';
        $errors = [];
        if (!Validator::text($content, 1, 1000)) {
            $errors['content'] = 'Escribe un comentario de 1 a 1000 caracteres.';
        }
        $latest = DB::query('SELECT created_at FROM comments WHERE user_id=? ORDER BY id DESC LIMIT 1', [$user['id']])->fetchColumn();
        if ($latest && time() - strtotime($latest) < 60) {
            $errors['content'] = 'Espera un minuto entre comentarios para evitar envíos repetidos.';
        }
        if (!empty($_POST['website']) || time() - ($_SESSION['comment_form_time'] ?? time()) < 2) {
            $errors['content'] = 'Espera dos segundos y vuelve a enviar el comentario.';
        }
        if ($errors) {
            $this->article($slug, $errors, $_POST);
            return;
        }
        DB::query('INSERT INTO comments (article_id,user_id,content) VALUES (?,?,?)', [$article['id'], $user['id'], trim($content)]);
        flash('Tu comentario se recibió y está pendiente de moderación.');
        redirect('noticia/' . $slug . '#comentarios');
    }

    public function subscribe(): void
    {
        $email = $_POST['email'] ?? '';
        if (!Validator::email($email)) {
            View::render('auth/form', ['mode' => 'subscribe', 'title' => 'Suscripción al boletín', 'errors' => ['email' => 'Escribe un correo válido para recibir el enlace de confirmación.'], 'data' => $_POST]);
            return;
        }
        $token = bin2hex(random_bytes(32));
        DB::query('INSERT INTO subscribers (email,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL 1 DAY)) ON DUPLICATE KEY UPDATE token_hash=VALUES(token_hash), expires_at=VALUES(expires_at)', [$email, hash('sha256', $token)]);
        AuthController::localMail($email, 'Confirma tu suscripción', 'boletin/confirmar/' . $token);
        flash('Revisa el enlace de confirmación. En este entorno local se guarda en storage/logs/mail.log.');
        redirect('');
    }

    public function confirmSubscription(string $token): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            View::render('auth/form', ['mode' => 'confirm-subscribe', 'title' => 'Confirmar suscripción', 'token' => $token]);
            return;
        }
        $updated = DB::query('UPDATE subscribers SET confirmed_at=NOW() WHERE token_hash=? AND expires_at>NOW() AND confirmed_at IS NULL', [hash('sha256', $token)])->rowCount();
        flash($updated ? 'Suscripción confirmada. Gracias por acompañarnos.' : 'El enlace ya se usó o caducó. Solicita otra suscripción.');
        redirect('');
    }

    public function cookies(): void
    {
        $choice = ($_POST['choice'] ?? '') === 'aceptar' ? 'aceptar' : 'rechazar';
        setcookie('cookie_choice', $choice, ['expires' => time() + 365 * 86400, 'path' => url(), 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'httponly' => true, 'samesite' => 'Lax']);
        flash('Tu preferencia de cookies se guardó. Solo usamos cookies necesarias.');
        redirect('');
    }

    public function rss(): void
    {
        header('Content-Type: application/rss+xml; charset=UTF-8');
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel/></rss>');
        $xml->channel->addChild('title', 'Pulso · Noticias');
        $xml->channel->addChild('link', $GLOBALS['config']['url']);
        $xml->channel->addChild('description', 'Noticias recientes en español');
        $xml->channel->addChild('language', 'es');
        foreach (Article::listing([], 1, 20)['items'] as $article) {
            $item = $xml->channel->addChild('item');
            $item->addChild('title', htmlspecialchars($article['title'], ENT_XML1, 'UTF-8'));
            $item->addChild('link', htmlspecialchars($GLOBALS['config']['url'] . '/noticia/' . $article['slug'], ENT_XML1, 'UTF-8'));
            $item->addChild('guid', $GLOBALS['config']['url'] . '/noticia/' . $article['slug']);
            $item->addChild('description', htmlspecialchars($article['excerpt'], ENT_XML1, 'UTF-8'));
            $item->addChild('pubDate', date(DATE_RSS, strtotime($article['published_at'])));
        }
        echo $xml->asXML();
    }
}
