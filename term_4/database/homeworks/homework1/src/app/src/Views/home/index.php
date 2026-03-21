<h1>Последние новости</h1>

<?php if (empty($news)): ?>
    <p>Новостей пока нет.</p>
<?php else: ?>
    <?php foreach ($news as $item): ?>
        <div class="news-item">
            <h2>
                <a href="/news/<?= htmlspecialchars($item->slug ?? '') ?>">
                    <?= htmlspecialchars($item->title ?? 'Без названия') ?>
                </a>
            </h2>
            <div class="news-meta">
                <?php if (!empty($item->category_name)): ?>
                    <span>📁 <?= htmlspecialchars($item->category_name) ?></span> |
                <?php endif; ?>
                <span>✍️ <?= htmlspecialchars($item->author_name ?? 'Аноним') ?></span> |
                <span>📅 <?= !empty($item->published_at) ? date('d.m.Y', strtotime($item->published_at)) : 'Дата не указана' ?></span>
            </div>
            <?php if (!empty($item->excerpt)): ?>
                <p><?= htmlspecialchars(mb_substr($item->excerpt, 0, 200)) ?>...</p>
            <?php endif; ?>
            <a href="/news/<?= htmlspecialchars($item->slug ?? '') ?>" class="btn">Читать далее →</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div style="margin-top: 20px;">
    <a href="/news" class="btn">Все новости</a>
</div>