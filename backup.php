<?php
include __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/db.php'; // db.php now loads credentials from .env
include __DIR__ . '/includes/functions.php';

// Parse the connection details from the PDO connection that's already established
// This avoids duplicating credential management
$dbDetails = explode(';', $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS));
$hostPart = explode(':', $dbDetails[0]);
$host = str_replace('SERVER=', '', $hostPart[1]);
// Get database name from existing PDO connection
$stmt = $pdo->query('SELECT DATABASE()');
$db = $stmt->fetchColumn();

// Use the same credentials that are in the .env file
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
}

$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backupFile . '"');

$command = "mysqldump --host=$host --user=$user --password=$pass $db";
passthru($command);

// Log the backup activity
logActivity($pdo, $_SESSION['user'], 'Database backup taken');
exit();
