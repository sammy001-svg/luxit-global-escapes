<?php
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 'QT-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $customer_id = $_POST['customer_id'];
    $tour_name = $_POST['tour_name'];
    $amount = $_POST['amount'];
    $status = $_POST['status'] ?? 'Draft';
    $valid_until = date('Y-m-d', strtotime('+30 days'));

    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE quotations SET customer_id=?, tour_name=?, amount=?, status=? WHERE id=?");
            $stmt->execute([$customer_id, $tour_name, $amount, $status, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO quotations (id, customer_id, tour_name, amount, status, valid_until) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $customer_id, $tour_name, $amount, $status, $valid_until]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
