<?php
// includes/db.php

$host = 'localhost';
$dbname = 'reg_card_db';
$username = 'root';
$password = 'root'; // если пароль пустой

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>