<?php
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$task_id = $_GET['task_id'] ?? 0;

if ($action === 'start') {
    startTaskTimer($pdo, $task_id);
    echo json_encode(['success' => true, 'start_time' => time()]);
} elseif ($action === 'stop') {
    stopTaskTimer($pdo, $task_id);
    echo json_encode(['success' => true]);
} elseif ($action === 'status') {
    // Check if a timer is running for the task
    $stmt = $pdo->prepare("SELECT start_time FROM task_time_tracking WHERE task_id = :task_id AND end_time IS NULL");
    $stmt->execute([':task_id' => $task_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['running' => true, 'start_time' => strtotime($result['start_time'])]);
    } else {
        echo json_encode(['running' => false]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
