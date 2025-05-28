<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$is_admin = ($_SESSION['role'] === 'admin'); // Define $is_admin

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? 'To Do';
    $project_id = $_POST['project_id'] ?? null;

    if (!empty($title) && !empty($due_date) && !empty($priority)) {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            // Update task
            updateTask($pdo, $_POST['id'], $title, $description, $due_date, $priority, $status, $project_id, $start_date);
        } else {
            // Use custom start date or default to today
            $start_date = !empty($start_date) ? $start_date : date('Y-m-d');
            createTask($pdo, $title, $description, $start_date, $due_date, $priority, $status, $project_id, $_SESSION['user_id']);
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
$project_filter = $_GET['project_id'] ?? '';
$status_filter = $_GET['status'] ?? '';

$tasks_query = "SELECT tasks.*, users.username AS created_by 
    FROM tasks 
    LEFT JOIN users ON tasks.user_id = users.id 
    WHERE tasks.user_id = :user_id";
$params = [':user_id' => $_SESSION['user_id']];

if ($project_filter !== '') {
    $tasks_query .= " AND tasks.project_id = :project_id";
    $params[':project_id'] = $project_filter;
}
if ($status_filter !== '') {
    $tasks_query .= " AND tasks.status = :status";
    $params[':status'] = $status_filter;
}
$tasks_query .= " ORDER BY tasks.due_date ASC";

$stmt = $pdo->prepare($tasks_query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$projects = getAllProjects($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include the script in the head or before the closing body tag -->
    <script src="../assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold">Tasks</h2>
            <button id="toggle-task-form" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">
                <i class="fas fa-plus mr-2"></i>Add Task
            </button>
        </div>
        <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
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
            <div>
                <label for="filter_status" class="block font-semibold mb-1">Filter by Status</label>
                <select id="filter_status" name="status" class="border border-gray-300 p-2 rounded">
                    <option value="">All Statuses</option>
                    <option value="To Do" <?= (isset($_GET['status']) && $_GET['status'] == 'To Do') ? 'selected' : '' ?>>To Do</option>
                    <option value="In Progress" <?= (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Done" <?= (isset($_GET['status']) && $_GET['status'] == 'Done') ? 'selected' : '' ?>>Done</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Apply Filters</button>
        </form>
        <form method="POST" id="task-form" class="space-y-4 hidden bg-blue-50 rounded-lg p-6 shadow-inner mb-6">
            <input type="hidden" name="id" id="task-id">
            <div class="mb-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="schedule-checkbox" class="form-checkbox mr-2">
                    <span class="font-semibold">Schedule Task (set start date)</span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div id="start-date-row" style="display:none;">
                    <label for="start_date" class="block font-semibold mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label for="title" class="block font-semibold mb-1">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label for="description" class="block font-semibold mb-1">Description</label>
                    <input id="description" name="description" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label for="due_date" class="block font-semibold mb-1">Due Date</label>
                    <input type="date" id="due_date" name="due_date" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="priority" class="block font-semibold mb-1">Priority</label>
                    <select id="priority" name="priority" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block font-semibold mb-1">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                        <option value="To Do">To Do</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Done">Done</option>
                    </select>
                </div>
                <div>
                    <label for="project_id" class="block font-semibold mb-1">Project</label>
                    <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                        <option value="">Unassigned</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">Save Task</button>
            </div>
        </form>
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="overflow-x-auto w-full">
                <table class="table-auto w-full border-collapse border border-gray-200 text-sm md:text-base">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="border border-gray-200 px-4 py-2">ID</th>
                            <th class="border border-gray-200 px-4 py-2">Title</th>
                            <th class="border border-gray-200 px-4 py-2">Description</th>
                            <th class="border border-gray-200 px-4 py-2">Due Date</th>
                            <th class="border border-gray-200 px-4 py-2">Priority</th>
                            <th class="border border-gray-200 px-4 py-2 min-w-[110px]">Status</th>
                            <?php if ($is_admin): ?>
                                <th class="border border-gray-200 px-4 py-2">Created By</th>
                            <?php endif; ?>
                            <th class="border border-gray-200 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($task['id']) ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($task['title']) ?></td>
                                <td class="border border-gray-200 px-4 py-2 whitespace-pre-line">
                                    <?= htmlspecialchars($task['description']) ?>
                                </td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($task['due_date']) ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($task['priority']) ?></td>
                                <td class="border border-gray-200 px-4 py-2 min-w-[110px]">
                                    <?php
                                    $status = $task['status'];
                                    $badgeClass = 'bg-gray-300 text-gray-800';
                                    if ($status === 'Done') {
                                        $badgeClass = 'bg-green-500 text-white';
                                    } elseif ($status === 'In Progress') {
                                        $badgeClass = 'bg-yellow-400 text-gray-900';
                                    } elseif ($status === 'To Do') {
                                        $badgeClass = 'bg-red-500 text-white';
                                    }
                                    ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <?php if ($is_admin): ?>
                                    <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($task['created_by']) ?></td>
                                <?php endif; ?>
                                <td class="border border-gray-200 px-4 py-2 flex gap-2">
                                    <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition" onclick="editTask(<?= htmlspecialchars(json_encode($task)) ?>)">Edit</button>
                                    <a href="?delete=<?= $task['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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

        // Toggle start date field
        const scheduleCheckbox = document.getElementById('schedule-checkbox');
        const startDateRow = document.getElementById('start-date-row');
        scheduleCheckbox.addEventListener('change', function() {
            startDateRow.style.display = this.checked ? '' : 'none';
            if (!this.checked) {
                document.getElementById('start_date').value = '';
            }
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

            // Show/hide start date based on value
            if (task.start_date) {
                scheduleCheckbox.checked = true;
                startDateRow.style.display = '';
                document.getElementById('start_date').value = task.start_date;
            } else {
                scheduleCheckbox.checked = false;
                startDateRow.style.display = 'none';
                document.getElementById('start_date').value = '';
            }
        }
    </script>
</body>

</html>