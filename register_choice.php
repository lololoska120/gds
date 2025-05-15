<?php
session_start();
require 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выберите роль</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>
<body>

<div class="hero">
    <div class="container">
        <header class="header">
            <h1>Платформа помощи</h1>
            <p>Выберите, кем вы хотите зарегистрироваться</p>
        </header>

        <div class="role-grid">
            <!-- Волонтер -->
            <a href="registr_volunteer.php" class="role-card volunteer">
                <div class="icon">🧑‍🤝‍🧑</div>
                <h3>Волонтер</h3>
                <p>Присоединяйтесь к проектам и помогайте нуждающимся</p>
            </a>

            <!-- Благополучатель -->
            <a href="registr_beneficiary.php" class="role-card beneficiary">
                <div class="icon">🙏</div>
                <h3>Благополучатель</h3>
                <p>Запросите помощь от фондов или волонтеров</p>
            </a>
        </div>

        <footer class="footer">
            <p><a href="login.php">← Назад</a></p>
        </footer>
    </div>
</div>

</body>
</html>