<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Fetch all notes for the user
        $notes = getAllNotes($pdo, $user_id);
        echo json_encode(['success' => true, 'notes' => $notes]);
        break;

    case 'POST':
        // Handle delete
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $note_id = $_POST['id'] ?? null;
            if (!$note_id) {
                echo json_encode(['success' => false, 'error' => 'Note ID required.']);
                exit;
            }
            try {
                deleteNote($pdo, $note_id);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        // Handle create or update
        $id = $_POST['id'] ?? '';
        $content = trim($_POST['content'] ?? '');
        $project_id = $_POST['project_id'] ?? null;

        if ($content === '') {
            echo json_encode(['success' => false, 'error' => 'Note content is required.']);
            exit;
        }

        try {
            if ($id) {
                // Update existing note
                updateNote($pdo, $id, $content, $project_id);
                $note = getNoteById($pdo, $id);
            } else {
                // Create new note
                $note_id = createNoteAndReturnId($pdo, $content, $project_id, $user_id);
                $note = getNoteById($pdo, $note_id);
            }
            echo json_encode(['success' => true, 'note' => $note]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
