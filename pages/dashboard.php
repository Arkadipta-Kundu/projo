<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Fetch counts
$totalProjects = getTotalProjects($pdo);
$totalTasks = getTotalTasks($pdo);
$pendingTasks = getPendingTasks($pdo);
$completedTasks = getCompletedTasks($pdo);
$overdueTasks = getOverdueTasks($pdo);
$totalIssues = getTotalIssues($pdo);
$upcomingTasks = getUpcomingTasks($pdo);

// Fetch all tasks for the calendar
$allTasks = getAllTasks($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projo</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/projo/assets/js/script.js"></script>

    <!-- Add FullCalendar CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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
            <div class="bg-purple-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Completed Tasks</h3>
                <p class="text-2xl font-bold text-purple-600"><?= $completedTasks ?></p>
            </div>
            <div class="bg-red-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Overdue Tasks</h3>
                <p class="text-2xl font-bold text-red-600"><?= $overdueTasks ?></p>
            </div>
            <div class="bg-orange-100 p-4 rounded shadow">
                <h3 class="text-xl font-semibold">Total Issues</h3>
                <p class="text-2xl font-bold text-orange-600"><?= $totalIssues ?></p>
            </div>
        </div>
        <div class="bg-purple-100 p-4 rounded shadow mb-8">
            <h3 class="text-xl font-semibold">Total Time Spent</h3>
            <p class="text-2xl font-bold text-purple-600"><?= gmdate("H:i:s", getTotalTimeSpent($pdo)) ?></p>
        </div>
        <div class="bg-white p-6 rounded shadow mb-8">
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
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-2xl font-bold mb-4">Task Calendar</h3>
            <div id="calendar" style="height: 800px;"></div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            // Initialize FullCalendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // Month view
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?= json_encode(array_map(function ($task) {
                            return [
                                'id' => $task['id'],
                                'title' => $task['title'],
                                'start' => $task['due_date'], // Use due_date as the start date
                                'end' => $task['due_date'],   // Use due_date as the end date to make it appear only on that day
                                'backgroundColor' => $task['priority'] === 'High' ? '#e3342f' : ($task['priority'] === 'Medium' ? '#f6993f' : '#38c172'),
                                'borderColor' => $task['priority'] === 'High' ? '#e3342f' : ($task['priority'] === 'Medium' ? '#f6993f' : '#38c172'),
                            ];
                        }, $allTasks)) ?>,
                dayCellClassNames: function(arg) {
                    const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
                    if (arg.date.toISOString().split('T')[0] === today) {
                        return ['highlight-today']; // Add a custom class for today's date
                    }
                    return [];
                }
            });

            calendar.render();
        });
    </script>
</body>

</html>