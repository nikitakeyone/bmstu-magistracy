<?php

namespace App\Repositories\Postgres;

use App\Config\Database;
use App\Models\Category;
use PDO;

class CategoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getPostgres();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $name, string $slug, ?int $parentId = null): int
    {
        $sql = "INSERT INTO categories (name, slug, parent_id) VALUES (:name, :slug, :parent_id) RETURNING id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId
        ]);
        return (int) $stmt->fetchColumn();
    }
}