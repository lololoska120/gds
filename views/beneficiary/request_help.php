<?php
session_start();
require '../../includes/database.php';
require '../../includes/functions.php';

$error = '';
$success = '';

if (!isLoggedIn() || userRole() !== 'beneficiary') {
    redirect('/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $location = $_POST['location'];
    $cost = $_POST['cost'];

    // Проверяем, что файл был загружен
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $error = "Ошибка: Фото не было загружено.";
    } else {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        $photo_name = uniqid('photo_') . '_' . basename($_FILES['photo']['name']);
        $target_file = $upload_dir . $photo_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Проверка, что это изображение
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check === false) {
            $error = "Файл не является изображением.";
        }

        // Проверка расширения
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = "Разрешены только JPG, JPEG, PNG и GIF";
        }

        // Проверяем, существует ли папка uploads
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $error = "Не удалось создать папку для загрузки фото.";
            }
        }

        // Если всё ок — перемещаем файл
        if (!$error && move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo_path = '/uploads/' . $photo_name;
        } else {
            $error = "Ошибка при сохранении файла.";
        }
    }

    if (!$error) {
        try {
            // Сохраняем заявку как "pending"
            $stmt = $pdo->prepare("INSERT INTO help_requests (beneficiary_id, description, location, cost, photo, status)
                                   VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $description, $location, $cost, $photo_path]);

            $success = "Заявка отправлена администратору на проверку";
        } catch (PDOException $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заполните анкету</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .logo h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 28px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-weight: bold;
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: bold;
            color: #333;
        }

        textarea,
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Красивая кастомная кнопка для загрузки фото */
        .custom-file-upload {
            display: block;
            padding: 12px 20px;
            background: linear-gradient(145deg, #28a745, #218838);
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
            max-width: 300px;
            margin: 20px auto;
        }

        .custom-file-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            background: linear-gradient(145deg, #218838, #1e7e34);
        }

        .custom-file-upload:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .custom-file-upload::before {
            content: "📎 ";
            font-size: 18px;
            vertical-align: middle;
        }

        .custom-file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            height: 100%;
            width: 100%;
            cursor: pointer;
        }

        /* Предпросмотр фото */
        #preview {
            text-align: center;
            margin-top: 20px;
        }

        #preview img {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Кнопка отправки формы */
        .submit-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 20px auto;
            padding: 12px;
            background: linear-gradient(145deg, #007bff, #0069d9);
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(145deg, #0069d9, #0056b3);
        }

        .submit-btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Уведомления об успехе/ошибке */
        .success, .error {
            text-align: center;
            padding: 12px;
            margin-top: 20px;
            border-radius: 8px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Ссылки */
        .btn {
            display: block;
            margin: 20px auto;
            text-align: center;
            color: white;
            background-color: #6c757d;
            padding: 10px 15px;
            border-radius: 8px;
            width: fit-content;
            max-width: 200px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #5a6268;
        }

        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        <h1>🧑‍🤝‍🧑 Платформа помощи</h1>
    </div>

    <h2>Заполните анкету</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" action="" enctype="multipart/form-data" class="auth-form">
        <label for="description">Описание проблемы:</label>
        <textarea name="description" id="description" required></textarea><br>

        <label for="location">Место оказания помощи:</label>
        <input type="text" name="location" id="location" placeholder="Город или адрес" required><br>

        <label for="cost">Стоимость помощи (если есть):</label>
        <input type="number" name="cost" id="cost" placeholder="₽"><br>

        <!-- Красивая кнопка выбора фото -->
        <label class="custom-file-upload">
            Прикрепить фото
            <input type="file" name="photo" id="photo" accept="image/*" required>
        </label>

        <!-- Предпросмотр фото -->
        <div id="preview"></div>

        <button type="submit" class="submit-btn">📤 Отправить заявку</button>
    </form>

    <!-- Кнопки навигации -->
    <a href="/views/beneficiary/notifications.php" class="btn">🔔 Посмотреть отклики</a>
    <a href="/index.html" class="btn">← Назад</a>
</div>

<!-- JS: предпросмотр изображения -->
<script>
    document.getElementById('photo').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('preview');
        preview.innerHTML = '';

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = `
                    <p>Предпросмотр:</p>
                    <img src="${e.target.result}" style="max-width: 200px; border-radius: 10px;">
                `;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<span style="color: red;">Выберите изображение</span>';
        }
    });
</script>

</body>
</html>