<?php
// Quick status change from the Packages list (Live / Draft / Archived).
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminJsonError('Invalid request method', 405);

$id     = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$labels = ['Active' => 'published', 'Draft' => 'moved to draft', 'Inactive' => 'archived'];
if (!$id || !isset($labels[$status])) adminJsonError('Invalid parameters');

try {
    $st = $pdo->prepare("SELECT title FROM tours WHERE id = ?");
    $st->execute([$id]);
    $title = $st->fetchColumn();
    if ($title === false) adminJsonError('Package not found.', 404);

    $pdo->prepare("UPDATE tours SET status = ? WHERE id = ?")->execute([$status, $id]);
    logActivity($pdo, $labels[$status] . ' the package', $title);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    adminJsonDbError($e, 'update-tour-status');
}
