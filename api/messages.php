<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'search') {
    $q = $_GET['q'] ?? '';
    $users = searchUsers($pdo, $q, $user_id);
    echo json_encode(['success' => true, 'users' => $users]);
    exit;
}

if ($action === 'fetch') {
    $other_id = intval($_GET['user_id'] ?? 0);
    $messages = getMessages($pdo, $user_id, $other_id);
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $receiver_id = intval($data['receiver_id'] ?? 0);
    $message = trim($data['message'] ?? '');
    if ($receiver_id && $message) {
        sendMessage($pdo, $user_id, $receiver_id, $message);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
    exit;
}

if ($action === 'conversations') {
    $convos = getConversations($pdo, $user_id);
    echo json_encode(['success' => true, 'conversations' => $convos]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
