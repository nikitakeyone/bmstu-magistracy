<?php

namespace App\Models;

class Category
{
    public int $id;
    public string $name;
    public string $slug;
    public ?int $parent_id;
    public ?string $created_at;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}