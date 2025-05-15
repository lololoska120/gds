<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Получаем всех пользователей
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🧾 Управление пользователями</title>
    <link rel="stylesheet" href="/assets/style/style.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <h2>👥 Пользователи системы</h2>

    <?php if (empty($users)): ?>
        <p>Нет зарегистрированных пользователей.</p>
    <?php else: ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Дата регистрации</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr class="<?= $u['role'] ?>">
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= ucfirst($u['role']) ?></td>
                        <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="/admin/dashboard.php" class="btn">← Назад</a>
</div>

</body>
</html>