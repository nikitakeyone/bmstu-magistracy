<h1>Админ-панель</h1>
<p>Добро пожаловать, <?= htmlspecialchars($currentUser['username']) ?>!</p>

<div style="margin: 20px 0;">
    <a href="/admin/news/create" class="btn">➕ Создать новость</a>
</div>

<h2>Все новости</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Заголовок</th>
            <th>Категория</th>
            <th>Автор</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($news)): ?>
            <tr><td colspan="7">Новостей нет</td></tr>
        <?php else: ?>
            <?php foreach ($news as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= htmlspecialchars($item['category_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['author_name'] ?? '-') ?></td>
                    <td class="status-<?= $item['status'] ?>"><?= htmlspecialchars($item['status']) ?></td>
                    <td><?= date('d.m.Y', strtotime($item['created_at'])) ?></td>
                    <td>
                        <a href="/admin/news/edit/<?= $item['id'] ?>" class="btn" style="padding: 5px 10px;">✏️</a>
                        <form method="POST" action="/admin/news/delete/<?= $item['id'] ?>" style="display:inline;" onsubmit="return confirm('Удалить новость?');">
                            <button type="submit" class="btn btn-danger" style="padding: 5px 10px;">🗑️</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>