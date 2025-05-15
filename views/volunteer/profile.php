<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

$volunteer_id = $_SESSION['user_id'];

// Получаем средний рейтинг
$stmt = $pdo->prepare("SELECT average_rating, review_count FROM ratings WHERE volunteer_id = ?");
$stmt->execute([$volunteer_id]);
$rating = $stmt->fetch(PDO::FETCH_ASSOC);

$avg = $rating['average_rating'] ?? 0;
$count = $rating['review_count'] ?? 0;
?>

<p>📊 Ваш рейтинг: <strong><?= $avg ?></strong>/5 (<?= $count ?> отзывов)</p>
<a href="/views/volunteer/view_feedbacks.php">📖 Посмотреть все отзывы</a>