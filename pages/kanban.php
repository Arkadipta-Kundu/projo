<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Fetch tasks grouped by status
$tasks = getAllTasks($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Kanban Board</h2>
        <!-- Responsive Kanban wrapper -->
        <div class="overflow-x-auto">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 min-w-[600px] sm:min-w-0 flex-shrink-0 sm:grid">
                <!-- To Do Column -->
                <div class="bg-gray-200 p-4 rounded shadow min-w-[250px]">
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
                <div class="bg-gray-200 p-4 rounded shadow min-w-[250px]">
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
                <div class="bg-gray-200 p-4 rounded shadow min-w-[250px]">
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
        </div>
    </main>
    <script>
        // Initialize Dragula for drag-and-drop functionality
        const drake = dragula([document.getElementById('todo'), document.getElementById('in-progress'), document.getElementById('done')], {
            accepts: (el, target, source, sibling) => {
                // Allow dropping into any column, including empty ones
                return true;
            }
        });

        // Add a placeholder to empty containers
        drake.on('drag', () => {
            document.querySelectorAll('.kanban-column').forEach(column => {
                if (column.children.length === 0) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'kanban-placeholder bg-gray-300 p-2 rounded text-center text-gray-500';
                    placeholder.textContent = 'Drop here';
                    column.appendChild(placeholder);
                }
            });
        });

        drake.on('drop', (el, target, source, sibling) => {
            // Remove placeholder after drop
            document.querySelectorAll('.kanban-placeholder').forEach(placeholder => placeholder.remove());

            // Ensure the task is dropped into a valid column
            if (!target || !target.classList.contains('kanban-column')) {
                alert('Invalid drop target. Reverting...');
                source.appendChild(el); // Move the task back to its original column
                return;
            }

            const taskId = el.dataset.id;
            let newStatus = target.id.replace('-', ' '); // Convert "in-progress" to "In Progress"

            // Ensure proper capitalization for "To Do"
            if (newStatus === 'todo') {
                newStatus = 'To Do';
            }

            // Send AJAX request to update task status
            fetch('../api/update_task_status.php', {
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
                    if (!data.success) {
                        alert('Failed to update task status. Reverting...');
                        source.appendChild(el); // Move the task back to its original column
                    } else {
                        // Update the task's status visually
                        el.dataset.status = newStatus;
                    }
                })
                .catch(error => {
                    alert('An error occurred while updating the task status. Reverting...');
                    console.error('Error:', error);
                    source.appendChild(el); // Move the task back to its original column
                });
        });

        drake.on('cancel', () => {
            // Remove placeholder if drag is canceled
            document.querySelectorAll('.kanban-placeholder').forEach(placeholder => placeholder.remove());
        });
    </script>
</body>

</html>