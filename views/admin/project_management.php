<?php
require '../../includes/database.php';

$stmt = $pdo->query("SELECT id, name, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Пользователи</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Роль</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>