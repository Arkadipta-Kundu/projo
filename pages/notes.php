<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$user_id = $_SESSION['user_id'];
$projects = getAllProjects($pdo, $user_id, ($_SESSION['role'] === 'admin'));

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = $_POST['note_id'] ?? '';
    $content = $_POST['content'] ?? '';
    $project_id = $_POST['project_id'] ?? null;
    if (!empty($content)) {
        if ($note_id) {
            updateNote($pdo, $note_id, $content, $project_id);
        } else {
            createNote($pdo, $content, $project_id, $user_id);
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteNote($pdo, $id);
}

// Fetch notes
if (isset($_GET['project_id']) && $_GET['project_id'] !== '') {
    $notes = getNotesByProject($pdo, $user_id, $_GET['project_id']);
} else {
    $notes = getAllNotes($pdo, $user_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        .note-content-short {
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }

        .note-content-fade:after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2.5em;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), #fff 90%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold">Notes</h2>
            <!-- Add Note Button on right -->
            <button id="show-note-form" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition-all duration-150">
                <i class="fas fa-plus mr-2"></i>Add Note
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="mb-6 flex gap-4 items-end">
            <div>
                <label for="filter_project" class="block font-semibold mb-1">Filter by Project</label>
                <select id="filter_project" name="project_id" class="border border-gray-300 p-2 rounded">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>" <?= (isset($_GET['project_id']) && $_GET['project_id'] == $project['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($project['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Apply Filter</button>
        </form>

        <!-- Note Form (hidden by default) -->
        <div id="note-form-container" class="bg-white p-6 rounded-xl shadow mb-8 hidden">
            <form method="POST" id="note-form" class="space-y-4">
                <input type="hidden" name="note_id" id="note-id">
                <div>
                    <label for="project_id" class="block font-semibold mb-1">Project (optional)</label>
                    <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded">
                        <option value="">Unassigned</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Content</label>
                    <div id="quill-editor" style="height: 250px;"></div> <!-- Increased height from 120px to 220px -->
                    <input type="hidden" name="content" id="content">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" id="cancel-note-form" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-semibold transition-all duration-150">Cancel</button>
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">Save Note</button>
                </div>
            </form>
        </div>

        <!-- Notes List (single column) -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="text-xl font-bold mb-4">Your Notes</h3>
            <?php if (empty($notes)): ?>
                <p class="text-gray-600">No notes yet.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($notes as $note): ?>
                        <div class="bg-blue-50 rounded-lg p-4 shadow-inner relative">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-500"><?= $note['project_title'] ? htmlspecialchars($note['project_title']) : 'Unassigned' ?></span>
                                <div class="flex gap-2">
                                    <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition" onclick="editNote(<?= htmlspecialchars(json_encode($note)) ?>)">Edit</button>
                                    <a href="?delete=<?= $note['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition" onclick="return confirm('Delete this note?');">Delete</a>
                                </div>
                            </div>
                            <?php
                            $plain = strip_tags($note['content']);
                            $isLong = mb_strlen($plain) > 200 || substr_count($note['content'], '<p>') > 2;
                            ?>
                            <div class="note-content-short<?= $isLong ? ' note-content-fade' : '' ?>" id="note-content-<?= $note['id'] ?>">
                                <div class="ql-editor"><?= $note['content'] ?></div>
                            </div>
                            <?php if ($isLong): ?>
                                <button class="text-blue-600 underline mt-2" onclick="toggleNoteContent(<?= $note['id'] ?>, this)">View More</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        // Quill setup
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Write your note here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Show/hide note form logic
        const showFormBtn = document.getElementById('show-note-form');
        const noteFormContainer = document.getElementById('note-form-container');
        const cancelFormBtn = document.getElementById('cancel-note-form');
        const noteForm = document.getElementById('note-form');
        const noteIdInput = document.getElementById('note-id');
        const projectIdInput = document.getElementById('project_id');

        showFormBtn.onclick = function() {
            noteForm.reset();
            noteIdInput.value = '';
            projectIdInput.value = '';
            quill.root.innerHTML = '';
            noteFormContainer.classList.remove('hidden');
            showFormBtn.classList.add('hidden');
        };
        cancelFormBtn.onclick = function() {
            noteForm.reset();
            noteIdInput.value = '';
            projectIdInput.value = '';
            quill.root.innerHTML = '';
            noteFormContainer.classList.add('hidden');
            showFormBtn.classList.remove('hidden');
        };

        // On submit, set hidden input to Quill HTML
        noteForm.onsubmit = function() {
            document.getElementById('content').value = quill.root.innerHTML;
        };

        // Edit note
        function editNote(note) {
            noteForm.reset();
            noteIdInput.value = note.id;
            projectIdInput.value = note.project_id || '';
            quill.root.innerHTML = note.content;
            noteFormContainer.classList.remove('hidden');
            showFormBtn.classList.add('hidden');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // View more/less
        function toggleNoteContent(id, btn) {
            const div = document.getElementById('note-content-' + id);
            if (div.classList.contains('note-content-short')) {
                div.classList.remove('note-content-short', 'note-content-fade');
                btn.textContent = 'View Less';
            } else {
                div.classList.add('note-content-short', 'note-content-fade');
                btn.textContent = 'View More';
            }
        }
    </script>
</body>

</html>