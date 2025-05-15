<?php
session_start();

// Подключаем БД и функции
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$volunteer_id = $_POST['volunteer_id'] ?? null;
$message = $_POST['message'] ?? null;
$rating = $_POST['rating'] ?? null;

if (!$volunteer_id || !$message) {
    redirect("/views/volunteer_search.php");
}

try {
    // Если рейтинг не выбран — передаём NULL
    $rating = is_numeric($rating) ? (int)$rating : null;

    // Сохраняем отзыв
    $pdo->prepare("INSERT INTO volunteer_feedback (volunteer_id, from_user_id, message, rating, created_at)
                   VALUES (?, ?, ?, ?, NOW())")
       ->execute([
           $volunteer_id,
           $_SESSION['user_id'],
           $message,
           $rating // Теперь это либо число, либо NULL
       ]);

    redirect("/views/volunteer_search.php");

} catch (PDOException $e) {
    die("Ошибка при отправке отзыва: " . $e->getMessage());
}