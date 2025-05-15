<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Получаем все заявки
// Показываем заявки, где цель ещё не достигнута
$stmt = $pdo->query("SELECT r.id, r.description, r.location, r.cost, r.amount_collected, u.name AS beneficiary_name
                     FROM help_requests r
                     JOIN users u ON r.beneficiary_id = u.id
                     WHERE r.status = 'approved'
                       AND r.amount_collected < r.cost");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>👮‍♂️ Заявки на проверку</title>
    <style>
        /* Тут можно использовать те же стили, что выше */
    </style>
</head>
<body>

<div class="container">
    <h2>👮‍♂️ Заявки на проверку</h2>

    <?php if (empty($requests)): ?>
        <p>Новых заявок нет</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="request-card">
                <p><strong>Автор:</strong> <?= htmlspecialchars($r['beneficiary_name']) ?></p>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Стоимость:</strong> <?= htmlspecialchars($r['cost'] ?: 'Не указана') ?></p>

                <!-- Статус -->
                <p><strong>Статус:</strong>
                    <?php if ($r['status'] === 'pending'): ?>
                        <span class="status pending">⏳ Ожидает</span>
                    <?php elseif ($r['status'] === 'approved'): ?>
                        <span class="status approved">✅ Одобрена</span>
                    <?php else: ?>
                        <span class="status rejected">❌ Отклонена</span>
                    <?php endif; ?>
                </p>

                <!-- Фото -->
                <?php if (!empty($r['photo'])): ?>
                    <img src="<?= htmlspecialchars($r['photo']) ?>" style="max-width: 100%; border-radius: 6px; margin-bottom: 10px;">
                <?php endif; ?>

                <!-- Кнопки управления -->
                <?php if ($r['status'] === 'pending'): ?>
                    <a href="/admin/approve.php?id=<?= $r['id'] ?>" onclick="return confirm('Одобрить заявку?')">✅ Одобрить</a>
                    <a href="/admin/reject.php?id=<?= $r['id'] ?>" onclick="return confirm('Отклонить заявку?')">❌ Отклонить</a>
                <?php endif; ?>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>