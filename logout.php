<?php
session_start();
include __DIR__ . '/includes/db.php'; // Include the database connection

if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/'); // Expire the cookie
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE username = :username");
    $stmt->execute([':username' => $_SESSION['user']]);
}

session_destroy();
header('Location: index.php');
exit();
