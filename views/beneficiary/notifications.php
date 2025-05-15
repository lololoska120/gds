<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}

$beneficiary_id = $_SESSION['user_id'] ?? null;

// Получаем все заявки пользователя
// Получаем все заявки пользователя
$stmt = $pdo->prepare("SELECT id, description, location, cost, photo, status 
                       FROM help_requests 
                       WHERE beneficiary_id = ?");
$stmt->execute([$beneficiary_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->execute([$beneficiary_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Для каждой заявки получаем волонтеров, которые откликнулись
$all_volunteers = [];

foreach ($requests as $r) {
    $stmt = $pdo->prepare("SELECT vr.volunteer_id, u.name, u.email, vr.request_id
                           FROM volunteer_requests vr
                           JOIN users u ON vr.volunteer_id = u.id
                           WHERE vr.request_id = ?");
    $stmt->execute([$r['id']]);
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($volunteers)) {
        foreach ($volunteers as $v) {
            $all_volunteers[] = $v;
        }
    }
}
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
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        p {
            text-align: center;
            color: #666;
        }

        .request-card {
            background-color: #f9f9f9;
            padding: 15px 20px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .request-card p {
            margin: 5px 0;
        }

        .request-card strong {
            color: #007bff;
        }

        .request-card img {
            max-width: 100%;
            border-radius: 6px;
            margin-top: 10px;
            display: block;
        }

        .status.pending {
            background-color: #cce5ff;
            color: #004085;
        }

        .status.approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #c82333;
        }

        .notification-card {
            background-color: #f9f9f9;
            padding: 15px 20px;
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📩 Ваши заявки</h2>

    <?php if (empty($requests)): ?>
        <p>У вас пока нет заявок.</p>
    <?php else: ?>
        <?php foreach ($requests as $req): ?>
            <div class="request-card">
                <p><strong>ID:</strong> <?= htmlspecialchars($req['id']) ?></p>
                <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($req['description'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($req['location']) ?></p>
                <p><strong>Стоимость:</strong> <?= htmlspecialchars($req['cost'] ?: 'Не указана') ?></p>

                <!-- Статус -->
                <p><strong>Статус:</strong>
                    <?php if ($req['status'] === 'pending'): ?>
                        <span class="status pending">⏳ Ожидает</span>
                    <?php elseif ($req['status'] === 'approved'): ?>
                        <span class="status approved">✅ Одобрена</span>
                    <?php else: ?>
                        <span class="status rejected">❌ Отклонена</span>
                    <?php endif; ?>
                </p>

                <!-- Фото -->
                <?php if (!empty($req['photo'])): ?>
                    <img src="<?= htmlspecialchars($req['photo']) ?>" alt="Фото к заявке">
                <?php endif; ?>

                <!-- Кнопка отклонения -->
                <?php if ($req['status'] === 'pending'): ?>
                    <a href="/controllers/beneficiary.php?action=cancel_request&id=<?= $req['id'] ?>" onclick="return confirm('Вы уверены, что хотите отклонить заявку?')" class="btn">✖ Отклонить заявку</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/views/beneficiary/request_help.php" class="back-link">← Назад</a>
</div>

</body>
</html>