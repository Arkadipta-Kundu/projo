<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? 'To Do';
    $project_id = $_POST['project_id'] ?? null;

    if (!empty($title) && !empty($due_date) && !empty($priority)) {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            // Update task
            updateTask($pdo, $_POST['id'], $title, $description, $due_date, $priority, $status, $project_id);
        } else {
            // Create new task
            createTask($pdo, $title, $description, $due_date, $priority, $status, $project_id);
        }
    }
}

// Handle task deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (!empty($id)) {
        deleteTask($pdo, $id);
        header('Location: tasks.php');
        exit();
    }
}

// Handle status toggle
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $new_status = $_GET['status'] ?? 'To Do';
    if (!empty($id)) {
        toggleTaskStatus($pdo, $id, $new_status);
        header('Location: tasks.php');
        exit();
    }
}

// Fetch all tasks and projects
$tasks = getAllTasks($pdo);
$projects = getAllProjects($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tasks</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include the script in the head or before the closing body tag -->
    <script src="/projo/assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Tasks</h2>
        <div class="bg-white p-6 rounded shadow mb-6">
            <button id="toggle-task-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Task</button>
            <form method="POST" id="task-form" class="space-y-4 hidden">
                <input type="hidden" name="id" id="task-id">
                <div>
                    <label for="title" class="block font-bold">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="description" class="block font-bold">Description</label>
                    <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded"></textarea>
                </div>
                <div>
                    <label for="due_date" class="block font-bold">Due Date</label>
                    <input type="date" id="due_date" name="due_date" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="priority" class="block font-bold">Priority</label>
                    <select id="priority" name="priority" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block font-bold">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 p-2 rounded">
                        <option value="To Do">To Do</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Done">Done</option>
                    </select>
                </div>
                <div>
                    <label for="project_id" class="block font-bold">Project</label>
                    <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded">
                        <option value="">Unassigned</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Task</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Title</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Priority</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                        <th class="border border-gray-300 px-4 py-2">Project</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['id']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['title']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['description']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['priority']) ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="?toggle_status=<?= $task['id'] ?>&status=<?= $task['status'] === 'Done' ? 'To Do' : 'Done' ?>"
                                    class="bg-<?= $task['status'] === 'Done' ? 'green' : 'gray' ?>-500 text-white px-2 py-1 rounded">
                                    <?= $task['status'] ?>
                                </a>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['project_title'] ?? 'Unassigned') ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editTask(<?= htmlspecialchars(json_encode($task)) ?>)">Edit</button>
                                <a href="?delete=<?= $task['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        // Toggle the visibility of the task form
        const toggleTaskFormButton = document.getElementById('toggle-task-form');
        const taskForm = document.getElementById('task-form');

        toggleTaskFormButton.addEventListener('click', () => {
            taskForm.classList.toggle('hidden');
            toggleTaskFormButton.textContent = taskForm.classList.contains('hidden') ? 'Add Task' : 'Cancel';
        });

        // Populate form for editing a task
        function editTask(task) {
            taskForm.classList.remove('hidden');
            toggleTaskFormButton.textContent = 'Cancel';
            document.getElementById('task-id').value = task.id;
            document.getElementById('title').value = task.title;
            document.getElementById('description').value = task.description;
            document.getElementById('due_date').value = task.due_date;
            document.getElementById('priority').value = task.priority;
            document.getElementById('status').value = task.status;
            document.getElementById('project_id').value = task.project_id;
        }

        function startTimer(taskId) {
            fetch('/projo/api/task_timer.php?action=start&task_id=' + taskId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Timer started!');
                        location.reload();
                    } else {
                        alert('Failed to start timer.');
                    }
                });
        }

        function stopTimer(taskId) {
            fetch('/projo/api/task_timer.php?action=stop&task_id=' + taskId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Timer stopped!');
                        location.reload();
                    } else {
                        alert('Failed to stop timer.');
                    }
                });
        }
    </script>
</body>

</html>