<?php
session_start();

// Подключаем БД и функции
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Проверяем авторизацию
if (!isLoggedIn() || userRole() !== 'volunteer') {
    redirect('/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'join_request') {
    $request_id = $_POST['request_id'] ?? null;

    if (!$request_id || !is_numeric($request_id)) {
        die('<div class="error">Ошибка: Некорректный ID заявки.</div>');
    }

    $volunteer_id = $_SESSION['user_id'];

    try {
        // 1. Сохраняем отклик волонтёра
        $pdo->prepare("INSERT INTO volunteer_requests (volunteer_id, request_id) VALUES (?, ?)")
           ->execute([$volunteer_id, $request_id]);

        // 2. Получаем данные волонтёра
        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$volunteer_id]);
        $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Получаем ID благополучателя
        $stmt = $pdo->prepare("SELECT u.id AS beneficiary_id 
                               FROM help_requests r
                               JOIN users u ON r.beneficiary_id = u.id
                               WHERE r.id = ?");
        $stmt->execute([$request_id]);
        $beneficiary = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Формируем сообщение
        $message = "{$volunteer['name']} откликнулся на вашу заявку. Свяжитесь с ним: {$volunteer['email']}";

        // 5. Сохраняем уведомление
        $pdo->prepare("INSERT INTO notifications (to_user_id, from_volunteer_id, message)
                       VALUES (?, ?, ?)")
           ->execute([$beneficiary['beneficiary_id'], $volunteer_id, $message]);

        // 6. Перенаправляем
        redirect('/views/volunteer/notifications.php');

    } catch (PDOException $e) {
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Ошибка</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f9f9f9;
                    padding: 40px;
                    text-align: center;
                }
                .container {
                    max-width: 500px;
                    margin: auto;
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
                .success {
                    color: #155724;
                    background-color: #d4edda;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .btn-back {
                    display: inline-block;
                    margin-top: 10px;
                    padding: 10px 15px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .btn-back:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>

        <div class="container">
            <h2>Успех!</h2>
            <p class="success">Вы успешно откликнулись на заявку.</p>
            <blockquote>
                «<?= htmlspecialchars($volunteer['name']) ?> откликнулся на вашу заявку. Связаться с ним можно по почте: <?= htmlspecialchars($volunteer['email']) ?>»
            </blockquote>
            <a href="/views/volunteer/notifications.php" class="btn-back">← Назад</a>
        </div>

        </body>
        </html>
        <?php
        exit;
    }
}
?>