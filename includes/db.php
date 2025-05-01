<?php
$host = 'localhost';
$port = 3308; // Add the port
$db   = 'solo_pm';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    throw new Exception("DB Connection failed: " . $e->getMessage());
}
