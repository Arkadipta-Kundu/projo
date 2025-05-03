<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$is_admin = ($_SESSION['role'] === 'admin');

$projects = getAllProjects($pdo, $_SESSION['user_id'], $is_admin);
$tasks = getAllTasks($pdo, $_SESSION['user_id'], $is_admin);

// Initialize variables with default values
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : (isset($_COOKIE['project_id']) ? (int)$_COOKIE['project_id'] : null);
$view_type = isset($_GET['view']) ? $_GET['view'] : (isset($_COOKIE['view_type']) ? $_COOKIE['view_type'] : 'all');

// Save preferences in cookies when "Apply Filters" is clicked
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['project_id'], $_GET['view'])) {
    setcookie('project_id', $project_id, time() + (86400 * 30), '/'); // 30 days
    setcookie('view_type', $view_type, time() + (86400 * 30), '/');
}

// Fetch projects and tasks based on filters
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
$view_type = isset($_GET['view']) ? $_GET['view'] : 'all'; // 'all', 'projects', 'tasks'

if ($project_id) {
    $projects = [];
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute([':id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($project) {
        $projects[] = $project;
    }

    $stmt = $pdo->prepare("
        SELECT tasks.*, projects.title as project_title 
        FROM tasks 
        LEFT JOIN projects ON tasks.project_id = projects.id
        WHERE tasks.project_id = :project_id
        ORDER BY tasks.due_date ASC
    ");
    $stmt->execute([':project_id' => $project_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all projects
    $projects = getAllProjects($pdo, $_SESSION['user_id']);

    // Fetch all tasks or only those with project assignments
    if ($view_type == 'tasks') {
        $stmt = $pdo->query("
            SELECT tasks.*, projects.title as project_title 
            FROM tasks 
            LEFT JOIN projects ON tasks.project_id = projects.id
            ORDER BY tasks.due_date ASC
            LIMIT 100
        ");
    } else {
        $stmt = $pdo->query("
            SELECT tasks.*, projects.title as project_title 
            FROM tasks 
            LEFT JOIN projects ON tasks.project_id = projects.id
            WHERE tasks.project_id IS NOT NULL
            ORDER BY tasks.due_date ASC
        ");
    }
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gantt Chart</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include the script in the head or before the closing body tag -->
    <script src="/projo/assets/js/script.js"></script>
    <!-- Frappe Gantt CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>

    <style>
        .gantt-container {
            width: 100%;
            overflow-x: auto;
        }

        .gantt .bar-label {
            font-size: 12px;
            font-weight: bold;
        }

        .gantt .bar-progress {
            fill: #3498db;
        }

        .gantt .bar {
            fill: #a0c5e8;
            stroke: #8baecb;
        }

        .gantt .project .bar {
            fill: #fdca40;
            stroke: #e3b53b;
        }

        .gantt .task .bar {
            fill: #a0e8c5;
            stroke: #8bcba1;
        }

        .gantt .high .bar {
            fill: #e8a0a0;
            stroke: #cb8b8b;
        }

        .gantt .medium .bar {
            fill: #e8d9a0;
            stroke: #cbbf8b;
        }

        .gantt .low .bar {
            fill: #a0e8c5;
            stroke: #8bcba1;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        /* Add this to your existing style section */
        .today-marker {
            pointer-events: none;
        }

        .gantt .grid-background {
            overflow: visible;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Gantt Chart</h2>

        <!-- Filter Options -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <form method="GET" class="filter-bar">
                <div>
                    <label for="project_id" class="font-bold">Filter by Project:</label>
                    <select id="project_id" name="project_id" class="border border-gray-300 p-1 rounded">
                        <option value="">All Projects</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" <?= ($project_id == $project['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($project['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="view" class="font-bold">View:</label>
                    <select id="view" name="view" class="border border-gray-300 p-1 rounded">
                        <option value="all" <?= ($view_type == 'all') ? 'selected' : '' ?>>Projects and Tasks</option>
                        <option value="projects" <?= ($view_type == 'projects') ? 'selected' : '' ?>>Projects Only</option>
                        <option value="tasks" <?= ($view_type == 'tasks') ? 'selected' : '' ?>>Tasks Only</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Apply Filters</button>
            </form>
        </div>

        <!-- Simplified View Mode Controls -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-bold mr-2">View Mode:</span>
                    <select id="view-mode" class="border border-gray-300 p-1 rounded">
                        <option value="Quarter Day">Quarter Day</option>
                        <option value="Half Day">Half Day</option>
                        <option value="Day">Day</option>
                        <option value="Week" selected>Week</option>
                        <option value="Month">Month</option>
                        <option value="Year">Year</option>
                    </select>
                </div>
                <div id="current-view-info" class="text-sm text-gray-600">
                    <span>Current view: <span id="current-mode">Week</span></span>
                </div>
            </div>
        </div>

        <!-- Gantt Chart Container -->
        <div class="bg-white p-6 rounded shadow">
            <div class="gantt-container">
                <svg id="gantt"></svg>
            </div>

            <?php if (empty($projects) && empty($tasks)): ?>
                <p class="text-center text-gray-500 my-8">No data available to display in Gantt chart.</p>
            <?php endif; ?>

            <div class="text-sm text-gray-500 mt-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-[#fdca40] mr-2"></div>
                    <span>Projects</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-[#e8a0a0] mr-2"></div>
                    <span>High Priority Tasks</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-[#e8d9a0] mr-2"></div>
                    <span>Medium Priority Tasks</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-[#a0e8c5] mr-2"></div>
                    <span>Low Priority Tasks</span>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare data for Gantt chart
            const tasks = [];

            <?php if ($view_type != 'tasks'): ?>
                // Add projects to chart
                <?php foreach ($projects as $project): ?>
                    tasks.push({
                        id: 'project-<?= $project['id'] ?>',
                        name: '<?= addslashes($project['title']) ?>',
                        start: '<?= $project['created_at'] ?>',
                        end: '<?= $project['deadline'] ?>',
                        progress: 0,
                        dependencies: '',
                        custom_class: 'project'
                    });
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($view_type != 'projects'): ?>
                // Add tasks to chart
                <?php foreach ($tasks as $task): ?>
                    <?php
                    // Calculate end date (if not present, use due date)
                    $startDate = isset($task['start_date']) ? $task['start_date'] : $task['created_at'];
                    $endDate = $task['due_date'];

                    // Set dependencies if task is linked to a project
                    $dependencies = '';
                    if (isset($task['project_id']) && $task['project_id']) {
                        $dependencies = 'project-' . $task['project_id'];
                    }

                    // Convert status to progress percentage
                    $progress = 0;
                    if ($task['status'] === 'In Progress') $progress = 50;
                    if ($task['status'] === 'Done') $progress = 100;
                    ?>

                    tasks.push({
                        id: 'task-<?= $task['id'] ?>',
                        name: '<?= addslashes($task['title']) ?>',
                        start: '<?= $startDate ?>',
                        end: '<?= $endDate ?>',
                        progress: <?= $progress ?>,
                        dependencies: '<?= $dependencies ?>',
                        custom_class: '<?= strtolower($task['priority']) ?> task'
                    });
                <?php endforeach; ?>
            <?php endif; ?>

            // Only initialize Gantt if we have data
            if (tasks.length > 0) {
                const gantt = new Gantt("#gantt", tasks, {
                    header_height: 50,
                    column_width: 30,
                    step: 24,
                    view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month', 'Year'],
                    bar_height: 20,
                    bar_corner_radius: 3,
                    arrow_curve: 5,
                    padding: 18,
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    language: 'en',
                    on_click: function(task) {
                        console.log(task);
                    },
                    on_date_change: function(task, start, end) {
                        // Could implement AJAX update here
                        console.log(task, start, end);
                    },
                    on_progress_change: function(task, progress) {
                        // Could implement AJAX update here
                        console.log(task, progress);
                    },
                    on_view_change: function(mode) {
                        document.getElementById('current-mode').textContent = mode;
                    }
                });

                // Add responsive behavior for the gantt chart
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        gantt.refresh(tasks);
                        addTodayMarker();
                    }, 200); // Debounce for 200ms
                });

                // View mode change handler
                const viewModeSelect = document.getElementById('view-mode');
                viewModeSelect.addEventListener('change', function() {
                    const selectedMode = this.value;
                    gantt.change_view_mode(selectedMode);
                    document.getElementById('current-mode').textContent = selectedMode;

                    setTimeout(addTodayMarker, 100);
                });

                // Set the select to match the initial view mode
                viewModeSelect.value = gantt.options.view_mode;

                // Function to add the current date indicator
                function addTodayMarker() {
                    const svg = document.querySelector('.gantt .grid-container');
                    if (!svg) return;

                    const existingLine = svg.querySelector('.today-marker-line');
                    const existingText = svg.querySelector('.today-marker-text');

                    if (existingLine || existingText) return; // Skip if marker already exists

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    const datePosition = gantt.get_date_position(today);
                    if (!datePosition) return;

                    const svgHeight = svg.getBoundingClientRect().height;

                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', datePosition);
                    line.setAttribute('y1', 0);
                    line.setAttribute('x2', datePosition);
                    line.setAttribute('y2', svgHeight);
                    line.setAttribute('stroke', 'red');
                    line.setAttribute('stroke-width', 2);
                    line.setAttribute('stroke-dasharray', '5,3');
                    line.classList.add('today-marker-line');

                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', datePosition + 5);
                    text.setAttribute('y', 15);
                    text.setAttribute('fill', 'red');
                    text.setAttribute('font-size', '12');
                    text.textContent = 'Today';
                    text.classList.add('today-marker-text');

                    svg.appendChild(line);
                    svg.appendChild(text);
                }

                // Add the today marker on initial load
                setTimeout(addTodayMarker, 100);
            }
        });
    </script>
</body>

</html>