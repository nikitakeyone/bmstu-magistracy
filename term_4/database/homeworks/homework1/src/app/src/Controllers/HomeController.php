<?php

namespace App\Controllers;

use App\Core\Response;
use App\Services\NewsService;
use App\Repositories\Postgres\NewsMetaRepository;
use App\Repositories\Postgres\CategoryRepository;
use App\Repositories\Mongo\NewsContentRepository;

class HomeController
{
    private Response $response;
    private NewsService $newsService;

    public function __construct()
    {
        $this->response = new Response();
        $this->newsService = new NewsService(
            new NewsMetaRepository(),
            new CategoryRepository(),
            new NewsContentRepository()
        );
    }

    public function index(): void
    {
        $news = $this->newsService->getPublishedNews(5);
        $categories = $this->newsService->getCategories();

        $this->response->view('layouts/main.php', [
            'title' => 'Главная',
            'content' => 'home/index.php',
            'news' => $news,
            'categories' => $categories
        ]);
    }
}