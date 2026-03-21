<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;
use App\Config\Database;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

Database::init();

$router = new Router();

$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/news', [\App\Controllers\NewsController::class, 'index']);
$router->get('/news/{slug}', [\App\Controllers\NewsController::class, 'show']);
$router->get('/admin', [\App\Controllers\AdminController::class, 'dashboard']);
$router->get('/admin/login', [\App\Controllers\AdminController::class, 'login']);
$router->post('/admin/login', [\App\Controllers\AdminController::class, 'authenticate']);
$router->get('/admin/news', [\App\Controllers\AdminController::class, 'newsList']);
$router->get('/admin/news/create', [\App\Controllers\AdminController::class, 'createNews']);
$router->post('/admin/news/create', [\App\Controllers\AdminController::class, 'storeNews']);
$router->get('/admin/news/edit/{id}', [\App\Controllers\AdminController::class, 'editNews']);
$router->post('/admin/news/update/{id}', [\App\Controllers\AdminController::class, 'updateNews']);
$router->post('/admin/news/delete/{id}', [\App\Controllers\AdminController::class, 'deleteNews']);
$router->get('/admin/logout', [\App\Controllers\AdminController::class, 'logout']);

$router->dispatch();