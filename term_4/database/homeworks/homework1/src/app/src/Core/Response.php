<?php

namespace App\Core;

class Response
{
    public function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    public function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function view(string $template, array $data = []): void
    {
        extract($data);
        include __DIR__ . "/../Views/$template";
    }
}