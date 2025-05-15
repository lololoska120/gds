<?php
require '../../includes/database.php';

$project_id = $_GET['id'] ?? null;
if (!$project_id) die("Проект не найден");

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) die("Проект не найден");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($project['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
    <p><strong>Срок:</strong> <?= $project['deadline'] ?></p>
    <p><strong>Фонд:</strong> <?= htmlspecialchars($project['fund_name']) ?></p>

    <form action="../controllers/volunteer.php?action=report" method="POST">
        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
        <textarea name="message" placeholder="Отчет о помощи"></textarea><br>
        <button type="submit">Отправить отчет</button>
    </form>
</body>
</html>