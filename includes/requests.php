<?php
/**
 * Guest requests from the public site: package bookings and enquiries
 * (booking-handler.php) and contact-form messages (contact-handler.php).
 *
 * Both land in the bookings table as Pending, so the admin panel's Bookings
 * tab is the single inbox the team works from.
 */

if (!function_exists('requestFail')) {
    function requestFail(string $error, int $status = 422): void {
        http_response_code($status);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }
}

if (!function_exists('requestGuard')) {
    /**
     * Shared spam protection. Call before validating anything.
     *  - honeypot: a hidden "website" field people never fill in; bots get a
     *    fake success so they have no signal to adapt to;
     *  - at most 5 requests per visitor session in 10 minutes.
     */
    function requestGuard(): void {
        if (trim($_POST['website'] ?? '') !== '') {
            echo json_encode(['success' => true, 'booking_id' => 'BK-000000',
                              'message' => 'Thank you. Your message has been received.']);
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
        $now = time();
        $_SESSION['booking_times'] = array_values(array_filter(
            $_SESSION['booking_times'] ?? [], fn($t) => $t > $now - 600
        ));
        if (count($_SESSION['booking_times']) >= 5) {
            requestFail('You have sent several requests in a short time. Please wait a few minutes, or call us on +254 737 800 900.', 429);
        }
    }
}

if (!function_exists('requestValidateContact')) {
    /** Validate name / email / phone; returns them trimmed. */
    function requestValidateContact(bool $phoneRequired): array {
        $name  = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($name === '' || $email === '') requestFail('Please enter your name and email address.');
        if (mb_strlen($name) > 100) requestFail('Please shorten your name (100 characters maximum).');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
            requestFail('Please enter a valid email address.');
        }
        if ($phone !== '' && !preg_match('/^[0-9+()\-\s]{7,25}$/', $phone)) {
            requestFail('Please enter a valid phone number, for example +254 700 000 000.');
        }
        if ($phoneRequired && $phone === '') {
            requestFail('Please add a phone number so our team can confirm your booking with you.');
        }
        return [$name, $email, $phone];
    }
}

if (!function_exists('requestRecord')) {
    /**
     * Store a request and return its reference (BK-xxxxxx).
     *
     * $r keys: type ('Booking'|'Enquiry'), tour_id (?int), tour_name, name,
     * email, phone, booking_date (Y-m-d), travelers (int), amount (float),
     * message (?string), activity (string shown in the admin feed).
     *
     * Also adds the guest to customers (counting bookings, not enquiries) and
     * logs it to the activity feed, all in one transaction.
     */
    function requestRecord(PDO $pdo, array $r): string {
        try {
            $pdo->beginTransaction();

            $check = $pdo->prepare("SELECT 1 FROM bookings WHERE id = ?");
            do {
                $id = 'BK-' . random_int(100000, 999999);
                $check->execute([$id]);
            } while ($check->fetchColumn());

            $pdo->prepare(
                "INSERT INTO bookings (id, type, tour_id, user_name, email, phone, tour_name, booking_date,
                                       travelers, amount, message, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
            )->execute([$id, $r['type'], $r['tour_id'], $r['name'], $r['email'], $r['phone'] ?: null,
                        $r['tour_name'], $r['booking_date'], $r['travelers'], $r['amount'], $r['message'] ?: null]);

            $isBooking = $r['type'] === 'Booking';
            $cust = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
            $cust->execute([$r['email']]);
            if ($custId = $cust->fetchColumn()) {
                if ($isBooking) {
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
                )->execute([$custId, $r['name'], $r['email'], $isBooking ? 1 : 0]);
            }

            $pdo->prepare(
                "INSERT INTO activity_feed (user, action, target, activity_time) VALUES (?, ?, ?, 'Just now')"
            )->execute([$r['name'], $r['activity'], $r['tour_name']]);

            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('requestRecord: ' . $e->getMessage());
            if (in_array($e->getCode(), ['42S22', '42S02'], true)) {
                requestFail('Our booking system is temporarily unavailable. Please call us on +254 737 800 900.', 503);
            }
            requestFail('We could not save your request. Please try again, or call us on +254 737 800 900.', 500);
        }

        $_SESSION['booking_times'][] = time();
        return $id;
    }
}
