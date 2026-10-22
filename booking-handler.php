<?php
/**
 * Public booking / enquiry endpoint (tour-detail.php and the homepage
 * quick-booking modal post here).
 *
 * Everything that matters is decided server-side: the package is looked up by
 * id, and the amount is the package's own price × travellers, so a visitor
 * cannot submit their own price. New requests land in the admin panel's
 * Bookings tab as Pending for the team to confirm.
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/includes/db.php';

function bookingFail(string $error, int $status = 422): void {
    http_response_code($status);
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    bookingFail('Invalid request method', 405);
}

// Spam guard 1: a hidden field real visitors never see or fill in.
if (trim($_POST['website'] ?? '') !== '') {
    // Pretend success so bots get no signal to adapt to.
    echo json_encode(['success' => true, 'booking_id' => 'BK-000000', 'type' => 'book',
                      'message' => 'Thank you. Your request has been received.']);
    exit;
}

// Spam guard 2: at most 5 requests per visitor session in 10 minutes.
session_start();
$now = time();
$_SESSION['booking_times'] = array_values(array_filter(
    $_SESSION['booking_times'] ?? [], fn($t) => $t > $now - 600
));
if (count($_SESSION['booking_times']) >= 5) {
    bookingFail('You have sent several requests in a short time. Please wait a few minutes, or call us on +254 737 800 900.', 429);
}

$type      = ($_POST['type'] ?? 'book') === 'enquire' ? 'Enquiry' : 'Booking';
$tourId    = (int)($_POST['tour_id'] ?? 0);
$fullName  = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$date      = trim($_POST['travel_date'] ?? '');
$travelers = (int)($_POST['travelers'] ?? 1);
$message   = trim($_POST['message'] ?? '');

if ($fullName === '' || $email === '') {
    bookingFail('Please enter your name and email address.');
}
if (mb_strlen($fullName) > 100) bookingFail('Please shorten your name (100 characters maximum).');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    bookingFail('Please enter a valid email address.');
}
if ($phone !== '' && !preg_match('/^[0-9+()\-\s]{7,25}$/', $phone)) {
    bookingFail('Please enter a valid phone number, for example +254 700 000 000.');
}
if ($type === 'Booking' && $phone === '') {
    bookingFail('Please add a phone number so our team can confirm your booking with you.');
}
if ($travelers < 1 || $travelers > 50) {
    bookingFail('Number of travellers must be between 1 and 50. For larger groups, please send an enquiry.');
}
if (mb_strlen($message) > 2000) bookingFail('Please keep your message under 2,000 characters.');

// Package: must exist and be live.
$tour = null;
if ($tourId > 0) {
    $st = $pdo->prepare("SELECT id, title, price FROM tours WHERE id = ? AND status = 'Active'");
    $st->execute([$tourId]);
    $tour = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}
if (!$tour) {
    bookingFail('This package is no longer available. Please choose another or contact us.');
}

// Travel date: required for a booking, optional for an enquiry; never in the past.
$travelDate = null;
if ($date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) bookingFail('Please choose a valid travel date.');
    if ($date <= date('Y-m-d')) bookingFail('Please choose a travel date from tomorrow onwards.');
    if ($date > date('Y-m-d', strtotime('+3 years'))) bookingFail('Please choose a travel date within the next three years.');
    $travelDate = $date;
} elseif ($type === 'Booking') {
    bookingFail('Please choose your travel date.');
}

// bookings.booking_date is NOT NULL; an enquiry without a date records today.
$bookingDate = $travelDate ?? date('Y-m-d');
$amount      = round((float)$tour['price'] * $travelers, 2);

try {
    $pdo->beginTransaction();

    // Unpredictable, unique reference.
    $check = $pdo->prepare("SELECT 1 FROM bookings WHERE id = ?");
    do {
        $bookingId = 'BK-' . random_int(100000, 999999);
        $check->execute([$bookingId]);
    } while ($check->fetchColumn());

    $pdo->prepare(
        "INSERT INTO bookings (id, type, tour_id, user_name, email, phone, tour_name, booking_date,
                               travelers, amount, message, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
    )->execute([$bookingId, $type, $tour['id'], $fullName, $email, $phone ?: null, $tour['title'],
                $bookingDate, $travelers, $amount, $message ?: null]);

    // Make sure the traveller appears in the admin's Customers list.
    $cust = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
    $cust->execute([$email]);
    if ($custId = $cust->fetchColumn()) {
        if ($type === 'Booking') {
            $pdo->prepare("UPDATE customers SET bookings_count = bookings_count + 1 WHERE id = ?")->execute([$custId]);
        }
    } else {
        $exists = $pdo->prepare("SELECT 1 FROM customers WHERE id = ?");
        do {
            $custId = 'CU-' . random_int(10000, 99999);
            $exists->execute([$custId]);
        } while ($exists->fetchColumn());
        $pdo->prepare(
            "INSERT INTO customers (id, name, email, bookings_count, joined_date) VALUES (?, ?, ?, ?, CURDATE())"
        )->execute([$custId, $fullName, $email, $type === 'Booking' ? 1 : 0]);
    }

    $pdo->prepare(
        "INSERT INTO activity_feed (user, action, target, activity_time) VALUES (?, ?, ?, 'Just now')"
    )->execute([$fullName, $type === 'Booking' ? 'requested a booking' : 'sent an enquiry', $tour['title']]);

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('booking-handler: ' . $e->getMessage());
    if (in_array($e->getCode(), ['42S22', '42S02'], true)) {
        bookingFail('Bookings are temporarily unavailable. Please call us on +254 737 800 900.', 503);
    }
    bookingFail('We could not save your request. Please try again, or call us on +254 737 800 900.', 500);
}

$_SESSION['booking_times'][] = $now;

echo json_encode([
    'success'    => true,
    'booking_id' => $bookingId,
    'type'       => $type === 'Enquiry' ? 'enquire' : 'book',
    'tour'       => $tour['title'],
    'travelers'  => $travelers,
    'date'       => $travelDate ? date('j F Y', strtotime($travelDate)) : null,
    'amount'     => $amount,
    'message'    => $type === 'Enquiry'
        ? 'Thank you. Your enquiry has been received and our team will reply within 24 hours.'
        : "Thank you. Your booking request $bookingId has been received. Our team will confirm availability within 24 hours.",
]);
