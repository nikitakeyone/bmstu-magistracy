<?php

namespace App\Controllers;

use App\Core\Response;
use App\Services\NewsService;
use App\Repositories\Postgres\NewsMetaRepository;
use App\Repositories\Postgres\CategoryRepository;
use App\Repositories\Postgres\UserRepository;
use App\Repositories\Mongo\NewsContentRepository;

class AdminController
{
    private Response $response;
    private NewsService $newsService;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->response = new Response();
        $this->newsService = new NewsService(
            new NewsMetaRepository(),
            new CategoryRepository(),
            new NewsContentRepository()
        );
        $this->userRepo = new UserRepository();
    }

    private function requireAuth(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->response->redirect('/admin/login');
            return null;
        }

        $user = $this->userRepo->findById($_SESSION['user_id']);
        if (!$user || !in_array($user['role'], ['admin', 'author'])) {
            session_destroy();
            $this->response->redirect('/admin/login');
            return null;
        }

        return $user;
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->response->view('layouts/main.php', [
            'title' => 'Вход',
            'content' => 'admin/login.php',
            'error' => null
        ]);
    }

    public function authenticate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userRepo->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $this->response->redirect('/admin');
        } else {
            $this->response->view('layouts/main.php', [
                'title' => 'Вход',
                'content' => 'admin/login.php',
                'error' => 'Неверный email или пароль'
            ]);
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->response->redirect('/admin/login');
    }

    public function dashboard(): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $news = $this->newsService->getAllNewsForAdmin();
        $categories = $this->newsService->getCategories();

        $this->response->view('layouts/main.php', [
            'title' => 'Админ-панель',
            'content' => 'admin/dashboard.php',
            'news' => $news,
            'categories' => $categories,
            'currentUser' => $user
        ]);
    }

    public function newsList(): void
    {
        $this->dashboard();
    }

    public function createNews(): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $categories = $this->newsService->getCategories();

        $this->response->view('layouts/main.php', [
            'title' => 'Создать новость',
            'content' => 'admin/news-form.php',
            'categories' => $categories,
            'news' => null,
            'currentUser' => $user
        ]);
    }

    public function storeNews(): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $data = [
            'title' => $_POST['title'] ?? '',
            'excerpt' => $_POST['excerpt'] ?? '',
            'content_html' => $_POST['content_html'] ?? '',
            'content_json' => $_POST['content_json'] ?? '{}',
            'category_id' => $_POST['category_id'] ?? null,
            'status' => $_POST['status'] ?? 'draft'
        ];

        try {
            $this->newsService->createNews($data, $user['id']);
            $this->response->redirect('/admin');
        } catch (\Exception $e) {
            $this->response->view('layouts/main.php', [
                'title' => 'Ошибка',
                'content' => 'admin/news-form.php',
                'error' => $e->getMessage(),
                'categories' => $this->newsService->getCategories(),
                'news' => null,
                'currentUser' => $user
            ]);
        }
    }

    public function editNews(array $params): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $newsMeta = (new NewsMetaRepository())->findById($params['id']);
        if (!$newsMeta) {
            $this->response->redirect('/admin');
            return;
        }

        $newsContent = (new NewsContentRepository())->findByNewsId($params['id']);

        $news = (object) array_merge($newsMeta, [
            'content_html' => $newsContent['content_html'] ?? '',
            'content_json' => $newsContent['content_json'] ?? '{}'
        ]);

        $categories = $this->newsService->getCategories();

        $this->response->view('layouts/main.php', [
            'title' => 'Редактировать новость',
            'content' => 'admin/news-form.php',
            'categories' => $categories,
            'news' => $news,
            'currentUser' => $user
        ]);
    }

    public function updateNews(array $params): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        $data = [
            'title' => $_POST['title'] ?? '',
            'excerpt' => $_POST['excerpt'] ?? '',
            'content_html' => $_POST['content_html'] ?? '',
            'content_json' => $_POST['content_json'] ?? '{}',
            'category_id' => $_POST['category_id'] ?? null,
            'status' => $_POST['status'] ?? 'draft'
        ];

        try {
            $this->newsService->updateNews($params['id'], $data, $user['id']);
            $this->response->redirect('/admin');
        } catch (\Exception $e) {
            $this->response->redirect('/admin/news/edit/' . $params['id']);
        }
    }

    public function deleteNews(array $params): void
    {
        $user = $this->requireAuth();
        if (!$user) return;

        try {
            $this->newsService->deleteNews($params['id']);
            $this->response->redirect('/admin');
        } catch (\Exception $e) {
            $this->response->redirect('/admin');
        }
    }
}