<?php
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 'INV-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $customer_id = $_POST['customer_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'] ?? 'Unpaid';

    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE invoices SET customer_id=?, amount=?, due_date=?, status=? WHERE id=?");
            $stmt->execute([$customer_id, $amount, $due_date, $status, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO invoices (id, customer_id, amount, due_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $customer_id, $amount, $due_date, $status]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
