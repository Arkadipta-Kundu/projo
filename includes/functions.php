<?php
// Fetch all tasks
function getAllTasks($pdo, $user_id, $is_admin = false)
{
    if ($is_admin) {
        $stmt = $pdo->prepare("
            SELECT tasks.*, users.username AS created_by 
            FROM tasks 
            LEFT JOIN users ON tasks.user_id = users.id
            ORDER BY due_date ASC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT tasks.*, users.username AS created_by 
            FROM tasks 
            LEFT JOIN users ON tasks.user_id = users.id
            WHERE tasks.user_id = :user_id
            ORDER BY due_date ASC
        ");
        $stmt->execute([':user_id' => $user_id]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new task
function createTask($pdo, $title, $description, $due_date, $priority, $status, $project_id, $user_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO tasks (title, description, start_date, due_date, priority, status, project_id, user_id) 
        VALUES (:title, :description, CURDATE(), :due_date, :priority, :status, :project_id, :user_id)
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':due_date' => $due_date,
        ':priority' => $priority,
        ':status' => $status,
        ':project_id' => $project_id,
        ':user_id' => $user_id,
    ]);
}

// Update an existing task
function updateTask($pdo, $id, $title, $description, $due_date, $priority, $status, $project_id)
{
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = :title, 
            description = :description, 
            due_date = :due_date, 
            priority = :priority, 
            status = :status, 
            project_id = :project_id 
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
function getAllProjects($pdo, $user_id, $is_admin = false)
{
    if ($is_admin) {
        $stmt = $pdo->prepare("
            SELECT projects.*, users.username AS created_by 
            FROM projects 
            LEFT JOIN users ON projects.user_id = users.id
            ORDER BY deadline ASC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT projects.*, users.username AS created_by 
            FROM projects 
            LEFT JOIN users ON projects.user_id = users.id
            WHERE projects.user_id = :user_id
            ORDER BY deadline ASC
        ");
        $stmt->execute([':user_id' => $user_id]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new project
function createProject($pdo, $title, $description, $deadline, $user_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO projects (title, description, deadline, user_id) 
        VALUES (:title, :description, :deadline, :user_id)
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':deadline' => $deadline,
        ':user_id' => $user_id,
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
function getTotalProjects($pdo, $user_id, $is_admin = false)
{
    if ($is_admin) {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM projects");
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM projects WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
    }
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get total tasks
function getTotalTasks($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get pending tasks
function getPendingTasks($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id AND status = 'To Do'");
    $stmt->execute([':user_id' => $user_id]);
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
function createNoteAndReturnId($pdo, $content, $project_id, $user_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO notes (content, project_id, user_id) 
        VALUES (:content, :project_id, :user_id)
    ");
    $stmt->execute([
        ':content' => $content,
        ':project_id' => $project_id,
        ':user_id' => $user_id,
    ]);
    return $pdo->lastInsertId();
}

function createNote($pdo, $content, $project_id, $user_id)
{
    $sql = "INSERT INTO notes (content, project_id, user_id) VALUES (:content, :project_id, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':content' => $content,
        ':project_id' => $project_id,
        ':user_id' => $user_id,
    ]);
}

function getNoteById($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT notes.*, projects.title AS project_title 
        FROM notes 
        LEFT JOIN projects ON notes.project_id = projects.id 
        WHERE notes.id = :id
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all notes
function getAllNotes($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT notes.*, projects.title AS project_title 
        FROM notes 
        LEFT JOIN projects ON notes.project_id = projects.id 
        WHERE notes.user_id = :user_id
        ORDER BY notes.id DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
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
function createIssue($pdo, $title, $description, $severity, $status, $project_id, $user_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO issues (title, description, severity, status, project_id, user_id) 
        VALUES (:title, :description, :severity, :status, :project_id, :user_id)
    ");
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':severity' => $severity,
        ':status' => $status,
        ':project_id' => $project_id,
        ':user_id' => $user_id,
    ]);
}

// Update an existing issue
function updateIssue($pdo, $id, $title, $description, $severity, $status, $project_id)
{
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
function getAllIssues($pdo, $user_id, $is_admin = false)
{
    if ($is_admin) {
        $stmt = $pdo->prepare("
            SELECT issues.*, users.username AS created_by, projects.title AS project_title 
            FROM issues 
            LEFT JOIN users ON issues.user_id = users.id
            LEFT JOIN projects ON issues.project_id = projects.id
            ORDER BY issues.id DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT issues.*, users.username AS created_by, projects.title AS project_title 
            FROM issues 
            LEFT JOIN users ON issues.user_id = users.id
            LEFT JOIN projects ON issues.project_id = projects.id
            WHERE issues.user_id = :user_id
            ORDER BY issues.id DESC
        ");
        $stmt->execute([':user_id' => $user_id]);
    }
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

        // Use the project_id and user_id from the issue when creating the task
        createTask(
            $pdo,
            $issue['title'],
            $issue['description'],
            date('Y-m-d'), // Default due date to today
            $priority, // Map severity to priority
            'To Do', // Default status
            $issue['project_id'], // Assign the same project as the issue
            $issue['user_id'] // Pass the user_id from the issue
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
function startTaskTimer($pdo, $task_id, $user_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO task_time_tracking (task_id, start_time, user_id) 
        VALUES (:task_id, NOW(), :user_id)
    ");
    $stmt->execute([
        ':task_id' => $task_id,
        ':user_id' => $user_id,
    ]);
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

// Get completed tasks
function getCompletedTasks($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id AND status = 'Done'");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get overdue tasks
function getOverdueTasks($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE user_id = :user_id AND due_date < CURDATE() AND status != 'Done'");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get total issues
function getTotalIssues($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM issues WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

function getTotalTimeByProject($pdo, $project_id)
{
    $stmt = $pdo->prepare("
        SELECT SUM(duration) AS total_time 
        FROM task_time_tracking 
        INNER JOIN tasks ON task_time_tracking.task_id = tasks.id
        WHERE tasks.project_id = :project_id
    ");
    $stmt->execute([':project_id' => $project_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total_time'] ?? 0;
}

// Send a message
function sendMessage($pdo, $sender_id, $receiver_id, $message)
{
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)");
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id,
        ':message' => $message
    ]);
}

// Fetch messages between two users
function getMessages($pdo, $user1_id, $user2_id)
{
    $stmt = $pdo->prepare("
        SELECT messages.*, u1.username AS sender_name, u2.username AS receiver_name
        FROM messages
        JOIN users u1 ON messages.sender_id = u1.id
        JOIN users u2 ON messages.receiver_id = u2.id
        WHERE (sender_id = :user1 AND receiver_id = :user2)
           OR (sender_id = :user2 AND receiver_id = :user1)
        ORDER BY created_at ASC
    ");
    $stmt->execute([':user1' => $user1_id, ':user2' => $user2_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Search users by username
function searchUsers($pdo, $query, $exclude_id)
{
    $stmt = $pdo->prepare("SELECT id, username, name FROM users WHERE username LIKE :query AND id != :exclude_id LIMIT 10");
    $stmt->execute([':query' => "%$query%", ':exclude_id' => $exclude_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all users (with real name) the current user has chatted with
function getConversations($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.username, MAX(m.created_at) as last_message_time
        FROM messages m
        JOIN users u ON (u.id = m.sender_id AND m.receiver_id = :uid) OR (u.id = m.receiver_id AND m.sender_id = :uid)
        WHERE u.id != :uid
        GROUP BY u.id, u.name, u.username
        ORDER BY last_message_time DESC
    ");
    $stmt->execute([':uid' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
