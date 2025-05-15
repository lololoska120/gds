<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

$volunteer_id = $_SESSION['user_id'];

// Получаем только те заявки, где сбор ещё не завершён
$stmt = $pdo->query("SELECT r.id, r.description, r.location, r.cost, r.photo, r.amount_collected, u.name AS beneficiary_name
                     FROM help_requests r
                     JOIN users u ON r.beneficiary_id = u.id
                     WHERE r.status = 'approved'
                       AND r.amount_collected < r.cost");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Заявки, на которые ты уже откликнулся
$stmt = $pdo->prepare("SELECT request_id FROM volunteer_requests WHERE volunteer_id = ?");
$stmt->execute([$volunteer_id]);
$joined_requests = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'request_id');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Проекты для участия</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-weight: bold;
        }

        .project-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            position: relative;
        }

        .profile-photo {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 50%;
            display: block;
            margin: 0 auto 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .project-card h3 {
            text-align: center;
            font-size: 22px;
            color: #2c3e50;
        }

        .project-card p {
            color: #555;
            line-height: 1.5;
        }

        .location {
            color: #888;
            font-style: italic;
        }

        .budget {
            color: #2e7d32;
            font-weight: bold;
        }

        .progress-bar {
            background-color: #e9ecef;
            height: 12px;
            border-radius: 20px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress {
            height: 100%;
            background: linear-gradient(to right, #4a90e2, #6fb1ff);
            width: <?= min(100, ($r['amount_collected'] / $r['cost']) * 100) ?>%;
            transition: width 0.5s ease;
        }

        .amount-collected {
            margin-top: 5px;
            font-size: 14px;
            color: #333;
        }

        .help-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            color: white;
            text-align: center;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .help-btn:hover {
            background-color: #357ABD;
        }

        .more-info {
            display: inline-block;
            margin-top: 10px;
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
        }

        .more-info:hover {
            text-decoration: underline;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #333;
            font-weight: bold;
            text-decoration: none;
            background: #fff;
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: fit-content;
            margin: 40px auto 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .back-link:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🟢 Доступные заявки</h2>

    <?php if (empty($requests)): ?>
        <p>Нет активных заявок — все цели достигнуты!</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="project-card">
                <?php if (!empty($r['photo'])): ?>
                    <img src="<?= htmlspecialchars($r['photo']) ?>" alt="Фото заявки" class="profile-photo">
                <?php endif; ?>

                <h3><?= htmlspecialchars($r['beneficiary_name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p class="location">📍 Место: <?= htmlspecialchars($r['location']) ?></p>
                <p class="budget">💰 Цель: <?= number_format($r['cost'], 0, '', ' ') ?> ₽</p>

                <!-- Прогресс -->
                <div class="progress-bar">
                    <div class="progress" style="width: <?= min(100, ($r['amount_collected'] / $r['cost']) * 100) ?>%;"></div>
                </div>

                <!-- Собранные средства -->
                <p class="amount-collected">
                    Уже собрано: <?= number_format($r['amount_collected'], 0, '', ' ') ?> ₽
                </p>

                <!-- Кнопка отклика -->
                <?php if (in_array($r['id'], $joined_requests)): ?>
                    <button disabled class="help-btn">✅ Вы уже откликнулись</button>
                <?php else: ?>
                    <form method="post" action="/controllers/volunteer.php?action=join_request">
                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                        <button type="submit" class="help-btn">📩 Присоединиться к заявке</button>
                    </form>
                <?php endif; ?>

                <!-- Подробности -->
                <a href="/views/project_details.php?id=<?= $r['id'] ?>" class="more-info">📖 Узнать историю</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/index.html" class="back-link">← На главную</a>
</div>

</body>
</html>