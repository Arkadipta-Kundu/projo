<?php
session_start();
include __DIR__ . '/db.php'; // Include the database connection

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE remember_token = :token");
    $stmt->execute([':token' => $_COOKIE['remember_me']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user['username'];
    }
}

if (!isset($_SESSION['user'])) {
    header('Location: /projo/login.php');
    exit();
}
