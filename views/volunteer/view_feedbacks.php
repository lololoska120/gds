<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

$volunteer_id = $_SESSION['user_id'];

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
    <title>Мои отзывы</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>💬 Ваши отзывы</h2>

    <?php if (empty($feedbacks)): ?>
        <p>Пока нет отзывов</p>
    <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
            <div class="feedback-card">
                <p><strong><?= htmlspecialchars($f['from_name']) ?></strong> оставил(а):</p>
                <p><?= nl2br(htmlspecialchars($f['comment'])) ?></p>
                <p>⭐ Рейтинг: <?= $f['rating'] ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/views/volunteer/project_list.php">← Назад</a>
</div>

</body>
</html>