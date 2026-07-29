<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';

// POST-only: a destructive action must never be reachable from a plain URL,
// which an <img src> on any page could trigger against a logged-in admin.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    adminJsonError('Invalid request method', 405);
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    adminJsonError('Tour ID is required');
}

try {
    $stmt = $pdo->prepare("DELETE FROM tours WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    adminJsonDbError($e, 'delete-tour');
}
