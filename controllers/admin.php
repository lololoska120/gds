<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

if (!isLoggedIn() || userRole() !== 'admin') {
    redirect('/login.php');
}

switch ($_GET['action']) {
    case 'approve_request':
        $request_id = $_POST['request_id'];

        try {
            // Обновляем статус заявки
            $pdo->prepare("UPDATE help_requests SET status = 'approved' WHERE id = ?")
               ->execute([$request_id]);

            // Получаем ID благополучателя
            $stmt = $pdo->prepare("SELECT beneficiary_id FROM help_requests WHERE id = ?");
            $stmt->execute([$request_id]);
            $help_request = $stmt->fetch();

            // Получаем всех волонтеров, которые откликнулись
            $stmt = $pdo->prepare("SELECT v.name, v.email 
                                   FROM volunteer_requests vr
                                   JOIN users v ON vr.volunteer_id = v.id
                                   WHERE vr.request_id = ?");
            $stmt->execute([$request_id]);
            $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Отправляем каждому благополучателю уведомление
            foreach ($volunteers as $v) {
                $pdo->prepare("INSERT INTO notifications (to_user_id, from_volunteer_id, request_id, message)
                               VALUES (?, ?, ?, ?)")
                   ->execute([
                        $help_request['beneficiary_id'],
                        $v['volunteer_id'],
                        $request_id,
                        "Заявка одобрена. {$v['name']} готов вам помочь. Email: {$v['email']}"
                    ]);
            }

            redirect("/admin/notifications.php");

        } catch (PDOException $e) {
            die("Ошибка при одобрении заявки: " . $e->getMessage());
        }
        break;

    default:
        echo "Неизвестное действие";
}
?>