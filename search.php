<?php
include __DIR__ . '/includes/auth.php';
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/functions.php';

$user_id = $_SESSION['user_id'] ?? null;
$q = trim($_GET['q'] ?? '');

$results = [
    'projects' => [],
    'tasks' => [],
    'notes' => [],
    'issues' => [],
];

if ($q !== '') {
    // Search Projects
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = :uid AND (title LIKE :q OR description LIKE :q) ORDER BY deadline DESC LIMIT 10");
    $stmt->execute([':uid' => $user_id, ':q' => "%$q%"]);
    $results['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search Tasks
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :uid AND (title LIKE :q OR description LIKE :q) ORDER BY due_date DESC LIMIT 10");
    $stmt->execute([':uid' => $user_id, ':q' => "%$q%"]);
    $results['tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search Notes
    $stmt = $pdo->prepare("SELECT notes.*, projects.title AS project_title FROM notes LEFT JOIN projects ON notes.project_id = projects.id WHERE notes.user_id = :uid AND notes.content LIKE :q ORDER BY notes.id DESC LIMIT 10");
    $stmt->execute([':uid' => $user_id, ':q' => "%$q%"]);
    $results['notes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search Issues
    $stmt = $pdo->prepare("SELECT issues.*, projects.title AS project_title FROM issues LEFT JOIN projects ON issues.project_id = projects.id WHERE issues.user_id = :uid AND (issues.title LIKE :q OR issues.description LIKE :q) ORDER BY issues.id DESC LIMIT 10");
    $stmt->execute([':uid' => $user_id, ':q' => "%$q%"]);
    $results['issues'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Search Results — Projo</title>
     <link rel="icon" type="image/x-icon" href="../assets/images/icon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/images/icon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen text-gray-800">
    <?php include __DIR__ . '/components/header.php'; ?>
    <main class="container mx-auto py-8">
        <h2 class="text-3xl font-bold mb-6">Search Results for "<span class="text-blue-600"><?= htmlspecialchars($q) ?></span>"</h2>
        <form method="GET" action="search.php" class="mb-8 flex gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search..." class="px-4 py-2 rounded bg-gray-100 border border-gray-300 w-full max-w-md">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
        </form>
        <?php if ($q === ''): ?>
            <div class="bg-white p-6 rounded shadow text-gray-500">Enter a search term above to find projects, tasks, notes, or issues.</div>
        <?php else: ?>
            <?php
            $hasResults = count($results['projects']) || count($results['tasks']) || count($results['notes']) || count($results['issues']);
            ?>
            <?php if (!$hasResults): ?>
                <div class="bg-white p-6 rounded shadow text-gray-400">No results found.</div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Projects -->
                    <?php if (count($results['projects'])): ?>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-blue-700">Projects</h3>
                            <ul class="space-y-2">
                                <?php foreach ($results['projects'] as $p): ?>
                                    <li class="bg-white p-4 rounded shadow hover:bg-blue-50 transition">
                                        <a href="pages/projects.php#project-<?= $p['id'] ?>" class="font-semibold text-blue-600 hover:underline"><?= htmlspecialchars($p['title']) ?></a>
                                        <div class="text-gray-500 text-sm"><?= htmlspecialchars($p['description']) ?></div>
                                        <div class="text-xs text-gray-400">Deadline: <?= htmlspecialchars($p['deadline']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- Tasks -->
                    <?php if (count($results['tasks'])): ?>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-green-700">Tasks</h3>
                            <ul class="space-y-2">
                                <?php foreach ($results['tasks'] as $t): ?>
                                    <li class="bg-white p-4 rounded shadow hover:bg-green-50 transition">
                                        <a href="pages/tasks.php#task-<?= $t['id'] ?>" class="font-semibold text-green-600 hover:underline"><?= htmlspecialchars($t['title']) ?></a>
                                        <div class="text-gray-500 text-sm"><?= htmlspecialchars($t['description']) ?></div>
                                        <div class="text-xs text-gray-400">Due: <?= htmlspecialchars($t['due_date']) ?> | Status: <?= htmlspecialchars($t['status']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- Notes -->
                    <?php if (count($results['notes'])): ?>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-purple-700">Notes</h3>
                            <ul class="space-y-2">
                                <?php foreach ($results['notes'] as $n): ?>
                                    <li class="bg-white p-4 rounded shadow hover:bg-purple-50 transition">
                                        <div class="font-semibold text-purple-600"><?= htmlspecialchars(strip_tags(substr($n['content'], 0, 60))) ?><?= strlen($n['content']) > 60 ? '…' : '' ?></div>
                                        <div class="text-xs text-gray-400"><?= $n['project_title'] ? 'Project: ' . htmlspecialchars($n['project_title']) : 'Unassigned' ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- Issues -->
                    <?php if (count($results['issues'])): ?>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-red-700">Issues</h3>
                            <ul class="space-y-2">
                                <?php foreach ($results['issues'] as $i): ?>
                                    <li class="bg-white p-4 rounded shadow hover:bg-red-50 transition">
                                        <a href="pages/issues.php#issue-<?= $i['id'] ?>" class="font-semibold text-red-600 hover:underline"><?= htmlspecialchars($i['title']) ?></a>
                                        <div class="text-gray-500 text-sm"><?= htmlspecialchars($i['description']) ?></div>
                                        <div class="text-xs text-gray-400"><?= $i['project_title'] ? 'Project: ' . htmlspecialchars($i['project_title']) : 'Unassigned' ?> | Severity: <?= htmlspecialchars($i['severity']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>

</html>