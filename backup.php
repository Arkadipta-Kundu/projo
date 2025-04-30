<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$host = 'localhost';
$db = 'solo_pm';
$user = 'root';
$pass = '';
$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backupFile . '"');

$command = "mysqldump --host=$host --user=$user --password=$pass $db";
passthru($command);

// Log the backup activity
logActivity($pdo, $_SESSION['user'], 'Database backup taken');
exit();
