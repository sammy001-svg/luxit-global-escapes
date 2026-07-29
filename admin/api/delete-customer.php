<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    adminJsonError('Invalid request method', 405);
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    adminJsonError('Customer ID is required');
}

try {
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    adminJsonDbError($e, 'delete-customer');
}
