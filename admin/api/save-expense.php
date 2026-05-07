<?php
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO expenses (category, description, amount, expense_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category, $description, $amount, $expense_date]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
