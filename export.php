<?php
error_reporting(0); // Disable error reporting

// Check if auth.php exists before including
if (file_exists(__DIR__ . '/includes/auth.php')) {
    include __DIR__ . '/includes/auth.php';
}

include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/functions.php';

$type = $_GET['type'] ?? null;
$format = $_GET['format'] ?? 'csv';

if (!$type || !in_array($type, ['tasks', 'projects', 'notes', 'issues'])) {
    die('Invalid type specified.');
}

if (!in_array($format, ['csv', 'json'])) {
    die('Invalid format specified.');
}

// Fetch data based on type
$data = [];
switch ($type) {
    case 'tasks':
        $data = getAllTasks($pdo);
        break;
    case 'projects':
        $data = getAllProjects($pdo);
        break;
    case 'notes':
        $data = getAllNotes($pdo);
        break;
    case 'issues':
        $data = getAllIssues($pdo);
        break;
}

// Export data
if ($format === 'csv') {
    exportCSV($data, $type);
} elseif ($format === 'json') {
    exportJSON($data, $type);
}

// Export as CSV
function exportCSV($data, $type)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $type . '.csv"');

    $output = fopen('php://output', 'w');

    if (!empty($data)) {
        // Write header row
        fputcsv($output, array_keys($data[0]));

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit();
}

// Export as JSON
function exportJSON($data, $type)
{
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $type . '.json"');

    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}
