<?php
include __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/functions.php';

$q = $_GET['q'] ?? '';
$results = [];

if ($q) {
    // Search in projects
    $stmt = $pdo->prepare("SELECT id, title, 'Project' AS type FROM projects WHERE title LIKE :q OR description LIKE :q");
    $stmt->execute([':q' => "%$q%"]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in tasks
    $stmt = $pdo->prepare("SELECT id, title, 'Task' AS type FROM tasks WHERE title LIKE :q OR description LIKE :q");
    $stmt->execute([':q' => "%$q%"]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in notes
    $stmt = $pdo->prepare("SELECT id, content AS title, 'Note' AS type FROM notes WHERE content LIKE :q");
    $stmt->execute([':q' => "%$q%"]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

    // Search in issues
    $stmt = $pdo->prepare("SELECT id, title, 'Issue' AS type FROM issues WHERE title LIKE :q OR description LIKE :q");
    $stmt->execute([':q' => "%$q%"]);
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projo</title>
    <link rel="icon" type="image/x-icon" href="/projo/assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/projo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include the script in the head or before the closing body tag -->
    <script src="/projo/assets/js/script.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-4">Search Results for "<?= htmlspecialchars($q) ?>"</h2>
        <?php if (empty($results)): ?>
            <p class="text-gray-600">No results found.</p>
        <?php else: ?>
            <ul class="space-y-4">
                <?php foreach ($results as $result): ?>
                    <li class="bg-white p-4 rounded shadow">
                        <p class="text-lg font-bold"><?= htmlspecialchars($result['title']) ?></p>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($result['type']) ?></p>
                        <a href="/projo/pages/<?= strtolower($result['type']) ?>s.php?id=<?= $result['id'] ?>" class="text-blue-500 hover:underline">View Details</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</body>

</html>