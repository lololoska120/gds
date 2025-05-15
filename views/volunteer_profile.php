<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

$volunteer_id = $_GET['id'] ?? null;
if (!$volunteer_id) {
    die("Не указан ID волонтера");
}

// Получаем данные волонтера
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ? AND role = 'volunteer'");
$stmt->execute([$volunteer_id]);
$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$volunteer) {
    die("Волонтер не найден");
}

// Получаем отзывы о волонтере
$stmt = $pdo->prepare("SELECT f.*, u.name AS from_name 
                       FROM feedbacks f
                       JOIN users u ON f.from_user_id = u.id
                       WHERE f.to_volunteer_id = ?");
$stmt->execute([$volunteer_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🧑‍🤝‍🧑 <?= htmlspecialchars($volunteer['name']) ?></title>
   <link rel="stylesheet" href="/assets/style/otz.css">
</head>
<body>

<div class="container">
    <h2>🧑‍🤝‍🧑 Профиль: <?= htmlspecialchars($volunteer['name']) ?></h2>
    <p>Email: <?= htmlspecialchars($volunteer['email']) ?></p>

    <h3>💬 Отзывы:</h3>

    <?php if (empty($feedbacks)): ?>
        <p>Пока нет отзывов</p>
    <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
            <div class="feedback-card">
                <p><strong><?= htmlspecialchars($f['from_name']) ?>:</strong></p>
                <p><?= nl2br(htmlspecialchars($f['comment'])) ?></p>
                <p>⭐ Рейтинг: <?= $f['rating'] ?>/5</p>
                <small><?= $f['created_at'] ?></small>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Кнопка "Оставить отзыв" -->
    <a href="/views/leave_feedback.php?volunteer_id=<?= $volunteer_id ?>" class="btn">✍️ Оставить отзыв</a>

    <br><br>
    <a href="/views/volunteer_search.php">← Назад к списку</a>
</div>

</body>
</html>