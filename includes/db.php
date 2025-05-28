<?php
// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? 3306;
    $db   = $env['DB_NAME'] ?? 'projo';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
} else {
    // Default values - NEVER USE IN PRODUCTION
    $host = 'localhost';
    $port = 3306;
    $db   = 'projo';
    $user = 'root';
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    throw new Exception("DB Connection failed: " . $e->getMessage());
}
