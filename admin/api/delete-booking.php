<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$id = trim($_POST['id'] ?? '');

if (empty($id)) {
    echo json_encode(['success' => false, 'error' => 'Missing booking ID']);
    exit;
}

try {
    $who = $pdo->prepare("SELECT user_name FROM bookings WHERE id = ?");
    $who->execute([$id]);
    $name = (string)($who->fetchColumn() ?: $id);

    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$id]);

    logActivity($pdo, 'deleted the booking', $id . ' (' . $name . ')');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
