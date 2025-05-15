<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

$volunteer_id = $_GET['volunteer_id'] ?? null;

if (!$volunteer_id || !isLoggedIn()) {
    redirect('/login.php');
}

// Получаем данные волонтёра
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ? AND role = 'volunteer'");
$stmt->execute([$volunteer_id]);
$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$volunteer) {
    die("Волонтёр не найден");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>✍️ Оставить отзыв</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✍️ Оставить отзыв о волонтёре: <?= htmlspecialchars($volunteer['name']) ?></h2>

    <form method="post" action="/controllers/volunteer/leave_feedback.php">
        <input type="hidden" name="volunteer_id" value="<?= $volunteer_id ?>">

        <label for="rating">Оцените волонтёра:</label>
        <select name="rating" id="rating">
            <option value="">Без оценки</option>
            <option value="1">1 звезда</option>
            <option value="2">2 звезды</option>
            <option value="3">3 звезды</option>
            <option value="4">4 звезды</option>
            <option value="5">5 звёзд</option>
        </select>

        <label for="message">Ваш отзыв:</label>
        <textarea name="message" id="message" rows="6" required></textarea>

        <button type="submit">📤 Отправить отзыв</button>
    </form>

    <a href="/views/volunteer_search.php" class="back-link">← Назад</a>
</div>

</body>
</html>