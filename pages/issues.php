<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

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
            createIssue($pdo, $title, $description, $severity, $status, $project_id);
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
        header('Location: tasks.php');
        exit();
    }
}

// Fetch all issues and projects
$issues = getAllIssues($pdo);
$projects = getAllProjects($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>issues</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include the script in the head or before the closing body tag -->
    <script src="/projo/assets/js/script.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Issue Tracker</h2>
        <div class="bg-white p-6 rounded shadow mb-6">
            <button id="toggle-issue-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Issue</button>
            <form method="POST" id="issue-form" class="space-y-4 hidden">
                <input type="hidden" name="id" id="issue-id">
                <div>
                    <label for="title" class="block font-bold">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="description" class="block font-bold">Description</label>
                    <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded" rows="4" required></textarea>
                </div>
                <div>
                    <label for="severity" class="block font-bold">Severity</label>
                    <select id="severity" name="severity" class="w-full border border-gray-300 p-2 rounded">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block font-bold">Status</label>
                    <select id="status" name="status" class="w-full border border-gray-300 p-2 rounded">
                        <option value="Open">Open</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div>
                    <label for="project_id" class="block font-bold">Assign to Project</label>
                    <select id="project_id" name="project_id" class="w-full border border-gray-300 p-2 rounded">
                        <option value="">No Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Issue</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Title</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Severity</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                        <th class="border border-gray-300 px-4 py-2">Project</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $issue): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['id']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['title']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['description']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['severity']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['status']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($issue['project_title'] ?? 'Unassigned') ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editIssue(<?= htmlspecialchars(json_encode($issue)) ?>)">Edit</button>
                                <a href="?delete=<?= $issue['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
                                <a href="?convert_to_task=<?= $issue['id'] ?>" class="bg-green-500 text-white px-2 py-1 rounded">Convert to Task</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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