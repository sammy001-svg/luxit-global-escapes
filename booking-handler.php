<?php
/**
 * Public booking / enquiry endpoint (tour-detail.php and the homepage
 * quick-booking modal post here).
 *
 * Everything that matters is decided server-side: the package is looked up by
 * id, and the amount is the package's own price × travellers, so a visitor
 * cannot submit their own price. New requests land in the admin panel's
 * Bookings tab as Pending for the team to confirm (see includes/requests.php).
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/requests.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    requestFail('Invalid request method', 405);
}

requestGuard();

$type      = ($_POST['type'] ?? 'book') === 'enquire' ? 'Enquiry' : 'Booking';
[$fullName, $email, $phone] = requestValidateContact($type === 'Booking');
$tourId    = (int)($_POST['tour_id'] ?? 0);
$date      = trim($_POST['travel_date'] ?? '');
$travelers = (int)($_POST['travelers'] ?? 1);
$message   = trim($_POST['message'] ?? '');

if ($travelers < 1 || $travelers > 50) {
    requestFail('Number of travellers must be between 1 and 50. For larger groups, please send an enquiry.');
}
if (mb_strlen($message) > 2000) requestFail('Please keep your message under 2,000 characters.');

// Package: must exist and be live.
$tour = null;
if ($tourId > 0) {
    $st = $pdo->prepare("SELECT id, title, price FROM tours WHERE id = ? AND status = 'Active'");
    $st->execute([$tourId]);
    $tour = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}
if (!$tour) {
    requestFail('This package is no longer available. Please choose another or contact us.');
}

// Travel date: required for a booking, optional for an enquiry; never in the past.
$travelDate = null;
if ($date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) requestFail('Please choose a valid travel date.');
    if ($date <= date('Y-m-d')) requestFail('Please choose a travel date from tomorrow onwards.');
    if ($date > date('Y-m-d', strtotime('+3 years'))) requestFail('Please choose a travel date within the next three years.');
    $travelDate = $date;
} elseif ($type === 'Booking') {
    requestFail('Please choose your travel date.');
}

$amount = round((float)$tour['price'] * $travelers, 2);

$bookingId = requestRecord($pdo, [
    'type'         => $type,
    'tour_id'      => (int)$tour['id'],
    'tour_name'    => $tour['title'],
    'name'         => $fullName,
    'email'        => $email,
    'phone'        => $phone,
    // bookings.booking_date is NOT NULL; an enquiry without a date records today.
    'booking_date' => $travelDate ?? date('Y-m-d'),
    'travelers'    => $travelers,
    'amount'       => $amount,
    'message'      => $message,
    'activity'     => $type === 'Booking' ? 'requested a booking' : 'sent an enquiry',
]);

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
