<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Fetch counts
$totalProjects = getTotalProjects($pdo);
$totalTasks = getTotalTasks($pdo);
$pendingTasks = getPendingTasks($pdo);
$upcomingTasks = getUpcomingTasks($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>


<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Dashboard Overview</h2>
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Total Projects</h3>
                <p class="text-2xl font-bold text-blue-600"><?= $totalProjects ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Total Tasks</h3>
                <p class="text-2xl font-bold text-green-600"><?= $totalTasks ?></p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Pending Tasks</h3>
                <p class="text-2xl font-bold text-yellow-600"><?= $pendingTasks ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-2xl font-bold mb-4">Export Data</h3>
            <form method="GET" action="/projo/export.php" class="space-y-4">
                <div>
                    <label for="type" class="block font-bold">What to Export</label>
                    <select id="type" name="type" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="projects">Projects</option>
                        <option value="tasks">Tasks</option>
                        <option value="notes">Notes</option>
                        <option value="issues">Issues</option>
                    </select>
                </div>
                <div>
                    <label for="format" class="block font-bold">Export Format</label>
                    <select id="format" name="format" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Export</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-2xl font-bold mb-4">Upcoming Tasks (Due Today/Tomorrow)</h3>
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">ID</th>
                        <th class="border border-gray-300 px-4 py-2">Title</th>
                        <th class="border border-gray-300 px-4 py-2">Due Date</th>
                        <th class="border border-gray-300 px-4 py-2">Priority</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingTasks as $task): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['id']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['title']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['due_date']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['priority']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>