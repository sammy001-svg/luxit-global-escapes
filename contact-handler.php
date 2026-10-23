<?php
/**
 * Contact page form. Messages are stored as Enquiries in the bookings table,
 * so they appear in the admin panel's Bookings tab beside package enquiries,
 * with the same reply-by-email / WhatsApp tools (see includes/requests.php).
 *
 * Replaces the template's assets/script/contact.php, which never existed:
 * until now every message sent from the contact page was lost.
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/requests.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    requestFail('Invalid request method', 405);
}

requestGuard();

const CONTACT_TOPICS  = ['Plan a trip', 'Existing booking', 'Group or corporate travel', 'Partnerships', 'Something else'];
const CONTACT_METHODS = ['Email', 'Phone call', 'WhatsApp'];

[$name, $email, $phone] = requestValidateContact(false);
$topic       = in_array($_POST['topic'] ?? '', CONTACT_TOPICS, true) ? $_POST['topic'] : 'Something else';
$method      = in_array($_POST['contact_method'] ?? '', CONTACT_METHODS, true) ? $_POST['contact_method'] : 'Email';
$destination = trim($_POST['destination'] ?? '');
$month       = trim($_POST['travel_month'] ?? '');
$travelers   = (int)($_POST['travelers'] ?? 0);
$message     = trim($_POST['message'] ?? '');

if ($message === '') requestFail('Please tell us a little about what you need.');
if (mb_strlen($message) > 3000) requestFail('Please keep your message under 3,000 characters.');
if (mb_strlen($destination) > 100) requestFail('Please shorten the destination (100 characters maximum).');
if ($method !== 'Email' && $phone === '') {
    requestFail('Please add your phone number so we can ' . ($method === 'WhatsApp' ? 'message you on WhatsApp.' : 'call you.'));
}
if ($travelers < 0 || $travelers > 500) requestFail('Please enter a realistic number of travellers.');

// Travel month (YYYY-MM) is optional; record it as the first of that month so
// the admin can sort by it, otherwise use today (booking_date is NOT NULL).
$bookingDate = date('Y-m-d');
$monthLabel  = '';
if ($month !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $month . '-01');
    if (!$d || $d->format('Y-m') !== $month || $month < date('Y-m')) {
        requestFail('Please choose a travel month from this month onwards.');
    }
    $bookingDate = $d->format('Y-m-d');
    $monthLabel  = $d->format('F Y');
}

// Everything the team needs in one place, readable in the admin's message box.
$details = array_filter([
    'Topic'             => $topic,
    'Destination'       => $destination,
    'Travel month'      => $monthLabel,
    'Travellers'        => $travelers ?: '',
    'Prefers to be contacted by' => $method,
], fn($v) => $v !== '');
$body = implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys($details), $details)) . "\n\n" . $message;

$ref = requestRecord($pdo, [
    'type'         => 'Enquiry',
    'tour_id'      => null,
    'tour_name'    => 'Contact form · ' . $topic . ($destination !== '' ? ' · ' . $destination : ''),
    'name'         => $name,
    'email'        => $email,
    'phone'        => $phone,
    'booking_date' => $bookingDate,
    'travelers'    => max(1, $travelers),
    'amount'       => 0,
    'message'      => $body,
    'activity'     => 'sent a message',
]);

echo json_encode([
    'success'    => true,
    'booking_id' => $ref,
    'method'     => $method,
    'message'    => 'Thank you, ' . strtok($name, ' ') . '. Your message has reached our travel team and we will reply'
                  . ($method === 'Email' ? ' by email' : ($method === 'WhatsApp' ? ' on WhatsApp' : ' by phone'))
                  . ' within 24 hours.',
]);
