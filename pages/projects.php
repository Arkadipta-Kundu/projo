<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$is_admin = ($_SESSION['role'] === 'admin'); // Define $is_admin

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $deadline = $_POST['deadline'] ?? '';

    if (!empty($title) && !empty($deadline)) {
        if (isset($_POST['id']) && $_POST['id'] !== '') {
            // Update project
            updateProject($pdo, $_POST['id'], $title, $description, $deadline);
        } else {
            // Create new project
            createProject($pdo, $title, $description, $deadline, $_SESSION['user_id']);
        }
    }
}

// Handle project deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (!empty($id)) {
        deleteProject($pdo, $id); // Call the delete function
        header('Location: projects.php'); // Redirect to refresh the page
        exit();
    }
}

// Fetch all projects
$projects = getAllProjects($pdo, $_SESSION['user_id'], $is_admin);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projects</title>
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
        <h2 class="text-3xl font-bold mb-4">Projects</h2>
        <div class="bg-white p-6 rounded shadow mb-6">
            <button id="toggle-project-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Project</button>
            <form method="POST" id="project-form" class="space-y-4 hidden">
                <input type="hidden" name="id" id="project-id">
                <div>
                    <label for="title" class="block font-bold">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="description" class="block font-bold">Description</label>
                    <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded"></textarea>
                </div>
                <div>
                    <label for="deadline" class="block font-bold">Deadline</label>
                    <input type="date" id="deadline" name="deadline" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Project</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Title</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Deadline</th>
                        <?php if ($is_admin): ?>
                            <th class="border border-gray-300 px-4 py-2">Created By</th>
                        <?php endif; ?>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($project['id']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($project['title']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($project['description']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($project['deadline']) ?></td>
                            <?php if ($is_admin): ?>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($project['created_by']) ?></td>
                            <?php endif; ?>
                            <td class="border border-gray-300 px-4 py-2">
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="editProject(<?= htmlspecialchars(json_encode($project)) ?>)">Edit</button>
                                <a href="?delete=<?= $project['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        // Toggle the visibility of the project form
        const toggleProjectFormButton = document.getElementById('toggle-project-form');
        const projectForm = document.getElementById('project-form');

        toggleProjectFormButton.addEventListener('click', () => {
            projectForm.classList.toggle('hidden');
            toggleProjectFormButton.textContent = projectForm.classList.contains('hidden') ? 'Add Project' : 'Cancel';
        });

        // Populate form for editing a project
        function editProject(project) {
            projectForm.classList.remove('hidden');
            toggleProjectFormButton.textContent = 'Cancel';
            document.getElementById('project-id').value = project.id;
            document.getElementById('title').value = project.title;
            document.getElementById('description').value = project.description;
            document.getElementById('deadline').value = project.deadline;
        }
    </script>
</body>

</html>