<?php

namespace App\Controllers;

use App\Core\Response;
use App\Services\NewsService;
use App\Repositories\Postgres\NewsMetaRepository;
use App\Repositories\Postgres\CategoryRepository;
use App\Repositories\Mongo\NewsContentRepository;

class NewsController
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
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $news = $this->newsService->getPublishedNews($limit, $offset);
        $categories = $this->newsService->getCategories();

        $totalNews = count($this->newsService->getAllNewsForAdmin());
        $totalPages = ceil($totalNews / $limit);

        $this->response->view('layouts/main.php', [
            'title' => 'Новости',
            'content' => 'news/index.php',
            'news' => $news,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function show(array $params): void
    {
        $news = $this->newsService->getNewsBySlug($params['slug']);

        if (!$news) {
            http_response_code(404);
            $this->response->view('layouts/main.php', [
                'title' => 'Страница не найдена',
                'content' => 'errors/404.php',
                'categories' => $this->newsService->getCategories()
            ]);
            return;
        }

        $categories = $this->newsService->getCategories();

        $this->response->view('layouts/main.php', [
            'title' => $news->title ?? 'Новость',
            'content' => 'news/show.php',
            'news' => $news,
            'categories' => $categories
        ]);
}
}