<?php
// Fetch all tasks
function getAllTasks($pdo)
{
    $stmt = $pdo->query("
        SELECT tasks.*, projects.title AS project_title 
        FROM tasks 
        LEFT JOIN projects ON tasks.project_id = projects.id 
        ORDER BY tasks.due_date ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new task
function createTask($pdo, $title, $description, $due_date, $priority, $status, $project_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO tasks (title, description, start_date, due_date, priority, status, project_id) 
        VALUES (:title, :description, CURDATE(), :due_date, :priority, :status, :project_id)
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':due_date' => $due_date,
        ':priority' => $priority,
        ':status' => $status,
        ':project_id' => $project_id,
    ]);
}

// Update an existing task
function updateTask($pdo, $id, $title, $description, $due_date, $priority, $status, $project_id)
{
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = :title, description = :description, due_date = :due_date, priority = :priority, status = :status, project_id = :project_id 
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $id,
        ':title' => $title,
        ':description' => $description,
        ':due_date' => $due_date,
        ':priority' => $priority,
        ':status' => $status,
        ':project_id' => $project_id,
    ]);
}

// Delete a task
function deleteTask($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Toggle task status
function toggleTaskStatus($pdo, $id, $new_status)
{
    $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':status' => $new_status,
    ]);
}

// Fetch all projects
function getAllProjects($pdo)
{
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY deadline ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new project
function createProject($pdo, $title, $description, $deadline)
{
    $stmt = $pdo->prepare("INSERT INTO projects (title, description, deadline) VALUES (:title, :description, :deadline)");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':deadline' => $deadline,
    ]);
}

// Update an existing project
function updateProject($pdo, $id, $title, $description, $deadline)
{
    $stmt = $pdo->prepare("UPDATE projects SET title = :title, description = :description, deadline = :deadline WHERE id = :id");
    $stmt->execute([
        ':id' => $id,
        ':title' => $title,
        ':description' => $description,
        ':deadline' => $deadline,
    ]);
}

// Delete a project
function deleteProject($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Get total projects
function getTotalProjects($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM projects");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get total tasks
function getTotalTasks($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tasks");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get pending tasks
function getPendingTasks($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM tasks WHERE status != 'Done'");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get upcoming tasks (due today or tomorrow)
function getUpcomingTasks($pdo)
{
    $stmt = $pdo->query("
        SELECT * 
        FROM tasks 
        WHERE due_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY
        ORDER BY due_date ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPriorityColor($priority)
{
    switch ($priority) {
        case 'High':
            return 'border-red-500';
        case 'Medium':
            return 'border-yellow-500';
        case 'Low':
            return 'border-green-500';
        default:
            return 'border-gray-500';
    }
}

// Create a new note
function createNote($pdo, $content, $project_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO notes (content, project_id) 
        VALUES (:content, :project_id)
    ");
    $stmt->execute([
        ':content' => $content,
        ':project_id' => $project_id,
    ]);
}

// Fetch all notes
function getAllNotes($pdo)
{
    $stmt = $pdo->query("
        SELECT notes.*, projects.title AS project_title 
        FROM notes 
        LEFT JOIN projects ON notes.project_id = projects.id 
        ORDER BY notes.id DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update an existing note
function updateNote($pdo, $id, $content, $project_id)
{
    $stmt = $pdo->prepare("
        UPDATE notes 
        SET content = :content, project_id = :project_id 
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $id,
        ':content' => $content,
        ':project_id' => $project_id,
    ]);
}

// Delete a note
function deleteNote($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Create a new issue
function createIssue($pdo, $title, $description, $severity, $status, $project_id) {
    $stmt = $pdo->prepare("
        INSERT INTO issues (title, description, severity, status, project_id) 
        VALUES (:title, :description, :severity, :status, :project_id)
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':severity' => $severity,
        ':status' => $status,
        ':project_id' => $project_id,
    ]);
}

// Update an existing issue
function updateIssue($pdo, $id, $title, $description, $severity, $status, $project_id) {
    $stmt = $pdo->prepare("
        UPDATE issues 
        SET title = :title, description = :description, severity = :severity, status = :status, project_id = :project_id 
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $id,
        ':title' => $title,
        ':description' => $description,
        ':severity' => $severity,
        ':status' => $status,
        ':project_id' => $project_id,
    ]);
}

// Delete an issue
function deleteIssue($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM issues WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

// Fetch all issues
function getAllIssues($pdo) {
    $stmt = $pdo->query("
        SELECT issues.*, projects.title AS project_title 
        FROM issues 
        LEFT JOIN projects ON issues.project_id = projects.id 
        ORDER BY issues.id DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Convert an issue to a task
function convertIssueToTask($pdo, $id)
{
    // Fetch the issue details
    $stmt = $pdo->prepare("SELECT * FROM issues WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $issue = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($issue) {
        // Map issue severity to task priority
        $priority = $issue['severity']; // Direct mapping: Low → Low, Medium → Medium, High → High

        // Use the project_id from the issue when creating the task
        createTask(
            $pdo,
            $issue['title'],
            $issue['description'],
            date('Y-m-d'), // Default due date to today
            $priority, // Map severity to priority
            'To Do', // Default status
            $issue['project_id'] // Assign the same project as the issue
        );

        // Delete the issue after converting it to a task
        deleteIssue($pdo, $id);
    }
}
function handleError($message)
{
    echo "<script>alert('" . addslashes($message) . "');</script>";
}

function logActivity($pdo, $username, $action)
{
    $stmt = $pdo->prepare("INSERT INTO activity_logs (username, action) VALUES (:username, :action)");
    $stmt->execute([
        ':username' => $username,
        ':action' => $action,
    ]);
}
// Start time tracking for a task
function startTaskTimer($pdo, $task_id)
{
    $stmt = $pdo->prepare("INSERT INTO task_time_tracking (task_id, start_time) VALUES (:task_id, NOW())");
    $stmt->execute([':task_id' => $task_id]);
}

// Stop time tracking for a task
function stopTaskTimer($pdo, $task_id)
{
    $stmt = $pdo->prepare("
        UPDATE task_time_tracking 
        SET end_time = NOW(), duration = TIMESTAMPDIFF(SECOND, start_time, NOW()) 
        WHERE task_id = :task_id AND end_time IS NULL
    ");
    $stmt->execute([':task_id' => $task_id]);
}

// Get total time spent on a task
function getTaskTotalTime($pdo, $task_id)
{
    $stmt = $pdo->prepare("
        SELECT SUM(duration) AS total_time 
        FROM task_time_tracking 
        WHERE task_id = :task_id
    ");
    $stmt->execute([':task_id' => $task_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total_time'] ?? 0;
}
// Get total time spent on all tasks
function getTotalTimeSpent($pdo)
{
    $stmt = $pdo->query("
        SELECT SUM(duration) AS total_time 
        FROM task_time_tracking
    ");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total_time'] ?? 0;
}
// Reset total time spent on all tasks
function resetTotalTime($pdo)
{
    $pdo->exec("TRUNCATE TABLE task_time_tracking");
}