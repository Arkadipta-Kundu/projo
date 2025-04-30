<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

// Handle reset action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_time'])) {
    resetTotalTime($pdo);
    header('Location: timer.php'); // Redirect to refresh the page
    exit();
}

// Fetch all tasks
$tasks = getAllTasks($pdo);

// Fetch total time spent on all tasks
$totalTimeSpent = getTotalTimeSpent($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task Timer</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/projo/assets/js/script.js"></script> <!-- Include the dark mode script -->
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Task Timer</h2>

        <!-- Total Time Spent -->
        <div class="bg-purple-100 p-4 rounded shadow mb-6">
            <h3 class="text-xl font-semibold">Total Time Spent</h3>
            <p class="text-2xl font-bold text-purple-600"><?= gmdate("H:i:s", $totalTimeSpent) ?></p>
            <form method="POST" class="mt-4">
                <button type="submit" name="reset_time" class="bg-red-500 text-white px-4 py-2 rounded">Reset Total Time</button>
            </form>
        </div>

        <!-- Task Selection -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-xl font-bold mb-4">Select a Task</h3>
            <select id="task-select" class="w-full border border-gray-300 p-2 rounded">
                <option value="">-- Select a Task --</option>
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= $task['id'] ?>"><?= htmlspecialchars($task['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Timer Section -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-xl font-bold mb-4">Timer</h3>
            <div id="timer-display" class="text-4xl font-bold text-center mb-4">00:00:00</div>
            <div class="flex justify-center space-x-4">
                <button id="start-timer" class="bg-green-500 text-white px-4 py-2 rounded">Start Timer</button>
                <button id="stop-timer" class="bg-red-500 text-white px-4 py-2 rounded" disabled>Stop Timer</button>
            </div>
        </div>

        <!-- Time Tracking Details -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-xl font-bold mb-4">Time Tracking Details</h3>
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">Task</th>
                        <th class="border border-gray-300 px-4 py-2">Time Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($task['title']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= gmdate("H:i:s", getTaskTotalTime($pdo, $task['id'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        let timerInterval;
        let elapsedTime = 0;

        // Start Timer
        document.getElementById('start-timer').addEventListener('click', () => {
            const taskId = document.getElementById('task-select').value;
            if (!taskId) {
                alert('Please select a task to start the timer.');
                return;
            }

            fetch(`/projo/api/task_timer.php?action=start&task_id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Timer started!');
                        document.getElementById('start-timer').disabled = true;
                        document.getElementById('stop-timer').disabled = false;

                        // Start visual timer
                        elapsedTime = 0;
                        timerInterval = setInterval(() => {
                            elapsedTime++;
                            const hours = String(Math.floor(elapsedTime / 3600)).padStart(2, '0');
                            const minutes = String(Math.floor((elapsedTime % 3600) / 60)).padStart(2, '0');
                            const seconds = String(elapsedTime % 60).padStart(2, '0');
                            document.getElementById('timer-display').textContent = `${hours}:${minutes}:${seconds}`;
                        }, 1000);
                    } else {
                        alert('Failed to start timer: ' + data.error);
                    }
                });
        });

        // Stop Timer
        document.getElementById('stop-timer').addEventListener('click', () => {
            const taskId = document.getElementById('task-select').value;
            if (!taskId) {
                alert('Please select a task to stop the timer.');
                return;
            }

            fetch(`/projo/api/task_timer.php?action=stop&task_id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Timer stopped!');
                        document.getElementById('start-timer').disabled = false;
                        document.getElementById('stop-timer').disabled = true;

                        // Stop visual timer
                        clearInterval(timerInterval);
                        location.reload(); // Reload to update time tracking details
                    } else {
                        alert('Failed to stop timer: ' + data.error);
                    }
                });
        });
    </script>
</body>

</html>