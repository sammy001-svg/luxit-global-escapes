<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxit Admin - Premium Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#006A72',
                        secondary: '#FFD214',
                        dark: {
                            800: '#1E293B',
                            900: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-900 text-slate-100 flex overflow-hidden h-screen">

    <!-- Sidebar -->
    <aside class="w-64 glass border-r border-white/10 hidden md:flex flex-col z-50">
        <div class="p-6">
            <img src="../assets/images/luxit-africa-logo.png" alt="Luxit Logo" class="w-32">
            <p class="text-xs text-slate-500 mt-2 font-semibold uppercase tracking-widest">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-4 space-y-2 overflow-y-auto flex flex-col">
            <a href="#" class="sidebar-link active" data-tab="dashboard">
                <i class="fas fa-chart-pie mr-3"></i> Dashboard
            </a>
            <a href="#" class="sidebar-link" data-tab="bookings">
                <i class="fas fa-calendar-check mr-3"></i> Bookings
            </a>
            <a href="#" class="sidebar-link" data-tab="tours">
                <i class="fas fa-route mr-3"></i> Tour Packages
            </a>
            <a href="#" class="sidebar-link" data-tab="destinations">
                <i class="fas fa-map-marker-alt mr-3"></i> Destinations
            </a>
            <a href="#" class="sidebar-link" data-tab="customers">
                <i class="fas fa-users mr-3"></i> Customers
            </a>
            <a href="#" class="sidebar-link" data-tab="finance">
                <i class="fas fa-file-invoice-dollar mr-3"></i> Finance
            </a>
            <a href="#" class="sidebar-link" data-tab="analytics">
                <i class="fas fa-chart-line mr-3"></i> Analytics
            </a>
            <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest px-4">System</div>
            <a href="#" class="sidebar-link" data-tab="settings">
                <i class="fas fa-cog mr-3"></i> Settings
            </a>
            <div class="pt-4 mt-auto border-t border-white/5 opacity-50">
                <a href="../index.php" class="sidebar-link text-sm hover:opacity-100 transition">
                    <i class="fas fa-arrow-left mr-3"></i> Back to Website
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-white/5">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white/5">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-full bg-primary flex-shrink-0 flex items-center justify-center font-bold text-white"><?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?></div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></p>
                        <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?></p>
                    </div>
                </div>
                <a href="logout.php" class="text-slate-400 hover:text-rose-500 transition ml-2" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative overflow-hidden">
        <!-- Header -->
        <header class="h-16 glass border-b border-white/10 flex items-center justify-between px-8 z-40">
            <div class="flex items-center bg-white/5 rounded-full px-4 py-1.5 border border-white/10 w-96">
                <i class="fas fa-search text-slate-500 mr-2"></i>
                <input type="text" id="global-search" placeholder="Search tours, bookings..." class="bg-transparent border-none outline-none text-sm w-full placeholder:text-slate-500">
            </div>
            <div class="flex items-center space-x-6">
                <button class="relative text-slate-400 hover:text-white transition">
                    <i class="fas fa-bell"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-secondary rounded-full"></span>
                </button>
                <div class="h-8 w-px bg-white/10"></div>
                <button id="add-new-btn" class="bg-primary hover:bg-opacity-90 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New
                </button>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <section id="content-area" class="flex-1 overflow-y-auto p-8 custom-scroll">
            <!-- Content will be injected here -->
        </section>
    </main>

    <!-- Modal for CRUD (Hidden by default) -->
    <div id="modal-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div id="modal-content" class="bg-dark-800 border border-white/10 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl animate-fade-in">
            <!-- Modal internal content injected by JS -->
        </div>
    </div>

    <?php
    require_once '../includes/db.php';

    // Helper: run a query safely; returns [] on any DB error so a missing
    // table or column never kills the whole page.
    function safeQuery(PDO $pdo, string $sql): array {
        try {
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Admin panel query error: ' . $e->getMessage());
            return [];
        }
    }

    // ── Fetch all data ────────────────────────────────────────────────────────

    // Tours
    $tours = safeQuery($pdo, "SELECT * FROM tours ORDER BY created_at DESC");

    // Destinations
    $destinations = safeQuery($pdo, "SELECT * FROM destinations ORDER BY name ASC");

    // Bookings — alias columns the JS expects
    $bookings = safeQuery($pdo,
        "SELECT id,
                user_name   AS user,
                email,
                tour_name   AS tour,
                booking_date AS date,
                amount,
                status,
                created_at
         FROM bookings ORDER BY created_at DESC");

    // Customers — alias columns the JS expects
    $customers = safeQuery($pdo,
        "SELECT id,
                name,
                email,
                country,
                bookings_count AS bookings,
                joined_date    AS joined
         FROM customers ORDER BY joined_date DESC");

    // Events — alias event_date so JS can use .date
    $events = safeQuery($pdo,
        "SELECT id, title, event_date AS date, type
         FROM events ORDER BY event_date ASC");

    // Activity Feed — alias activity_time so JS can use .time
    $activityFeed = safeQuery($pdo,
        "SELECT id, user, action, target, activity_time AS time
         FROM activity_feed ORDER BY created_at DESC LIMIT 20");

    // Finance — LEFT JOINs so rows without a matching customer still appear
    $quotations = safeQuery($pdo,
        "SELECT q.*, COALESCE(c.name, 'Unknown') AS customer_name
         FROM quotations q
         LEFT JOIN customers c ON q.customer_id = c.id
         ORDER BY q.created_at DESC");

    $invoices = safeQuery($pdo,
        "SELECT i.*, COALESCE(c.name, 'Unknown') AS customer_name
         FROM invoices i
         LEFT JOIN customers c ON i.customer_id = c.id
         ORDER BY i.created_at DESC");

    $expenses = safeQuery($pdo, "SELECT * FROM expenses ORDER BY expense_date DESC");

    // ── Analytics ─────────────────────────────────────────────────────────────
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

    $popularTours = safeQuery($pdo,
        "SELECT tour_name as name, COUNT(*) as bookings, SUM(amount) as revenue
         FROM bookings GROUP BY tour_name ORDER BY bookings DESC LIMIT 5");

    $monthlyStats = safeQuery($pdo,
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

    $mock_data = [
        'tours'       => $tours,
        'destinations'=> $destinations,
        'bookings'    => $bookings,
        'customers'   => $customers,
        'analytics'   => $analytics,
        'events'      => $events,
        'activityFeed'=> $activityFeed,
        'finance'     => [
            'quotations' => $quotations,
            'invoices'   => $invoices,
            'expenses'   => $expenses,
        ],
    ];
    ?>
    <script>
        window.MOCK_DATA = <?php echo json_encode($mock_data); ?>;
    </script>
    <script src="js/main.js?v=8"></script>
    <script>
        if (!window.MOCK_DATA) {
            document.getElementById('content-area').innerHTML = '<div class="p-8 text-rose-500 font-bold">CRITICAL ERROR: Data could not be loaded from Database. Please check your .env settings.</div>';
        }
    </script>
</body>
</html>
