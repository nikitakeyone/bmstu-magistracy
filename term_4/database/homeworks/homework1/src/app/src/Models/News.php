<?php

namespace App\Models;

class News
{
    public int $id;
    public string $title;
    public string $slug;

    public ?string $excerpt;
    public ?string $content_html;
    public ?string $content_json;

    public int $category_id;
    public int $author_id;

    public string $status;
    public ?string $published_at;
    public ?string $created_at;
    public ?string $updated_at;

    public ?string $category_name;
    public ?string $category_slug;
    public ?string $author_name;

    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}