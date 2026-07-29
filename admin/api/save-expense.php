<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO expenses (category, description, amount, expense_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category, $description, $amount, $expense_date]);
        logActivity($pdo, 'recorded an expense', $description . ' ($' . number_format((float)$amount) . ')');
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
