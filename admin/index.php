<?php
require_once __DIR__ . '/includes/auth.php';
adminSessionStart();

if (!adminIsLoggedIn()) {
    header("Location: login.php");
    exit;
}

$csrfToken = adminCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Luxit Global Escapes</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=15">
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

    <!-- Sidebar: fixed drawer on phones, static column from md up -->
    <div id="nav-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden" onclick="window.closeMobileNav()"></div>
    <aside id="sidebar" class="w-64 glass border-r border-white/10 flex flex-col z-50 fixed inset-y-0 left-0 -translate-x-full md:static md:translate-x-0 transition-transform duration-200 bg-dark-900 md:bg-transparent">
        <div class="px-6 pt-6 pb-4 flex items-center justify-between">
            <a href="index.php" class="block">
                <span class="inline-block rounded-lg bg-white px-2.5 py-1.5"><img src="../assets/images/luxit-africa-logo.png" alt="Luxit Global Escapes" class="w-28"></span>
                <span class="block text-[10px] text-slate-500 mt-2 font-bold uppercase tracking-[.2em]">Admin</span>
            </a>
            <button class="md:hidden w-9 h-9 rounded-lg bg-white/5" onclick="window.closeMobileNav()" aria-label="Close menu"><i class="fas fa-xmark"></i></button>
        </div>

        <nav class="flex-1 px-3 pb-4 overflow-y-auto custom-scroll" aria-label="Admin">
            <p class="nav-group">Overview</p>
            <a href="#" class="sidebar-link active" data-tab="dashboard"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="#" class="sidebar-link" data-tab="analytics"><i class="fas fa-chart-line"></i> Analytics</a>

            <p class="nav-group">Sales</p>
            <a href="#" class="sidebar-link" data-tab="bookings"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="#" class="sidebar-link" data-tab="customers"><i class="fas fa-users"></i> Customers</a>
            <a href="#" class="sidebar-link" data-tab="finance"><i class="fas fa-file-invoice-dollar"></i> Finance</a>

            <p class="nav-group">Website</p>
            <a href="#" class="sidebar-link" data-tab="tours"><i class="fas fa-suitcase-rolling"></i> Packages</a>
            <a href="#" class="sidebar-link" data-tab="homepage"><i class="fas fa-house"></i> Homepage</a>
            <a href="#" class="sidebar-link" data-tab="blogs"><i class="fas fa-newspaper"></i> Blog posts</a>
            <a href="#" class="sidebar-link" data-tab="destinations"><i class="fas fa-map-location-dot"></i> Destinations</a>

            <p class="nav-group">Account</p>
            <a href="#" class="sidebar-link" data-tab="settings"><i class="fas fa-gear"></i> Settings</a>
            <a href="../index.php" target="_blank" rel="noopener" class="sidebar-link"><i class="fas fa-arrow-up-right-from-square"></i> View website</a>
        </nav>

        <div class="p-4 border-t border-white/5">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white/5">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-primary flex-shrink-0 flex items-center justify-center font-bold text-white"><?php echo htmlspecialchars(strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1))); ?></div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></p>
                        <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'Administrator'); ?></p>
                    </div>
                </div>
                <a href="logout.php" class="text-slate-400 hover:text-rose-400 transition ml-2 p-2" title="Log out" aria-label="Log out"><i class="fas fa-right-from-bracket"></i></a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative overflow-hidden min-w-0">
        <header class="h-16 glass border-b border-white/10 flex items-center justify-between gap-3 px-4 md:px-8 z-30">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <button class="md:hidden w-10 h-10 rounded-xl bg-white/5 shrink-0" onclick="window.openMobileNav()" aria-label="Open menu"><i class="fas fa-bars"></i></button>
                <div class="flex items-center bg-white/5 rounded-xl px-3.5 py-2 border border-white/10 w-full max-w-md">
                    <i class="fas fa-search text-slate-500 mr-2 text-sm"></i>
                    <input type="search" id="global-search" placeholder="Search this section…" class="bg-transparent border-none outline-none text-sm w-full placeholder:text-slate-500">
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="../index.php" target="_blank" rel="noopener" class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:bg-white/5"><i class="fas fa-arrow-up-right-from-square mr-2 text-xs"></i>Website</a>
                <button id="add-new-btn" class="bg-primary hover:bg-primary/80 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center">
                    <i class="fas fa-plus sm:mr-2"></i><span class="hidden sm:inline">Add new</span>
                </button>
            </div>
        </header>

        <!-- Shown when the database is missing tables/columns (see updateSchemaBanner in main.js) -->
        <div id="schema-banner" class="hidden"></div>

        <!-- Dynamic Content Area -->
        <section id="content-area" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 custom-scroll">
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
    require_once 'includes/dashboard-data.php';

    // Same payload the partial-refresh endpoint (api/get-data.php) returns.
    $adminData = getAdminDashboardData($pdo);
    ?>
    <script>
        // HEX_TAG etc.: guest-supplied text (names, messages) must never close this <script>.
        window.ADMIN_DATA = <?php echo json_encode($adminData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        window.CSRF_TOKEN = <?php echo json_encode($csrfToken); ?>;
        window.ADMIN_USER = <?php echo json_encode($_SESSION['admin_username'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        // Destination names for the package editor's suggestions.
        window.ADMIN_DESTINATIONS = <?php require_once '../includes/destinations.php'; echo json_encode(array_values(array_map(fn($c) => $c['name'], DEST_COUNTRIES)), JSON_HEX_TAG); ?>;
        window.openMobileNav = () => { document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('nav-overlay').classList.remove('hidden'); };
        window.closeMobileNav = () => { document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('nav-overlay').classList.add('hidden'); };
    </script>
    <script src="js/main.js?v=16"></script>
    <script src="js/packages.js?v=16"></script>
    <script>
        if (!window.ADMIN_DATA) {
            document.getElementById('content-area').innerHTML = '<div class="p-8 text-rose-500 font-bold">CRITICAL ERROR: Data could not be loaded from Database. Please check your .env settings.</div>';
        }
    </script>
</body>
</html>
