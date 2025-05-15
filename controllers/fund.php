<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

// ❌ Раньше могло быть так:
// if (!isLoggedIn() || userRole() !== 'fund') { ... }

// ✅ Теперь можно просто проверять вход
if (!isLoggedIn()) {
    redirect('/login.php');
}

switch ($_GET['action']) {
    case 'view_donations':
        $stmt = $pdo->query("SELECT p.title, p.collected_amount, p.target_amount 
                             FROM projects p WHERE p.status = 'open'");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require '../views/fund/donations.php';
        break;

    default:
        echo "Неизвестная команда";
}
?>