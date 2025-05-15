<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

$request_id = $_POST['request_id'] ?? null;
$amount = $_POST['amount'] ?? null;

if (!$request_id || !$amount || $amount <= 0) {
    redirect("/views/project_details.php?id=$request_id");
}

try {
    // Сохраняем донат
    $pdo->prepare("INSERT INTO donations (request_id, volunteer_id, amount) VALUES (?, ?, ?)")
       ->execute([$request_id, $_SESSION['user_id'], $amount]);

    // Обновляем общую сумму
    $pdo->prepare("UPDATE help_requests SET amount_collected = amount_collected + ? WHERE id = ?")
       ->execute([$amount, $request_id]);

    redirect("/views/project_details.php?id=$request_id");

} catch (PDOException $e) {
    die("Ошибка при переводе: " . $e->getMessage());
}