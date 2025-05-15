<?php
session_start();
require 'includes/database.php';
require 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            redirect('/index.php');
        } else {
            $error = "Неверный email, пароль или роль";
        }
    } catch (PDOException $e) {
        $error = "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>
<body>

<div class="container">
    <div class="logo">
        <h1>👮‍♂️ Платформа помощи</h1>
    </div>

    <h2>Вход в систему</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" action="" class="auth-form">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="example@example.com" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" placeholder="Введите пароль" required>

        <label for="role">Роль:</label>
        <select id="role" name="role" required>
            <option value="">-- Выберите роль --</option>
            <option value="volunteer">Волонтер</option>
            <option value="admin">Администратор</option>
            <option value="beneficiary">Благополучатель</option>
        </select>

        <button type="submit">Войти</button>
    </form>

    <div class="links">
      <a href="/index.html">Выйти</a>
      <a href="register_choice.php">Зарегистрироваться</a>
    </div>
</div>

</body>
</html>