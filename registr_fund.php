<?php
session_start();
require 'includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'fund';

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Пользователь с таким email уже существует.";
        } else {
            $error = "Ошибка регистрации: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация администратора</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Платформа помощи</h1>
        </div>

        <h2>Регистрация фонда</h2>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="post" action="" class="auth-form">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="example@example.com" required>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" placeholder="Введите пароль" required>

            <button type="submit">Зарегистрироваться как фонд</button>
        </form>

        <div class="links">
<p><a href="register_choice.php">← Вернуться на главную</a></p>
        </div>
    </div>
</body>
</html>