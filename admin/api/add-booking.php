<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_name = $_POST['user_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $tour_name = $_POST['tour_name'] ?? '';
        $booking_date = $_POST['booking_date'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $status = $_POST['status'] ?? 'Pending';

        if (empty($user_name) || empty($email) || empty($tour_name)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Generate a booking ID
        $booking_id = 'BK-' . rand(1000, 9999);

        $stmt = $pdo->prepare("INSERT INTO bookings (id, user_name, email, tour_name, booking_date, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$booking_id, $user_name, $email, $tour_name, $booking_date, $amount, $status]);

        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id
        ]);
    } catch (PDOException $e) {
        adminJsonDbError($e, 'add-booking');
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
