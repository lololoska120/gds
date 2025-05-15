<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

$request_id = $_GET['id'] ?? null;

if (!$request_id) {
    redirect('/admin/notifications.php');
}

try {
    // Обновляем статус на "rejected"
    $pdo->prepare("UPDATE help_requests SET status = 'rejected' WHERE id = ?")->execute([$request_id]);
    redirect('/admin/notifications.php');
} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}