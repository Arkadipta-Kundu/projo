<?php

/**
 * Projo - Setup Script
 * This file handles the initial setup for the Projo application
 */

session_start();
$errors = [];
$success = false;
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Function to test database connection
function testDbConnection($host, $port, $user, $pass)
{
    try {
        $dsn = "mysql:host=$host;port=$port";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return [true, $pdo];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
}

// Function to create database and tables
function setupDatabase($pdo, $dbName)
{
    try {
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
        $pdo->exec("USE `$dbName`");

        // Import SQL from file
        $sql = file_get_contents(__DIR__ . '/projo.sql');
        $pdo->exec($sql);

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
}

// Function to create env file
function createEnvFile($host, $port, $dbName, $user, $pass)
{
    $envContent = "DB_HOST=$host\n" .
        "DB_PORT=$port\n" .
        "DB_NAME=$dbName\n" .
        "DB_USER=$user\n" .
        "DB_PASS=$pass\n" .
        "APP_DEBUG=false\n";

    return file_put_contents(__DIR__ . '/.env', $envContent) !== false;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Step 1: Setup database connection
        $host = $_POST['db_host'] ?? 'localhost';
        $port = $_POST['db_port'] ?? 3306;
        $dbName = $_POST['db_name'] ?? 'projo';
        $user = $_POST['db_user'] ?? 'root';
        $pass = $_POST['db_pass'] ?? '';

        list($connected, $result) = testDbConnection($host, $port, $user, $pass);

        if ($connected) {
            // Connection successful, create environment file
            if (createEnvFile($host, $port, $dbName, $user, $pass)) {
                list($dbCreated, $dbError) = setupDatabase($result, $dbName);
                if ($dbCreated) {
                    $step = 2;
                    $success = true;
                } else {
                    $errors[] = "Database setup failed: $dbError";
                }
            } else {
                $errors[] = "Failed to create configuration file. Check file permissions.";
            }
        } else {
            $errors[] = "Database connection failed: $result";
        }
    } else if ($step === 2) {
        // Step 2: Create admin user
        $adminUser = $_POST['admin_user'] ?? '';
        $adminPass = $_POST['admin_pass'] ?? '';
        $adminPassConfirm = $_POST['admin_pass_confirm'] ?? '';

        if (empty($adminUser) || empty($adminPass)) {
            $errors[] = "Admin username and password are required.";
        } else if ($adminPass !== $adminPassConfirm) {
            $errors[] = "Passwords do not match.";
        } else {
            // Everything looks good, create admin user
            try {
                // Load connection from .env
                $env = parse_ini_file(__DIR__ . '/.env');
                $host = $env['DB_HOST'];
                $port = $env['DB_PORT'];
                $dbName = $env['DB_NAME'];
                $user = $env['DB_USER'];
                $pass = $env['DB_PASS'];

                // Connect to DB
                $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Hash the password
                $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);

                // Check if users table exists
                $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
                if (count($tables) === 0) {
                    $errors[] = "Database setup not complete. Please go back to step 1.";
                } else {
                    // Delete any default users
                    $pdo->exec("DELETE FROM users WHERE username = 'admin'");

                    // Insert the new admin user
                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                    $stmt->execute([$adminUser, $hashedPassword]);

                    $step = 3;
                    $success = true;
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// HTML for the setup wizard
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projo - Setup Wizard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .setup-container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .setup-steps {
            display: flex;
            margin-bottom: 20px;
        }

        .setup-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-bottom: 3px solid #ddd;
        }

        .setup-step.active {
            border-color: #1e88e5;
            font-weight: bold;
        }

        .setup-step.completed {
            border-color: #4caf50;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .error-message {
            color: #f44336;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .success-message {
            color: #4caf50;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="setup-container">
        <h1>Projo Setup Wizard</h1>

        <div class="setup-steps">
            <div class="setup-step <?php echo $step === 1 ? 'active' : ($step > 1 ? 'completed' : ''); ?>">
                1. Database
            </div>
            <div class="setup-step <?php echo $step === 2 ? 'active' : ($step > 2 ? 'completed' : ''); ?>">
                2. Admin User
            </div>
            <div class="setup-step <?php echo $step === 3 ? 'active' : ''; ?>">
                3. Complete
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <?php if ($step === 2): ?>
                    Database configured successfully! Now let's create your admin user.
                <?php elseif ($step === 3): ?>
                    Setup completed successfully! You can now log in to Projo.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <!-- Database Configuration -->
            <form method="post">
                <h2>Database Configuration</h2>

                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>

                <div class="form-group">
                    <label for="db_port">Database Port</label>
                    <input type="number" id="db_port" name="db_port" value="3306" required>
                </div>

                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="projo" required>
                </div>

                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>

                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass">
                    <small>* Leave empty if no password is required</small>
                </div>

                <button type="submit" class="btn">Configure Database</button>
            </form>
        <?php elseif ($step === 2): ?>
            <!-- Admin User Creation -->
            <form method="post">
                <h2>Create Admin User</h2>

                <div class="form-group">
                    <label for="admin_user">Admin Username</label>
                    <input type="text" id="admin_user" name="admin_user" value="admin" required>
                </div>

                <div class="form-group">
                    <label for="admin_pass">Admin Password</label>
                    <input type="password" id="admin_pass" name="admin_pass" required>
                </div>

                <div class="form-group">
                    <label for="admin_pass_confirm">Confirm Password</label>
                    <input type="password" id="admin_pass_confirm" name="admin_pass_confirm" required>
                </div>

                <button type="submit" class="btn">Create Admin User</button>
            </form>
        <?php elseif ($step === 3): ?>
            <!-- Setup Complete -->
            <div>
                <h2>Setup Complete!</h2>
                <p>Congratulations! Projo has been successfully installed.</p>
                <p>You can now log in using the admin credentials you just created.</p>
                <p><a href="login.php" class="btn">Go to Login</a></p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form validation could be added here
    </script>
</body>

</html>