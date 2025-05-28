<?php
header('Content-Type: application/json');

// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? 3306;
    $db   = $env['DB_NAME'] ?? 'projo';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
} else {
    // Default values - these should never be used in production
    $host = 'localhost';
    $port = 3306;
    $db   = 'projo';
    $user = 'root';
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
