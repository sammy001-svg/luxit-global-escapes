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

        // Events — alias event_date so JS can use .date
        $events = adminSafeQuery($pdo,
            "SELECT id, title, event_date AS date, type
             FROM events ORDER BY event_date ASC");

        // Activity Feed — alias activity_time so JS can use .time
        $activityFeed = adminSafeQuery($pdo,
            "SELECT id, user, action, target, activity_time AS time
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
        $totalRevenue       = 0;
        $currentMonthIncome = 0;
        $newBookingsToday   = 0;
        $today              = date('Y-m-d');
        $currentMonth       = date('Y-m');

        foreach ($bookings as $b) {
            if ($b['status'] === 'Confirmed') {
                $totalRevenue += $b['amount'];
                if (strpos((string)$b['date'], $currentMonth) === 0) {
                    $currentMonthIncome += $b['amount'];
                }
            }
            if (strpos((string)($b['created_at'] ?? ''), $today) === 0) {
                $newBookingsToday++;
            }
        }

        $popularTours = adminSafeQuery($pdo,
            "SELECT tour_name as name, COUNT(*) as bookings, SUM(amount) as revenue
             FROM bookings GROUP BY tour_name ORDER BY bookings DESC LIMIT 5");

        $monthlyStats = adminSafeQuery($pdo,
            "SELECT DATE_FORMAT(booking_date, '%b') as month, SUM(amount) as revenue
             FROM bookings WHERE status = 'Confirmed'
             GROUP BY month, DATE_FORMAT(booking_date, '%m')
             ORDER BY DATE_FORMAT(booking_date, '%m') ASC LIMIT 6");

        $analytics = [
            'totalRevenue'       => (float)$totalRevenue,
            'currentMonthIncome' => (float)$currentMonthIncome,
            'totalBookings'      => count($bookings),
            'newBookingsToday'   => $newBookingsToday,
            'activeTours'        => count(array_filter($tours, fn($t) => $t['status'] === 'Active')),
            'popularTours'       => $popularTours,
            'monthlyStats'       => $monthlyStats,
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
