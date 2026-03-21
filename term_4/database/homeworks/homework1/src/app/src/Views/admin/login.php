<h1>Вход в админ-панель</h1>

<form method="POST" action="/admin/login" style="max-width: 400px; margin: 20px auto;">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
    </div>

    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
    </div>

    <button type="submit" class="btn">Войти</button>
</form>

<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
    <strong>Тестовые данные:</strong><br>
    Email: admin@example.com<br>
    Пароль: password
</div>