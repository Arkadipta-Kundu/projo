<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Handle form submission (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $content = $_POST['content'] ?? '';
    $project_id = $_POST['project_id'] ?? null;

    if (!empty($content)) {
        if ($id) {
            // Update note
            updateNote($pdo, $id, $content, $project_id);
        } else {
            // Create new note
            createNote($pdo, $content, $project_id);
        }
    }
}

// Handle note deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (!empty($id)) {
        deleteNote($pdo, $id);
        header('Location: notes.php');
        exit();
    }
}

// Fetch all notes and projects
$notes = getAllNotes($pdo);
$projects = getAllProjects($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notes</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Notes</h2>
        <div class="bg-white p-6 rounded shadow mb-6">
            <button id="toggle-note-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Note</button>
            <form method="POST" id="note-form" class="space-y-4 hidden">
                <input type="hidden" name="id" id="note-id">
                <div>
                    <label for="content" class="block font-bold">Note</label>
                    <textarea id="content" name="content" class="w-full border border-gray-300 p-2 rounded" rows="4" placeholder="Write a note..."></textarea>
                </div>
                <div>
                    <label for="project_id" class="block font-bold">Tag to Project</label>
                    <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded">
                        <option value="">No Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Note</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <input type="text" id="search" class="w-full border border-gray-300 p-2 rounded mb-4" placeholder="Search notes...">
            <ul id="notes-list" class="space-y-2">
                <?php foreach ($notes as $note): ?>
                    <li class="bg-gray-100 p-4 rounded shadow flex justify-between items-center">
                        <div>
                            <p class="text-gray-800"><?= htmlspecialchars($note['content']) ?></p>
                            <?php if ($note['project_title']): ?>
                                <p class="text-sm text-gray-600">Project: <?= htmlspecialchars($note['project_title']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="space-x-2">
                            <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editNote(<?= htmlspecialchars(json_encode($note)) ?>)">Edit</button>
                            <a href="?delete=<?= $note['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    <script>
        // Toggle the visibility of the note form
        const toggleNoteFormButton = document.getElementById('toggle-note-form');
        const noteForm = document.getElementById('note-form');

        toggleNoteFormButton.addEventListener('click', () => {
            noteForm.classList.toggle('hidden');
            toggleNoteFormButton.textContent = noteForm.classList.contains('hidden') ? 'Add Note' : 'Cancel';
        });

        // Populate form for editing a note
        function editNote(note) {
            noteForm.classList.remove('hidden');
            toggleNoteFormButton.textContent = 'Cancel';
            document.getElementById('note-id').value = note.id;
            document.getElementById('content').value = note.content;
            document.getElementById('project_id').value = note.project_id || '';
        }
    </script>
</body>

</html>