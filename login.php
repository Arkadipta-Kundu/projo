<?php
session_start();
include __DIR__ . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['user_id'] = $user['id']; // Add this line to set the user ID in the session
        $_SESSION['role'] = $user['role']; // If roles are used

        if (!empty($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(16));
            setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            $stmt = $pdo->prepare("UPDATE users SET remember_token = :token WHERE username = :username");
            $stmt->execute([':token' => $token, ':username' => $user['username']]);
        }

        header('Location: pages/dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login — Projo</title>
    <link rel="icon" type="image/x-icon" href="assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex flex-col font-sans relative">
    <!-- Decorative background shapes -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-100 rounded-full opacity-40 blur-2xl z-0"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-200 rounded-full opacity-30 blur-2xl z-0"></div>

    <main class="flex-1 flex items-center justify-center relative z-10">
        <div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-xl p-8 animate-fade-in-slow">
            <div class="flex flex-col items-center mb-6">
                <img src="assets/images/logo.png" alt="Projo Logo" class="h-12 mb-2 animate-slide-down drop-shadow-lg">
                <h2 class="text-2xl font-bold mb-2 text-blue-700">Sign In to Projo</h2>
                <p class="text-gray-500 mb-4 text-center">Welcome back! Manage your projects with ease.</p>
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block font-bold">Username</label>
                    <input type="text" id="username" name="username" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div>
                    <label for="password" class="block font-bold">Password</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="remember_me" name="remember_me" class="mr-2">
                    <label for="remember_me" class="text-sm">Remember me</label>
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-3 rounded-lg font-bold shadow hover:scale-105 hover:from-blue-700 hover:to-blue-600 transition w-full">Login</button>
                <p class="text-center mt-4">
                    Don't have an account? <a href="register.php" class="text-blue-500 hover:underline">Register here</a>.
                </p>
            </form>
        </div>
    </main>
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
    </style>
    <style>
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
</body>

</html>