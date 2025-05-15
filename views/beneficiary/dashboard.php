<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Привет, <?= htmlspecialchars($_SESSION['role']) ?>!</h2>
    <p><a href="/views/beneficiary/requests.php">📌 Мои заявки</a></p>
    
    <!-- Кнопка уведомлений -->
    <p><a href="/views/beneficiary/notifications.php" class="btn">📩 Уведомления</a></p>
    
    <p><a href="/login.php">← Выйти</a></p>
</div>

</body>
</html>