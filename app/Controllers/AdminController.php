<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Cache;
use App\Core\DB;
use App\Core\Sanitizer;
use App\Core\Slug;
use App\Core\Upload;
use App\Core\Validator;
use App\Core\View;
use App\Models\Article;
use App\Models\Catalog;

final class AdminController
{
    public function dashboard(): void
    {
        $user = Auth::user();
        $params = $user['role'] === 'editor' ? [$user['id']] : [];
        $where = $params ? ' AND author_id=?' : '';
        $counts = [
            'Noticias' => DB::query('SELECT COUNT(*) FROM articles WHERE deleted_at IS NULL' . $where, $params)->fetchColumn(),
            'Publicadas' => DB::query("SELECT COUNT(*) FROM articles WHERE deleted_at IS NULL AND status='publicado' AND published_at<=NOW()" . $where, $params)->fetchColumn(),
        ];
        if ($user['role'] === 'administrador') {
            $counts['Usuarios activos'] = DB::query('SELECT COUNT(*) FROM users WHERE active=1 AND deleted_at IS NULL')->fetchColumn();
            $counts['Comentarios pendientes'] = DB::query("SELECT COUNT(*) FROM comments WHERE status='pendiente'")->fetchColumn();
        }
        View::render('admin/dashboard', ['title' => 'Tu sala de redacción', 'counts' => $counts]);
    }

    public function articles(): void
    {
        $filters = ['q' => is_string($_GET['q'] ?? '') ? mb_substr($_GET['q'] ?? '', 0, 150) : '', 'status' => is_string($_GET['status'] ?? '') ? $_GET['status'] ?? '' : '', 'trash' => ($_GET['trash'] ?? '') === '1'];
        $result = Article::listing($filters, $_GET['page'] ?? 1, 15, Auth::user());
        View::render('admin/articles', compact('result', 'filters') + ['title' => $filters['trash'] ? 'Papelera de noticias' : 'Noticias']);
    }

    private function owned(string $id): array|false
    {
        $article = Article::find(Validator::id($id));
        if (!$article) {
            View::error(404, 'La noticia no existe.');
            return false;
        }
        if (!Auth::canManage(Auth::user(), $article)) {
            View::error(403, 'Solo puedes gestionar tus propias noticias.');
            return false;
        }
        return $article;
    }

    public function articleForm(string $id = '0', array $errors = [], ?array $data = null): void
    {
        $article = $id !== '0' ? $this->owned($id) : [];
        if ($article === false) {
            return;
        }
        if (!empty($article['deleted_at'])) {
            View::error(403, 'Restaura la noticia desde la papelera antes de editarla.');
            return;
        }
        $selectedTags = $id !== '0' ? DB::query('SELECT tag_id FROM article_tag WHERE article_id=?', [$id])->fetchAll(\PDO::FETCH_COLUMN) : [];
        $data ??= $article;
        if (isset($data['tags']) && is_array($data['tags'])) {
            $selectedTags = $data['tags'];
        }
        View::render('admin/article-form', compact('data', 'errors', 'selectedTags') + ['title' => $id === '0' ? 'Crear noticia' : 'Editar noticia', 'id' => $id, 'tags' => Catalog::all('tags')]);
    }

    public function saveArticle(string $id = '0'): void
    {
        $old = $id !== '0' ? $this->owned($id) : [];
        if ($old === false) {
            return;
        }
        if (!empty($old['deleted_at'])) {
            View::error(403, 'La noticia está en la papelera. Restáurala primero.');
            return;
        }
        $data = $_POST;
        $errors = [];
        foreach (['title' => [1, 150, 'Escribe un título de 1 a 150 caracteres.'], 'content' => [1, 200000, 'Escribe el contenido, hasta 200 000 caracteres.'], 'image_alt' => [1, 255, 'Describe la imagen con un texto alternativo de 1 a 255 caracteres.']] as $field => [$min, $max, $message]) {
            if (!Validator::text($data[$field] ?? '', $min, $max)) {
                $errors[$field] = $message;
            }
        }
        $category = Validator::id($data['category_id'] ?? '');
        if (!DB::query('SELECT id FROM categories WHERE id=?', [$category])->fetch()) {
            $errors['category_id'] = 'Elige una categoría existente.';
        }
        $status = $data['status'] ?? '';
        if (!in_array($status, ['borrador', 'revision', 'publicado', 'archivado'], true)) {
            $errors['status'] = 'Elige un estado válido.';
        }
        if (!Sanitizer::headings(is_string($data['content'] ?? null) ? $data['content'] : '')) {
            $errors['content'] = 'Usa títulos h2, luego h3 y h4 sin saltos. El título principal ya es h1.';
        }
        $content = Sanitizer::html(is_string($data['content'] ?? null) ? $data['content'] : '');
        if (!Validator::text(strip_tags($content), 1, 200000)) {
            $errors['content'] = 'El contenido debe incluir texto legible además de las etiquetas.';
        }
        $dateText = is_string($data['published_at'] ?? null) ? $data['published_at'] : '';
        $date = $dateText ? \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $dateText) : null;
        if ($dateText && (!$date || $date->format('Y-m-d\TH:i') !== $dateText)) {
            $errors['published_at'] = 'Indica una fecha y hora válidas.';
        }
        if ($old && ($data['updated_at'] ?? '') !== $old['updated_at']) {
            $errors['title'] = 'Conflicto: otra persona guardó cambios. Copia tu texto y recarga para comparar antes de guardar.';
        }
        $image = $old['image_path'] ?? 'assets/news-1.webp';
        if (!$errors) {
            try {
                $image = Upload::image($_FILES['image'] ?? []) ?? $image;
            } catch (\RuntimeException $exception) {
                $errors['image'] = $exception->getMessage();
            }
        }
        if ($errors) {
            $this->articleForm($id, $errors, $data);
            return;
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        $published = $date ? $date->format('Y-m-d H:i:s') : ($old['published_at'] ?? ($status === 'publicado' ? date('Y-m-d H:i:s') : null));
        $excerpt = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($content))), 0, 260);
        $values = [trim($data['title']), $content, $excerpt, $category, $image, trim($data['image_alt']), $status, isset($data['featured']) ? 1 : 0, $published];
        try {
            if ($old) {
                $statement = DB::query('UPDATE articles SET title=?,content=?,excerpt=?,category_id=?,image_path=?,image_alt=?,status=?,featured=?,published_at=?,updated_at=NOW(6) WHERE id=? AND updated_at=? AND deleted_at IS NULL', [...$values, $id, $data['updated_at']]);
                if ($statement->rowCount() === 0) {
                    $pdo->rollBack();
                    $this->articleForm($id, ['title' => 'Conflicto: la noticia cambió durante el guardado. Copia tus datos y recarga.'], $data);
                    return;
                }
            } else {
                // La clave única también protege frente a creaciones concurrentes.
                $slug = Slug::unique($data['title'], fn ($candidate) => (bool) DB::query('SELECT id FROM articles WHERE slug=?', [$candidate])->fetch());
                DB::query('INSERT INTO articles (title,content,excerpt,category_id,image_path,image_alt,status,featured,published_at,author_id,slug) VALUES (?,?,?,?,?,?,?,?,?,?,?)', [...$values, Auth::user()['id'], $slug]);
                $id = (string) $pdo->lastInsertId();
            }
            DB::query('DELETE FROM article_tag WHERE article_id=?', [$id]);
            $validTags = array_column(Catalog::all('tags'), 'id');
            foreach (array_unique(is_array($data['tags'] ?? null) ? $data['tags'] : []) as $tag) {
                if (in_array($tag, $validTags)) {
                    DB::query('INSERT INTO article_tag (article_id,tag_id) VALUES (?,?)', [$id, $tag]);
                }
            }
            DB::audit($old ? 'editar_noticia' : 'crear_noticia', 'articles', (int) $id);
            $pdo->commit();
        } catch (\PDOException $exception) {
            $pdo->rollBack();
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $this->articleForm($old ? $id : '0', ['title' => 'Otro guardado utilizó el mismo título. Vuelve a guardar para generar una URL única.'], $data);
            return;
        }
        Cache::clear();
        flash('Noticia guardada. Las fechas futuras se muestran al llegar la hora programada.');
        redirect('admin/noticias/' . $id . '/editar?guardado=1');
    }

    public function articleAction(string $id, string $action): void
    {
        $article = $this->owned($id);
        if (!$article) {
            return;
        }
        if ($action === 'eliminar' && ($_POST['confirm'] ?? '') !== '1') {
            flash('Marca la confirmación antes de enviar la noticia a la papelera.');
            redirect('admin/noticias');
        }
        DB::query('UPDATE articles SET deleted_at=' . ($action === 'restaurar' ? 'NULL' : 'NOW()') . ', updated_at=NOW(6) WHERE id=?', [$id]);
        DB::audit($action . '_noticia', 'articles', (int) $id);
        Cache::clear();
        flash($action === 'restaurar' ? 'Noticia restaurada.' : 'Noticia movida a la papelera. Puedes restaurarla.');
        redirect('admin/noticias');
    }

    public function catalog(string $kind, array $errors = [], array $data = []): void
    {
        $table = $kind === 'categorias' ? 'categories' : 'tags';
        View::render('admin/catalog', compact('kind', 'errors', 'data') + ['title' => $kind === 'categorias' ? 'Categorías' : 'Etiquetas', 'items' => Catalog::all($table)]);
    }

    public function saveCatalog(string $kind): void
    {
        $table = $kind === 'categorias' ? 'categories' : 'tags';
        $id = Validator::id($_POST['id'] ?? '');
        $name = $_POST['name'] ?? '';
        if (!Validator::text($name, 1, 80)) {
            $this->catalog($kind, ['name' => 'Escribe un nombre de 1 a 80 caracteres.'], $_POST);
            return;
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        try {
            if ($id) {
                DB::query('UPDATE ' . $table . ' SET name=? WHERE id=?', [$name, $id]);
            } else {
                $slug = Slug::unique($name, fn ($candidate) => (bool) DB::query('SELECT id FROM ' . $table . ' WHERE slug=?', [$candidate])->fetch());
                DB::query('INSERT INTO ' . $table . ' (name,slug) VALUES (?,?)', [$name, $slug]);
                $id = (int) $pdo->lastInsertId();
            }
            DB::audit('guardar_' . $kind, $table, $id);
            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
        Cache::clear();
        flash('Catálogo actualizado.');
        redirect('admin/' . $kind);
    }

    public function deleteCatalog(string $kind, string $id): void
    {
        $table = $kind === 'categorias' ? 'categories' : 'tags';
        if (($_POST['confirm'] ?? '') !== '1') {
            flash('Confirma la eliminación con la casilla.');
            redirect('admin/' . $kind);
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        try {
            DB::query('SELECT id FROM ' . $table . ' WHERE id=? FOR UPDATE', [$id]);
            if ($kind === 'categorias') {
                $replacement = Validator::id($_POST['replacement'] ?? '');
                $count = DB::query('SELECT COUNT(*) FROM articles WHERE category_id=?', [$id])->fetchColumn();
                if ($count && ($replacement === (int) $id || !DB::query('SELECT id FROM categories WHERE id=?', [$replacement])->fetch())) {
                    $pdo->rollBack();
                    flash('La categoría tiene noticias. Elige otra categoría para reasignarlas antes de eliminar.');
                    redirect('admin/categorias');
                }
                if ($count) {
                    DB::query('UPDATE articles SET category_id=?,updated_at=NOW(6) WHERE category_id=?', [$replacement, $id]);
                }
            }
            DB::query('DELETE FROM ' . $table . ' WHERE id=?', [$id]);
            DB::audit('eliminar_' . $kind, $table, (int) $id);
            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
        Cache::clear();
        flash('Elemento eliminado del catálogo.');
        redirect('admin/' . $kind);
    }

    public function users(): void
    {
        $q = is_string($_GET['q'] ?? '') ? mb_substr($_GET['q'] ?? '', 0, 100) : '';
        $trash = ($_GET['trash'] ?? '') === '1';
        $where = 'u.deleted_at IS ' . ($trash ? 'NOT ' : '') . 'NULL AND (u.name LIKE ? OR u.email LIKE ?)';
        $params = ['%' . $q . '%', '%' . $q . '%'];
        $total = (int) DB::query('SELECT COUNT(*) FROM users u WHERE ' . $where, $params)->fetchColumn();
        $pages = max(1, (int) ceil($total / 20));
        $page = Validator::page($_GET['page'] ?? 1, $pages);
        $offset = ($page - 1) * 20;
        $items = DB::query('SELECT u.*,r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE ' . $where . " ORDER BY u.id LIMIT 20 OFFSET $offset", $params)->fetchAll();
        View::render('admin/users', compact('items', 'q', 'trash') + ['title' => 'Usuarios', 'result' => compact('total', 'page', 'pages')]);
    }

    public function userForm(string $id = '0', array $errors = [], ?array $data = null): void
    {
        $data ??= $id !== '0' ? DB::query('SELECT * FROM users WHERE id=?', [Validator::id($id)])->fetch() : [];
        if ($data === false) {
            View::error(404, 'Este usuario no existe.');
            return;
        }
        View::render('admin/user-form', compact('id', 'data', 'errors') + ['title' => $id === '0' ? 'Crear usuario' : 'Editar usuario', 'roles' => Catalog::all('roles')]);
    }

    public function saveUser(string $id = '0'): void
    {
        $errors = [];
        if (!Validator::text($_POST['name'] ?? '', 2, 100)) {
            $errors['name'] = 'Escribe un nombre de 2 a 100 caracteres.';
        }
        if (!Validator::email($_POST['email'] ?? '')) {
            $errors['email'] = 'Escribe un correo válido.';
        } elseif (DB::query('SELECT id FROM users WHERE email=? AND id<>?', [$_POST['email'], $id])->fetch()) {
            $errors['email'] = 'Este correo ya está en uso. Usa uno distinto.';
        }
        $password = $_POST['password'] ?? '';
        if (($id === '0' || $password !== '') && !Validator::password($password)) {
            $errors['password'] = 'Usa entre 8 y 128 caracteres, una mayúscula y un número.';
        }
        $role = Validator::id($_POST['role_id'] ?? '');
        if (!in_array($role, [1, 2, 3], true)) {
            $errors['role_id'] = 'Selecciona un rol válido.';
        }
        if ($errors) {
            $this->userForm($id, $errors, $_POST);
            return;
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        // Bloquea el conjunto de administradores para impedir dos bajas simultáneas.
        $admins = DB::query('SELECT id FROM users WHERE role_id=1 AND active=1 AND deleted_at IS NULL FOR UPDATE')->fetchAll(\PDO::FETCH_COLUMN);
        $old = $id === '0' ? [] : DB::query('SELECT * FROM users WHERE id=? FOR UPDATE', [$id])->fetch();
        $active = isset($_POST['active']) ? 1 : 0;
        if ($id !== '0' && !$old) {
            $pdo->rollBack();
            View::error(404, 'Este usuario no existe.');
            return;
        }
        if ($old && in_array($id, $admins) && count($admins) <= 1 && ($role !== 1 || !$active)) {
            $pdo->rollBack();
            $this->userForm($id, ['role_id' => 'Debe quedar al menos un administrador activo. Crea otro antes de cambiar este rol.'], $_POST);
            return;
        }
        $hash = $password !== '' ? Auth::hash($password) : ($old['password_hash'] ?? '');
        if ($old) {
            DB::query('UPDATE users SET name=?,email=?,role_id=?,active=?,password_hash=? WHERE id=?', [$_POST['name'], $_POST['email'], $role, $active, $hash, $id]);
        } else {
            DB::query('INSERT INTO users (name,email,role_id,active,password_hash,email_verified_at) VALUES (?,?,?,?,?,NOW())', [$_POST['name'], $_POST['email'], $role, $active, $hash]);
            $id = (string) $pdo->lastInsertId();
        }
        DB::audit('guardar_usuario', 'users', (int) $id);
        $pdo->commit();
        flash('Usuario guardado.');
        redirect('admin/usuarios');
    }

    public function userAction(string $id, string $action): void
    {
        if ($action === 'eliminar' && ($_POST['confirm'] ?? '') !== '1') {
            flash('Confirma la eliminación con la casilla.');
            redirect('admin/usuarios');
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        $admins = DB::query('SELECT id FROM users WHERE role_id=1 AND active=1 AND deleted_at IS NULL FOR UPDATE')->fetchAll(\PDO::FETCH_COLUMN);
        if ($action === 'eliminar' && in_array($id, $admins) && count($admins) <= 1) {
            $pdo->rollBack();
            flash('No puedes eliminar al último administrador activo.');
            redirect('admin/usuarios');
        }
        DB::query('UPDATE users SET deleted_at=' . ($action === 'restaurar' ? 'NULL' : 'NOW()') . ' WHERE id=?', [$id]);
        DB::audit($action . '_usuario', 'users', (int) $id);
        $pdo->commit();
        flash($action === 'restaurar' ? 'Usuario restaurado.' : 'Usuario movido a la papelera.');
        redirect('admin/usuarios');
    }

    public function comments(): void
    {
        $status = in_array($_GET['status'] ?? '', ['pendiente', 'aprobado', 'rechazado', 'spam'], true) ? $_GET['status'] : 'pendiente';
        $q = is_string($_GET['q'] ?? '') ? mb_substr($_GET['q'] ?? '', 0, 100) : '';
        $params = [$status, '%' . $q . '%'];
        $total = (int) DB::query('SELECT COUNT(*) FROM comments WHERE status=? AND content LIKE ?', $params)->fetchColumn();
        $pages = max(1, (int) ceil($total / 20));
        $page = Validator::page($_GET['page'] ?? 1, $pages);
        $offset = ($page - 1) * 20;
        $items = DB::query('SELECT c.*,u.name,a.title FROM comments c JOIN users u ON u.id=c.user_id JOIN articles a ON a.id=c.article_id WHERE c.status=? AND c.content LIKE ? ORDER BY c.id DESC LIMIT 20 OFFSET ' . $offset, $params)->fetchAll();
        View::render('admin/comments', compact('items', 'status', 'q') + ['title' => 'Moderar comentarios', 'result' => compact('total', 'pages', 'page')]);
    }

    public function moderate(string $id): void
    {
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['aprobado', 'rechazado', 'spam'], true)) {
            View::error(403, 'Elige una acción de moderación válida.');
            return;
        }
        DB::query('UPDATE comments SET status=? WHERE id=?', [$status, Validator::id($id)]);
        DB::audit('moderar_' . $status, 'comments', (int) $id);
        flash('Comentario actualizado: ' . $status . '.');
        redirect('admin/comentarios');
    }

    public function settings(array $errors = [], ?array $data = null): void
    {
        View::render('admin/settings', ['title' => 'Configuración del sitio', 'data' => $data ?? Catalog::settings(), 'errors' => $errors]);
    }

    public function saveSettings(): void
    {
        $errors = [];
        foreach (['site_name' => 60, 'seo_description' => 160] as $field => $max) {
            if (!Validator::text($_POST[$field] ?? '', 1, $max)) {
                $errors[$field] = 'Escribe un texto de 1 a ' . $max . ' caracteres.';
            }
        }
        if (!Validator::email($_POST['contact_email'] ?? '')) {
            $errors['contact_email'] = 'Escribe un correo de contacto válido.';
        }
        foreach (['social_facebook', 'social_x'] as $field) {
            $value = $_POST[$field] ?? '';
            if ($value && (!is_string($value) || !preg_match('~^https://~', $value) || !filter_var($value, FILTER_VALIDATE_URL))) {
                $errors[$field] = 'Usa una dirección HTTPS completa o deja el campo vacío.';
            }
        }
        $logo = Catalog::settings()['logo'] ?? '';
        if (!$errors) {
            try {
                $logo = Upload::image($_FILES['logo'] ?? []) ?? $logo;
            } catch (\RuntimeException $exception) {
                $errors['logo'] = $exception->getMessage();
            }
        }
        if ($errors) {
            $this->settings($errors, $_POST);
            return;
        }
        $pdo = DB::connection();
        $pdo->beginTransaction();
        foreach (['site_name', 'seo_description', 'contact_email', 'social_facebook', 'social_x'] as $key) {
            DB::query('INSERT INTO settings (`key`,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)', [$key, $_POST[$key] ?? '']);
        }
        DB::query('INSERT INTO settings (`key`,value) VALUES (\'logo\',?) ON DUPLICATE KEY UPDATE value=VALUES(value)', [$logo]);
        DB::audit('guardar_configuracion', 'settings');
        $pdo->commit();
        Cache::clear();
        flash('Configuración actualizada.');
        redirect('admin/configuracion');
    }

    public function audit(): void
    {
        $total = (int) DB::query('SELECT COUNT(*) FROM audit_log')->fetchColumn();
        $pages = max(1, (int) ceil($total / 30));
        $page = Validator::page($_GET['page'] ?? 1, $pages);
        $offset = ($page - 1) * 30;
        $items = DB::query('SELECT l.*,u.name FROM audit_log l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 30 OFFSET ' . $offset)->fetchAll();
        View::render('admin/audit', compact('items') + ['title' => 'Registro de auditoría', 'result' => compact('total', 'page', 'pages')]);
    }
}
