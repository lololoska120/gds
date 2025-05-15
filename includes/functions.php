<?php
function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function userRole() {
    return $_SESSION['role'] ?? null;
}

// Убираем дублирующуюся функцию
function getAverageRating($volunteer_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating 
                           FROM volunteer_feedback 
                           WHERE volunteer_id = ?");
    $stmt->execute([$volunteer_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return round($result['avg_rating'] ?? 0, 1);
}

function getUserStats($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_projects FROM projects WHERE author_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>