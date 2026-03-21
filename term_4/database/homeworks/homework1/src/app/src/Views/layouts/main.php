<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Новости') ?> - News App</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #333; color: #fff; padding: 1rem 0; }
        header nav { display: flex; justify-content: space-between; align-items: center; }
        header nav a { color: #fff; text-decoration: none; margin-left: 20px; }
        header nav a:hover { text-decoration: underline; }
        .content { background: #fff; padding: 20px; margin-top: 20px; border-radius: 5px; }
        .news-item { border-bottom: 1px solid #ddd; padding: 20px 0; }
        .news-item h2 a { color: #333; text-decoration: none; }
        .news-item h2 a:hover { color: #007bff; }
        .news-meta { color: #666; font-size: 0.9em; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group textarea { min-height: 300px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; }
        .status-published { color: green; }
        .status-draft { color: orange; }
        .pagination { margin: 20px 0; text-align: center; }
        .pagination a { margin: 0 5px; padding: 5px 10px; background: #007bff; color: #fff; text-decoration: none; border-radius: 3px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        footer { text-align: center; padding: 20px; margin-top: 20px; color: #666; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div>
                    <a href="/">🏠 Главная</a>
                    <a href="/news">📰 Новости</a>
                </div>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/admin">⚙️ Админка</a>
                        <a href="/admin/logout">🚪 Выход</a>
                        <span>(<?= htmlspecialchars($_SESSION['username']) ?>)</span>
                    <?php else: ?>
                        <a href="/admin/login">🔐 Вход</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="content">
            <?php include __DIR__ . '/../' . $content; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> News App. PostgreSQL + MongoDB</p>
    </footer>
</body>
</html>