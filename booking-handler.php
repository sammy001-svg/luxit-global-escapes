<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$full_name   = trim($_POST['full_name']   ?? '');
$email       = trim($_POST['email']        ?? '');
$phone       = trim($_POST['phone']        ?? '');
$tour_name   = trim($_POST['tour_name']    ?? '');
$travel_date = trim($_POST['travel_date']  ?? '');
$travelers   = intval($_POST['travelers']  ?? 1);
$message     = trim($_POST['message']      ?? '');
$type        = trim($_POST['type']         ?? 'book'); // 'book' or 'enquire'

// Basic validation
if (empty($full_name) || empty($email) || empty($tour_name)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

// Build a meaningful tour label
$tour_label = ($type === 'enquire' ? '[Enquiry] ' : '[Booking] ') . $tour_name;
if ($phone)     $tour_label .= " | Phone: $phone";
if ($travelers) $tour_label .= " | Travelers: $travelers";
if ($message)   $tour_label .= " | Note: " . substr($message, 0, 120);

// Use today if no travel date provided
$booking_date = !empty($travel_date) ? $travel_date : date('Y-m-d');

// Ensure unique booking ID
do {
    $booking_id = 'BK-' . rand(1000, 9999);
    $check = $pdo->prepare("SELECT id FROM bookings WHERE id = ?");
    $check->execute([$booking_id]);
} while ($check->fetch());

try {
    $stmt = $pdo->prepare(
        "INSERT INTO bookings (id, user_name, email, tour_name, booking_date, amount, status)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$booking_id, $full_name, $email, $tour_label, $booking_date, 0.00, 'Pending']);

    echo json_encode([
        'success'    => true,
        'booking_id' => $booking_id,
        'type'       => $type,
        'message'    => $type === 'enquire'
            ? 'Your enquiry has been received! Our team will contact you within 24 hours.'
            : "Your booking request has been submitted! Reference: $booking_id. We will confirm your reservation shortly.",
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'A server error occurred. Please try again.']);
}
