<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

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

        logActivity($pdo, $id ? 'updated the destination' : 'added the destination', $name);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        adminJsonDbError($e, 'save-destination');
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
