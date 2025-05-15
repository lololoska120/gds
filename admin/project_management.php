<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Получаем все заявки (из help_requests)
$stmt = $pdo->query("SELECT r.id, r.description, u.name AS author, r.status, r.location, r.cost 
                     FROM help_requests r
                     JOIN users u ON r.beneficiary_id = u.id");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>📋 Управление проектами</title>
    <link rel="stylesheet" href="/assets/style/style.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <h2>📦 Все заявки</h2>

    <?php if (empty($requests)): ?>
        <p>Нет созданных заявок</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="request-card status-<?= $r['status'] ?>">
                <p><strong>Автор:</strong> <?= htmlspecialchars($r['author']) ?></p>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Бюджет:</strong> <?= $r['cost'] ?: 'Не указан' ?></p>
                <p><strong>Статус:</strong> <?= ucfirst($r['status']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/admin/dashboard.php" class="btn">← Назад</a>
</div>

</body>
</html>