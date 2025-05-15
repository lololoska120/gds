<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}

$request_id = $_GET['id'] ?? null;

if (!$request_id) {
    redirect('/views/beneficiary/notifications.php');
}

try {
    // Проверяем, принадлежит ли заявка этому пользователю
    $stmt = $pdo->prepare("SELECT id FROM help_requests WHERE id = ? AND beneficiary_id = ?");
    $stmt->execute([$request_id, $_SESSION['user_id']]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        throw new Exception("Заявка не найдена или недоступна");
    }

    // Меняем статус на 'rejected'
    $pdo->prepare("UPDATE help_requests SET status = 'rejected' WHERE id = ?")
       ->execute([$request_id]);

    redirect('/views/beneficiary/notifications.php');

} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}