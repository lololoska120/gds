<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Статистика 1: Заявок за месяц
$stmt = $pdo->query("SELECT COUNT(*) AS total_requests FROM help_requests WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$requestsThisMonth = $stmt->fetch();

// Статистика 2: Самый популярный проект
$stmt = $pdo->query("
    SELECT p.description AS title, COUNT(vp.volunteer_id) AS volunteers_count 
    FROM help_requests p
    LEFT JOIN volunteer_requests vp ON p.id = vp.request_id
    WHERE p.status = 'approved'
    GROUP BY p.id ORDER BY volunteers_count DESC LIMIT 1
");
$topProject = $stmt->fetch(PDO::FETCH_ASSOC);

// Статистика 3: Кому быстрее всего помогли
$stmt = $pdo->query("
    SELECT p.description AS title, DATEDIFF(p.completed_at, p.created_at) AS days_to_complete
    FROM help_requests p
    WHERE p.status = 'approved' AND p.completed_at IS NOT NULL
    ORDER BY days_to_complete ASC LIMIT 1
");
$fastHelp = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>📊 Статистика</title>
    <link rel="stylesheet" href="/assets/style/style.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <h2>📈 Общая статистика</h2>

    <div class="stat-box">
        <h3>Заявок за месяц</h3>
        <p><?= $requestsThisMonth['total_requests'] ?? 0 ?></p>
    </div>

    <div class="stat-box">
        <h3>Самый популярный проект</h3>
        <p><?= htmlspecialchars($topProject['title'] ?? 'Нет данных') ?>
            (<?= $topProject['volunteers_count'] ?? 0 ?> волонтеров)</p>
    </div>

    <div class="stat-box">
        <h3>Быстрее всех помог</h3>
    </div>
    <div class = "stat-box">
        <a href="/admin/dashboard.php">Выйти</a>
    </div>