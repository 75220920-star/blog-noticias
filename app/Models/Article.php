<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use App\Core\Validator;

final class Article
{
    private const SELECT = 'SELECT a.*, c.name AS category, c.slug AS category_slug, u.name AS author FROM articles a JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.author_id';
    private const PUBLIC_WHERE = " a.deleted_at IS NULL AND a.status='publicado' AND a.published_at<=NOW()";

    public static function listing(array $filters = [], mixed $page = 1, int $perPage = 9, ?array $editor = null): array
    {
        $where = $editor ? ' a.deleted_at IS NULL' : self::PUBLIC_WHERE;
        $params = [];
        if ($editor && $editor['role'] === 'editor') {
            $where .= ' AND a.author_id=?';
            $params[] = $editor['id'];
        }
        if (!empty($filters['trash']) && $editor) {
            $where = str_replace('a.deleted_at IS NULL', 'a.deleted_at IS NOT NULL', $where);
        }
        foreach (['category_id', 'status'] as $field) {
            if (!empty($filters[$field])) {
                $where .= ' AND a.' . $field . '=?';
                $params[] = $filters[$field];
            }
        }
        if (!empty($filters['tag_id'])) {
            $where .= ' AND EXISTS (SELECT 1 FROM article_tag atg WHERE atg.article_id=a.id AND atg.tag_id=?)';
            $params[] = $filters['tag_id'];
        }
        if (!empty($filters['q'])) {
            $where .= ' AND MATCH(a.title,a.content) AGAINST (? IN NATURAL LANGUAGE MODE)';
            $params[] = $filters['q'];
        }
        $total = (int) DB::query('SELECT COUNT(*) FROM articles a WHERE' . $where, $params)->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = Validator::page($page, $pages);
        $offset = ($page - 1) * $perPage;
        $items = DB::query(self::SELECT . ' WHERE' . $where . " ORDER BY a.published_at DESC, a.id DESC LIMIT $perPage OFFSET $offset", $params)->fetchAll();
        return compact('items', 'total', 'pages', 'page');
    }

    public static function published(string $slug): array|false
    {
        return DB::query(self::SELECT . ' WHERE' . self::PUBLIC_WHERE . ' AND a.slug=?', [$slug])->fetch();
    }

    public static function find(int $id): array|false
    {
        return DB::query(self::SELECT . ' WHERE a.id=?', [$id])->fetch();
    }

    public static function popular(): array
    {
        return DB::query(self::SELECT . ' WHERE' . self::PUBLIC_WHERE . ' ORDER BY a.views DESC, a.id DESC LIMIT 4')->fetchAll();
    }

    public static function featured(): array|false
    {
        return DB::query(self::SELECT . ' WHERE' . self::PUBLIC_WHERE . ' ORDER BY a.featured DESC, a.published_at DESC LIMIT 1')->fetch();
    }
}
