<h1><?= isset($news->id) ? 'Редактировать новость' : 'Создать новость' ?></h1>

<form method="POST" action="<?= isset($news->id) ? '/admin/news/update/' . $news->id : '/admin/news/create' ?>" style="max-width: 800px;">
    <div class="form-group">
        <label for="title">Заголовок *</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($news->title ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="excerpt">Краткое описание</label>
        <textarea name="excerpt" id="excerpt" rows="3"><?= htmlspecialchars($news->excerpt ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id">
            <option value="">-- Выберите категорию --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($news->category_id) && $news->category_id == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="content_html">Содержимое (HTML) *</label>
        <textarea name="content_html" id="content_html" rows="15" required><?= htmlspecialchars($news->content_html ?? '') ?></textarea>
        <small>Можно использовать HTML теги</small>
    </div>

    <input type="hidden" name="content_json" value='{}'>

    <div class="form-group">
        <label for="status">Статус</label>
        <select name="status" id="status">
            <option value="draft" <?= (isset($news->status) && $news->status === 'draft') ? 'selected' : '' ?>>Черновик</option>
            <option value="published" <?= (isset($news->status) && $news->status === 'published') ? 'selected' : '' ?>>Опубликовано</option>
        </select>
    </div>

    <div style="margin-top: 20px;">
        <button type="submit" class="btn">💾 Сохранить</button>
        <a href="/admin" class="btn" style="background: #6c757d;">← Отмена</a>
    </div>
</form>