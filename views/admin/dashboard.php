<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Получаем все нерассмотренные заявки
$stmt = $pdo->query("SELECT r.*, u.name AS beneficiary_name 
                     FROM help_requests r
                     JOIN users u ON r.beneficiary_id = u.id
                     WHERE r.status = 'pending'");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>👮‍♂️ Админка</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>🔔 Заявки на помощь</h2>

    <?php if (empty($requests)): ?>
        <p>Новых заявок нет.</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="request-card">
                <p><strong>Автор:</strong> <?= htmlspecialchars($r['beneficiary_name']) ?></p>
                <p><strong>Проблема:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Стоимость:</strong> <?= $r['cost'] ?: 'Не указана' ?></p>

                <form method="post" action="/controllers/admin.php?action=approve_request">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button type="submit">✅ Одобрить</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/index.html">← На главную</a>
</div>

</body>
</html>