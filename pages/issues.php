<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$is_admin = ($_SESSION['role'] === 'admin');

// Fetch all issues and projects with filters
$project_filter = $_GET['project_id'] ?? '';
$status_filter = $_GET['status'] ?? '';
$severity_filter = $_GET['severity'] ?? '';

$query = "SELECT issues.*, users.username AS created_by, projects.title AS project_title
    FROM issues
    LEFT JOIN users ON issues.user_id = users.id
    LEFT JOIN projects ON issues.project_id = projects.id
    WHERE issues.user_id = :user_id";
$params = [':user_id' => $_SESSION['user_id']];

if ($project_filter !== '') {
    $query .= " AND issues.project_id = :project_id";
    $params[':project_id'] = $project_filter;
}
if ($status_filter !== '') {
    $query .= " AND issues.status = :status";
    $params[':status'] = $status_filter;
}
if ($severity_filter !== '') {
    $query .= " AND issues.severity = :severity";
    $params[':severity'] = $severity_filter;
}
$query .= " ORDER BY issues.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

$projects = getAllProjects($pdo, $_SESSION['user_id'], $is_admin);
// Handle form submission (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $severity = $_POST['severity'] ?? 'Low';
    $status = $_POST['status'] ?? 'Open';
    $project_id = $_POST['project_id'] ?? null;

    if (!empty($title) && !empty($description)) {
        if ($id) {
            // Update issue
            updateIssue($pdo, $id, $title, $description, $severity, $status, $project_id);
        } else {
            // Create new issue
            createIssue($pdo, $title, $description, $severity, $status, $project_id, $_SESSION['user_id']);
        }
    }
}

// Handle issue deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (!empty($id)) {
        deleteIssue($pdo, $id);
        header('Location: issues.php');
        exit();
    }
}

// Handle "Convert to Task"
if (isset($_GET['convert_to_task'])) {
    $id = $_GET['convert_to_task'];
    if (!empty($id)) {
        convertIssueToTask($pdo, $id);
        header('Location: tasks.php'); // Redirect to the tasks page after conversion
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>issues</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include the script in the head or before the closing body tag -->
    <script src="../assets/js/script.js"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold">Issue Tracker</h2>
            <button id="toggle-issue-form" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">
                <i class="fas fa-plus mr-2"></i>Add Issue
            </button>
        </div>
        <form method="POST" id="issue-form" class="space-y-4 hidden bg-blue-50 rounded-lg p-6 shadow-inner mb-6">
            <input type="hidden" name="id" id="issue-id">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="title" class="block font-semibold mb-1">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label for="severity" class="block font-semibold mb-1">Severity</label>
                    <select id="severity" name="severity" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block font-semibold mb-1">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                        <option value="Open">Open</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="description" class="block font-semibold mb-1">Description</label>
                <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" rows="3" required></textarea>
            </div>
            <div>
                <label for="project_id" class="block font-semibold mb-1">Assign to Project</label>
                <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                    <option value="">No Project</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">Save Issue</button>
            </div>
        </form>
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
                    <option value="Open" <?= (isset($_GET['status']) && $_GET['status'] == 'Open') ? 'selected' : '' ?>>Open</option>
                    <option value="Resolved" <?= (isset($_GET['status']) && $_GET['status'] == 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>
            <div>
                <label for="filter_severity" class="block font-semibold mb-1">Filter by Severity</label>
                <select id="filter_severity" name="severity" class="border border-gray-300 p-2 rounded">
                    <option value="">All Severities</option>
                    <option value="Low" <?= (isset($_GET['severity']) && $_GET['severity'] == 'Low') ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= (isset($_GET['severity']) && $_GET['severity'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= (isset($_GET['severity']) && $_GET['severity'] == 'High') ? 'selected' : '' ?>>High</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Apply Filters</button>
        </form>
        <div class="bg-white p-6 rounded-xl shadow">
            <!-- Wrap your table like this -->
            <div class="overflow-x-auto w-full">
                <table class="table-auto w-full border-collapse border border-gray-200 text-sm md:text-base">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="border border-gray-200 px-4 py-2">ID</th>
                            <th class="border border-gray-200 px-4 py-2">Title</th>
                            <th class="border border-gray-200 px-4 py-2">Description</th>
                            <th class="border border-gray-200 px-4 py-2">Severity</th>
                            <th class="border border-gray-200 px-4 py-2">Status</th>
                            <th class="border border-gray-200 px-4 py-2">Project</th>
                            <?php if ($is_admin): ?>
                                <th class="border border-gray-200 px-4 py-2">Created By</th>
                            <?php endif; ?>
                            <th class="border border-gray-200 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues as $issue): ?>
                            <?php
                            // If the issue is linked to a task, get the task status
                            $display_status = $issue['status'];
                            if (!empty($issue['task_id'])) {
                                $task_status = getTaskStatus($pdo, $issue['task_id']);
                                if ($task_status === 'Done') {
                                    $display_status = 'Resolved';
                                } elseif ($task_status) {
                                    $display_status = $task_status;
                                }
                            }
                            ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['id']) ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['title']) ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['description']) ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['severity']) ?></td>
                                <td class="border border-gray-200 px-4 py-2">
                                    <span class="px-2 py-1 rounded font-semibold 
                                        <?php
                                        if ($display_status === 'To Do') echo 'border border-blue-500 text-blue-600 bg-blue-50';
                                        elseif ($display_status === 'In Progress') echo 'border border-yellow-500 text-yellow-600 bg-yellow-50';
                                        elseif ($display_status === 'Resolved') echo 'border border-green-500 text-green-600 bg-green-50';
                                        else echo 'border border-gray-400 text-gray-600 bg-gray-100';
                                        ?>">
                                        <?= htmlspecialchars($display_status) ?>
                                    </span>
                                </td>
                                <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['project_title'] ?? 'Unassigned') ?></td>
                                <?php if ($is_admin): ?>
                                    <td class="border border-gray-200 px-4 py-2"><?= htmlspecialchars($issue['created_by']) ?></td>
                                <?php endif; ?>
                                <td class="border border-gray-200 px-4 py-2 flex gap-2">
                                    <?php if (empty($issue['task_id'])): ?>
                                        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition" onclick="editIssue(<?= htmlspecialchars(json_encode($issue)) ?>)">Edit</button>
                                        <a href="?convert_to_task=<?= $issue['id'] ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Convert to Task</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $issue['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        // Toggle the visibility of the issue form
        const toggleIssueFormButton = document.getElementById('toggle-issue-form');
        const issueForm = document.getElementById('issue-form');

        toggleIssueFormButton.addEventListener('click', () => {
            issueForm.classList.toggle('hidden');
            toggleIssueFormButton.textContent = issueForm.classList.contains('hidden') ? 'Add Issue' : 'Cancel';
        });

        // Populate form for editing an issue
        function editIssue(issue) {
            issueForm.classList.remove('hidden');
            toggleIssueFormButton.textContent = 'Cancel';
            document.getElementById('issue-id').value = issue.id;
            document.getElementById('title').value = issue.title;
            document.getElementById('description').value = issue.description;
            document.getElementById('severity').value = issue.severity;
            document.getElementById('status').value = issue.status;
            document.getElementById('project_id').value = issue.project_id || '';
        }
    </script>
</body>

</html>