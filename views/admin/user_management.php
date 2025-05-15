<?php
require '../../includes/database.php';

$stmt = $pdo->query("SELECT id, title, fund_name, deadline FROM projects");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Проекты</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Фонд</th>
        <th>Срок</th>
    </tr>
    <?php foreach ($projects as $project): ?>
        <tr>
            <td><?= $project['id'] ?></td>
            <td><?= htmlspecialchars($project['title']) ?></td>
            <td><?= htmlspecialchars($project['fund_name']) ?></td>
            <td><?= $project['deadline'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>