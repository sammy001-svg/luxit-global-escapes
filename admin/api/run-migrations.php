<?php
// "Update database" button in the admin panel: runs migrate.php (safe to run
// repeatedly; it only adds missing tables and columns, never drops data) and
// reports what is still missing afterwards.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/schema.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminJsonError('Invalid request method', 405);

ob_start();
try {
    require __DIR__ . '/../../migrate.php';
} catch (Throwable $e) {
    error_log('run-migrations: ' . $e->getMessage());
    echo "\nError: " . $e->getMessage() . "\n";
}
$log = ob_get_clean();

// migrate.php sends text/plain when run from a browser; this endpoint is JSON.
header('Content-Type: application/json', true);

$missing = schemaMissing($pdo);
if (!$missing) logActivity($pdo, 'updated the database schema', 'Admin panel');

echo json_encode([
    'success' => !$missing,
    'missing' => $missing,
    'log'     => $log,
    'error'   => $missing ? 'Some updates could not be applied: ' . implode(', ', $missing) . '. Your database user may not have permission to change tables; ask your host, or run migrate.php from your hosting control panel.' : null,
]);
