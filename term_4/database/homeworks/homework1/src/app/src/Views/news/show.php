<?php if (!$news): ?>
    <div class="error-message" style="text-align: center; padding: 50px;">
        <h1>🚫 Новость не найдена</h1>
        <p>К сожалению, запрашиваемая страница не существует.</p>
        <a href="/news" class="btn" style="margin-top: 20px;">← Вернуться к списку новостей</a>
    </div>
<?php else: ?>
    <article>
        <h1><?= htmlspecialchars($news->title ?? 'Без названия') ?></h1>

        <div class="news-meta" style="margin: 20px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <?php if (!empty($news->category_name)): ?>
                <span>📁 <?= htmlspecialchars($news->category_name) ?></span> |
            <?php endif; ?>
            <span>✍️ <?= htmlspecialchars($news->author_name ?? 'Аноним') ?></span> |
            <span>📅 <?= !empty($news->published_at) ? date('d.m.Y H:i', strtotime($news->published_at)) : 'Дата не указана' ?></span>
        </div>

        <div class="news-content" style="margin: 20px 0;">
            <?= $news->content_html ?? '' ?>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="/news" class="btn">← Назад к новостям</a>
        </div>
    </article>
<?php endif; ?>