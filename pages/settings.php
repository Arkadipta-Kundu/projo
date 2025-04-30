<?php
include __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset_tables'])) {
        // Reset tables logic (unchanged)
    } elseif (isset($_POST['change_password'])) {
        $username = $_POST['username'] ?? '';
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if (!empty($username) && !empty($oldPassword) && !empty($newPassword)) {
            try {
                // Check if the username exists and the old password matches
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && password_verify($oldPassword, $user['password'])) {
                    // Update the password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
                    $stmt->execute([':password' => $hashedPassword, ':username' => $username]);
                    $message = 'Password updated successfully!';
                    logActivity($pdo, $username, 'Changed password');
                } else {
                    $message = 'Invalid username or old password.';
                }
            } catch (Exception $e) {
                $message = 'Error updating password: ' . $e->getMessage();
            }
        } else {
            $message = 'All fields are required.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset_tables'])) {
        try {
            // Disable foreign key checks to allow truncation of tables with relationships
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

            // Truncate all tables to reset data and AUTO_INCREMENT values
            $pdo->exec("TRUNCATE TABLE users;");
            $pdo->exec("TRUNCATE TABLE projects;");
            $pdo->exec("TRUNCATE TABLE tasks;");
            $pdo->exec("TRUNCATE TABLE notes;");
            $pdo->exec("TRUNCATE TABLE issues;");

            // Re-enable foreign key checks
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

            // Reinsert default admin user
            $pdo->exec("INSERT INTO users (username, password) VALUES ('admin', '" . password_hash('password123', PASSWORD_DEFAULT) . "');");

            $message = 'All tables have been reset successfully!';
            logActivity($pdo, $_SESSION['user'], 'Reset Database');
        } catch (Exception $e) {
            $message = 'Error resetting tables: ' . $e->getMessage();
        }
    } elseif (isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'] ?? '';
        if (!empty($newPassword)) {
            try {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $pdo->exec("UPDATE users SET password = '$hashedPassword' WHERE username = 'admin';");
                $message = 'Admin password updated successfully!';
            } catch (Exception $e) {
                $message = 'Error updating password: ' . $e->getMessage();
            }
        } else {
            $message = 'Password cannot be empty.';
        }
    } elseif (isset($_POST['import_data'])) {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['import_file']['tmp_name'];
            $fileType = $_POST['file_type'] ?? '';
            $tableName = $_POST['table_name'] ?? '';

            if (empty($tableName) && $fileType === 'csv') {
                $message = 'Table name is required for CSV import.';
            } else {
                try {
                    $fileContents = file_get_contents($fileTmpPath);
                    if ($fileType === 'json') {
                        $data = json_decode($fileContents, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            foreach ($data as $table => $rows) {
                                foreach ($rows as $row) {
                                    $columns = implode(',', array_keys($row));
                                    $placeholders = ':' . implode(',:', array_keys($row));
                                    $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
                                    $stmt->execute($row);
                                }
                            }
                            $message = 'Data imported successfully!';
                            logActivity($pdo, $_SESSION['user'], 'Imported data');
                        } else {
                            $message = 'Invalid JSON file.';
                        }
                    } elseif ($fileType === 'csv') {
                        $handle = fopen($fileTmpPath, 'r');
                        if ($handle) {
                            $header = fgetcsv($handle);
                            while (($row = fgetcsv($handle)) !== false) {
                                $data = array_combine($header, $row);
                                // Remove invalid columns
                                unset($data['project_title']);
                                $columns = implode(',', array_keys($data));
                                $placeholders = ':' . implode(',:', array_keys($data));
                                $stmt = $pdo->prepare("INSERT INTO notes ($columns) VALUES ($placeholders)");
                                $stmt->execute($data);
                            }
                            fclose($handle);
                            $message = 'Data imported successfully!';
                            logActivity($pdo, $_SESSION['user'], 'Imported data');
                        }
                    } else {
                        $message = 'Unsupported file type.';
                    }
                } catch (Exception $e) {
                    $message = 'Error importing data: ' . $e->getMessage();
                }
            }
        } else {
            $message = 'No file uploaded or file upload error.';
        }
    }
}

// Fetch system infoUnreleased
$appVersion = '1.4.0 (God edition)'; // Update this as needed
$lastUpdated = '01-05-2025'; // Update this as needed

// Fetch logs with pagination
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Settings</h2>
        <?php if ($message): ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">Toggle Dark Mode</h3>
            <button id="theme-toggle" class="bg-gray-200 text-gray-800 px-4 py-2 rounded">Dark Mode</button>
        </div>
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">Import Data</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="file_type" class="block font-bold">File Type</label>
                    <select id="file_type" name="file_type" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div>
                    <label for="table_name" class="block font-bold">Table Name</label>
                    <select id="table_name" name="table_name" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="projects">Projects</option>
                        <option value="tasks">Tasks</option>
                        <option value="notes">Notes</option>
                        <option value="issues">Issues</option>
                    </select>
                </div>
                <div>
                    <label for="import_file" class="block font-bold">Upload File</label>
                    <input type="file" id="import_file" name="import_file" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" name="import_data" class="bg-blue-500 text-white px-4 py-2 rounded">Import Data</button>
            </form>
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
        <!-- filepath: c:\xampp\htdocs\projo\pages\settings.php -->
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">Backup Database</h3>
            <p class="text-gray-600 mb-4">Click the button below to download a backup of the current database as a .sql file.</p>
            <form method="POST" action="../backup.php">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Backup Database</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-xl font-bold mb-4">Change Password</h3>
            <button id="toggle-password-form" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Change Password</button>
            <form method="POST" id="password-form" class="space-y-4 hidden">
                <div>
                    <label for="username" class="block font-bold">Username</label>
                    <input type="text" id="username" name="username" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="old_password" class="block font-bold">Old Password</label>
                    <input type="password" id="old_password" name="old_password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="new_password" class="block font-bold">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" name="change_password" class="bg-blue-500 text-white px-4 py-2 rounded">Update Password</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-xl font-bold mb-4">Logout</h3>
            <p class="text-gray-600 mb-4">Click the button below to log out of your account.</p>
            <form method="POST" action="/projo/logout.php">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Logout</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-xl font-bold mb-4">Reset Application</h3>
            <p class="text-gray-600 mb-4">Click the button below to reset all tables and their identity generation. This action cannot be undone.</p>
            <form method="POST" onsubmit="return confirm('Are you sure you want to reset all tables? This action cannot be undone!');">
                <button type="submit" name="reset_tables" class="bg-red-500 text-white px-4 py-2 rounded">Reset All Tables</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">Activity Logs</h3>
            <?php if (empty($logs)): ?>
                <p class="text-gray-600">No activity logs available.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($logs as $log): ?>
                        <li class="bg-gray-100 p-4 rounded shadow">
                            <p><strong>User:</strong> <?= htmlspecialchars($log['username']) ?></p>
                            <p><strong>Action:</strong> <?= htmlspecialchars($log['action']) ?></p>
                            <p class="text-sm text-gray-600"><strong>Timestamp:</strong> <?= htmlspecialchars($log['timestamp']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-xl font-bold mb-4">System Info / Version Info</h3>
            <p><strong>App Version:</strong> <?= htmlspecialchars($appVersion) ?></p>
            <p><strong>Last Updated:</strong> <?= htmlspecialchars($lastUpdated) ?></p>
        </div>
    </main>
    <script>
        // Toggle dark mode
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        // Load saved theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark');
            themeToggle.textContent = 'Light Mode';
        }

        // Toggle theme on button click
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark');
            const isDark = body.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeToggle.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        });
        // Toggle the visibility of the password form
        const togglePasswordFormButton = document.getElementById('toggle-password-form');
        const passwordForm = document.getElementById('password-form');

        togglePasswordFormButton.addEventListener('click', () => {
            passwordForm.classList.toggle('hidden');
            togglePasswordFormButton.textContent = passwordForm.classList.contains('hidden') ? 'Change Password' : 'Cancel';
        });
    </script>
</body>

</html>