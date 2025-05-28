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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
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
            <h2 class="text-3xl font-bold">Projects</h2>
            <button id="toggle-project-form" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">
                <i class="fas fa-plus mr-2"></i>Add Project
            </button>
        </div>
        <form method="POST" id="project-form" class="space-y-4 hidden bg-blue-50 rounded-lg p-6 shadow-inner mb-6">
            <input type="hidden" name="id" id="project-id">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="title" class="block font-semibold mb-1">Title</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label for="description" class="block font-semibold mb-1">Description</label>
                    <input id="description" name="description" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label for="deadline" class="block font-semibold mb-1">Deadline</label>
                    <input type="date" id="deadline" name="deadline" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-400" required>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition-all duration-150">Save Project</button>
            </div>
        </form>
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($projects as $project): ?>
                    <div class="bg-blue-50 rounded-xl p-5 shadow hover:shadow-lg transition flex flex-col justify-between h-full">
                        <div>
                            <h3 class="text-xl font-bold text-blue-700 mb-2"><?= htmlspecialchars($project['title']) ?></h3>
                            <p class="text-gray-700 mb-2 break-words"><?= htmlspecialchars($project['description']) ?: '<span class=\'text-gray-400\'>No description</span>' ?></p>
                            <div class="text-sm text-gray-500 mb-2">Deadline: <span class="font-semibold text-blue-600"><?= htmlspecialchars($project['deadline']) ?></span></div>
                            <?php if ($is_admin): ?>
                                <div class="text-xs text-gray-400 mb-2">Created By: <?= htmlspecialchars($project['created_by']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition w-full" onclick="editProject(<?= htmlspecialchars(json_encode($project)) ?>)">Edit</button>
                            <a href="?delete=<?= $project['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition w-full text-center" onclick="return confirm('Are you sure you want to delete this project? This action cannot be undone.');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?>
                    <div class="col-span-full text-center text-gray-400 py-8">No projects found.</div>
                <?php endif; ?>
            </div>
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