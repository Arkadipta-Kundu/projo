<?php
$host = 'localhost';
$port = 3308; // Update this if your MySQL server uses a different port
$db = 'solo_pm';
$user = 'root';
$pass = '';

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    $pdo->exec("USE $db");

    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            deadline DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            start_date DATE DEFAULT CURRENT_DATE,
            due_date DATE NOT NULL,
            priority ENUM('Low', 'Medium', 'High') NOT NULL,
            status ENUM('To Do', 'In Progress', 'Done') DEFAULT 'To Do',
            project_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content TEXT NOT NULL,
            project_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS issues (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            severity ENUM('Low', 'Medium', 'High') NOT NULL,
            status ENUM('Open', 'Resolved') DEFAULT 'Open',
            project_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            action TEXT NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS task_time_tracking (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task_id INT NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME DEFAULT NULL,
            duration INT DEFAULT NULL,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );
    ");

    // Insert default admin user
    $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO users (username, password) 
        VALUES ('admin', '$defaultPassword')
        ON DUPLICATE KEY UPDATE username = username;
    ");

    echo "Database setup completed successfully!";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
