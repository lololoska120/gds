<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}

$request_id = $_GET['request_id'] ?? null;

// Получаем ID волонтера из последнего отклика
$stmt = $pdo->prepare("SELECT volunteer_id 
                        FROM volunteer_requests 
                        WHERE request_id = ? 
                        ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$request_id]);
$vr = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vr) {
    die("Нет откликов на эту заявку");
}

$volunteer_id = $vr['volunteer_id'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оставить отзыв</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>📝 Оставить отзыв о волонтере</h2>

    <form method="post" action="/controllers/beneficiary.php?action=leave_feedback">
        <input type="hidden" name="volunteer_id" value="<?= $volunteer_id ?>">
        <input type="hidden" name="request_id" value="<?= $request_id ?>">

        <label for="rating">Ваша оценка (1–5):</label><br>
        <select name="rating" required>
            <option value="">-- Выберите оценку --</option>
            <option value="1">1 ⭐ Очень плохо</option>
            <option value="2">2 ⭐ Плохо</option>
            <option value="3">3 ⭐ Нормально</option>
            <option value="4">4 ⭐ Хорошо</option>
            <option value="5">5 ⭐ Отлично</option>
        </select><br><br>

        <label for="comment">Комментарий:</label><br>
        <textarea name="comment" rows="5" cols="40"></textarea><br><br>

        <button type="submit">Отправить отзыв</button>
    </form>

    <a href="/views/beneficiary/notifications.php">← Назад</a>
</div>

</body>
</html>