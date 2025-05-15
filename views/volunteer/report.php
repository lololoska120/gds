<?php
require '../../includes/database.php';
require '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'];
    $message = $_POST['message'];
    $volunteer_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO reports (volunteer_id, project_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$volunteer_id, $project_id, $message]);
        redirect('/views/volunteer/project_list.php');
    } catch (PDOException $e) {
        die("Ошибка: " . $e->getMessage());
    }
}
?>