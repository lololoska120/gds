<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}

$action = $_GET['action'] ?? null;
$request_id = $_GET['id'] ?? null;

if ($action === 'cancel_request' && $request_id) {
    try {
        // Проверяем, принадлежит ли заявка этому пользователю
        $stmt = $pdo->prepare("SELECT id FROM help_requests WHERE id = ? AND beneficiary_id = ?");
        $stmt->execute([$request_id, $_SESSION['user_id']]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            die("Заявка не найдена");
        }

        // Меняем статус на 'rejected'
        $pdo->prepare("UPDATE help_requests SET status = 'rejected' WHERE id = ?")
           ->execute([$request_id]);

        redirect('/views/beneficiary/notifications.php');
    } catch (PDOException $e) {
        die("Ошибка: " . $e->getMessage());
    }
}

redirect('/views/beneficiary/notifications.php');