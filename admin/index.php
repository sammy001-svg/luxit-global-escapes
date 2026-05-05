<?php
require_once '../includes/db.php';

// Fetch data for the initial state
$data = [
    'tours' => $pdo->query("SELECT * FROM tours")->fetchAll(),
    'destinations' => $pdo->query("SELECT * FROM destinations")->fetchAll(),
    'bookings' => $pdo->query("SELECT * FROM bookings")->fetchAll(),
    'customers' => $pdo->query("SELECT * FROM customers")->fetchAll(),
    'analytics' => [
        'totalBookings' => $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
        'totalRevenue' => $pdo->query("SELECT SUM(amount) FROM bookings WHERE status='Confirmed'")->fetchColumn() ?: 0,
        'activeTours' => $pdo->query("SELECT COUNT(*) FROM tours WHERE status='Active'")->fetchColumn(),
        'newCustomers' => $pdo->query("SELECT COUNT(*) FROM customers WHERE joined_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(),
        'revenueData' => [5000, 7500, 6200, 8900, 11000, 15000, 13500],
        'bookingTrend' => [25, 40, 35, 55, 70, 95, 80]
    ],
    'activityFeed' => $pdo->query("SELECT id, user, action, target, created_at as time FROM activity_log ORDER BY created_at DESC LIMIT 10")->fetchAll(),
    'settings' => []
];

$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
foreach ($settings_raw as $s) {
    $data['settings'][$s['setting_key']] = $s['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxit Admin - Premium Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        window.MOCK_DATA = <?php echo json_encode($data); ?>;
        
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
<body class="bg-dark-900 text-slate-100 flex overflow-hidden h-screen font-base">

    <!-- Sidebar -->
    <aside id="admin-sidebar" class="w-64 glass border-r border-white/10 fixed md:relative inset-y-0 left-0 -translate-x-full md:translate-x-0 flex flex-col z-[60] transition-transform duration-300 ease-in-out">
        <div class="p-6 flex items-center justify-between">
            <img src="../assets/images/luxit-africa-logo.png" alt="Luxit Logo" class="w-32">
            <button class="md:hidden text-slate-500 hover:text-white" id="close-sidebar">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="px-6 pb-4">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest">Admin Panel</p>
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
            <div class="flex items-center space-x-3 p-2 rounded-xl bg-white/5">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold">A</div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium truncate">Admin User</p>
                    <p class="text-xs text-slate-500 truncate">admin@luxit.com</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative overflow-hidden w-full">
        <!-- Header -->
        <header class="h-16 glass border-b border-white/10 flex items-center justify-between px-4 md:px-8 z-40">
            <div class="flex items-center space-x-4">
                <button id="mobile-menu-toggle" class="md:hidden text-slate-400 hover:text-white p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center bg-white/5 rounded-full px-4 py-1.5 border border-white/10 w-48 md:w-96">
                    <i class="fas fa-search text-slate-500 mr-2"></i>
                    <input type="text" id="global-search" placeholder="Search..." class="bg-transparent border-none outline-none text-sm w-full placeholder:text-slate-500">
                </div>
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

    <script src="js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
