<?php
session_start();
require '../includes/database.php';
require '../includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

// Получаем заявки со статусом pending
$stmt = $pdo->query("SELECT r.id, r.description, r.location, r.cost, r.photo, u.name AS beneficiary_name
                     FROM help_requests r
                     JOIN users u ON r.beneficiary_id = u.id
                     WHERE r.status = 'pending'");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🔔 Уведомления</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .request-card {
            background-color: #f9f9f9;
            padding: 15px 20px;
            border-left: 5px solid #007bff;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .request-card p {
            margin: 5px 0;
        }

        .request-card strong {
            color: #007bff;
        }

        .request-card img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
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
    <h2>📩 Заявки от пользователей</h2>

    <?php if (empty($requests)): ?>
        <p>Новых заявок нет</p>
    <?php else: ?>
        <?php foreach ($requests as $r): ?>
            <div class="request-card">
                <p><strong>ID:</strong> <?= htmlspecialchars($r['id']) ?></p>
                <p><strong>Автор:</strong> <?= htmlspecialchars($r['beneficiary_name']) ?></p>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($r['description'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Стоимость:</strong> <?= htmlspecialchars($r['cost'] ?: 'Не указана') ?></p>

                <!-- Отображение фото -->
                <?php if (!empty($r['photo'])): ?>
                    <div class="photo-preview">
                        <img src="<?= htmlspecialchars($r['photo']) ?>" alt="Фото заявки">
                    </div>
                <?php else: ?>
                    <p class="no-photo">Фото не прикреплено</p>
                <?php endif; ?>

                <!-- Кнопки одобрения / отклонения -->
                <div class="actions">
                    <a href="/admin/approve.php?id=<?= $r['id'] ?>" onclick="return confirm('Одобрить заявку?')" class="btn btn-approve">✅ Одобрить</a>
                    <a href="/admin/reject.php?id=<?= $r['id'] ?>" onclick="return confirm('Отклонить заявку?')" class="btn btn-reject">❌ Отклонить</a>
                </div>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/admin/dashboard.php" class="back-link">← Назад</a>
</div>

</body>
</html>