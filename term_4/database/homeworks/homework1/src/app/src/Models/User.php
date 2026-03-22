<?php

namespace App\Models;

class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $password_hash;
    public string $role;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAuthor(): bool
    {
        return in_array($this->role, ['admin', 'author']);
    }
}