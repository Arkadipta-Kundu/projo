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
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
</head>

<body class="bg-gray-100 text-gray-800">
    <header class="bg-white text-gray-800 py-4">
        <div class="container mx-auto flex justify-center">
            <a href="/projo/index.php">
                <img src="/projo/assets/images/logo.png" alt="Projo Logo" class="h-12">
            </a>
        </div>
    </header>
    <main class="container mx-auto py-8">
        <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
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
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Login</button>
                <p class="text-center mt-4">
                    Don't have an account? <a href="/projo/register.php" class="text-blue-500 hover:underline">Register here</a>.
                </p>
            </form>
        </div>
    </main>
</body>

</html>