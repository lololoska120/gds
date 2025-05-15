<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

// Получаем всех волонтеров
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'volunteer'");
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Для каждого волонтёра получаем отзывы
$volunteers_with_feedback = [];

foreach ($volunteers as $v) {
    $stmt = $pdo->prepare("SELECT feedback.message, u.name AS from_user, feedback.rating 
                           FROM volunteer_feedback feedback
                           JOIN users u ON feedback.from_user_id = u.id
                           WHERE feedback.volunteer_id = ?
                           ORDER BY feedback.created_at DESC LIMIT 5");
    $stmt->execute([$v['id']]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $v['feedbacks'] = $feedbacks;
    $v['avg_rating'] = getAverageRating($v['id']);

    $volunteers_with_feedback[] = $v;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🔍 Поиск волонтеров</title>
    <style>
        /* Базовые стили */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #e9ecef);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 60px auto;
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        input[type="text"] {
            width: 70%;
            padding: 12px 20px;
            font-size: 16px;
            border: 2px solid #a6d8ff;
            border-radius: 8px 0 0 8px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        button {
            padding: 12px 20px;
            background-color: #4a90e2;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 0 8px 8px 0;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #357abd;
        }

        .volunteer-card {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .volunteer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .volunteer-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
            border: 2px solid #ddd;
        }

        .volunteer-info {
            flex-grow: 1;
        }

        .volunteer-info h4 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .volunteer-info p {
            margin: 5px 0 10px;
            color: #666;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .feedback-list {
            margin-top: 10px;
            list-style: none;
            padding-left: 0;
        }

        .feedback-list li {
            background-color: #f1f1f1;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .feedback-list strong {
            color: #4a90e2;
        }

        .btn-feedback {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-feedback:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            text-align: center;
            margin: 40px auto 0;
            color: #333;
            font-weight: bold;
            text-decoration: none;
            background: #fff;
            padding: 12px 24px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: fit-content;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .back-link:hover {
            background-color: #f1f1f1;
        }

        /* Модальное окно */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 25px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .submit-btn {
            margin-top: 15px;
            background-color: #4a90e2;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #357abd;
        }
    </style>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins :wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2>🧑‍🤝‍🧑 Все волонтеры</h2>

    <!-- Форма поиска -->
    <form method="get" action="">
        <input type="text" name="search" placeholder="Имя волонтера">
        <button type="submit">🔍 Найти</button>
    </form>

    <?php if (empty($volunteers_with_feedback)): ?>
        <p style="text-align:center;">Нет волонтеров в системе</p>
    <?php else: ?>
        <?php foreach ($volunteers_with_feedback as $v): ?>
            <div class="volunteer-card">
                <img src="/img/diplomaimg/avatar.png" alt="Аватар">
                <div class="volunteer-info">
                    <h4><?= htmlspecialchars($v['name']) ?></h4>
                    <p><?= htmlspecialchars($v['email']) ?></p>

                    <!-- Вывод звёзд -->
                    <div class="rating-stars">
                        <?php
                        $rating = round($v['avg_rating'], 0);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? "★" : "☆";
                        }
                        ?> (<?= number_format($v['avg_rating'], 1) ?: '0.0' ?>)
                    </div>

                    <!-- Последние отзывы -->
                    <?php if (!empty($v['feedbacks'])): ?>
                        <ul class="feedback-list">
                            <?php foreach ($v['feedbacks'] as $fb): ?>
                                <li>
                                    <strong><?= htmlspecialchars($fb['from_user']) ?>:</strong><br>
                                    <?= htmlspecialchars($fb['message']) ?><br>
                                    <?php
                                    $r = $fb['rating'];
                                    if ($r > 0):
                                        echo str_repeat('⭐', $r);
                                    endif;
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Кнопка с модальным окном -->
                    <a href="#" class="btn-feedback open-modal" data-volunteer="<?= $v['id'] ?>">✍️ Оставить отзыв</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/index.html" class="back-link">← На главную</a>
</div>

<!-- Модальное окно -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Оставить отзыв</h3>
        <form id="feedbackForm" method="post" action="/controllers/volunteer/leave_feedback.php">
            <input type="hidden" name="volunteer_id" id="modalVolunteerId">
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
            <textarea name="message" id="message" rows="4" required></textarea>

            <button type="submit" class="submit-btn">📤 Отправить</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("feedbackModal");
    const btns = document.querySelectorAll(".open-modal");
    const span = document.getElementsByClassName("close")[0];
    const modalInput = document.getElementById("modalVolunteerId");

    btns.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const volunteerId = this.getAttribute("data-volunteer");
            document.getElementById("modalVolunteerId").value = volunteerId;
            modal.style.display = "block";
        });
    });

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>