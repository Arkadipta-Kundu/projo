<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$projects = getAllProjects($pdo, $_SESSION['user_id']);
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
    <script src="/projo/assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Notes</h2>
        <div class="bg-white p-6 rounded shadow mb-6">
            <button id="toggle-note-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Note</button>
            <form id="note-form" class="space-y-4 hidden">
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
            <ul id="notes-list" class="space-y-2"></ul>
        </div>
    </main>
    <script>
        const toggleNoteFormButton = document.getElementById('toggle-note-form');
        const noteForm = document.getElementById('note-form');
        const notesList = document.getElementById('notes-list');

        toggleNoteFormButton.addEventListener('click', () => {
            noteForm.classList.toggle('hidden');
            toggleNoteFormButton.textContent = noteForm.classList.contains('hidden') ? 'Add Note' : 'Cancel';
        });

        function renderNotes(notes) {
            notesList.innerHTML = '';
            notes.forEach(note => {
                const li = document.createElement('li');
                li.className = 'bg-gray-100 p-4 rounded shadow flex justify-between items-center';
                li.innerHTML = `
                    <div>
                        <p class="text-gray-800">${note.content}</p>
                        ${note.project_title ? `<p class="text-sm text-gray-600">Project: ${note.project_title}</p>` : ''}
                    </div>
                    <div class="space-x-2">
                        <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick='editNote(${JSON.stringify(note)})'>Edit</button>
                        <button class="bg-red-500 text-white px-2 py-1 rounded" onclick='deleteNote(${note.id})'>Delete</button>
                    </div>
                `;
                notesList.appendChild(li);
            });
        }

        function fetchNotes() {
            fetch('/projo/api/notes.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) renderNotes(data.notes);
                    else alert('Failed to load notes: ' + data.error);
                });
        }

        function editNote(note) {
            noteForm.classList.remove('hidden');
            toggleNoteFormButton.textContent = 'Cancel';
            document.getElementById('note-id').value = note.id;
            document.getElementById('content').value = note.content;
            document.getElementById('project_id').value = note.project_id || '';
        }

        function deleteNote(id) {
            if (!confirm('Delete this note?')) return;
            fetch('/projo/api/notes.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) fetchNotes();
                    else alert('Failed to delete note: ' + data.error);
                });
        }

        noteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(noteForm);
            fetch('/projo/api/notes.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fetchNotes();
                        noteForm.reset();
                        noteForm.classList.add('hidden');
                        toggleNoteFormButton.textContent = 'Add Note';
                    } else {
                        alert('Failed to save note: ' + data.error);
                    }
                });
        });

        // Initial load
        fetchNotes();
    </script>
</body>

</html>