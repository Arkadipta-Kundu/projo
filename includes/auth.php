<?php
// filepath: c:\xampp\htdocs\projo\includes\auth.php
session_start();

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
