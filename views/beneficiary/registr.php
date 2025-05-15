<?php
session_start();
require 'includes/database.php';
require 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Проверка, что все поля заполнены
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "Все поля обязательны для заполнения.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role'] = $role;
            redirect("/index.php");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Пользователь с таким email уже существует.";
            } else {
                $error = "Ошибка регистрации: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
    <h1>Регистрация</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Имя:</label><br>
        <input type="text" name="name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Пароль:</label><br>
        <input type="password" name="password" required><br><br>

        <label for="role">Выберите роль:</label><br>
        <select name="role" required>
            <option value="">-- Выберите --</option>
            <option value="volunteer">Волонтер</option>
            <option value="beneficiary">Благополучатель</option>
            <option value="fund">Фонд</option>
            <option value="admin">Администратор</option>
        </select><br><br>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <p>Уже зарегистрированы? <a href="login.php">Войти</a></p>
</body>
</html>