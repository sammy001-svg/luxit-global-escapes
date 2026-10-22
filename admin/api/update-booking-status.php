<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$id     = trim($_POST['id']     ?? '');
$status = trim($_POST['status'] ?? '');

if (empty($id) || !in_array($status, ['Confirmed', 'Cancelled', 'Pending'], true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

// Optional when confirming: the final agreed amount (the public form only
// records an estimate) and an internal note for the team.
$amount = null;
if (isset($_POST['amount']) && $_POST['amount'] !== '') {
    if (!is_numeric($_POST['amount']) || (float)$_POST['amount'] < 0 || (float)$_POST['amount'] > 99999999) {
        echo json_encode(['success' => false, 'error' => 'Enter a valid amount.']);
        exit;
    }
    $amount = round((float)$_POST['amount'], 2);
}
$note = isset($_POST['admin_note']) ? mb_substr(trim($_POST['admin_note']), 0, 2000) : null;
// Optional: convert an enquiry into a booking (or back).
$type = $_POST['type'] ?? null;
if ($type !== null && !in_array($type, ['Booking', 'Enquiry'], true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid type']);
    exit;
}

try {
    $sets   = ['status = ?'];
    $params = [$status];
    if ($amount !== null) { $sets[] = 'amount = ?';     $params[] = $amount; }
    if ($note !== null)   { $sets[] = 'admin_note = ?'; $params[] = $note !== '' ? $note : null; }
    if ($type !== null)   { $sets[] = 'type = ?';       $params[] = $type; }
    $params[] = $id;

    $stmt = $pdo->prepare('UPDATE bookings SET ' . implode(', ', $sets) . ' WHERE id = ?');
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        $exists = $pdo->prepare('SELECT 1 FROM bookings WHERE id = ?');
        $exists->execute([$id]);
        if (!$exists->fetchColumn()) {
            echo json_encode(['success' => false, 'error' => 'Booking not found.']);
            exit;
        }
    }

    logActivity($pdo, 'marked booking ' . strtolower($status), $id);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('update-booking-status: ' . $e->getMessage());
    // admin_note arrives with migrate.php; say so instead of a bare failure.
    if ($e->getCode() === '42S22') {
        echo json_encode(['success' => false, 'error' => 'Your database schema is out of date. Run migrate.php in the site root, then try again.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
}
