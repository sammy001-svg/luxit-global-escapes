<?php
/**
 * Footer newsletter sign-up. Stores the address in newsletter_subscribers;
 * the admin can download the list as CSV (admin/api/export-subscribers.php)
 * and import it into a mailing service.
 */
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/includes/db.php';

function newsFail(string $error, int $status = 422): void {
    http_response_code($status);
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') newsFail('Invalid request method', 405);

$ok = ['success' => true, 'message' => 'Thank you for subscribing. Look out for our next email.'];

// Honeypot: bots get a success response and nothing is stored.
if (trim($_POST['website'] ?? '') !== '') { echo json_encode($ok); exit; }

// At most 5 sign-ups per visitor session in 10 minutes.
session_start();
$now = time();
$_SESSION['news_times'] = array_values(array_filter($_SESSION['news_times'] ?? [], fn($t) => $t > $now - 600));
if (count($_SESSION['news_times']) >= 5) newsFail('Please wait a few minutes before trying again.', 429);

$email = mb_strtolower(trim($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    newsFail('Please enter a valid email address.');
}
$source = preg_replace('/[^a-z0-9._-]/i', '', (string)($_POST['source'] ?? '')) ?: null;

try {
    $st = $pdo->prepare("INSERT IGNORE INTO newsletter_subscribers (email, source) VALUES (?, ?)");
    $st->execute([$email, $source ? mb_substr($source, 0, 100) : null]);
} catch (PDOException $e) {
    error_log('newsletter-handler: ' . $e->getMessage());
    newsFail($e->getCode() === '42S02'
        ? 'Sign-ups are temporarily unavailable. Please try again later.'
        : 'We could not save your sign-up. Please try again.', 500);
}

$_SESSION['news_times'][] = $now;
// Already subscribed: same friendly reply, so the form does not reveal who is on the list.
echo json_encode($ok);
