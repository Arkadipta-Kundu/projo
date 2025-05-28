<?php
ob_start(); // Start output buffering
session_start();

try {
    include __DIR__ . '/db.php'; // Include the database connection
} catch (Exception $e) {
    header('Location: ../catchy_error_page.php');
    exit();
}

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE remember_token = :token");
    $stmt->execute([':token' => $_COOKIE['remember_me']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['user_id'] = $user['id']; // Ensure user_id is set
        $_SESSION['role'] = $user['role'];
    }
}

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit();
}

ob_end_flush(); // End output buffering
