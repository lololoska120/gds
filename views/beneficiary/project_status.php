<?php
require '../../includes/database.php';

$beneficiary_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT p.title, p.status FROM projects p
                       JOIN help_requests hr ON p.request_id = hr.id
                       WHERE hr.beneficiary_id = ?");
$stmt->execute([$beneficiary_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Статус ваших проектов</h1>
<ul>
    <?php foreach ($projects as $project): ?>
        <li><?= htmlspecialchars($project['title']) ?> — <?= htmlspecialchars($project['status']) ?></li>
    <?php endforeach; ?>
</ul>