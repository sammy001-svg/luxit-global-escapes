<?php
/**
 * Shared admin dashboard data layer.
 *
 * Builds the full payload the admin panel renders from. Used by both the
 * initial page load (admin/index.php) and the partial-refresh endpoint
 * (admin/api/get-data.php) so a refresh always returns exactly the same
 * shape the page booted with.
 */

if (!function_exists('adminSafeQuery')) {
    /**
     * Run a query safely; returns [] on any DB error so a missing table or
     * column never kills the whole page.
     */
    function adminSafeQuery(PDO $pdo, string $sql): array {
        try {
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Admin panel query error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('getAdminDashboardData')) {
    function getAdminDashboardData(PDO $pdo): array {
        // ── Core records ──────────────────────────────────────────────────────
        $tours = adminSafeQuery($pdo, "SELECT * FROM tours ORDER BY created_at DESC");

        $destinations = adminSafeQuery($pdo, "SELECT * FROM destinations ORDER BY name ASC");

        // Bookings — alias columns the JS expects
        $bookings = adminSafeQuery($pdo,
            "SELECT id,
                    user_name    AS user,
                    email,
                    tour_name    AS tour,
                    booking_date AS date,
                    amount,
                    status,
                    created_at
             FROM bookings ORDER BY created_at DESC");

        // Customers — alias columns the JS expects
        $customers = adminSafeQuery($pdo,
            "SELECT id,
                    name,
                    email,
                    country,
                    bookings_count AS bookings,
                    joined_date    AS joined
             FROM customers ORDER BY joined_date DESC");

        // Schedule — upcoming departures derived from confirmed bookings, merged
        // with any manually entered events. Previously this read only the events
        // table, which nothing ever wrote to, so it showed the same stale seed
        // rows forever.
        $manualEvents = adminSafeQuery($pdo,
            "SELECT id, title, event_date AS date, type
             FROM events
             WHERE event_date >= CURDATE()
             ORDER BY event_date ASC");

        $departures = adminSafeQuery($pdo,
            "SELECT CONCAT('bk-', id) AS id,
                    CONCAT(tour_name, ' — ', user_name) AS title,
                    booking_date AS date,
                    'tour' AS type
             FROM bookings
             WHERE status = 'Confirmed' AND booking_date >= CURDATE()
             ORDER BY booking_date ASC
             LIMIT 20");

        $events = array_merge($manualEvents, $departures);
        usort($events, fn($a, $b) => strcmp((string)$a['date'], (string)$b['date']));

        // Activity Feed — created_at drives the age; the old activity_time column
        // held literal strings like "2 hours ago" that never changed.
        $activityFeed = adminSafeQuery($pdo,
            "SELECT id, user, action, target, created_at
             FROM activity_feed ORDER BY created_at DESC LIMIT 20");

        // Finance — LEFT JOINs so rows without a matching customer still appear
        $quotations = adminSafeQuery($pdo,
            "SELECT q.*, COALESCE(c.name, 'Unknown') AS customer_name
             FROM quotations q
             LEFT JOIN customers c ON q.customer_id = c.id
             ORDER BY q.created_at DESC");

        $invoices = adminSafeQuery($pdo,
            "SELECT i.*, COALESCE(c.name, 'Unknown') AS customer_name
             FROM invoices i
             LEFT JOIN customers c ON i.customer_id = c.id
             ORDER BY i.created_at DESC");

        $expenses = adminSafeQuery($pdo, "SELECT * FROM expenses ORDER BY expense_date DESC");

        $blogPosts = adminSafeQuery($pdo, "SELECT * FROM blog_posts ORDER BY created_at DESC");

        // ── Analytics ─────────────────────────────────────────────────────────
        // Every figure below is computed from real rows. The panel previously
        // displayed hardcoded values for conversion rate, customer lifetime
        // value and all four trend percentages.
        $totalRevenue       = 0;
        $currentMonthIncome = 0;
        $prevMonthIncome    = 0;
        $newBookingsToday   = 0;
        $confirmedCount     = 0;
        $today              = date('Y-m-d');
        $currentMonth       = date('Y-m');
        $prevMonth          = date('Y-m', strtotime('first day of last month'));

        foreach ($bookings as $b) {
            if ($b['status'] === 'Confirmed') {
                $confirmedCount++;
                $totalRevenue += $b['amount'];
                if (strpos((string)$b['date'], $currentMonth) === 0) {
                    $currentMonthIncome += $b['amount'];
                }
                if (strpos((string)$b['date'], $prevMonth) === 0) {
                    $prevMonthIncome += $b['amount'];
                }
            }
            if (strpos((string)($b['created_at'] ?? ''), $today) === 0) {
                $newBookingsToday++;
            }
        }

        // Percentage change, or null when there is no baseline to compare to —
        // the UI renders "no prior data" rather than inventing a number.
        $pctChange = function (float $now, float $before): ?float {
            if ($before <= 0) return null;
            return round((($now - $before) / $before) * 100, 1);
        };

        $totalBookings  = count($bookings);
        $customerCount  = count($customers);

        // Share of bookings that reached Confirmed. This is a real funnel
        // measure, unlike the fixed "4.2% conversion rate" shown before.
        $confirmationRate = $totalBookings > 0
            ? round(($confirmedCount / $totalBookings) * 100, 1)
            : null;

        // Average confirmed booking value.
        $avgBookingValue = $confirmedCount > 0
            ? round($totalRevenue / $confirmedCount, 2)
            : null;

        // Revenue per customer — a measurable stand-in for lifetime value,
        // rather than the previous hardcoded $2,840.
        $revenuePerCustomer = $customerCount > 0
            ? round($totalRevenue / $customerCount, 2)
            : null;

        // Customers with more than one booking.
        $repeatRate = null;
        if ($customerCount > 0) {
            $repeat = adminSafeQuery($pdo,
                "SELECT COUNT(*) AS c FROM (
                     SELECT email FROM bookings GROUP BY email HAVING COUNT(*) > 1
                 ) r");
            $repeatCustomers = (int)($repeat[0]['c'] ?? 0);
            $repeatRate = round(($repeatCustomers / $customerCount) * 100, 1);
        }

        $popularTours = adminSafeQuery($pdo,
            "SELECT tour_name as name, COUNT(*) as bookings, SUM(amount) as revenue
             FROM bookings GROUP BY tour_name ORDER BY bookings DESC LIMIT 5");

        // Last 6 months in chronological order. The previous query grouped by
        // month name only, so bookings from different years collapsed together
        // and the series was ordered by month number regardless of year.
        $monthlyStats = adminSafeQuery($pdo,
            "SELECT DATE_FORMAT(booking_date, '%b %Y') AS month,
                    SUM(amount) AS revenue
             FROM bookings
             WHERE status = 'Confirmed'
               AND booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY YEAR(booking_date), MONTH(booking_date)
             ORDER BY YEAR(booking_date) ASC, MONTH(booking_date) ASC");

        $analytics = [
            'totalRevenue'        => (float)$totalRevenue,
            'currentMonthIncome'  => (float)$currentMonthIncome,
            'prevMonthIncome'     => (float)$prevMonthIncome,
            'revenueTrend'        => $pctChange((float)$currentMonthIncome, (float)$prevMonthIncome),
            'totalBookings'       => $totalBookings,
            'confirmedBookings'   => $confirmedCount,
            'newBookingsToday'    => $newBookingsToday,
            'activeTours'         => count(array_filter($tours, fn($t) => $t['status'] === 'Active')),
            'confirmationRate'    => $confirmationRate,
            'avgBookingValue'     => $avgBookingValue,
            'revenuePerCustomer'  => $revenuePerCustomer,
            'repeatRate'          => $repeatRate,
            'popularTours'        => $popularTours,
            'monthlyStats'        => $monthlyStats,
        ];

        // Report tables the panel needs but the database does not have, so the
        // UI can say so instead of rendering a silently empty tab. Databases
        // created before a feature shipped will be missing its table until
        // migrate.php is run.
        $missingTables = [];
        try {
            $present = $pdo->query(
                "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()"
            )->fetchAll(PDO::FETCH_COLUMN);
            $present = array_map('strtolower', $present);
            foreach (['tours', 'destinations', 'bookings', 'customers', 'events',
                      'quotations', 'invoices', 'expenses', 'blog_posts', 'activity_feed'] as $t) {
                if (!in_array($t, $present, true)) $missingTables[] = $t;
            }
        } catch (PDOException $e) {
            error_log('Schema check failed: ' . $e->getMessage());
        }

        return [
            'missingTables' => $missingTables,
            'tours'        => $tours,
            'destinations' => $destinations,
            'bookings'     => $bookings,
            'customers'    => $customers,
            'analytics'    => $analytics,
            'events'       => $events,
            'activityFeed' => $activityFeed,
            'finance'      => [
                'quotations' => $quotations,
                'invoices'   => $invoices,
                'expenses'   => $expenses,
            ],
            'blogPosts'    => $blogPosts,
        ];
    }
}
