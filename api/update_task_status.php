<?php
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['status'])) {
    $id = $data['id'];
    $status = $data['status'];

    try {
        toggleTaskStatus($pdo, $id, $status);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log('Error updating task status: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    error_log('Invalid input: ' . json_encode($data));
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
