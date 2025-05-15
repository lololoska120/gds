<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>👮‍♂️ Админка</title>
    <link rel="stylesheet" href="/assets/style/style.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <div class="logo">
        <h1>👮‍♂️ Панель администратора</h1>
        <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['role']) ?></p>
    </div>

    <div class="admin-menu">
        <h2>📊 Статистика</h2>
        <ul>
            <li><a href="statistics.php">📈 Общая статистика</a></li>
        </ul>

        <h2>👥 Управление пользователями</h2>
        <ul>
            <li><a href="user_management.php">🧾 Список пользователей</a></li>
        </ul>

        <h2>📦 Проекты</h2>
        <ul>
            <li><a href="project_management.php">📋 Все проекты</a></li>
        </ul>

        <h2>🔔 Уведомления</h2>
       
            <a href="notifications.php">📩 Последние заявки</a>
       
    </div>

    <br>
    <div class="links">
        <a class = "buttons"  href="/index.html" class="btn">← На главную</a> | 
        <a href="/login.php" class="btn">Выход</a>
    </div>
</div>

</body>
</html>