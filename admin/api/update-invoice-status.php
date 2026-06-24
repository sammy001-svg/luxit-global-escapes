<?php
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$id     = trim($_POST['id']     ?? '');
$status = trim($_POST['status'] ?? '');
$allowed = ['Paid', 'Unpaid', 'Partial', 'Overdue'];

if (empty($id) || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
