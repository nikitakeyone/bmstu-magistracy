<?php

namespace App\Repositories\Postgres;

use App\Config\Database;
use App\Models\News;
use PDO;

class NewsMetaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getPostgres();
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT nm.*, c.name as category_name, c.slug as category_slug,
                       u.username as author_name
                FROM news_meta nm
                LEFT JOIN categories c ON nm.category_id = c.id
                LEFT JOIN users u ON nm.author_id = u.id
                WHERE nm.status = 'published'
                ORDER BY nm.published_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT nm.*, c.name as category_name, c.slug as category_slug,
                       u.username as author_name, u.id as author_id
                FROM news_meta nm
                LEFT JOIN categories c ON nm.category_id = c.id
                LEFT JOIN users u ON nm.author_id = u.id
                WHERE nm.slug = :slug";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM news_meta WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAllForAdmin(): array
    {
        $sql = "SELECT nm.*, c.name as category_name, u.username as author_name
                FROM news_meta nm
                LEFT JOIN categories c ON nm.category_id = c.id
                LEFT JOIN users u ON nm.author_id = u.id
                ORDER BY nm.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO news_meta (title, slug, excerpt, category_id, author_id, status, published_at)
                VALUES (:title, :slug, :excerpt, :category_id, :author_id, :status, :published_at)
                RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "$key = :$key";
        }
        $sets[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE news_meta SET " . implode(', ', $sets) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM news_meta WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM news_meta WHERE status = 'published'";
        return (int) $this->db->query($sql)->fetchColumn();
    }
}