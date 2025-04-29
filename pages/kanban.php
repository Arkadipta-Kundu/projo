<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Fetch tasks grouped by status
$tasks = getAllTasks($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>kanban</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Kanban Board</h2>
        <div class="grid grid-cols-3 gap-4">
            <!-- To Do Column -->
            <div class="bg-gray-200 p-4 rounded shadow">
                <h3 class="text-xl font-bold mb-2">To Do</h3>
                <div id="todo" class="kanban-column">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'To Do'): ?>
                            <div class="kanban-card bg-white p-2 rounded shadow mb-2 border-l-4 <?= getPriorityColor($task['priority']) ?>" data-id="<?= $task['id'] ?>">
                                <h4 class="font-bold"><?= htmlspecialchars($task['title']) ?></h4>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($task['description']) ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="bg-gray-200 p-4 rounded shadow">
                <h3 class="text-xl font-bold mb-2">In Progress</h3>
                <div id="in-progress" class="kanban-column">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'In Progress'): ?>
                            <div class="kanban-card bg-white p-2 rounded shadow mb-2 border-l-4 <?= getPriorityColor($task['priority']) ?>" data-id="<?= $task['id'] ?>">
                                <h4 class="font-bold"><?= htmlspecialchars($task['title']) ?></h4>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($task['description']) ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Done Column -->
            <div class="bg-gray-200 p-4 rounded shadow">
                <h3 class="text-xl font-bold mb-2">Done</h3>
                <div id="done" class="kanban-column">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['status'] === 'Done'): ?>
                            <div class="kanban-card bg-white p-2 rounded shadow mb-2 border-l-4 <?= getPriorityColor($task['priority']) ?>" data-id="<?= $task['id'] ?>">
                                <h4 class="font-bold"><?= htmlspecialchars($task['title']) ?></h4>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($task['description']) ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    <script>
        // Initialize SortableJS for each column
        ['todo', 'in-progress', 'done'].forEach(columnId => {
            new Sortable(document.getElementById(columnId), {
                group: 'kanban',
                animation: 150,
                dragClass: 'sortable-drag', // Add a class for the dragged element
                ghostClass: 'sortable-ghost', // Add a class for the ghost element
                onStart: function(evt) {
                    evt.item.style.opacity = '0.5'; // Add visual feedback for dragging
                },
                onEnd: function(evt) {
                    evt.item.style.opacity = '1'; // Reset opacity after dragging
                    const taskId = evt.item.dataset.id;
                    const newStatus = evt.to.id.replace('-', ' '); // Convert "in-progress" to "In Progress"
                    updateTaskStatus(taskId, newStatus);
                }
            });
        });

        // Function to update task status via AJAX
        function updateTaskStatus(taskId, newStatus) {
            const spinner = document.createElement('div');
            spinner.className = 'spinner';
            document.body.appendChild(spinner);

            fetch('/projo/api/update_task_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: taskId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.body.removeChild(spinner);
                    if (!data.success) {
                        alert('Failed to update task status. Reverting...');
                        location.reload();
                    }
                })
                .catch(error => {
                    document.body.removeChild(spinner);
                    alert('An error occurred. Reverting...');
                    location.reload();
                });
        }
    </script>
</body>

</html>