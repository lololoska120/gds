<?php
session_start();

// Подключаем БД и функции через DOCUMENT_ROOT — работает независимо от текущей папки
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

$volunteer_id = $_SESSION['user_id'];

// Получаем заявки, на которые откликнулся волонтёр
// Получаем заявки, на которые ты откликнулся
$stmt = $pdo->prepare("SELECT r.id, r.description, r.location, r.cost, r.amount_collected, u.name AS beneficiary_name
                       FROM volunteer_requests vr
                       JOIN help_requests r ON vr.request_id = r.id
                       JOIN users u ON r.beneficiary_id = u.id
                       WHERE vr.volunteer_id = ?
                         AND r.amount_collected < r.cost");
$stmt->execute([$volunteer_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🔔 Ваши уведомления</title>
    <link rel="stylesheet" href="/assets/style/style.css">
</head>
<body>

<div class="container">
    <h2>📩 Ваши уведомления</h2>

    <?php if (empty($requests)): ?>
        <p>Новых уведомлений пока нет</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="notification-card">
                <p><strong>Заявка:</strong> <?= htmlspecialchars($r['description']) ?></p>
                <p><strong>Автор:</strong> <?= htmlspecialchars($r['beneficiary_name']) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Бюджет:</strong> <?= htmlspecialchars($r['cost']) ?></p>
                <a href="/views/project_details.php?id=<?= $r['id'] ?>">📌 Посмотреть заявку</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/views/volunteer/project_list.php" class="back-link">← Назад</a>
</div>

</body>
</html>