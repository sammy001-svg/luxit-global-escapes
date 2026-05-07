<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $parent_id = $_POST['parent_id'] ?? null;
        $region = $_POST['region'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Destination name is required']);
            exit;
        }

        // Handle empty parent_id as null
        if (empty($parent_id)) {
            $parent_id = null;
        }

        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE destinations SET name = ?, parent_id = ?, region = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $parent_id, $region, $description, $id]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO destinations (name, parent_id, region, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $parent_id, $region, $description]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
