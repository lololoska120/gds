<?php
require '../includes/database.php';

// За месяц
$stmt = $pdo->query("SELECT SUM(collected_amount) AS total FROM projects WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$monthlyTotal = $stmt->fetch();

// Самый популярный проект
$stmt = $pdo->query("SELECT p.title, COUNT(vp.project_id) AS volunteers_count
                     FROM volunteers_projects vp
                     JOIN projects p ON vp.project_id = p.id
                     GROUP BY p.id ORDER BY volunteers_count DESC LIMIT 1");
$topProject = $stmt->fetch();

// Кому быстрее всего помогли
$stmt = $pdo->query("SELECT title, DATEDIFF(completed_at, created_at) AS days_to_complete
                     FROM projects
                     WHERE status = 'completed'
                     ORDER BY days_to_complete ASC LIMIT 1");
$fastHelp = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>📊 Статистика</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>📊 Общая статистика</h2>

    <div class="stat-box">
        <h3>Собрано за месяц</h3>
        <p><?= number_format($monthlyTotal['total'] ?? 0, 2, ',', ' ') ?> ₽</p>
    </div>

    <div class="stat-box">
        <h3>Самый популярный проект</h3>
        <p><?= htmlspecialchars($topProject['title']) ?> — <?= $topProject['volunteers_count'] ?> волонтеров</p>
    </div>

    <div class="stat-box">
        <h3>Быстрее всех помогли</h3>
        <p><?= htmlspecialchars($fastHelp['title']) ?> за <?= $fastHelp['days_to_complete'] ?> дней</p>
    </div>

    <div class="links">
        <p><a href="/admin/dashboard.php">← Назад</a></p>
    </div>
</div>

</body>
</html>