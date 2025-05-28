<?php
session_start();
include __DIR__ . '/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (:name, :username, :password, 'user')");
            $stmt->execute([
                ':name' => $name,
                ':username' => $username,
                ':password' => $hashed
            ]);
            $success = 'Registration successful! You can now <a href="login.php" class="text-blue-500 underline">login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register — Projo</title>
    <link rel="icon" type="image/x-icon" href="assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .animate-fade-in-slow {
            animation: fadeIn 2s ease;
        }

        .animate-slide-down {
            animation: slideDown 1.2s cubic-bezier(.23, 1.01, .32, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {

            .max-w-md,
            .max-w-lg,
            .max-w-xl {
                max-width: 100% !important;
            }

            .rounded-xl,
            .rounded-3xl {
                border-radius: 0.75rem !important;
            }

            .shadow-xl,
            .shadow-2xl {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            }

            .p-8,
            .p-6,
            .p-4 {
                padding: 1rem !important;
            }

            .mb-6,
            .mb-8,
            .mb-10 {
                margin-bottom: 1rem !important;
            }

            .mt-6,
            .mt-8,
            .mt-10 {
                margin-top: 1rem !important;
            }

            .text-2xl,
            .text-3xl {
                font-size: 1.3rem !important;
            }

            .text-xl {
                font-size: 1.1rem !important;
            }

            .w-full {
                width: 100% !important;
            }

            .flex.space-x-4 {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }

            .absolute.-top-24,
            .absolute.-bottom-24 {
                display: none !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex flex-col font-sans relative">
    <!-- Decorative background shapes -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-100 rounded-full opacity-40 blur-2xl z-0"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-2xl z-0"></div>

    <main class="flex-1 flex items-center justify-center relative z-10">
        <div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-xl p-8 animate-fade-in-slow">
            <div class="flex flex-col items-center mb-6">
                <img src="assets/images/logo.png" alt="Projo Logo" class="h-12 mb-2 animate-slide-down drop-shadow-lg">
                <h2 class="text-2xl font-bold mb-2 text-blue-700">Create Your Projo Account</h2>
                <p class="text-gray-500 mb-4 text-center">You took Right decision 👍</p>
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($success): ?>
                <p class="text-green-600 mb-4"><?= $success ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block font-bold">Name</label>
                    <input type="text" id="name" name="name" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="username" class="block font-bold">Username</label>
                    <input type="text" id="username" name="username" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="password" class="block font-bold">Password</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="confirm_password" class="block font-bold">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-3 rounded-lg font-bold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition w-full">Register</button>
                <p class="text-center mt-4">
                    Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login here</a>.
                </p>
            </form>
        </div>
    </main>
</body>

</html>