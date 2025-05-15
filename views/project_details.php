<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    die("Проект не найден");
}

try {
    // Получаем данные проекта
    $stmt = $pdo->prepare("SELECT r.id, r.description, r.location, r.cost, r.photo, r.amount_collected, u.name AS beneficiary_name
                           FROM help_requests r
                           JOIN users u ON r.beneficiary_id = u.id
                           WHERE r.id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        die("Заявка не найдена");
    }

    // Проверяем, достигнута ли цель
    $goal_reached = $project['amount_collected'] >= $project['cost'];

} catch (PDOException $e) {
    die("Ошибка при загрузке данных: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['description']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .project-photo {
            max-width: 100%;
            border-radius: 6px;
        }

        .goal-reached {
            text-align: center;
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            font-weight: bold;
        }

        .donate-form {
            margin-top: 20px;
        }

        .donate-form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .donate-form button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .donate-form button:hover {
            background-color: #357ABD;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            width: fit-content;
            margin: 20px auto;
        }

        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($project['beneficiary_name']) ?></h2>
    <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
    <p><strong>Место:</strong> <?= htmlspecialchars($project['location']) ?></p>
    <p><strong>Стоимость помощи:</strong> <?= number_format($project['cost'], 2, ',', ' ') ?> ₽</p>
    <p><strong>Уже собрано:</strong> <?= number_format($project['amount_collected'], 2, ',', ' ') ?> ₽</p>

    <?php if ($goal_reached): ?>
        <div class="goal-reached">✅ Цель достигнута — помощь больше не требуется</div>
    <?php else: ?>
        <!-- Форма перевода -->
        <form class="donate-form" method="POST" action="/controllers/volunteer/donate.php">
            <input type="hidden" name="request_id" value="<?= $project['id'] ?>">
            <input type="number" name="amount" placeholder="Введите сумму" step="0.01" min="0.01" required>
            <button type="submit">💸 Перевести</button>
        </form>
    <?php endif; ?>

    <!-- Кнопка назад -->
    <a href="/views/volunteer/project_list.php" class="back-link">← Назад</a>
</div>

</body>
</html>