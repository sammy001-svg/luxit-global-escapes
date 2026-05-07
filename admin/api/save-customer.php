<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $country = $_POST['country'] ?? '';

        if (empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'error' => 'Name and Email are required']);
            exit;
        }

        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, country = ? WHERE id = ?");
            $stmt->execute([$name, $email, $country, $id]);
        } else {
            // Insert - Generate ID like CU-001
            $stmt = $pdo->query("SELECT id FROM customers ORDER BY created_at DESC LIMIT 1");
            $last = $stmt->fetch();
            $nextNum = 1;
            if ($last) {
                $num = (int)substr($last['id'], 3);
                $nextNum = $num + 1;
            }
            $newId = 'CU-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            
            $stmt = $pdo->prepare("INSERT INTO customers (id, name, email, country, joined_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$newId, $name, $email, $country, date('Y-m-d')]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
