<?php
session_start();
include __DIR__ . '/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->execute([':username' => $username, ':password' => $hashed_password]);
            $success = 'User created successfully. You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">>
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
            <h2 class="text-2xl font-bold mb-4 text-center">Register</h2>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
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
                <div>
                    <label for="confirm_password" class="block font-bold">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Register</button>
                <p class="text-center mt-4">
                    Already have an account? <a href="/projo/login.php" class="text-blue-500 hover:underline">Login here</a>.
                </p>
            </form>
        </div>
    </main>
</body>

</html>