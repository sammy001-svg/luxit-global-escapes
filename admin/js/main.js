// Data is injected by admin/index.php as window.ADMIN_DATA and refreshed
// in place from api/get-data.php — see refreshData() below.
console.log('Main script loading...');

// Global Error Handler for Remote Debugging
window.onerror = function(message, source, lineno, colno, error) {
    const errorMsg = `Error: ${message} at ${source}:${lineno}:${colno}`;
    console.error(errorMsg);
    const contentArea = document.getElementById('content-area');
    if (contentArea) {
        contentArea.innerHTML = `
            <div class="p-8 bg-rose-500/10 border border-rose-500/50 rounded-2xl text-rose-500 space-y-4">
                <h2 class="text-xl font-bold flex items-center"><i class="fas fa-exclamation-triangle mr-3"></i> Runtime Error Detected</h2>
                <p class="text-sm font-mono bg-black/20 p-4 rounded-xl">${errorMsg}</p>
                <div class="flex space-x-4">
                    <button onclick="window.hardReset()" class="bg-rose-500 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-rose-500/20">Nuclear Reset (Clear Data)</button>
                    <button onclick="location.reload()" class="bg-white/10 text-white px-6 py-2 rounded-xl text-sm font-bold border border-white/10">Retry Page Load</button>
                </div>
                <p class="text-xs text-rose-500/70 italic">Note: Resetting will clear your local changes and restore defaults.</p>
            </div>
        `;
    }
    return false;
};

// State Management
//
// state.data is server-owned: it is seeded from window.ADMIN_DATA on boot and
// replaced wholesale by refreshData() after every write. It is never cached to
// localStorage — the DB is the single source of truth, so a hand-patched local
// copy can only drift from it.
//
// Everything else in `state` is UI-only (which tab, filters, search) and is
// deliberately kept outside state.data so it survives a refresh.
let state = {
    currentTab: 'dashboard',
    data: (() => {
        if (!window.ADMIN_DATA) {
            console.error('window.ADMIN_DATA NOT FOUND!');
            return { tours: [], destinations: [], bookings: [], customers: [] };
        }
        return window.ADMIN_DATA;
    })(),
    searchQuery: '',
    tourStatusFilter: 'All',
    tourPageFilter: 'All',
    bookingFilter: 'All',
    bookingType: 'All',
    financeTab: 'quotations',
    isRefreshing: false
};

// Legacy localStorage cache from the pre-refresh data model. Clear any stale
// copy left in browsers that used an earlier build.
try { localStorage.removeItem('adminData'); } catch (e) { /* ignore */ }

window.hardReset = () => {
    if (confirm('Reload the panel and pull fresh data from the server?')) {
        location.reload();
    }
};

// ── API layer ─────────────────────────────────────────────────────────────────
// Every write goes through apiPost so the CSRF token cannot be forgotten at a
// call site. The server rejects any non-GET request without it.
async function apiPost(endpoint, data = {}) {
    const body = data instanceof FormData ? data : new FormData();
    if (!(data instanceof FormData)) {
        Object.entries(data).forEach(([k, v]) => body.append(k, v));
    }
    body.append('csrf_token', window.CSRF_TOKEN || '');

    const res = await fetch(`api/${endpoint}`, { method: 'POST', body });
    return handleApiResponse(res);
}

async function apiGet(endpoint) {
    const res = await fetch(`api/${endpoint}`, {
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
    });
    return handleApiResponse(res);
}

// A 401 means the session lapsed; a 403 means the token is stale (typically
// after the session was rebuilt). Both are recoverable only by logging in again.
async function handleApiResponse(res) {
    if (res.status === 401) {
        showToast('Your session has expired. Redirecting to login…', 'error');
        setTimeout(() => { window.location.href = 'login.php'; }, 1500);
        throw new Error('Session expired');
    }
    if (res.status === 403) {
        showToast('Security token rejected. Please reload the page.', 'error');
        throw new Error('CSRF rejected');
    }
    return res.json();
}

// ── Partial Refresh ───────────────────────────────────────────────────────────
// Pulls the canonical payload from the server and re-renders the current tab
// in place. Replaces location.reload() after writes: same correctness, but the
// admin keeps their tab, filters, search text and scroll position.
async function refreshData({ silent = false } = {}) {
    if (state.isRefreshing) return false;
    state.isRefreshing = true;
    if (!silent) showRefreshIndicator(true);

    try {
        // apiGet bounces to login on 401 rather than rendering stale data.
        const payload = await apiGet('get-data.php');
        if (!payload.success) throw new Error(payload.error || 'Refresh failed');

        state.data = payload.data;
        rerenderCurrentTab();
        return true;
    } catch (err) {
        console.error('Refresh failed:', err);
        if (err.message !== 'Session expired' && err.message !== 'CSRF rejected') {
            showToast('Saved, but the view could not refresh. Reload to see the latest.', 'error');
        }
        return false;
    } finally {
        state.isRefreshing = false;
        showRefreshIndicator(false);
    }
}
window.refreshData = refreshData;

// New website bookings arrive without the admin having to reload: check once a
// minute while the panel is visible and no form is open (a refresh would not
// touch an open modal, but there is no point fetching behind one).
const pendingIds = () => new Set((state.data.bookings || []).filter(b => b.status === 'Pending').map(b => b.id));
setInterval(async () => {
    if (document.hidden || !modalOverlay.classList.contains('hidden')) return;
    const before = pendingIds();
    if (!(await refreshData({ silent: true }))) return;
    const fresh = (state.data.bookings || []).filter(b => b.status === 'Pending' && !before.has(b.id));
    if (fresh.length === 1) {
        const info = bookingInfo(fresh[0]);
        showToast(`New ${info.type === 'Enquiry' ? 'enquiry' : 'booking request'} from ${fresh[0].user}: ${info.tour}`);
    } else if (fresh.length > 1) {
        showToast(`${fresh.length} new booking requests`);
    }
}, 60000);

// Re-render the active tab, preserving scroll position so the admin does not
// lose their place in a long table after saving a row.
function rerenderCurrentTab() {
    const scrollTop = contentArea ? contentArea.scrollTop : 0;
    destroyCharts();
    const render = tabs[state.currentTab];
    if (typeof render === 'function') render();
    updateBookingBadge();
    if (contentArea) contentArea.scrollTop = scrollTop;
}

function showRefreshIndicator(active) {
    let bar = document.getElementById('refresh-indicator');
    if (active) {
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'refresh-indicator';
            bar.className = 'fixed top-0 left-0 right-0 h-0.5 bg-secondary z-[99998] animate-pulse';
            document.body.appendChild(bar);
        }
    } else if (bar) {
        bar.remove();
    }
}

// Chart.js instances must be destroyed before their canvas is replaced,
// otherwise each re-render leaks a live chart bound to a detached node.
const chartRegistry = [];
function registerChart(chart) {
    if (chart) chartRegistry.push(chart);
    return chart;
}
function destroyCharts() {
    while (chartRegistry.length) {
        const chart = chartRegistry.pop();
        try { chart.destroy(); } catch (e) { /* already gone */ }
    }
}

// ── Toast Notification System ─────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const existing = document.getElementById('admin-toast');
    if (existing) existing.remove();

    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const colors = { success: 'bg-emerald-500', error: 'bg-rose-500', info: 'bg-primary' };

    const toast = document.createElement('div');
    toast.id = 'admin-toast';
    toast.className = `fixed bottom-6 right-6 z-[99999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-bold text-white ${colors[type] || colors.success} translate-y-0 opacity-100 transition-all duration-300`;
    // Messages can carry server errors and guest names: always text, never HTML.
    toast.innerHTML = `<i class="fas ${icons[type] || icons.success} text-base"></i><span>${escHtml(message)}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(1rem)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}
window.showToast = showToast;

// Selectors
const contentArea = document.getElementById('content-area');
const sidebarLinks = document.querySelectorAll('.sidebar-link');
const modalOverlay = document.getElementById('modal-overlay');
const modalContent = document.getElementById('modal-content');
const addNewBtn = document.getElementById('add-new-btn');
const globalSearch = document.getElementById('global-search');

// Global "Add New" Handler
if (addNewBtn) {
    addNewBtn.addEventListener('click', () => {
        if (state.currentTab === 'tours') window.openTourModal();
        else if (state.currentTab === 'destinations') window.openDestinationModal();
        else if (state.currentTab === 'bookings') window.openBookingModal();
        else if (state.currentTab === 'customers') window.openCustomerModal();
        else if (state.currentTab === 'blogs') window.openBlogModal();
        else if (state.currentTab === 'finance') {
            if (state.financeTab === 'quotations') window.openQuotationModal();
            else if (state.financeTab === 'invoices') window.openInvoiceModal();
            else if (state.financeTab === 'expenses') window.openExpenseModal();
        }
        else showToast('Please switch to a management tab to add new records.', 'info');
    });
}

// Tab Configuration
const tabs = {
    dashboard: renderDashboard,
    bookings: renderBookings,
    tours: renderTours,
    homepage: renderHomepage,
    destinations: renderDestinations,
    customers: renderCustomers,
    finance: renderFinance,
    analytics: renderAnalytics,
    blogs: renderBlogs,
    settings: renderSettings
};

// Navigation using Event Delegation (More Robust)
const sidebarNav = document.querySelector('nav');
if (sidebarNav) {
    sidebarNav.addEventListener('click', (e) => {
        const link = e.target.closest('.sidebar-link');
        if (link) {
            const tab = link.getAttribute('data-tab');
            if (tab) {
                e.preventDefault();
                console.log(`Sidebar clicked: ${tab}`);
                switchTab(tab);
            }
        }
    });
}

// Search Listener
if (globalSearch) {
    globalSearch.addEventListener('input', (e) => {
        state.searchQuery = e.target.value.toLowerCase();
        tabs[state.currentTab]();
    });
}

function switchTab(tab) {
    try {
        console.log(`Switching to tab: ${tab}`);
        state.currentTab = tab;
        destroyCharts();
        sidebarLinks.forEach(l => {
            l.classList.toggle('active', l.getAttribute('data-tab') === tab);
        });
        if (tabs[tab]) {
            console.log(`Executing tab function for ${tab}`);
            tabs[tab]();
            updateBookingBadge();
        } else {
            console.error(`Tab ${tab} not found`);
        }
    } catch (error) {
        console.error('Error switching tab:', error);
        contentArea.innerHTML = `<div class="p-8 text-rose-500">Error loading tab: ${error.message}</div>`;
    }
}

// Initial Render
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed. Initializing Admin Panel...');
    try {
        switchTab('dashboard');
    } catch (e) {
        console.error('Initial render failed:', e);
    }
});

// --- Render Functions ---

function renderDashboard() {
    console.log('Rendering Dashboard...');
    try {
        const { analytics, bookings, activityFeed, customers } = state.data;
        if (!analytics || !bookings || !activityFeed || !customers) {
            throw new Error('Critical data missing from state');
        }
        const currentMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });
        
        contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Dashboard Overview</h1>
                    <p class="text-slate-500 mt-1">Tracking performance for ${currentMonth}</p>
                </div>
                <div class="flex items-center space-x-2 bg-slate-800/50 p-1.5 rounded-xl border border-white/5">
                    <button class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white shadow-lg">Overview</button>
                    <button class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-white" onclick="window.switchTab('analytics')">Reports</button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                ${renderStatCard('Monthly Income', `$${analytics.currentMonthIncome.toLocaleString()}`, 'fa-wallet', 'text-emerald-400')}
                ${renderStatCard('Pending Bookings', bookings.filter(b => b.status === 'Pending').length, 'fa-clock', 'text-amber-400')}
                ${renderStatCard('New Bookings Today', analytics.newBookingsToday, 'fa-plus-circle', 'text-blue-400')}
                ${renderStatCard('Total Customers', customers.length, 'fa-users', 'text-purple-400')}
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold">Revenue Performance</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Confirmed bookings, last 6 months</p>
                        </div>
                        ${renderTrendBadge(analytics.revenueTrend)}
                    </div>
                    <canvas id="revenueChart" class="w-full h-80"></canvas>
                </div>

                <div class="glass-card rounded-2xl p-6 flex flex-col">
                    <h2 class="text-xl font-bold mb-6">Recent Activity</h2>
                    <div class="flex-1 space-y-5">
                        ${activityFeed.length ? activityFeed.slice(0, 8).map(item => `
                            <div class="flex space-x-4">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas ${getActivityIcon(item.action)} text-primary text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium leading-snug">
                                        <span class="text-primary">${escHtml(item.user)}</span> ${escHtml(item.action)}
                                    </p>
                                    <p class="text-xs text-slate-400 font-bold opacity-80 mt-0.5 truncate" title="${escHtml(item.target)}">${escHtml(item.target)}</p>
                                    <p class="text-[10px] text-slate-600 mt-0.5">${timeAgo(item.created_at)}</p>
                                </div>
                            </div>
                        `).join('') : `
                            <div class="text-center py-10">
                                <i class="fas fa-clock-rotate-left text-4xl text-slate-800 mb-3 block"></i>
                                <p class="text-sm text-slate-500">No activity yet</p>
                                <p class="text-xs text-slate-600 mt-1">Actions you take in the panel will appear here.</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold">Departure Calendar</h2>
                            <p class="text-xs text-slate-500 mt-0.5" id="calendar-label">${calendarMonthLabel()}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="window.shiftCalendar(-1)" class="p-1.5 hover:bg-white/5 rounded-lg text-slate-400 transition" title="Previous month"><i class="fas fa-chevron-left"></i></button>
                            <button onclick="window.shiftCalendar(1)" class="p-1.5 hover:bg-white/5 rounded-lg text-slate-400 transition" title="Next month"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-2 text-center text-[10px] text-slate-500 font-bold mb-4 uppercase tracking-tighter">
                        <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                    </div>
                    <div class="grid grid-cols-7 gap-2" id="calendar-grid">
                        ${renderCalendarDays()}
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold">Upcoming Departures</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Confirmed bookings and scheduled events</p>
                    </div>
                    <div class="space-y-4">
                        ${state.data.events.length ? state.data.events.slice(0, 5).map(event => `
                            <div class="flex items-center p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition group">
                                <div class="w-12 h-12 rounded-xl ${getEventTypeColor(event.type)} flex flex-col items-center justify-center mr-4 flex-shrink-0">
                                    <span class="text-[10px] font-bold uppercase opacity-80">${new Date(event.date).toLocaleString('default', { month: 'short' })}</span>
                                    <span class="text-lg font-bold leading-none">${new Date(event.date).getDate()}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold group-hover:text-primary transition truncate" title="${event.title}">${event.title}</h4>
                                    <p class="text-xs text-slate-500">${daysUntil(event.date)}</p>
                                </div>
                            </div>
                        `).join('') : `
                            <div class="text-center py-10">
                                <i class="fas fa-calendar-day text-4xl text-slate-800 mb-3 block"></i>
                                <p class="text-sm text-slate-500">No upcoming departures</p>
                                <p class="text-xs text-slate-600 mt-1">Confirmed bookings with a future travel date appear here.</p>
                            </div>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    setTimeout(() => {
        try {
            initDashboardCharts();
        } catch (e) {
            console.error('Chart init failed:', e);
        }
    }, 0);
    } catch (error) {
        console.error('Error rendering dashboard:', error);
        contentArea.innerHTML = `
            <div class="p-8 bg-rose-500/10 border border-rose-500/50 rounded-2xl text-rose-500 space-y-4">
                <h2 class="text-xl font-bold flex items-center"><i class="fas fa-exclamation-triangle mr-3"></i> Rendering Error</h2>
                <p class="text-sm font-mono bg-black/20 p-4 rounded-xl">${error.message}</p>
                <button onclick="window.hardReset()" class="bg-rose-500 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-rose-500/20">Nuclear Reset (Clear Data)</button>
            </div>
        `;
    }
}

function renderStatCard(title, value, icon, iconColor) {
    return `
        <div class="glass-card rounded-2xl p-6 flex items-center shadow-lg border border-white/5">
            <div class="w-12 h-12 rounded-xl bg-dark-900 flex items-center justify-center mr-4">
                <i class="fas ${icon} ${iconColor} text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">${title}</p>
                <p class="text-2xl font-bold">${value}</p>
            </div>
        </div>
    `;
}

// Pages a package can be published to. Kept in one place so the filter bar,
// the card badges and the edit modal cannot drift apart.
const PACKAGE_PAGES = [
    { key: 'International', label: 'International', icon: 'fa-globe',    file: 'international-packages.php' },
    { key: 'Local',         label: 'Local',         icon: 'fa-location-dot', file: 'local-packages.php' },
    { key: 'Safari',        label: 'Safari',        icon: 'fa-binoculars', file: 'safari-packages.php' },
];

// Homepage sections a package can be shown in (tours.home_sections).
// "page" is the package page a package must be on to qualify.
const HOMEPAGE_SECTIONS = [
    { key: 'safari',        label: 'Safari',         icon: 'fa-paw',        page: 'Safari',        title: 'Safari Packages' },
    { key: 'local',         label: 'Local',          icon: 'fa-location-dot', page: 'Local',       title: 'Local Packages' },
    { key: 'international', label: 'International',  icon: 'fa-plane',      page: 'International', title: 'International Packages' },
    { key: 'offers',        label: 'Special offer',  icon: 'fa-tag',        page: null,            title: 'Special Offers' },
];

function tourHomeSections(tour) {
    return String(tour.home_sections || '').split(',').map(s => s.trim()).filter(Boolean);
}

// Travel-style pages a package can also appear on (tours.travel_styles).
const TRAVEL_STYLES = [
    { key: 'romantic',   label: 'Romantic',   icon: 'fa-heart' },
    { key: 'wellness',   label: 'Wellness',   icon: 'fa-spa' },
    { key: 'halal',      label: 'Halal',      icon: 'fa-moon' },
    { key: 'kosher',     label: 'Kosher',     icon: 'fa-star-of-david' },
    { key: 'faith',      label: 'Faith',      icon: 'fa-place-of-worship' },
    { key: 'accessible', label: 'Accessible', icon: 'fa-wheelchair' },
    { key: 'mice',       label: 'MICE',       icon: 'fa-briefcase' },
];

// Escape a value for interpolation into the HTML templates below. Without it a
// title containing a quote, or a description containing "</textarea>", breaks
// the edit form and the package is saved back truncated.
function escHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Package photos are resized in the browser before upload. Phone and camera
// photos are routinely 5–12 MB, well over the server's 3 MB limit, and even
// when they fit they make the homepage carousels slow to load.
const TOUR_IMAGE_MAX_EDGE = 1600;
const TOUR_IMAGE_MAX_BYTES = 2.5 * 1024 * 1024;

function prepareTourImage(file) {
    return new Promise((resolve, reject) => {
        if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
            reject(new Error('Please choose a JPG, PNG or WEBP image.'));
            return;
        }
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => {
            URL.revokeObjectURL(url);
            const scale = Math.min(1, TOUR_IMAGE_MAX_EDGE / Math.max(img.naturalWidth, img.naturalHeight));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.naturalWidth * scale);
            canvas.height = Math.round(img.naturalHeight * scale);
            const ctx = canvas.getContext('2d');
            // JPEG has no alpha; paint white so transparent PNGs don't turn black.
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            let quality = 0.85;
            let dataUrl = canvas.toDataURL('image/jpeg', quality);
            // base64 is ~4/3 the size of the bytes it encodes.
            while (dataUrl.length * 0.75 > TOUR_IMAGE_MAX_BYTES && quality > 0.5) {
                quality -= 0.1;
                dataUrl = canvas.toDataURL('image/jpeg', quality);
            }
            resolve(dataUrl);
        };
        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('That file could not be read as an image.'));
        };
        img.src = url;
    });
}

// package_pages is stored as a comma-separated string; treat it as a set.
function tourPages(tour) {
    return String(tour.package_pages || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
}

function renderTours() {
    const { tours } = state.data;
    const filteredTours = tours.filter(t => {
        const matchesSearch = t.title.toLowerCase().includes(state.searchQuery) || t.location.toLowerCase().includes(state.searchQuery);
        const matchesStatus = state.tourStatusFilter === 'All' || t.status === state.tourStatusFilter;
        const matchesPage   = state.tourPageFilter === 'All' || tourPages(t).includes(state.tourPageFilter);
        return matchesSearch && matchesStatus && matchesPage;
    });

    const stats = {
        total:  tours.length,
        active: tours.filter(t => t.status === 'Active').length,
        value:  tours.filter(t => t.status === 'Active').reduce((sum, t) => sum + Number(t.price || 0), 0),
        onHome: tours.filter(t => tourHomeSections(t).length && t.status === 'Active').length,
    };

    // A package with no page assignment is invisible on the public site. This is
    // the single most common reason "it doesn't show up", so surface it loudly.
    const orphans = tours.filter(t => t.status === 'Active' && tourPages(t).length === 0);
    const orphanWarning = orphans.length ? `
        <div class="rounded-2xl p-5 bg-amber-500/10 border border-amber-500/40 text-amber-300 space-y-2">
            <p class="font-bold flex items-center"><i class="fas fa-triangle-exclamation mr-2"></i>
                ${orphans.length} active package${orphans.length > 1 ? 's are' : ' is'} not assigned to any page</p>
            <p class="text-sm text-amber-300/80">
                ${orphans.map(o => `<button onclick="window.openTourModal(${o.id})" class="underline hover:text-white">${o.title}</button>`).join(', ')}
                — these will not appear anywhere on the public site until you tick a package page.
            </p>
        </div>` : '';

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold">Tour Packages</h1>
                    <p class="text-slate-500 mt-1">Manage your travel products and control where each one appears on the website</p>
                </div>
                <button onclick="window.openTourModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Create Package
                </button>
            </div>

            ${orphanWarning}

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card p-6 rounded-2xl border border-white/5 bg-gradient-to-br from-primary/5 to-transparent">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Portfolio Value</p>
                    <p class="text-2xl font-bold">$${stats.value.toLocaleString()}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Sum of active package prices</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Active Packages</p>
                    <p class="text-2xl font-bold text-emerald-400">${stats.active}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Live on the website</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Featured on Home</p>
                    <p class="text-2xl font-bold text-secondary">${stats.onHome}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Shown on the homepage</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Drafts</p>
                    <p class="text-2xl font-bold text-slate-400">${stats.total - stats.active}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Hidden from the public</p>
                </div>
            </div>

            <div class="flex items-center gap-6 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Status</span>
                    <div class="flex items-center bg-slate-800/50 p-1 rounded-xl border border-white/5">
                        ${['All', 'Active', 'Draft', 'Inactive'].map(s => `
                            <button onclick="window.setTourStatusFilter('${s}')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition ${state.tourStatusFilter === s ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-white'}">
                                ${s}
                            </button>
                        `).join('')}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Page</span>
                    <div class="flex items-center bg-slate-800/50 p-1 rounded-xl border border-white/5">
                        ${['All', ...PACKAGE_PAGES.map(p => p.key)].map(p => `
                            <button onclick="window.setTourPageFilter('${p}')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition ${state.tourPageFilter === p ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-white'}">
                                ${p}
                            </button>
                        `).join('')}
                    </div>
                </div>
                <span class="text-xs text-slate-500 ml-auto">${filteredTours.length} of ${tours.length} package${tours.length === 1 ? '' : 's'}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                ${filteredTours.length > 0 ? filteredTours.map(tour => `
                    <div class="glass-card rounded-2xl overflow-hidden group border border-white/5 hover:border-primary/30 transition-all duration-300 flex flex-col h-full">
                        <div class="h-48 bg-slate-800 relative overflow-hidden">
                            ${tour.image ? `<img src="../${tour.image}" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110" onerror="this.src='https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80'">` : ''}
                            <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/20 to-transparent z-10"></div>
                            <div class="absolute top-4 right-4 z-20">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold ${tour.status === 'Active' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-slate-500/20 text-slate-400'} border border-white/5">
                                    ${tour.status}
                                </span>
                            </div>
                            <div class="absolute bottom-4 left-4 z-20">
                                <span class="bg-primary/90 backdrop-blur-md text-white text-[9px] uppercase font-bold px-2 py-1 rounded-md mb-2 inline-block">${tour.duration}</span>
                                <h3 class="text-xl font-bold text-white leading-tight">${tour.title}</h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-400 flex items-center font-medium">
                                        <i class="fas fa-map-marker-alt mr-2 text-primary opacity-70"></i>${tour.location}
                                    </span>
                                    <span class="font-bold text-secondary text-lg">$${Number(tour.price).toLocaleString()}</span>
                                </div>
                                <div class="flex items-center text-xs text-slate-500">
                                    <i class="fas fa-tag mr-2 opacity-50"></i>
                                    <span>${tour.category || 'Adventure'}</span>
                                    <span class="mx-2">•</span>
                                    <div class="flex gap-1 text-secondary">
                                        ${renderStars(tour.rating)}
                                    </div>
                                </div>

                                <!-- Where this package is published -->
                                <div class="pt-3 border-t border-white/5 space-y-2">
                                    <p class="text-[9px] text-slate-500 uppercase font-bold tracking-wider">Appears on</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        ${tourPages(tour).length
                                            ? tourPages(tour).map(p => {
                                                const meta = PACKAGE_PAGES.find(x => x.key === p);
                                                return `<span class="px-2 py-1 rounded-md text-[10px] font-bold bg-primary/15 text-primary border border-primary/20">
                                                            <i class="fas ${meta ? meta.icon : 'fa-file'} mr-1"></i>${p}
                                                        </span>`;
                                              }).join('')
                                            : `<span class="px-2 py-1 rounded-md text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/20">
                                                   <i class="fas fa-eye-slash mr-1"></i>No page — hidden
                                               </span>`}
                                        ${tourHomeSections(tour).length ? `
                                            <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-secondary/15 text-secondary border border-secondary/20" title="Homepage: ${tourHomeSections(tour).map(k => (HOMEPAGE_SECTIONS.find(x => x.key === k) || {}).title).filter(Boolean).join(', ')}">
                                                <i class="fas fa-house mr-1"></i>Home
                                            </span>` : ''}
                                        ${tour.promo_badge ? `
                                            <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/20">
                                                <i class="fas fa-bullhorn mr-1"></i>${escHtml(tour.promo_badge)}
                                            </span>` : ''}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-5 border-t border-white/5">
                                <button onclick="window.viewTourPublic('${tour.id}')" class="text-xs font-bold text-slate-400 hover:text-white transition">
                                    <i class="fas fa-arrow-up-right-from-square mr-1.5"></i>View live
                                </button>
                                <div class="flex space-x-2">
                                    <button class="w-9 h-9 flex items-center justify-center bg-white/5 hover:bg-primary/10 rounded-xl text-slate-400 hover:text-primary transition" title="Edit" onclick="window.openTourModal(${tour.id})">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button class="w-9 h-9 flex items-center justify-center bg-white/5 hover:bg-rose-500/10 rounded-xl text-slate-400 hover:text-rose-500 transition" title="Delete" onclick="window.deleteTour(${tour.id})">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('') : `
                    <div class="col-span-full p-20 text-center glass-card rounded-3xl border border-white/5">
                        <i class="fas fa-route text-6xl text-slate-800 mb-6 block"></i>
                        <h3 class="text-xl font-bold text-white mb-2">No Packages Found</h3>
                        <p class="text-slate-500 mb-6">Create your first tour package to start selling!</p>
                        <button onclick="window.openTourModal()" class="bg-primary text-white px-8 py-3 rounded-2xl font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">Initialize New Package</button>
                    </div>
                `}
            </div>
        </div>
    `;
}

function renderDestinations() {
    const { destinations, tours } = state.data;
    const filteredDestinations = destinations.filter(d => 
        d.name.toLowerCase().includes(state.searchQuery)
    );

    // Grouping by Parent
    const parents = filteredDestinations.filter(d => !d.parent_id);
    
    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Destinations</h1>
                    <p class="text-slate-500 mt-1">Manage global regions and sub-locations</p>
                </div>
                <button onclick="window.openDestinationModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-map-marked-alt mr-2"></i> Add Destination
                </button>
            </div>

            <div class="space-y-12">
                ${parents.length > 0 ? parents.map(parent => {
                    const children = filteredDestinations.filter(d => d.parent_id == parent.id);
                    const count = tours.filter(t => t.location === parent.name).length;
                    
                    return `
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-map-marker-alt mr-3 text-primary"></i>
                                ${parent.name}
                                <span class="ml-3 text-[10px] font-black bg-white/5 px-2 py-1 rounded-md text-slate-500 uppercase tracking-widest">${parent.region}</span>
                            </h2>
                            <div class="h-px flex-1 bg-white/5"></div>
                            <div class="flex space-x-1">
                                <button class="w-8 h-8 flex items-center justify-center bg-white/5 hover:bg-primary/10 rounded-lg text-slate-400 hover:text-primary transition" title="Edit" onclick="window.openDestinationModal(${parent.id})">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center bg-white/5 hover:bg-rose-500/10 rounded-lg text-slate-400 hover:text-rose-500 transition" title="Delete" onclick="window.deleteDestination(${parent.id})">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Parent Card (Summary) -->
                            <div class="glass-card rounded-2xl overflow-hidden group border border-primary/20 bg-primary/5 transition-all duration-300">
                                <div class="p-5 h-full flex flex-col justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Primary Region</p>
                                        <h3 class="text-lg font-bold text-white mb-2">${parent.name}</h3>
                                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">${parent.description || 'Global hub for travel packages.'}</p>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-400">${count} tours active</span>
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Children Cards -->
                            ${children.map(child => {
                                const childCount = tours.filter(t => t.location === child.name).length;
                                return `
                                <div class="glass-card rounded-2xl overflow-hidden group border border-white/5 hover:border-primary/30 transition-all duration-300">
                                    <div class="h-24 bg-slate-800 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 to-transparent z-10"></div>
                                        <div class="absolute bottom-3 left-4 z-20">
                                            <h3 class="text-md font-bold text-white">${child.name}</h3>
                                        </div>
                                    </div>
                                    <div class="p-4 flex items-center justify-between border-t border-white/5">
                                        <span class="text-[10px] text-slate-500 font-bold uppercase">${childCount} Tours</span>
                                        <div class="flex space-x-1">
                                            <button class="w-7 h-7 flex items-center justify-center bg-white/5 hover:bg-primary/10 rounded-lg text-slate-400 hover:text-primary transition" onclick="window.openDestinationModal(${child.id})">
                                                <i class="fas fa-edit text-[10px]"></i>
                                            </button>
                                            <button class="w-7 h-7 flex items-center justify-center bg-white/5 hover:bg-rose-500/10 rounded-lg text-slate-400 hover:text-rose-500 transition" onclick="window.deleteDestination(${child.id})">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                `;
                            }).join('')}
                            
                            <!-- Quick Add Child -->
                            <button onclick="window.openDestinationModal()" class="glass-card rounded-2xl border-2 border-dashed border-white/5 flex flex-col items-center justify-center p-6 text-slate-600 hover:text-primary hover:border-primary/50 transition group min-h-[160px]">
                                <i class="fas fa-plus-circle text-2xl mb-2 group-hover:scale-110 transition"></i>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Add Sub-Location</span>
                            </button>
                        </div>
                    </div>
                    `;
                }).join('') : `
                    <div class="col-span-full py-16 text-center glass-card rounded-3xl border border-white/5">
                        <i class="fas fa-globe-africa text-5xl text-slate-800 mb-4 block"></i>
                        <h3 class="text-lg font-bold text-white mb-1">Destination Not Found</h3>
                        <p class="text-slate-500 text-sm">Try adding a primary destination first</p>
                    </div>
                `}
            </div>
        </div>
    `;
}

// Normalise a booking row. Newer rows have structured columns; rows saved
// before migrate.php packed everything into the tour name as
// "[Booking] Title | Phone: x | Travelers: N | Note: y".
function bookingInfo(b) {
    const info = {
        type: b.type || 'Booking',
        tour: b.tour || '',
        phone: b.phone || '',
        travelers: b.travelers || null,
        note: b.message || '',
    };
    const m = info.tour.match(/^\[(Booking|Enquiry)\]\s*/);
    if (m) {
        info.type = m[1];
        const parts = info.tour.slice(m[0].length).split('|').map(s => s.trim());
        info.tour = parts.shift();
        parts.forEach(seg => {
            if (seg.startsWith('Phone:')) info.phone = seg.slice(6).trim();
            else if (seg.startsWith('Travelers:')) info.travelers = parseInt(seg.slice(10), 10) || null;
            else if (seg.startsWith('Note:')) info.note = seg.slice(5).trim();
        });
    }
    return info;
}

function bookingMoney(n) {
    return '$' + Number(n || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });
}

function bookingDate(d) {
    if (!d) return '—';
    const x = new Date(String(d).replace(' ', 'T'));
    return isNaN(x) ? escHtml(d) : x.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

// Enquiries reuse the status column: Pending = open, Cancelled = closed.
function bookingStatusLabel(status, type) {
    if (type === 'Enquiry') return { Pending: 'Open', Cancelled: 'Closed', Confirmed: 'Answered' }[status] || status;
    return { Pending: 'Awaiting confirmation' }[status] || status;
}

// Pending-count badge on the Bookings menu item, refreshed with every render.
function updateBookingBadge() {
    const link = document.querySelector('.sidebar-link[data-tab="bookings"]');
    if (!link) return;
    const n = (state.data.bookings || []).filter(b => b.status === 'Pending').length;
    let badge = link.querySelector('.booking-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'booking-badge ml-auto min-w-5 h-5 px-1.5 rounded-full bg-amber-400 text-dark-900 text-[10px] font-black flex items-center justify-center';
        link.appendChild(badge);
    }
    badge.textContent = n > 99 ? '99+' : String(n);
    badge.style.display = n ? '' : 'none';
    badge.title = n + ' awaiting action';
}

function renderBookings() {
    const all = (state.data.bookings || []).map(b => ({ ...b, info: bookingInfo(b) }));
    const q = state.searchQuery;
    const list = all.filter(b => {
        const hay = [b.user, b.email, b.info.tour, b.id, b.info.phone].join(' ').toLowerCase();
        return (!q || hay.includes(q))
            && (state.bookingFilter === 'All' || b.status === state.bookingFilter)
            && (state.bookingType === 'All' || b.info.type === state.bookingType);
    });

    const pending = all.filter(b => b.status === 'Pending');
    const stats = {
        awaiting: pending.filter(b => b.info.type === 'Booking').length,
        enquiries: pending.filter(b => b.info.type === 'Enquiry').length,
        pipeline: pending.filter(b => b.info.type === 'Booking').reduce((s, b) => s + Number(b.amount || 0), 0),
        revenue: all.filter(b => b.status === 'Confirmed' && b.info.type === 'Booking').reduce((s, b) => s + Number(b.amount || 0), 0),
    };
    const pill = (active, label, onclick) => `
        <button onclick="${onclick}" class="px-4 py-1.5 rounded-lg text-xs font-bold transition ${active ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-white'}">${label}</button>`;

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">Bookings</h1>
                    <p class="text-slate-500 mt-1">Requests from the website land here as pending. Review each one and confirm or decline it.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center space-x-1 bg-slate-800/50 p-1 rounded-xl border border-white/5">
                        ${['All', 'Booking', 'Enquiry'].map(t => pill(state.bookingType === t, t === 'All' ? 'All types' : t === 'Booking' ? 'Bookings' : 'Enquiries', `window.setBookingType('${t}')`)).join('')}
                    </div>
                    <div class="flex items-center space-x-1 bg-slate-800/50 p-1 rounded-xl border border-white/5">
                        ${['All', 'Pending', 'Confirmed', 'Cancelled'].map(f => pill(state.bookingFilter === f, f, `window.setBookingFilter('${f}')`)).join('')}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Awaiting Confirmation</p>
                    <p class="text-2xl font-bold text-amber-400">${stats.awaiting}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Open Enquiries</p>
                    <p class="text-2xl font-bold text-sky-400">${stats.enquiries}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Pending Value (est.)</p>
                    <p class="text-2xl font-bold">${bookingMoney(stats.pipeline)}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Confirmed Revenue</p>
                    <p class="text-2xl font-bold text-emerald-400">${bookingMoney(stats.revenue)}</p>
                </div>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl overflow-x-auto">
                <table class="w-full admin-table text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Guest</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Trip</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Amount</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Status</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${list.length ? list.map(b => {
                            const isNew = b.status === 'Pending' && b.created_at && (Date.now() - new Date(String(b.created_at).replace(' ', 'T'))) < 864e5;
                            const id = escHtml(b.id);
                            return `
                            <tr class="border-t border-white/5 hover:bg-white/5 transition group cursor-pointer" onclick="window.viewBookingDetails('${id}')">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 rounded-full bg-primary/20 flex items-center justify-center mr-3 shrink-0">
                                            <span class="text-xs font-bold text-primary">${escHtml((b.user || '?').charAt(0).toUpperCase())}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-white group-hover:text-primary transition">${escHtml(b.user)}${isNew ? ' <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-400 text-dark-900 text-[9px] font-black align-middle">NEW</span>' : ''}</div>
                                            <div class="text-[11px] text-slate-500">${escHtml(b.email)}</div>
                                            <div class="text-[10px] text-slate-600 font-mono">${id}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase ${b.info.type === 'Enquiry' ? 'bg-sky-400/10 text-sky-400' : 'bg-amber-400/10 text-amber-400'}">${b.info.type}</span>
                                        <span class="text-sm font-medium">${escHtml(b.info.tour)}</span>
                                    </div>
                                    <div class="text-xs text-slate-500">${bookingDate(b.date)}${b.info.travelers ? ` · ${Number(b.info.travelers)} traveller${b.info.travelers > 1 ? 's' : ''}` : ''}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-bold text-secondary">${bookingMoney(b.amount)}</div>
                                    ${b.status === 'Pending' && b.info.type === 'Booking' ? '<div class="text-[10px] text-slate-500">estimate</div>' : ''}
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold ${getBookingStatusClass(b.status)}">
                                        <i class="fas fa-circle mr-1.5 text-[6px]"></i>${bookingStatusLabel(b.status, b.info.type)}
                                    </span>
                                </td>
                                <td class="p-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="window.viewBookingDetails('${id}')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition ${b.status === 'Pending' ? 'bg-primary text-white hover:bg-primary/80' : 'bg-white/5 text-slate-300 hover:bg-white/10'}">
                                            ${b.status === 'Pending' ? 'Review' : 'View'}
                                        </button>
                                        <button onclick="window.deleteBooking('${id}')" class="p-2 hover:bg-rose-500/20 text-rose-500 rounded-lg transition" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                        }).join('') : `
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-slate-700 mb-4"></i>
                                        <p class="text-slate-500 font-bold">No bookings match these filters</p>
                                        <button onclick="window.setBookingFilter('All'); window.setBookingType('All')" class="text-primary text-sm mt-2 hover:underline">Clear filters</button>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderCustomers() {
    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Customers Management</h1>
                    <p class="text-slate-500 mt-1">Manage your registered travelers and client profiles</p>
                </div>
                <button onclick="window.openCustomerModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-user-plus mr-2"></i> Add Client
                </button>
            </div>

            ${(() => {
                const subs = state.data.newsletter || [];
                const recent = subs.slice(0, 4);
                return `
            <div class="glass-card rounded-2xl p-6 border border-white/5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/15 text-primary flex items-center justify-center"><i class="fas fa-envelope-open-text"></i></div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Newsletter subscribers</p>
                        <p class="text-2xl font-bold">${subs.length}</p>
                        ${recent.length ? `<p class="text-xs text-slate-500 mt-1">Latest: ${recent.map(r => escHtml(r.email)).join(', ')}</p>` : '<p class="text-xs text-slate-500 mt-1">Sign-ups from the website footer appear here.</p>'}
                    </div>
                </div>
                ${subs.length ? `<a href="api/export-subscribers.php" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-bold transition"><i class="fas fa-download mr-2"></i>Download CSV</a>` : ''}
            </div>`;
            })()}

            <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
                <table class="w-full admin-table text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">ID</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Client Info</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Location</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500 text-center">Bookings</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Joined</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${state.data.customers
                            .filter(c => c.name.toLowerCase().includes(state.searchQuery) || c.email.toLowerCase().includes(state.searchQuery))
                            .map(cust => `
                            <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                                <td class="p-4 text-xs font-mono text-slate-500">${cust.id}</td>
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-primary">${escHtml(String(cust.name || '?').charAt(0))}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white group-hover:text-primary transition">${escHtml(cust.name)}</div>
                                            <div class="text-[10px] text-slate-500 font-medium tracking-tight">${escHtml(cust.email)}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm">${escHtml(cust.country || 'Not Set')}</td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-md bg-white/5 text-xs font-bold">${cust.bookings || 0}</span>
                                </td>
                                <td class="p-4 text-xs text-slate-500 font-bold opacity-80">${cust.joined}</td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition">
                                        <button onclick="window.openCustomerModal('${cust.id}')" class="p-2 hover:bg-primary/10 text-slate-400 hover:text-primary rounded-lg transition" title="Edit Profile">
                                            <i class="fas fa-user-edit text-xs"></i>
                                        </button>
                                        <button onclick="window.deleteCustomer('${cust.id}')" class="p-2 hover:bg-rose-500/10 text-slate-400 hover:text-rose-500 rounded-lg transition" title="Delete Client">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function renderAnalytics() {
    console.log('Rendering Analytics & Reports...');
    try {
        const { analytics, bookings, tours } = state.data;
        
        // Every figure here comes from the server's computed analytics. Values
        // that cannot be derived from real rows render as "—" rather than a
        // plausible-looking invention.
        const money = (v) => v === null || v === undefined
            ? '—'
            : '$' + Number(v).toLocaleString(undefined, { maximumFractionDigits: 0 });
        const pct = (v) => v === null || v === undefined ? '—' : `${v}%`;

        contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Analytics & Reports</h1>
                    <p class="text-slate-500 mt-1">Deep dive into your agency performance and trends</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl text-sm font-semibold border border-white/10 transition flex items-center">
                        <i class="fas fa-download mr-2"></i> Export Report
                    </button>
                    <div class="h-10 w-px bg-white/10 mx-2"></div>
                    <select class="bg-slate-800 border border-white/10 rounded-xl px-4 py-2 text-sm outline-none focus:border-primary">
                        <option>Last 6 Months</option>
                        <option>Last 12 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>

            <!-- KPI Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                ${renderAnalyticsStatCard('Total Revenue', money(analytics.totalRevenue), 'fa-dollar-sign', 'text-emerald-400', 'From confirmed bookings')}
                ${renderAnalyticsStatCard('Confirmation Rate', pct(analytics.confirmationRate), 'fa-bullseye', 'text-amber-400', `${analytics.confirmedBookings} of ${analytics.totalBookings} bookings`)}
                ${renderAnalyticsStatCard('Avg. Booking Value', money(analytics.avgBookingValue), 'fa-receipt', 'text-blue-400', 'Per confirmed booking')}
                ${renderAnalyticsStatCard('Revenue / Customer', money(analytics.revenuePerCustomer), 'fa-user-graduate', 'text-purple-400', `${pct(analytics.repeatRate)} book again`)}
            </div>

            <!-- Main Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold">Revenue Growth</h2>
                            <p class="text-xs text-slate-500">Monthly financial performance tracking</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-primary mr-2"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Revenue</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="analyticsRevenueChart"></canvas>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-2">Regional Distribution</h2>
                    <p class="text-xs text-slate-500 mb-8">Bookings by destination region</p>
                    <div class="h-64 w-full mb-6">
                        <canvas id="bookingsDoughnut"></canvas>
                    </div>
                    <div class="space-y-3">
                        ${regionLegend().map(item => `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 rounded-full ${item.color} mr-3"></span>
                                    <span class="text-xs font-medium text-slate-400">${item.label}</span>
                                </div>
                                <span class="text-xs font-bold">${item.value}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>

            <!-- Advanced Metrics & Popular Tours -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-white/5 flex items-center justify-between">
                        <h2 class="text-xl font-bold">Top Performing Tours</h2>
                        <button class="text-xs font-bold text-primary hover:underline">View All</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">Tour Name</th>
                                    <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">Bookings</th>
                                    <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">Revenue</th>
                                    <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">Share of Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                ${analytics.popularTours.map(tour => `
                                    <tr class="hover:bg-white/5 transition group">
                                        <td class="p-4 flex items-center">
                                            <div class="w-8 h-8 rounded-lg bg-primary/10 mr-3 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <i class="fas fa-route text-primary text-[10px]"></i>
                                            </div>
                                            <span class="text-sm font-bold group-hover:text-primary transition truncate max-w-[150px]">${escHtml(tour.name)}</span>
                                        </td>
                                        <td class="p-4 text-sm font-medium">${tour.bookings}</td>
                                        <td class="p-4 text-sm font-bold text-secondary">$${parseFloat(tour.revenue || 0).toLocaleString()}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-16 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                                    <div class="h-full bg-primary rounded-full" style="width: ${revenueShare(tour)}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-slate-400">${revenueShare(tour)}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Booking pipeline. This panel previously showed traffic
                     source percentages, but the site records no traffic data,
                     so those figures could only ever be invented. -->
                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-1">Booking Pipeline</h2>
                    <p class="text-xs text-slate-500 mb-8">Where your bookings currently stand</p>
                    <div class="space-y-8">
                        ${bookingPipeline().map(stage => `
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-400 uppercase tracking-widest">${stage.label}</span>
                                    <span class="font-bold">${stage.count} <span class="text-slate-600 font-medium">(${stage.percent}%)</span></span>
                                </div>
                                <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="${stage.color} h-full rounded-full transition-all duration-500" style="width: ${stage.percent}%"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="mt-12 p-4 rounded-xl bg-primary/5 border border-primary/10">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-lightbulb text-primary mt-1"></i>
                            <div>
                                <h4 class="text-sm font-bold text-white">${pipelineInsight().title}</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">${pipelineInsight().body}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        setTimeout(() => {
            try {
                initAnalyticsCharts();
            } catch (e) {
                console.error('Analytics chart init failed:', e);
            }
        }, 0);
    } catch (error) {
        console.error('Error rendering analytics:', error);
        contentArea.innerHTML = `<div class="p-8 text-rose-500">Error: ${error.message}</div>`;
    }
}

// ── Analytics helpers ─────────────────────────────────────────────────────────
// All three derive from state.data, so the numbers on screen always agree with
// the charts beside them.

// Legend for the regional doughnut, computed from the same destination counts
// the chart itself uses. It previously listed fixed percentages that had no
// relationship to the chart.
function regionLegend() {
    const counts = {};
    (state.data.destinations || []).forEach(d => {
        if (!d.parent_id && d.region) counts[d.region] = (counts[d.region] || 0) + 1;
    });

    const total = Object.values(counts).reduce((a, b) => a + b, 0);
    if (!total) return [];

    const palette = ['bg-[#006A72]', 'bg-[#FFD214]', 'bg-[#10B981]', 'bg-[#1E293B]', 'bg-[#8B5CF6]', 'bg-[#F43F5E]'];

    return Object.entries(counts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 6)
        .map(([label, n], i) => ({
            label,
            value: `${Math.round((n / total) * 100)}%`,
            color: palette[i % palette.length]
        }));
}

// A tour's share of total booking revenue — a real, stable comparison, unlike
// the Math.random() trend that changed on every render.
function revenueShare(tour) {
    const total = (state.data.analytics.popularTours || [])
        .reduce((sum, t) => sum + parseFloat(t.revenue || 0), 0);
    if (!total) return 0;
    return Math.round((parseFloat(tour.revenue || 0) / total) * 100);
}

function bookingPipeline() {
    const bookings = state.data.bookings || [];
    const total = bookings.length;
    const stages = [
        { label: 'Confirmed', status: 'Confirmed', color: 'bg-emerald-400' },
        { label: 'Pending',   status: 'Pending',   color: 'bg-secondary' },
        { label: 'Cancelled', status: 'Cancelled', color: 'bg-rose-500' },
    ];
    return stages.map(s => {
        const count = bookings.filter(b => b.status === s.status).length;
        return { ...s, count, percent: total ? Math.round((count / total) * 100) : 0 };
    });
}

// An observation drawn from the pipeline, or an honest note when there is not
// enough data to say anything.
function pipelineInsight() {
    const bookings = state.data.bookings || [];
    if (!bookings.length) {
        return { title: 'No bookings yet', body: 'Once bookings come in, this panel will highlight where they are getting stuck.' };
    }

    const pending = bookings.filter(b => b.status === 'Pending').length;
    const cancelled = bookings.filter(b => b.status === 'Cancelled').length;
    const pendingValue = bookings
        .filter(b => b.status === 'Pending')
        .reduce((sum, b) => sum + Number(b.amount || 0), 0);

    if (pending > 0) {
        return {
            title: `${pending} booking${pending === 1 ? '' : 's'} awaiting confirmation`,
            body: `That is $${pendingValue.toLocaleString()} of revenue not yet confirmed. Confirming these moves them into your reported totals.`
        };
    }
    if (cancelled / bookings.length > 0.2) {
        return {
            title: 'Cancellation rate is above 20%',
            body: `${cancelled} of ${bookings.length} bookings were cancelled. Worth reviewing which packages they came from.`
        };
    }
    return {
        title: 'Pipeline is clear',
        body: 'No bookings are waiting on confirmation. Every booking has been actioned.'
    };
}

// `note` describes how the figure was derived. It replaced a trend badge that
// rendered a fixed percentage regardless of the underlying data.
function renderAnalyticsStatCard(title, value, icon, iconColor, note) {
    return `
        <div class="glass-card rounded-2xl p-6 border border-white/5 hover:border-white/10 transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-dark-900 flex items-center justify-center">
                    <i class="fas ${icon} ${iconColor} text-lg"></i>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-1">${title}</p>
                <p class="text-2xl font-bold">${value}</p>
                ${note ? `<p class="text-[10px] text-slate-600 mt-1.5">${note}</p>` : ''}
            </div>
        </div>
    `;
}


function renderFinance() {
    const { finance } = state.data;
    const currentTab = state.financeTab;

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Finance Management</h1>
                    <p class="text-slate-500 mt-1">Manage quotations, invoices and company expenses</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="window.openQuotationModal()" class="bg-primary/10 text-primary px-4 py-2 rounded-xl text-xs font-bold hover:bg-primary hover:text-white transition">
                        <i class="fas fa-file-contract mr-2"></i> New Quote
                    </button>
                    <button onclick="window.openExpenseModal()" class="bg-rose-500/10 text-rose-500 px-4 py-2 rounded-xl text-xs font-bold hover:bg-rose-500 hover:text-white transition">
                        <i class="fas fa-receipt mr-2"></i> Record Expense
                    </button>
                </div>
            </div>

            <!-- Finance Sub-tabs -->
            <div class="flex items-center space-x-6 border-b border-white/5">
                ${['quotations', 'invoices', 'expenses'].map(tab => `
                    <button onclick="window.setFinanceTab('${tab}')" class="pb-4 px-2 text-sm font-bold uppercase tracking-wider transition relative ${currentTab === tab ? 'text-primary' : 'text-slate-500 hover:text-slate-300'}">
                        ${tab}
                        ${currentTab === tab ? '<div class="absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-full"></div>' : ''}
                    </button>
                `).join('')}
            </div>

            <div id="finance-content">
                ${currentTab === 'quotations' ? renderQuotations(finance.quotations) : 
                  currentTab === 'invoices' ? renderInvoices(finance.invoices) : 
                  renderExpenses(finance.expenses)}
            </div>
        </div>
    `;
}

window.setFinanceTab = (tab) => {
    state.financeTab = tab;
    renderFinance();
};

function renderQuotations(quotes) {
    return `
        <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
            <table class="w-full admin-table text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">ID</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Customer</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Tour Package</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Amount</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Status</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${quotes.map(q => `
                        <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                            <td class="p-4 text-xs font-mono text-slate-500">${q.id}</td>
                            <td class="p-4 font-bold text-sm text-white">${escHtml(q.customer_name)}</td>
                            <td class="p-4 text-sm text-slate-400">${escHtml(q.tour_name)}</td>
                            <td class="p-4 font-bold text-emerald-400">$${parseFloat(q.amount).toLocaleString()}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase ${getQuotationStatusClass(q.status)}">${q.status}</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="window.openQuotationModal('${q.id}')" class="p-2 hover:bg-primary/10 text-slate-400 hover:text-primary rounded-lg transition" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                                    <button onclick="window.location.href='view-quotation.php?id=${q.id}'" class="p-2 hover:bg-white/10 text-slate-400 hover:text-white rounded-lg transition" title="View & Download PDF"><i class="fas fa-print text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderInvoices(invoices) {
    return `
        <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
            <table class="w-full admin-table text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Invoice #</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Customer</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Due Date</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Amount</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Status</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${invoices.map(i => `
                        <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                            <td class="p-4 text-xs font-mono text-slate-500">${i.id}</td>
                            <td class="p-4 font-bold text-sm text-white">${escHtml(i.customer_name)}</td>
                            <td class="p-4 text-sm text-slate-400">${i.due_date}</td>
                            <td class="p-4 font-bold text-secondary">$${parseFloat(i.amount).toLocaleString()}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase ${getInvoiceStatusClass(i.status)}">${i.status}</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="window.location.href='view-invoice.php?id=${i.id}'" class="p-2 hover:bg-primary/10 text-slate-400 hover:text-primary rounded-lg transition" title="View & Download PDF"><i class="fas fa-eye text-xs"></i></button>
                                    ${i.status !== 'Paid' ? `<button onclick="window.markInvoicePaid('${i.id}')" class="p-2 hover:bg-emerald-500/10 text-slate-400 hover:text-emerald-500 rounded-lg transition" title="Mark as Paid"><i class="fas fa-check-double text-xs"></i></button>` : ''}
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderExpenses(expenses) {
    return `
        <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
            <table class="w-full admin-table text-left border-collapse">
                <thead>
                    <tr class="bg-white/5">
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Date</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Category</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Description</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500">Amount</th>
                        <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${expenses.map(e => `
                        <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                            <td class="p-4 text-sm text-slate-400">${e.expense_date}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-white/5 text-slate-300 border border-white/5">${e.category}</span>
                            </td>
                            <td class="p-4 text-sm text-white">${e.description}</td>
                            <td class="p-4 font-bold text-rose-500">-$${parseFloat(e.amount).toLocaleString()}</td>
                            <td class="p-4 text-right">
                                <button onclick="window.deleteExpense(${e.id})" class="p-2 hover:bg-rose-500/10 text-slate-400 hover:text-rose-500 rounded-lg transition"><i class="fas fa-trash-alt text-xs"></i></button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function getQuotationStatusClass(status) {
    switch (status) {
        case 'Accepted': return 'bg-emerald-500/10 text-emerald-500';
        case 'Sent': return 'bg-blue-500/10 text-blue-500';
        case 'Expired': return 'bg-rose-500/10 text-rose-500';
        default: return 'bg-slate-500/10 text-slate-500';
    }
}

function getInvoiceStatusClass(status) {
    switch (status) {
        case 'Paid': return 'bg-emerald-500/10 text-emerald-500';
        case 'Overdue': return 'bg-rose-500/10 text-rose-500';
        case 'Unpaid': return 'bg-amber-400/10 text-amber-400';
        default: return 'bg-blue-500/10 text-blue-500';
    }
}

function renderSettings() {
    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in">
            <h1 class="text-3xl font-bold">System Settings</h1>
            <div class="max-w-xl space-y-6">
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <h3 class="font-bold">General Settings</h3>
                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Agency Name</label>
                        <input type="text" value="Luxit Global Escapes" class="w-full bg-dark-900 border border-white/10 rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                </div>
            </div>
        </div>
    `;
}

// ── Blog Management ───────────────────────────────────────────────────────────

function renderBlogs() {
    const allPosts = state.data.blogPosts || [];
    const blogs = allPosts.filter(b =>
        String(b.title || '').toLowerCase().includes(state.searchQuery) ||
        (b.author || '').toLowerCase().includes(state.searchQuery) ||
        (b.category || '').toLowerCase().includes(state.searchQuery)
    );

    const published = allPosts.filter(b => b.status === 'Published').length;
    const drafts    = allPosts.filter(b => b.status === 'Draft').length;

    // Without this table nothing can save and nothing reaches the public site.
    // Say so explicitly rather than rendering an empty tab that looks normal.
    const tableMissing = (state.data.missingTables || []).includes('blog_posts');
    const schemaWarning = tableMissing ? `
        <div class="rounded-2xl p-5 bg-rose-500/10 border border-rose-500/40 text-rose-300 space-y-2">
            <p class="font-bold flex items-center"><i class="fas fa-triangle-exclamation mr-2"></i> The blog_posts table is missing from your database</p>
            <p class="text-sm text-rose-300/80">Posts cannot be saved and nothing will appear on the public site until it exists. Run <code class="bg-black/30 px-1.5 py-0.5 rounded font-mono">migrate.php</code> in your site root to create it — your existing bookings, customers and tours are not affected.</p>
        </div>` : '';

    const img = (src) => escHtml(String(src).startsWith('assets') ? '../' + src : src);

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            ${schemaWarning}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">Blog Posts</h1>
                    <p class="text-slate-500 mt-1">Published posts appear on the Blog page straight away, and the latest three on the homepage.</p>
                </div>
                <button onclick="window.openBlogModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-pen-nib mr-2"></i> Write New Post
                </button>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Total Posts</p>
                    <p class="text-3xl font-black text-white">${allPosts.length}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Live on Website</p>
                    <p class="text-3xl font-black text-emerald-400">${published}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Drafts</p>
                    <p class="text-3xl font-black text-amber-400">${drafts}</p>
                </div>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl overflow-x-auto">
                ${blogs.length > 0 ? `
                <table class="w-full admin-table text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Post</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Author</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Category</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Status</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Date</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${blogs.map(b => `
                        <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    ${b.image ? `<img src="${img(b.image)}" alt="" class="w-14 h-14 rounded-xl object-cover border border-white/10 flex-shrink-0">` : `<div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-newspaper text-primary text-sm"></i></div>`}
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-white leading-tight line-clamp-2 max-w-sm">${escHtml(b.title)}</p>
                                        ${Number(b.featured) === 1 ? '<span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded bg-amber-400/15 text-amber-400 text-[10px] font-bold"><i class="fas fa-star text-[8px]"></i> Featured</span>' : ''}
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-slate-400">${escHtml(b.author || '—')}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-primary/10 text-primary">${escHtml(b.category || '—')}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase ${b.status === 'Published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-400/10 text-amber-400'}">${b.status === 'Published' ? 'Live' : 'Draft'}</span>
                            </td>
                            <td class="p-4 text-xs text-slate-500">${bookingDate(b.created_at)}</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    ${b.status === 'Published' ? `<a href="../blog-detail.php?slug=${encodeURIComponent(b.slug || '')}" target="_blank" rel="noopener" class="p-2 hover:bg-white/10 text-slate-400 hover:text-white rounded-lg transition" title="View on website"><i class="fas fa-arrow-up-right-from-square text-xs"></i></a>` : ''}
                                    <button onclick="window.openBlogModal(${Number(b.id)})" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-primary/20 text-slate-300 hover:text-primary text-xs font-bold transition">Edit</button>
                                    <button onclick="window.deleteBlog(${Number(b.id)})" class="p-2 hover:bg-rose-500/10 text-slate-400 hover:text-rose-500 rounded-lg transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        `).join('')}
                    </tbody>
                </table>
                ` : `
                <div class="py-20 text-center">
                    <i class="fas fa-newspaper text-6xl text-slate-800 mb-6 block"></i>
                    <h3 class="text-xl font-bold text-white mb-2">${allPosts.length ? 'No posts match your search' : 'No Blog Posts Yet'}</h3>
                    <p class="text-slate-500 mb-6">Start writing to engage your audience and drive traffic to your site.</p>
                    <button onclick="window.openBlogModal()" class="bg-primary text-white px-8 py-3 rounded-2xl font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">Write a Post</button>
                </div>
                `}
            </div>
        </div>
    `;
}

// Strip anything executable before older post HTML is loaded into the editor
// (innerHTML does not run <script>, but it does run onerror= and friends).
function sanitizeForEditor(html) {
    const doc = new DOMParser().parseFromString(`<div>${html || ''}</div>`, 'text/html');
    doc.querySelectorAll('script,style,iframe,object,embed,form,link,meta').forEach(n => n.remove());
    doc.querySelectorAll('*').forEach(el => {
        [...el.attributes].forEach(a => {
            const v = a.value.replace(/\s+/g, '').toLowerCase();
            if (a.name.startsWith('on') || ((a.name === 'href' || a.name === 'src') && v.startsWith('javascript:'))) el.removeAttribute(a.name);
        });
    });
    return doc.body.firstChild.innerHTML;
}

const BLOG_CATEGORIES = ['Travel Tips', 'Safari', 'Destinations', 'Culture', 'Planning', 'Photography', 'Adventure', 'Luxury', 'Food & Cuisine', 'News'];

window.openBlogModal = (blogId = null) => {
    const blog = blogId ? (state.data.blogPosts || []).find(b => Number(b.id) === Number(blogId)) : null;
    const categories = [...new Set([...BLOG_CATEGORIES, ...(state.data.blogPosts || []).map(b => b.category).filter(Boolean)])];
    const isPublished = !blog || blog.status === 'Published';
    const imgSrc = blog && blog.image ? (blog.image.startsWith('assets') ? '../' + blog.image : blog.image) : '';
    const tool = (cmd, icon, label, arg = '') => `
        <button type="button" data-cmd="${cmd}" data-arg="${arg}" title="${label}" aria-label="${label}"
                class="h-8 min-w-8 px-2 rounded-lg text-slate-300 hover:bg-white/10 hover:text-white transition text-xs font-bold">${icon}</button>`;

    modalOverlay.classList.remove('hidden');
    modalContent.classList.replace('max-w-2xl', 'max-w-4xl'); // room to write; closeModal restores it
    modalContent.innerHTML = `
        <form id="blog-form" class="p-8 space-y-6 max-h-[90vh] overflow-y-auto custom-scroll w-full max-w-4xl">
            <div class="flex items-center justify-between sticky top-0 bg-dark-800 z-10 pb-4 border-b border-white/5">
                <div>
                    <h2 class="text-2xl font-bold">${blog ? 'Edit Post' : 'Write a New Post'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Write it here as you would in a document. Publish to put it on the website, or save a draft.</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white" aria-label="Close"><i class="fas fa-times text-xl"></i></button>
            </div>

            <div class="space-y-2">
                <label for="bf-title" class="text-xs text-slate-500 uppercase font-bold tracking-wider">Title</label>
                <input id="bf-title" type="text" name="title" required maxlength="200" value="${blog ? escHtml(blog.title) : ''}"
                    class="w-full bg-dark-900 border border-white/10 rounded-xl p-4 outline-none focus:border-primary transition text-lg font-bold"
                    placeholder="e.g. When to Visit the Maasai Mara for the Great Migration">
            </div>

            <div class="space-y-2">
                <div class="flex items-end justify-between">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Article</label>
                    <span id="be-count" class="text-[11px] text-slate-500"></span>
                </div>
                <div class="rounded-xl border border-white/10 bg-dark-900 focus-within:border-primary transition overflow-hidden">
                    <div id="be-toolbar" class="flex flex-wrap items-center gap-1 p-2 border-b border-white/10 bg-white/5 sticky top-0">
                        ${tool('formatBlock', 'Text', 'Normal paragraph', 'P')}
                        ${tool('formatBlock', 'H2', 'Section heading', 'H2')}
                        ${tool('formatBlock', 'H3', 'Sub-heading', 'H3')}
                        <span class="w-px h-5 bg-white/10 mx-1"></span>
                        ${tool('bold', '<i class="fas fa-bold"></i>', 'Bold')}
                        ${tool('italic', '<i class="fas fa-italic"></i>', 'Italic')}
                        <span class="w-px h-5 bg-white/10 mx-1"></span>
                        ${tool('insertUnorderedList', '<i class="fas fa-list-ul"></i>', 'Bullet list')}
                        ${tool('insertOrderedList', '<i class="fas fa-list-ol"></i>', 'Numbered list')}
                        ${tool('formatBlock', '<i class="fas fa-quote-right"></i>', 'Quote', 'BLOCKQUOTE')}
                        <span class="w-px h-5 bg-white/10 mx-1"></span>
                        ${tool('createLink', '<i class="fas fa-link"></i>', 'Add link')}
                        ${tool('unlink', '<i class="fas fa-link-slash"></i>', 'Remove link')}
                        ${tool('removeFormat', '<i class="fas fa-eraser"></i>', 'Clear formatting')}
                    </div>
                    <div id="be-editor" class="blog-editor" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Article"
                         data-placeholder="Start writing your article. Use H2 for section headings; they become the article's contents list on the website."></div>
                </div>
                <input type="hidden" name="content" id="be-content">
                <p class="text-[10px] text-slate-500">Pasting from Word or Google Docs brings the text in as clean paragraphs; add headings and bold with the toolbar.</p>
            </div>

            <div class="space-y-2">
                <div class="flex items-end justify-between">
                    <label for="bf-excerpt" class="text-xs text-slate-500 uppercase font-bold tracking-wider">Summary <span class="text-slate-600 normal-case">(optional, shown on cards and in Google)</span></label>
                    <span id="bf-excerpt-count" class="text-[11px] text-slate-500"></span>
                </div>
                <textarea id="bf-excerpt" name="excerpt" rows="2" maxlength="400" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition resize-none text-sm" placeholder="Leave empty and we'll use the first lines of the article.">${blog ? escHtml(blog.excerpt || '') : ''}</textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 md:row-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Cover Photo</label>
                    <div id="blog-image-dropzone" class="relative group cursor-pointer border-2 border-dashed border-white/10 rounded-2xl p-4 hover:border-primary/50 transition bg-dark-900/50">
                        <input type="file" id="blog-image-input" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <div id="blog-image-preview-placeholder" class="flex flex-col items-center justify-center space-y-2 py-6 ${imgSrc ? 'hidden' : ''}">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary"><i class="fas fa-cloud-upload-alt text-xl"></i></div>
                            <p class="text-xs font-bold text-slate-400">Click or drag a photo here</p>
                            <p class="text-[10px] text-slate-600">Landscape works best. Large photos are resized automatically.</p>
                        </div>
                        <div id="blog-image-preview" class="relative ${imgSrc ? '' : 'hidden'}">
                            <img src="${escHtml(imgSrc)}" id="blog-preview-img" alt="" class="w-full h-44 object-cover rounded-xl border border-white/5">
                            <button type="button" id="blog-remove-img" class="absolute top-2 right-2 bg-rose-500 text-white size-8 rounded-lg shadow-xl flex items-center justify-center" aria-label="Remove photo">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="image" id="blog-image-hidden" value="${blog ? escHtml(blog.image || '') : ''}">
                    <input type="hidden" name="image_removed" id="blog-image-removed" value="">
                </div>
                <div class="space-y-2">
                    <label for="bf-category" class="text-xs text-slate-500 uppercase font-bold tracking-wider">Category</label>
                    <input id="bf-category" name="category" list="bf-category-list" maxlength="100" value="${escHtml(blog ? blog.category || '' : 'Travel Tips')}"
                           class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="Choose or type a new one">
                    <datalist id="bf-category-list">${categories.map(c => `<option value="${escHtml(c)}">`).join('')}</datalist>
                </div>
                <div class="space-y-2">
                    <label for="bf-author" class="text-xs text-slate-500 uppercase font-bold tracking-wider">Author</label>
                    <input id="bf-author" type="text" name="author" maxlength="100" value="${escHtml(blog ? blog.author || '' : (window.ADMIN_USER || 'Luxit Global Escapes'))}"
                        class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition">
                </div>
                <div class="space-y-2">
                    <label for="bf-tags" class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tags <span class="text-slate-600 normal-case">(optional, comma-separated)</span></label>
                    <input id="bf-tags" type="text" name="tags" maxlength="255" value="${blog ? escHtml(blog.tags || '') : ''}"
                        class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="safari, kenya, wildlife">
                </div>
                <label class="flex items-start gap-3 p-3.5 bg-dark-900 border border-white/10 rounded-xl cursor-pointer">
                    <input type="checkbox" name="featured" class="mt-1 accent-amber-400" ${blog && Number(blog.featured) === 1 ? 'checked' : ''}>
                    <span>
                        <span class="block text-xs font-bold text-white"><i class="fas fa-star text-amber-400 mr-1"></i> Feature this post</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Shown as the lead story on the Blog page and first on the homepage. Replaces the current featured post.</span>
                    </span>
                </label>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-6 border-t border-white/5 sticky bottom-0 bg-dark-800 z-10">
                <div class="flex p-1 bg-dark-900 border border-white/10 rounded-xl" role="radiogroup" aria-label="Visibility">
                    <label class="px-4 text-center py-2 rounded-lg cursor-pointer text-xs font-bold transition" data-status-label="Published">
                        <input type="radio" name="status" value="Published" class="hidden" ${isPublished ? 'checked' : ''}>
                        <i class="fas fa-globe mr-1"></i> Live on website
                    </label>
                    <label class="px-4 text-center py-2 rounded-lg cursor-pointer text-xs font-bold transition" data-status-label="Draft">
                        <input type="radio" name="status" value="Draft" class="hidden" ${isPublished ? '' : 'checked'}>
                        <i class="fas fa-file-alt mr-1"></i> Draft
                    </label>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="window.closeModal()" class="px-6 py-3 rounded-xl text-slate-500 hover:text-white font-bold transition">Cancel</button>
                    <button type="submit" id="bf-submit" class="bg-primary px-8 py-3 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition"></button>
                </div>
            </div>
        </form>
    `;

    // ── Editor ──────────────────────────────────────────────────────────────
    const editor = document.getElementById('be-editor');
    editor.innerHTML = sanitizeForEditor(blog ? blog.content : '') || '<p><br></p>';
    try { document.execCommand('defaultParagraphSeparator', false, 'p'); } catch (e) {}

    const counter = document.getElementById('be-count');
    const updateCount = () => {
        const words = (editor.innerText.trim().match(/\S+/g) || []).length;
        counter.textContent = words ? `${words.toLocaleString()} words · about ${Math.max(1, Math.round(words / 200))} min read` : '';
    };
    editor.addEventListener('input', updateCount);
    updateCount();

    document.getElementById('be-toolbar').addEventListener('mousedown', (e) => {
        const btn = e.target.closest('[data-cmd]');
        if (!btn) return;
        e.preventDefault(); // keep the selection in the editor
        const cmd = btn.dataset.cmd;
        if (cmd === 'createLink') {
            const url = (prompt('Link address (https://…)') || '').trim();
            if (!url) return;
            if (!/^(https?:\/\/|mailto:|tel:|\/|[\w-]+\.php)/i.test(url)) { showToast('Links must start with https://, mailto: or tel:', 'error'); return; }
            document.execCommand('createLink', false, url);
        } else if (cmd === 'formatBlock') {
            document.execCommand('formatBlock', false, btn.dataset.arg);
        } else {
            document.execCommand(cmd, false, null);
        }
        editor.focus();
        updateCount();
    });

    // Paste as clean paragraphs: Word / Docs HTML carries fonts, colours and
    // spans that would fight the website's typography.
    editor.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text/plain');
        const paras = text.replace(/\r\n?/g, '\n').split(/\n{2,}/).map(p => p.trim()).filter(Boolean);
        const html = paras.length > 1
            ? paras.map(p => `<p>${escHtml(p).replace(/\n/g, '<br>')}</p>`).join('')
            : escHtml(text).replace(/\n/g, '<br>');
        document.execCommand('insertHTML', false, html);
        updateCount();
    });

    // ── Summary counter ─────────────────────────────────────────────────────
    const excerpt = document.getElementById('bf-excerpt');
    const excerptCount = document.getElementById('bf-excerpt-count');
    const updateExcerpt = () => { excerptCount.textContent = excerpt.value.length ? `${excerpt.value.length}/400` : ''; };
    excerpt.addEventListener('input', updateExcerpt);
    updateExcerpt();

    // ── Status toggle + submit label ────────────────────────────────────────
    const submitBtn = document.getElementById('bf-submit');
    const syncStatus = () => {
        const status = document.querySelector('#blog-form [name="status"]:checked').value;
        document.querySelectorAll('[data-status-label]').forEach(l => {
            const on = l.dataset.statusLabel === status;
            l.classList.toggle('bg-emerald-500', on && status === 'Published');
            l.classList.toggle('bg-slate-700', on && status === 'Draft');
            l.classList.toggle('text-white', on);
            l.classList.toggle('text-slate-500', !on);
        });
        submitBtn.textContent = status === 'Draft' ? 'Save draft' : (blog && blog.status === 'Published' ? 'Update post' : 'Publish post');
    };
    document.querySelectorAll('#blog-form [name="status"]').forEach(r => r.addEventListener('change', syncStatus));
    syncStatus();

    // ── Cover photo ─────────────────────────────────────────────────────────
    const dropzone    = document.getElementById('blog-image-dropzone');
    const fileInput   = document.getElementById('blog-image-input');
    const placeholder = document.getElementById('blog-image-preview-placeholder');
    const previewDiv  = document.getElementById('blog-image-preview');
    const previewImg  = document.getElementById('blog-preview-img');
    const hiddenInput = document.getElementById('blog-image-hidden');
    const removedFlag = document.getElementById('blog-image-removed');
    const removeBtn   = document.getElementById('blog-remove-img');

    const useImage = async (file) => {
        if (!file) return;
        try {
            const dataUrl = await prepareTourImage(file); // same resize/compress as package photos
            previewImg.src = dataUrl;
            hiddenInput.value = dataUrl;
            removedFlag.value = '';
            placeholder.classList.add('hidden');
            previewDiv.classList.remove('hidden');
        } catch (err) {
            fileInput.value = '';
            showToast(err.message, 'error');
        }
    };
    dropzone.onclick = (e) => { if (!removeBtn.contains(e.target)) fileInput.click(); };
    fileInput.onchange = (e) => useImage(e.target.files[0]);
    dropzone.ondragover = (e) => { e.preventDefault(); dropzone.classList.add('border-primary'); };
    dropzone.ondragleave = () => dropzone.classList.remove('border-primary');
    dropzone.ondrop = (e) => { e.preventDefault(); dropzone.classList.remove('border-primary'); useImage(e.dataTransfer.files[0]); };
    removeBtn.onclick = (e) => {
        e.stopPropagation();
        previewImg.src = '';
        hiddenInput.value = '';
        removedFlag.value = '1';
        fileInput.value = '';
        placeholder.classList.remove('hidden');
        previewDiv.classList.add('hidden');
    };

    // ── Save ────────────────────────────────────────────────────────────────
    document.getElementById('blog-form').onsubmit = async (e) => {
        e.preventDefault();
        const status = document.querySelector('#blog-form [name="status"]:checked').value;
        const hasText = editor.innerText.trim().length > 0;
        if (status === 'Published' && !hasText) {
            showToast('Write the article before publishing it, or save it as a draft.', 'error');
            editor.focus();
            return;
        }
        document.getElementById('be-content').value = hasText ? editor.innerHTML : '';

        const formData = new FormData(e.target);
        if (blog) formData.append('id', blog.id);

        const origLabel = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

        try {
            const data = await apiPost('save-blog.php', formData);
            if (!data.success) throw new Error(data.error || 'Save failed');
            showToast(data.status === 'Published'
                ? 'Post is live on the Blog page and homepage'
                : 'Draft saved. It is not visible on the website yet');
            window.closeModal();
            await refreshData();
        } catch (err) {
            showToast('Could not save the post: ' + err.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = origLabel;
        }
    };

    document.getElementById('bf-title').focus();
};

window.deleteBlog = async (id) => {
    if (!confirm('Delete this blog post permanently? This cannot be undone.')) return;
    try {
        const data = await apiPost('delete-blog.php', { id });
        if (!data.success) throw new Error(data.error || 'Delete failed');
        showToast('Post deleted');
        await refreshData();
    } catch (err) {
        showToast('Error deleting post: ' + err.message, 'error');
    }
};

// --- Helpers & UI Events ---

window.switchTab = switchTab; // Expose for onclick

function renderStars(rating) {
    const stars = [];
    for (let i = 0; i < 5; i++) {
        stars.push(`<i class="fas fa-star ${i < Math.floor(rating) ? 'text-secondary' : 'text-slate-600'}"></i>`);
    }
    return stars.join('');
}

function getActivityIcon(action) {
    const a = (action || '').toLowerCase();
    if (a.includes('booking'))     return 'fa-calendar-check';
    if (a.includes('package'))     return 'fa-route';
    if (a.includes('blog'))        return 'fa-newspaper';
    if (a.includes('client'))      return 'fa-user';
    if (a.includes('destination')) return 'fa-map-marker-alt';
    if (a.includes('expense'))     return 'fa-receipt';
    if (a.includes('invoice'))     return 'fa-file-invoice-dollar';
    if (a.includes('quotation'))   return 'fa-file-lines';
    if (a.includes('deleted'))     return 'fa-trash';
    return 'fa-clock-rotate-left';
}

// Age of a MySQL DATETIME, computed at render time. The activity feed used to
// show a stored string ("2 hours ago") that never changed.
function timeAgo(timestamp) {
    if (!timestamp) return '';
    // MySQL returns "YYYY-MM-DD HH:MM:SS"; Safari needs the T separator.
    const then = new Date(String(timestamp).replace(' ', 'T'));
    if (isNaN(then)) return '';

    const secs = Math.floor((Date.now() - then.getTime()) / 1000);
    if (secs < 60)    return 'just now';
    if (secs < 3600)  return `${Math.floor(secs / 60)}m ago`;
    if (secs < 86400) return `${Math.floor(secs / 3600)}h ago`;
    if (secs < 604800) return `${Math.floor(secs / 86400)}d ago`;
    return then.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
}

// Percentage change badge. Renders a neutral note when there is no baseline,
// rather than implying a trend that was never measured.
function renderTrendBadge(pct, label = 'vs last month') {
    if (pct === null || pct === undefined) {
        return `<span class="text-xs text-slate-500 font-bold bg-white/5 px-2 py-1 rounded-md">No prior data</span>`;
    }
    const up = pct >= 0;
    const cls = up ? 'text-emerald-400 bg-emerald-400/10' : 'text-rose-400 bg-rose-400/10';
    return `<span class="text-xs font-bold ${cls} px-2 py-1 rounded-md">
                <i class="fas fa-arrow-${up ? 'up' : 'down'} mr-1"></i>${up ? '+' : ''}${pct}% ${label}
            </span>`;
}

function getEventTypeColor(type) {
    switch (type) {
        case 'tour': return 'bg-primary/20 text-primary';
        case 'meeting': return 'bg-amber-400/20 text-amber-400';
        case 'promo': return 'bg-emerald-400/20 text-emerald-400';
        default: return 'bg-slate-400/20 text-slate-400';
    }
}

// How far in the future a scheduled date falls.
function daysUntil(dateStr) {
    const target = new Date(String(dateStr).replace(' ', 'T'));
    if (isNaN(target)) return '';
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    target.setHours(0, 0, 0, 0);

    const days = Math.round((target - today) / 86400000);
    if (days < 0)  return `${Math.abs(days)} day${Math.abs(days) === 1 ? '' : 's'} ago`;
    if (days === 0) return 'Departs today';
    if (days === 1) return 'Departs tomorrow';
    if (days < 7)  return `In ${days} days`;
    if (days < 30) return `In ${Math.round(days / 7)} week${Math.round(days / 7) === 1 ? '' : 's'}`;
    return target.toLocaleDateString(undefined, { day: 'numeric', month: 'long', year: 'numeric' });
}

function calendarMonthLabel() {
    const d = new Date();
    d.setDate(1);
    d.setMonth(d.getMonth() + (state.calendarOffset || 0));
    return d.toLocaleString('default', { month: 'long', year: 'numeric' });
}

// Move the calendar without re-rendering the whole dashboard, so the charts
// and activity feed are not rebuilt just to page through months.
window.shiftCalendar = (delta) => {
    state.calendarOffset = (state.calendarOffset || 0) + delta;
    const grid  = document.getElementById('calendar-grid');
    const label = document.getElementById('calendar-label');
    if (grid)  grid.innerHTML = renderCalendarDays();
    if (label) label.textContent = calendarMonthLabel();
};

function renderCalendarDays() {
    const days = [];
    const today = new Date();

    // Anchor to the first of the month so adding an offset cannot skip a month
    // (e.g. 31 Jan + 1 month lands in March).
    const cursor = new Date(today.getFullYear(), today.getMonth(), 1);
    cursor.setMonth(cursor.getMonth() + (state.calendarOffset || 0));

    const month = cursor.getMonth();
    const year  = cursor.getFullYear();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Adjust for Monday start (default is Sunday=0)
    const offset = firstDay === 0 ? 6 : firstDay - 1;

    for (let i = 0; i < offset; i++) {
        days.push(`<div class="h-10 border border-white/5 rounded-lg opacity-20"></div>`);
    }

    const events = state.data.events || [];

    for (let d = 1; d <= daysInMonth; d++) {
        const currentDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const dayEvents = events.filter(e => String(e.date).slice(0, 10) === currentDate);
        const hasEvent = dayEvents.length > 0;
        const isToday = d === today.getDate()
                     && month === today.getMonth()
                     && year === today.getFullYear();

        days.push(`
            <div class="h-10 relative group border border-white/5 rounded-lg flex items-center justify-center text-xs font-bold ${isToday ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'hover:bg-white/5'} transition cursor-pointer">
                ${d}
                ${hasEvent ? `<span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-secondary"></span>` : ''}
                ${hasEvent ? `
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 p-2 bg-slate-900 border border-white/10 rounded-lg text-[9px] whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-50">
                        ${dayEvents.map(e => e.title).join('<br>')}
                    </div>
                ` : ''}
            </div>
        `);
    }

    return days.join('');
}

function initDashboardCharts() {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded yet. Retrying...');
        setTimeout(initDashboardCharts, 500);
        return;
    }
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    registerChart(new Chart(ctx, {
        type: 'line',
        data: {
            labels: state.data.analytics.monthlyStats.map(s => s.month),
            datasets: [{
                label: 'Revenue',
                data: state.data.analytics.monthlyStats.map(s => s.revenue),
                borderColor: '#006A72',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(0, 106, 114, 0.1)'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    }));
}

function initAnalyticsCharts() {
    if (typeof Chart === 'undefined') return;

    const revCtx = document.getElementById('analyticsRevenueChart');
    const distCtx = document.getElementById('bookingsDoughnut');
    
    if (revCtx) {
        const gradient = revCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 106, 114, 0.4)');
        gradient.addColorStop(1, 'rgba(0, 106, 114, 0)');

        registerChart(new Chart(revCtx, {
            type: 'line',
            data: {
                labels: state.data.analytics.monthlyStats.map(s => s.month),
                datasets: [{
                    label: 'Revenue ($)',
                    data: state.data.analytics.monthlyStats.map(s => s.revenue),
                    borderColor: '#006A72',
                    borderWidth: 3,
                    pointBackgroundColor: '#006A72',
                    pointBorderColor: 'rgba(255,255,255,0.1)',
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        ticks: { color: '#64748b', font: { size: 10 } },
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10 } },
                        border: { display: false }
                    }
                }
            }
        }));
    }

    if (distCtx) {
        // Calculate Regional Distribution from Destinations
        const regionCounts = {};
        state.data.destinations.forEach(d => {
            if (!d.parent_id) {
                regionCounts[d.region] = (regionCounts[d.region] || 0) + 1;
            }
        });

        const labels = Object.keys(regionCounts);
        const data = Object.values(regionCounts);
        const colors = ['#006A72', '#FFD214', '#10B981', '#1E293B', '#8B5CF6', '#F43F5E'];

        registerChart(new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    hoverOffset: 15,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                cutout: '75%'
            }
        }));
    }
}


window.openTourModal = (tourId = null) => {
    const tour = tourId ? state.data.tours.find(t => t.id === tourId) : null;
    const destinations = state.data.destinations || [];
    
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="tour-form" class="p-8 space-y-6 max-h-[90vh] overflow-y-auto custom-scroll">
            <div class="flex items-center justify-between sticky top-0 bg-dark-800 z-10 pb-4 border-b border-white/5">
                <div>
                    <h2 class="text-2xl font-bold">${tour ? 'Edit Tour Package' : 'Create New Package'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Fill in the details for your travel offering</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <!-- Title Row -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tour Title</label>
                    <input type="text" name="title" required maxlength="120" value="${tour ? escHtml(tour.title) : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. Serengeti Luxury Safari">
                </div>

                <!-- Image Upload Row -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Package Image</label>
                    <div id="image-dropzone" class="relative group cursor-pointer border-2 border-dashed border-white/10 rounded-2xl p-4 hover:border-primary/50 transition bg-dark-900/50">
                        <input type="file" id="tour-image-input" accept="image/*" class="hidden">
                        <div id="image-preview-container" class="flex flex-col items-center justify-center space-y-2 ${tour && tour.image ? 'hidden' : ''}">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-400">Click or drag to upload image</p>
                            <p class="text-[10px] text-slate-600">JPG, PNG or WEBP. Large photos are resized automatically. Landscape works best.</p>
                        </div>
                        <div id="image-preview" class="relative ${tour && tour.image ? '' : 'hidden'}">
                            <img src="${tour && tour.image ? escHtml(tour.image.startsWith('assets') ? '../' + tour.image : tour.image) : ''}" id="preview-img" class="w-full h-48 object-cover rounded-xl border border-white/5 shadow-2xl">
                            <button type="button" id="remove-img" class="absolute top-2 right-2 bg-rose-500 text-white size-8 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="image" id="image-path-hidden" value="${tour ? escHtml(tour.image) : ''}">
                </div>

                <!-- Destination & Price Row -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Destination</label>
                    <select name="location" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        <option value="" disabled ${!tour ? 'selected' : ''}>Select Destination</option>
                        ${tour && tour.location && !destinations.some(d => d.name === tour.location) ? `
                            <option value="${escHtml(tour.location)}" selected>${escHtml(tour.location)} (current)</option>
                        ` : ''}
                        ${destinations.map(d => `
                            <option value="${escHtml(d.name)}" ${tour && tour.location === d.name ? 'selected' : ''}>${escHtml(d.name)}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Price ($)</label>
                    <input type="number" name="price" required min="1" step="0.01" value="${tour ? escHtml(tour.price) : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="1200">
                </div>

                <!-- Duration & Category Row -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Duration</label>
                    <input type="text" name="duration" required maxlength="40" value="${tour ? escHtml(tour.duration) : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. 5 Days / 4 Nights">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Category</label>
                    <select name="category" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        ${['Safari', 'Luxury', 'Adventure', 'Culture', 'Beach'].map(c => `
                            <option value="${c}" ${tour && tour.category === c ? 'selected' : ''}>${c}</option>
                        `).join('')}
                    </select>
                </div>

                <!-- Status & Home Page Row -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status</label>
                    <div class="flex p-1 bg-dark-900 border border-white/10 rounded-xl">
                        <label class="flex-1 text-center py-2.5 rounded-lg cursor-pointer text-xs font-bold transition ${(!tour || tour.status === 'Active') ? 'bg-primary text-white' : 'text-slate-500 hover:text-white'}">
                            <input type="radio" name="status" value="Active" class="hidden" ${(!tour || tour.status === 'Active') ? 'checked' : ''} onchange="this.parentElement.parentElement.querySelectorAll('label').forEach(l=>l.classList.remove('bg-primary','text-white')); this.parentElement.classList.add('bg-primary','text-white')">
                            Active
                        </label>
                        <label class="flex-1 text-center py-2.5 rounded-lg cursor-pointer text-xs font-bold transition ${tour && tour.status === 'Draft' ? 'bg-slate-700 text-white' : 'text-slate-500 hover:text-white'}">
                            <input type="radio" name="status" value="Draft" class="hidden" ${tour && tour.status === 'Draft' ? 'checked' : ''} onchange="this.parentElement.parentElement.querySelectorAll('label').forEach(l=>l.classList.remove('bg-primary','text-white')); this.parentElement.classList.add('bg-slate-700','text-white')">
                            Draft
                        </label>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Rating</label>
                    <input type="number" name="rating" step="0.1" min="0" max="5" value="${tour ? (tour.rating || 5.0) : 5.0}"
                           class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition">
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="Short highlights and key features...">${tour ? escHtml(tour.description) : ''}</textarea>
                </div>

                <!-- ── Package page content ─────────────────────────────── -->
                <div class="col-span-2 pt-4 mt-2 border-t border-white/5 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-white">Package Page Content</h3>
                        <p class="text-xs text-slate-500 mt-0.5">One item per line. Anything left empty is simply not shown on the package page.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Highlights</label>
                        <textarea name="highlights" rows="4" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition text-sm" placeholder="Big Five game drives at dawn and dusk&#10;Two nights in a private conservancy camp">${tour ? escHtml(tour.highlights) : ''}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Day-by-Day Itinerary</label>
                        <textarea name="itinerary" rows="6" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition text-sm" placeholder="Arrival in Nairobi: Meet your guide and transfer to your hotel.&#10;Into the Maasai Mara: Scenic drive to camp, afternoon game drive.">${tour ? escHtml(tour.itinerary) : ''}</textarea>
                        <p class="text-[10px] text-slate-500">One line per day, as <span class="text-slate-300">Day title: what happens</span>. Days are numbered automatically.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Included</label>
                            <textarea name="inclusions" rows="5" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition text-sm" placeholder="Park entry fees&#10;All game drives">${tour ? escHtml(tour.inclusions) : ''}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Not Included</label>
                            <textarea name="exclusions" rows="5" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition text-sm" placeholder="International flights&#10;Travel insurance">${tour ? escHtml(tour.exclusions) : ''}</textarea>
                        </div>
                    </div>
                </div>

                <!-- ── Where this package appears ─────────────────────────── -->
                <div class="col-span-2 pt-4 mt-2 border-t border-white/5 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-white">Publishing</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Controls where this package shows up on the public website.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Package Pages <span class="text-rose-400">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            ${PACKAGE_PAGES.map(p => {
                                const checked = tour ? tourPages(tour).includes(p.key) : false;
                                return `
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="packagePages[]" value="${p.key}" class="sr-only peer" ${checked ? 'checked' : ''}>
                                    <div class="p-3.5 rounded-xl border text-center transition peer-checked:border-primary peer-checked:bg-primary/10 border-white/10 bg-dark-900 hover:border-white/20">
                                        <i class="fas ${p.icon} block mb-1.5 text-slate-400 peer-checked:text-primary"></i>
                                        <span class="text-xs font-bold text-slate-300">${p.label}</span>
                                        <span class="block text-[9px] text-slate-600 mt-0.5">${p.file}</span>
                                    </div>
                                </label>`;
                            }).join('')}
                        </div>
                        <p class="text-[10px] text-slate-500">Tick every page this package belongs on. A package with none selected will not appear anywhere.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Travel Styles <span class="text-slate-600 normal-case">(optional)</span></label>
                        <div class="flex flex-wrap gap-2">
                            ${TRAVEL_STYLES.map(s => {
                                const on = tour ? String(tour.travel_styles || '').split(',').map(x => x.trim()).includes(s.key) : false;
                                return `
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="travelStyles[]" value="${s.key}" class="sr-only peer" ${on ? 'checked' : ''}>
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold transition border-white/10 bg-dark-900 text-slate-400 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-white hover:border-white/20">
                                        <i class="fas ${s.icon}"></i>${s.label}
                                    </span>
                                </label>`;
                            }).join('')}
                        </div>
                        <p class="text-[10px] text-slate-500">Also list this package on these travel-style pages (Tours menu), e.g. a honeymoon package under Romantic.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Show on the homepage <span class="text-slate-600 normal-case">(optional)</span></label>
                        <div class="flex flex-wrap gap-2" id="home-sections">
                            ${HOMEPAGE_SECTIONS.map(s => {
                                const on = tour ? tourHomeSections(tour).includes(s.key) : false;
                                return `
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="homeSections[]" value="${s.key}" class="sr-only peer" ${on ? 'checked' : ''} onchange="window.togglePromoFields()">
                                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold transition border-white/10 bg-dark-900 text-slate-400 peer-checked:border-secondary peer-checked:bg-secondary/10 peer-checked:text-white hover:border-white/20">
                                        <i class="fas ${s.icon}"></i>${s.label}
                                    </span>
                                </label>`;
                            }).join('')}
                        </div>
                        <p class="text-[10px] text-slate-500">Safari, Local and International only work if the package is also on that page above. You can also manage this, and the order, in the Homepage tab.</p>
                    </div>

                    <!-- Promo fields only matter for the homepage Special Offers section -->
                    <div id="promo-fields" class="space-y-4 rounded-xl border border-white/10 bg-dark-900/50 p-4 transition-opacity ${tour && tourHomeSections(tour).includes('offers') ? '' : 'opacity-40 pointer-events-none'}">
                        <p class="text-xs font-bold text-slate-300"><i class="fas fa-bullhorn mr-1.5 text-secondary"></i>Promotion — shown when the package is in Special Offers</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Badge Text</label>
                                <input type="text" name="promoBadge" maxlength="30" value="${tour ? escHtml(tour.promo_badge) : ''}"
                                       class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary transition" placeholder="e.g. Flash Sale: 40% OFF">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Badge Colour</label>
                                <select name="promoStyle" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary transition appearance-none">
                                    ${[['primary','Teal'],['secondary','Yellow'],['citrus','Citrus'],['red','Red']].map(([v, l]) => `
                                        <option value="${v}" ${tour && tour.promo_style === v ? 'selected' : ''}>${l}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="space-y-2 col-span-2">
                                <label class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Tagline</label>
                                <input type="text" name="promoTagline" maxlength="90" value="${tour ? escHtml(tour.promo_tagline) : ''}"
                                       class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary transition" placeholder="e.g. The Land of Fire and Ice awaits you.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5 sticky bottom-0 bg-dark-800 z-10">
                <button type="button" onclick="window.closeModal()" class="px-8 py-3 rounded-xl text-slate-500 hover:text-white font-bold transition">Discard</button>
                <button type="submit" class="bg-primary px-10 py-3 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">
                    ${tour ? 'Update Package' : 'Create Package'}
                </button>
            </div>
        </form>
    `;

    // Image Upload Handlers
    const uploadZone = document.getElementById('image-dropzone');
    const fileInput = document.getElementById('tour-image-input');
    const previewContainer = document.getElementById('image-preview-container');
    const previewDiv = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const pathHidden = document.getElementById('image-path-hidden');
    const removeBtn = document.getElementById('remove-img');

    uploadZone.onclick = (e) => {
        if (e.target !== removeBtn && !removeBtn.contains(e.target)) fileInput.click();
    };

    const useImageFile = async (file) => {
        if (!file) return;
        try {
            const dataUrl = await prepareTourImage(file);
            previewImg.src = dataUrl;
            pathHidden.value = dataUrl;
            previewContainer.classList.add('hidden');
            previewDiv.classList.remove('hidden');
        } catch (err) {
            fileInput.value = '';
            showToast(err.message, 'error');
        }
    };

    fileInput.onchange = (e) => useImageFile(e.target.files[0]);

    // The dropzone has always said "drag to upload"; make that true.
    uploadZone.ondragover = (e) => { e.preventDefault(); uploadZone.classList.add('border-primary'); };
    uploadZone.ondragleave = () => uploadZone.classList.remove('border-primary');
    uploadZone.ondrop = (e) => {
        e.preventDefault();
        uploadZone.classList.remove('border-primary');
        useImageFile(e.dataTransfer.files[0]);
    };

    removeBtn.onclick = (e) => {
        e.stopPropagation();
        previewImg.src = '';
        pathHidden.value = '';
        fileInput.value = '';
        previewContainer.classList.remove('hidden');
        previewDiv.classList.add('hidden');
    };


    document.getElementById('tour-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (tour) formData.append('id', tour.id);

        const submitBtn = e.target.querySelector('[type="submit"]');
        const origLabel = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

        try {
            const data = await apiPost('save-tour.php', formData);
            if (!data.success) throw new Error(data.error || 'Save failed');

            showToast(tour ? 'Tour updated successfully' : 'Tour created successfully');
            window.closeModal();
            await refreshData();
        } catch (err) {
            showToast('Error saving tour: ' + err.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = origLabel;
        }
    };
};


window.setTourStatusFilter = (status) => {
    state.tourStatusFilter = status;
    renderTours();
};

window.setTourPageFilter = (page) => {
    state.tourPageFilter = page;
    renderTours();
};

// Promo badge / tagline only apply to the homepage Special Offers section.
window.togglePromoFields = () => {
    const promo = document.getElementById('promo-fields');
    const offers = document.querySelector('#home-sections input[value="offers"]');
    if (!promo) return;
    const active = !!(offers && offers.checked);
    promo.classList.toggle('opacity-40', !active);
    promo.classList.toggle('pointer-events-none', !active);
};

// ── Homepage tab ─────────────────────────────────────────────────────────────
// Choose which packages appear in each homepage section, in what order, and
// switch whole sections on or off. Backed by tours.home_sections /
// tours.home_order and site_settings (admin/api/update-homepage.php).
const HOMEPAGE_LIMIT = 6;

function homeSectionOn(key) {
    return ((state.data.settings || {})[`home_${key}_enabled`] ?? '1') !== '0';
}

function renderHomepage() {
    const tours = (state.data.tours || []).filter(t => t.status === 'Active');
    const img = (src) => escHtml(String(src || '').startsWith('assets') ? '../' + src : (src || ''));
    const onPage = (t, page) => !page || String(t.package_pages || '').split(',').map(x => x.trim()).includes(page);

    const sectionHtml = (sec) => {
        const enabled = homeSectionOn(sec.key);
        const shown = tours.filter(t => tourHomeSections(t).includes(sec.key))
            .sort((a, b) => (Number(a.home_order) || 0) - (Number(b.home_order) || 0) || String(b.created_at).localeCompare(String(a.created_at)));
        const eligible = tours.filter(t => onPage(t, sec.page) && !tourHomeSections(t).includes(sec.key));
        const live = enabled && shown.length > 0;
        const status = !enabled ? ['Section switched off', 'bg-slate-500/15 text-slate-400']
                     : !shown.length ? ['Hidden: no packages chosen', 'bg-amber-400/10 text-amber-400']
                     : ['Live on homepage', 'bg-emerald-500/10 text-emerald-400'];

        return `
        <div class="glass-card rounded-2xl border border-white/5 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 p-5 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/15 text-primary flex items-center justify-center"><i class="fas ${sec.icon}"></i></div>
                    <div>
                        <h2 class="text-lg font-bold">${sec.title}</h2>
                        <p class="text-xs text-slate-500">${sec.page ? `Packages on the ${sec.page} page` : 'Any active package'} · shows up to ${HOMEPAGE_LIMIT}</p>
                    </div>
                    <span class="ml-2 px-2 py-1 rounded-md text-[10px] font-bold ${status[1]}">${status[0]}</span>
                </div>
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <span class="text-xs font-bold text-slate-400">Show section</span>
                    <span class="relative inline-flex">
                        <input type="checkbox" class="sr-only peer" ${enabled ? 'checked' : ''} onchange="window.setHomeSectionEnabled('${sec.key}', this.checked)">
                        <span class="w-11 h-6 bg-slate-700 rounded-full peer-checked:bg-primary transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></span>
                    </span>
                </label>
            </div>
            <div class="p-5 space-y-4 ${enabled ? '' : 'opacity-50'}">
                ${shown.length ? `
                <ol class="space-y-2">
                    ${shown.map((t, i) => `
                    <li class="flex items-center gap-3 p-2.5 rounded-xl bg-white/5 ${i >= HOMEPAGE_LIMIT ? 'opacity-50' : ''}">
                        <span class="w-6 text-center text-xs font-bold text-slate-500">${i + 1}</span>
                        ${t.image ? `<img src="${img(t.image)}" alt="" class="w-12 h-12 rounded-lg object-cover">` : '<div class="w-12 h-12 rounded-lg bg-primary/10"></div>'}
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold truncate">${escHtml(t.title)}</p>
                            <p class="text-xs text-slate-500 truncate">${escHtml(t.location)} · $${Number(t.price).toLocaleString()}${sec.key === 'offers' && t.promo_badge ? ` · <span class="text-rose-400">${escHtml(t.promo_badge)}</span>` : ''}${i >= HOMEPAGE_LIMIT ? ' · not shown (over the limit)' : ''}</p>
                        </div>
                        <button class="p-2 rounded-lg hover:bg-white/10 text-slate-400 disabled:opacity-30" title="Move up" ${i === 0 ? 'disabled' : ''} onclick="window.moveHomePackage('${sec.key}', ${Number(t.id)}, -1)"><i class="fas fa-arrow-up text-xs"></i></button>
                        <button class="p-2 rounded-lg hover:bg-white/10 text-slate-400 disabled:opacity-30" title="Move down" ${i === shown.length - 1 ? 'disabled' : ''} onclick="window.moveHomePackage('${sec.key}', ${Number(t.id)}, 1)"><i class="fas fa-arrow-down text-xs"></i></button>
                        <button class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-bold" onclick="window.toggleHomePackage('${sec.key}', ${Number(t.id)}, false)">Remove</button>
                    </li>`).join('')}
                </ol>` : `<p class="text-sm text-slate-500">No packages chosen yet${live ? '' : ', so this section is not shown on the homepage'}. Add one below.</p>`}

                ${eligible.length ? `
                <div class="flex flex-wrap items-center gap-2">
                    <select id="home-add-${sec.key}" class="flex-1 min-w-60 bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary">
                        <option value="">Add a package to ${sec.title}…</option>
                        ${eligible.map(t => `<option value="${Number(t.id)}">${escHtml(t.title)} (${escHtml(t.location)})</option>`).join('')}
                    </select>
                    <button class="px-5 py-3 rounded-xl bg-primary text-white text-sm font-bold" onclick="window.addHomePackage('${sec.key}')"><i class="fas fa-plus mr-2"></i>Add</button>
                </div>` : `<p class="text-xs text-slate-500">${sec.page ? `Every active package on the ${sec.page} page is already in this section.` : 'Every active package is already in this section.'}</p>`}
                ${sec.key === 'offers' ? '<p class="text-[11px] text-slate-500">Badge text and tagline for offers are set in each package\'s form (Promotion).</p>' : ''}
            </div>
        </div>`;
    };

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">Homepage</h1>
                    <p class="text-slate-500 mt-1">Choose the packages shown in each homepage section and the order they appear in. Changes are live straight away.</p>
                </div>
                <a href="../index.php" target="_blank" rel="noopener" class="px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-bold transition"><i class="fas fa-arrow-up-right-from-square mr-2"></i>View homepage</a>
            </div>
            ${HOMEPAGE_SECTIONS.map(sectionHtml).join('')}
        </div>`;
}

async function homeApi(params, ok) {
    try {
        const data = await apiPost('update-homepage.php', params);
        if (!data.success) throw new Error(data.error || 'Update failed');
        if (ok) showToast(ok);
        await refreshData({ silent: true });
    } catch (err) {
        showToast(err.message, 'error');
        renderHomepage();
    }
}

window.setHomeSectionEnabled = (section, on) =>
    homeApi({ action: 'enable', section, on: on ? '1' : '0' }, on ? 'Section shown on the homepage' : 'Section hidden from the homepage');

window.toggleHomePackage = (section, id, on) =>
    homeApi({ action: 'toggle', section, id, on: on ? '1' : '0' }, on ? 'Added to the homepage' : 'Removed from the homepage');

// Sorted ids of the active packages in a section, as the homepage orders them.
function homeSectionIds(section) {
    return state.data.tours.filter(t => t.status === 'Active' && tourHomeSections(t).includes(section))
        .sort((a, b) => (Number(a.home_order) || 0) - (Number(b.home_order) || 0) || String(b.created_at).localeCompare(String(a.created_at)))
        .map(t => Number(t.id));
}

window.addHomePackage = async (section) => {
    const sel = document.getElementById('home-add-' + section);
    if (!sel || !sel.value) { showToast('Choose a package to add first.', 'error'); return; }
    const id = Number(sel.value);
    const before = homeSectionIds(section);
    await window.toggleHomePackage(section, id, true);
    if (!homeSectionIds(section).includes(id)) return; // the server refused it
    await saveHomeOrder(section, [...before, id]); // new additions go last
};

async function saveHomeOrder(section, ids) {
    const fd = new FormData();
    fd.append('action', 'order');
    fd.append('section', section);
    ids.forEach(id => fd.append('ids[]', id));
    await homeApi(fd, null);
}

window.moveHomePackage = (section, id, dir) => {
    const ids = homeSectionIds(section);
    const i = ids.indexOf(Number(id)), j = i + dir;
    if (i < 0 || j < 0 || j >= ids.length) return;
    [ids[i], ids[j]] = [ids[j], ids[i]];
    saveHomeOrder(section, ids);
};

window.viewTourPublic = (id) => {
    window.open('../tour-detail.php?id=' + id, '_blank');
};

window.deleteTour = async (id) => {
    if (!confirm('Are you sure you want to delete this tour? This cannot be undone.')) return;
    try {
        const data = await apiPost('delete-tour.php', { id });
        if (!data.success) throw new Error(data.error || 'Delete failed');
        showToast('Tour deleted');
        await refreshData();
    } catch (err) {
        showToast('Error deleting tour: ' + err.message, 'error');
    }
};

window.openDestinationModal = (destId = null) => {
    const dest = destId ? state.data.destinations.find(d => d.id == destId) : null;
    const parentDestinations = state.data.destinations.filter(d => !d.parent_id || d.parent_id === null);
    
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="dest-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">${dest ? 'Edit Destination' : 'Add New Destination'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Define properties for a global travel region or sub-location</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <input type="hidden" name="id" value="${dest ? dest.id : ''}">
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Destination Name</label>
                    <input type="text" name="name" required value="${dest ? dest.name : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. Bali">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Parent Destination (Optional)</label>
                    <select name="parent_id" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        <option value="">None (Primary Destination)</option>
                        ${parentDestinations.filter(p => p.id != destId).map(p => `
                            <option value="${p.id}" ${dest && dest.parent_id == p.id ? 'selected' : ''}>${p.name}</option>
                        `).join('')}
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Region / Continent</label>
                    <select name="region" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        ${['Africa', 'Europe', 'Asia', 'Americas', 'Middle East', 'Oceania'].map(r => `
                            <option value="${r}" ${dest && dest.region === r ? 'selected' : ''}>${r}</option>
                        `).join('')}
                    </select>
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="Short description of why users should visit...">${dest ? (dest.description || '') : ''}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-8 py-2.5 rounded-xl text-slate-500 hover:text-white font-bold transition">Discard</button>
                <button type="submit" class="bg-primary px-10 py-2.5 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">
                    ${dest ? 'Update Destination' : 'Add Destination'}
                </button>
            </div>
        </form>
    `;

    document.getElementById('dest-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const result = await apiPost('save-destination.php', formData);
            
            if (result.success) {
                showToast(dest ? 'Destination updated' : 'Destination added');
                window.closeModal();
                await refreshData();
            } else {
                showToast('Error: ' + result.error, 'error');
            }
        } catch (err) {
            showToast('Network error while saving destination', 'error');
            console.error(err);
        }
    };
};

window.deleteDestination = async (id) => {
    if (!confirm('Are you sure you want to delete this destination? All nested sub-locations will also be deleted.')) return;
    try {
        const result = await apiPost('delete-destination.php', { id });
        if (result.success) {
            showToast('Destination deleted');
            await refreshData();
        } else {
            showToast('Error: ' + result.error, 'error');
        }
    } catch (err) {
        showToast('Network error while deleting destination', 'error');
        console.error(err);
    }
};

window.closeModal = () => {
    modalOverlay.classList.add('hidden');
    modalContent.classList.replace('max-w-4xl', 'max-w-2xl');
};

// Update a booking's status, optionally with the final amount, an internal
// note or a type change (enquiry -> booking).
async function updateBooking(id, status, extra = {}, okMessage = 'Booking updated') {
    try {
        const data = await apiPost('update-booking-status.php', { id, status, ...extra });
        if (data.success) {
            showToast(okMessage);
            window.closeModal();
            await refreshData();
        } else {
            showToast(data.error || 'Could not update the booking', 'error');
        }
    } catch (e) { showToast('Network error. Please try again.', 'error'); }
}

window.confirmBooking = (id, extra = {}) => updateBooking(id, 'Confirmed', extra, 'Booking confirmed');
window.cancelBooking = (id, extra = {}) => updateBooking(id, 'Cancelled', extra, 'Booking declined');

// Collect the review form's amount / note so every action saves them.
function bookingFormExtras() {
    const extra = {};
    const amount = document.getElementById('bk-amount');
    const note = document.getElementById('bk-note');
    if (amount && amount.value !== '') extra.amount = amount.value;
    if (note) extra.admin_note = note.value;
    return extra;
}

window.bookingAction = (id, action) => {
    const b = state.data.bookings.find(x => x.id === id);
    if (!b) return;
    const extra = bookingFormExtras();
    switch (action) {
        case 'confirm':
            updateBooking(id, 'Confirmed', extra, 'Booking confirmed');
            break;
        case 'decline':
            if (!confirm('Decline this booking request?')) return;
            updateBooking(id, 'Cancelled', extra, 'Booking declined');
            break;
        case 'convert':
            updateBooking(id, 'Pending', { ...extra, type: 'Booking' }, 'Enquiry converted to a booking request');
            break;
        case 'close':
            updateBooking(id, 'Cancelled', extra, 'Enquiry closed');
            break;
        case 'reopen':
            updateBooking(id, 'Pending', extra, 'Moved back to pending');
            break;
        case 'save':
            updateBooking(id, b.status, extra, 'Saved');
            break;
    }
};

window.deleteBooking = async (id) => {
    if (!confirm('Delete this booking permanently?')) return;
    try {
        const data = await apiPost('delete-booking.php', { id });
        if (data.success) {
            showToast('Booking deleted');
            window.closeModal();
            await refreshData();
        } else {
            showToast(data.error || 'Failed to delete booking', 'error');
        }
    } catch (e) { showToast('Network error. Please try again.', 'error'); }
};

window.viewBookingDetails = (id) => {
    const booking = state.data.bookings.find(b => b.id === id);
    if (!booking) return;
    const info = bookingInfo(booking);
    const isEnquiry = info.type === 'Enquiry';
    const pending = booking.status === 'Pending';
    const ref = escHtml(booking.id);
    const firstName = String(booking.user || '').split(' ')[0];

    // Quick-reply links, pre-filled so the team only has to add specifics.
    const subject = `${isEnquiry ? 'Your question' : 'Your booking request'} ${booking.id} – ${info.tour}`;
    const body = `Dear ${firstName},\n\nThank you for your ${isEnquiry ? 'question about' : 'booking request for'} ${info.tour}` +
                 `${booking.date && !isEnquiry ? ` on ${bookingDate(booking.date)}` : ''}.\n\n\n\nKind regards,\nLuxit Global Escapes\n+254 737 800 900`;
    const mailto = `mailto:${encodeURIComponent(booking.email || '')}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    const digits = String(info.phone || '').replace(/[^\d]/g, '');
    const wa = digits ? `https://wa.me/${digits}?text=${encodeURIComponent(`Hello ${firstName}, this is Luxit Global Escapes about your ${isEnquiry ? 'question' : 'booking request'} ${booking.id} for ${info.tour}.`)}` : '';

    const field = (label, value) => `
        <div class="space-y-1">
            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">${label}</p>
            <p class="text-white font-medium break-words">${value}</p>
        </div>`;

    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <div class="p-8 space-y-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto custom-scroll">
            <div class="flex items-start justify-between border-b border-white/5 pb-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold ${isEnquiry ? 'text-sky-400 bg-sky-400/10' : 'text-amber-400 bg-amber-400/10'}">
                            <i class="fas fa-${isEnquiry ? 'question-circle' : 'calendar-check'} mr-1.5"></i>${info.type}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold ${getBookingStatusClass(booking.status)}">
                            <i class="fas fa-circle mr-1.5 text-[6px]"></i>${bookingStatusLabel(booking.status, info.type)}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-white">${escHtml(info.tour)}</h2>
                    <p class="text-xs text-slate-500 mt-1"><span class="font-mono">${ref}</span> · received ${bookingDate(booking.created_at)}</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white text-lg mt-1" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                ${field(isEnquiry ? 'Preferred date' : 'Travel date', isEnquiry && booking.date === (booking.created_at || '').slice(0, 10) ? 'Not given' : bookingDate(booking.date))}
                ${field('Travellers', info.travelers ? Number(info.travelers) : '—')}
                ${field(pending && !isEnquiry ? 'Estimate' : 'Amount', bookingMoney(booking.amount))}
                ${field('Package', booking.tour_id ? `<a href="../tour-detail.php?id=${Number(booking.tour_id)}" target="_blank" rel="noopener" class="text-primary hover:underline">View page <i class="fas fa-arrow-up-right-from-square text-[10px]"></i></a>` : '—')}
            </div>

            <div class="rounded-xl bg-white/5 p-4 grid sm:grid-cols-3 gap-4 text-sm">
                ${field('Guest', escHtml(booking.user || '—'))}
                ${field('Email', booking.email ? `<a href="mailto:${escHtml(booking.email)}" class="hover:text-primary">${escHtml(booking.email)}</a>` : '—')}
                ${field('Phone', info.phone ? `<a href="tel:${escHtml(info.phone)}" class="hover:text-primary">${escHtml(info.phone)}</a>` : '—')}
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="${escHtml(mailto)}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-bold transition"><i class="fas fa-envelope"></i> Reply by email</a>
                ${wa ? `<a href="${escHtml(wa)}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-400 text-sm font-bold transition"><i class="fab fa-whatsapp"></i> WhatsApp</a>` : ''}
                ${info.phone ? `<a href="tel:${escHtml(info.phone)}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-bold transition"><i class="fas fa-phone"></i> Call</a>` : ''}
            </div>

            ${info.note ? `
            <div class="rounded-xl border border-white/10 p-4 space-y-1">
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">${isEnquiry ? 'Their question' : 'Message from guest'}</p>
                <p class="text-sm text-slate-200 whitespace-pre-line">${escHtml(info.note)}</p>
            </div>` : ''}

            <div class="grid sm:grid-cols-3 gap-4">
                ${!isEnquiry ? `
                <div class="space-y-2">
                    <label for="bk-amount" class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Final amount ($)</label>
                    <input id="bk-amount" type="number" min="0" step="0.01" value="${Number(booking.amount || 0)}"
                           class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary transition">
                    <p class="text-[10px] text-slate-500">Adjust before confirming if the quote changed.</p>
                </div>` : ''}
                <div class="space-y-2 ${isEnquiry ? 'sm:col-span-3' : 'sm:col-span-2'}">
                    <label for="bk-note" class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Internal note</label>
                    <textarea id="bk-note" rows="3" maxlength="2000" placeholder="Only visible to your team, e.g. lodge confirmed, deposit due 1 Nov"
                              class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 text-sm outline-none focus:border-primary transition">${escHtml(booking.admin_note || '')}</textarea>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-white/5">
                ${pending && !isEnquiry ? `
                    <button onclick="window.bookingAction('${ref}', 'confirm')" class="flex-1 min-w-40 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition text-sm"><i class="fas fa-check mr-2"></i>Confirm booking</button>
                    <button onclick="window.bookingAction('${ref}', 'decline')" class="flex-1 min-w-32 bg-rose-500/15 hover:bg-rose-500/25 text-rose-400 font-bold py-3 rounded-xl transition text-sm"><i class="fas fa-times mr-2"></i>Decline</button>` : ''}
                ${pending && isEnquiry ? `
                    <button onclick="window.bookingAction('${ref}', 'convert')" class="flex-1 min-w-40 bg-primary hover:bg-primary/80 text-white font-bold py-3 rounded-xl transition text-sm"><i class="fas fa-arrow-right mr-2"></i>Convert to booking</button>
                    <button onclick="window.bookingAction('${ref}', 'close')" class="flex-1 min-w-32 bg-white/5 hover:bg-white/10 text-slate-300 font-bold py-3 rounded-xl transition text-sm"><i class="fas fa-check-double mr-2"></i>Close enquiry</button>` : ''}
                ${!pending ? `
                    <button onclick="window.bookingAction('${ref}', 'reopen')" class="px-5 bg-white/5 hover:bg-white/10 text-slate-300 font-bold py-3 rounded-xl transition text-sm"><i class="fas fa-rotate-left mr-2"></i>Move back to pending</button>` : ''}
                <button onclick="window.bookingAction('${ref}', 'save')" class="px-5 bg-white/5 hover:bg-white/10 text-slate-300 font-bold py-3 rounded-xl transition text-sm">Save note</button>
            </div>
        </div>
    `;
};

window.setBookingType = (type) => {
    state.bookingType = type;
    renderBookings();
};

window.setBookingFilter = (filter) => {
    state.bookingFilter = filter;
    renderBookings();
};

function getBookingStatusClass(status) {
    switch (status) {
        case 'Confirmed': return 'bg-emerald-500/10 text-emerald-500';
        case 'Pending': return 'bg-amber-400/10 text-amber-400';
        case 'Cancelled': return 'bg-rose-500/10 text-rose-500';
        default: return 'bg-slate-400/10 text-slate-400';
    }
}

window.openCustomerModal = (id = null) => {
    const cust = id ? state.data.customers.find(c => c.id == id) : null;
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="customer-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">${cust ? 'Edit Client Profile' : 'Register New Client'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Manage personal information and residency</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            
            <input type="hidden" name="id" value="${cust ? cust.id : ''}">
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Full Name</label>
                    <input type="text" name="name" required value="${cust ? cust.name : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="John Doe">
                </div>
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Email Address</label>
                    <input type="email" name="email" required value="${cust ? cust.email : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="john@example.com">
                </div>
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Country of Origin</label>
                    <input type="text" name="country" value="${cust ? (cust.country || '') : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. United Kingdom">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-8 py-2.5 rounded-xl text-slate-500 hover:text-white font-bold transition">Discard</button>
                <button type="submit" class="bg-primary px-10 py-2.5 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">
                    ${cust ? 'Update Profile' : 'Register Client'}
                </button>
            </div>
        </form>
    `;

    document.getElementById('customer-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const result = await apiPost('save-customer.php', formData);
            
            if (result.success) {
                showToast(cust ? 'Client profile updated' : 'New client registered');
                window.closeModal();
                await refreshData();
            } else {
                showToast('Error: ' + result.error, 'error');
            }
        } catch (err) {
            showToast('Network error while saving client', 'error');
            console.error(err);
        }
    };
};

window.deleteCustomer = async (id) => {
    if (!confirm('Are you sure you want to delete this client? This will NOT delete their bookings, but they will be unlinked.')) return;
    try {
        const result = await apiPost('delete-customer.php', { id });
        if (result.success) {
            showToast('Client deleted');
            await refreshData();
        } else {
            showToast('Error: ' + result.error, 'error');
        }
    } catch (err) {
        showToast('Network error while deleting client', 'error');
        console.error(err);
    }
};

window.openBookingModal = () => {
    const tours = state.data.tours || [];
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="booking-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">Initiate New Booking</h2>
                    <p class="text-slate-500 text-xs mt-1">Manual booking entry for a client</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Customer Name</label>
                    <input type="text" name="user_name" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="John Doe">
                </div>
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="john@example.com">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Select Tour</label>
                    <select name="tour_name" id="booking-tour-select" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        <option value="" disabled selected>Select Package</option>
                        ${tours.map(t => `<option value="${t.title}" data-price="${t.price}">${t.title}</option>`).join('')}
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Travel Date</label>
                    <input type="date" name="booking_date" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Amount ($)</label>
                    <input type="number" name="amount" id="booking-amount-input" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="0.00">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status</label>
                    <select name="status" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-8 py-3 rounded-xl text-slate-500 hover:text-white font-bold transition">Cancel</button>
                <button type="submit" class="bg-primary px-10 py-3 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">
                    Create Booking
                </button>
            </div>
        </form>
    `;

    // Auto-fill amount when tour is selected
    const tourSelect = document.getElementById('booking-tour-select');
    const amountInput = document.getElementById('booking-amount-input');
    tourSelect.addEventListener('change', () => {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) amountInput.value = price;
    });

    document.getElementById('booking-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const result = await apiPost('add-booking.php', formData);
            
            if (result.success) {
                showToast('Booking created successfully');
                window.closeModal();
                await refreshData();
            } else {
                showToast('Error creating booking: ' + result.error, 'error');
            }
        } catch (error) {
            showToast('Server error. Please check connection.', 'error');
            console.error('Failed to create booking:', error);
        }
    };
};

window.openQuotationModal = (quoteId = null) => {
    const quote = quoteId ? state.data.finance.quotations.find(q => q.id === quoteId) : null;
    const customers = state.data.customers || [];
    
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="quotation-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">${quote ? 'Edit Quotation' : 'Create New Quotation'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Generate a travel offer for a client</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Customer</label>
                    <select name="customer_id" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                        <option value="" disabled ${!quote ? 'selected' : ''}>Select Client</option>
                        ${customers.map(c => `<option value="${c.id}" ${quote && quote.customer_id === c.id ? 'selected' : ''}>${escHtml(c.name)} (${escHtml(c.id)})</option>`).join('')}
                    </select>
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tour / Service</label>
                    <input type="text" name="tour_name" required value="${quote ? quote.tour_name : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="e.g. 7-Day Luxury Safari">
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Quote Amount ($)</label>
                    <input type="number" name="amount" required value="${quote ? quote.amount : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="0.00">
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status</label>
                    <select name="status" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                        <option value="Draft" ${quote && quote.status === 'Draft' ? 'selected' : ''}>Draft</option>
                        <option value="Sent" ${quote && quote.status === 'Sent' ? 'selected' : ''}>Sent</option>
                        <option value="Accepted" ${quote && quote.status === 'Accepted' ? 'selected' : ''}>Accepted</option>
                        <option value="Expired" ${quote && quote.status === 'Expired' ? 'selected' : ''}>Expired</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-400 hover:text-white transition">Cancel</button>
                <button type="submit" class="bg-primary hover:bg-opacity-90 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary/20 transition">Save Quotation</button>
            </div>
        </form>
    `;

    document.getElementById('quotation-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (quoteId) formData.append('id', quoteId);

        try {
            const data = await apiPost('save-quotation.php', formData);
            if (data.success) { showToast(quoteId ? 'Quotation updated' : 'Quotation created'); window.closeModal(); await refreshData(); }
            else { showToast(data.message || 'Error saving quotation', 'error'); }
        } catch (err) { console.error(err); showToast('Network error while saving', 'error'); }
    });
};

window.openInvoiceModal = (invoiceId = null) => {
    const inv = invoiceId ? state.data.finance.invoices.find(i => i.id === invoiceId) : null;
    const customers = state.data.customers || [];
    
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="invoice-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">${inv ? 'Edit Invoice' : 'Create New Invoice'}</h2>
                    <p class="text-slate-500 text-xs mt-1">Request payment from a client</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Customer</label>
                    <select name="customer_id" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                        <option value="" disabled ${!inv ? 'selected' : ''}>Select Client</option>
                        ${customers.map(c => `<option value="${c.id}" ${inv && inv.customer_id === c.id ? 'selected' : ''}>${escHtml(c.name)} (${escHtml(c.id)})</option>`).join('')}
                    </select>
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Amount Due ($)</label>
                    <input type="number" name="amount" required value="${inv ? inv.amount : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="0.00">
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Due Date</label>
                    <input type="date" name="due_date" required value="${inv ? inv.due_date : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status</label>
                    <select name="status" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                        <option value="Unpaid" ${inv && inv.status === 'Unpaid' ? 'selected' : ''}>Unpaid</option>
                        <option value="Paid" ${inv && inv.status === 'Paid' ? 'selected' : ''}>Paid</option>
                        <option value="Partial" ${inv && inv.status === 'Partial' ? 'selected' : ''}>Partial</option>
                        <option value="Overdue" ${inv && inv.status === 'Overdue' ? 'selected' : ''}>Overdue</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-400 hover:text-white transition">Cancel</button>
                <button type="submit" class="bg-primary hover:bg-opacity-90 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary/20 transition">Save Invoice</button>
            </div>
        </form>
    `;

    document.getElementById('invoice-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (invoiceId) formData.append('id', invoiceId);

        try {
            const data = await apiPost('save-invoice.php', formData);
            if (data.success) { showToast(invoiceId ? 'Invoice updated' : 'Invoice created'); window.closeModal(); await refreshData(); }
            else { showToast(data.message || 'Error saving invoice', 'error'); }
        } catch (err) { console.error(err); showToast('Network error while saving', 'error'); }
    });
};

window.openExpenseModal = () => {
    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="expense-form" class="p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div>
                    <h2 class="text-2xl font-bold">Record Company Expense</h2>
                    <p class="text-slate-500 text-xs mt-1">Log internal costs and operational spending</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="grid grid-cols-2 gap-6 text-sm">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Category</label>
                    <select name="category" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                        <option value="Operations">Operations</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Flight">Flight</option>
                        <option value="Accommodation">Accommodation</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Amount ($)</label>
                    <input type="number" name="amount" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="0.00">
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Description</label>
                    <input type="text" name="description" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition" placeholder="e.g. Office electricity bill">
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Expense Date</label>
                    <input type="date" name="expense_date" required value="${new Date().toISOString().split('T')[0]}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3 outline-none focus:border-primary transition">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5">
                <button type="button" onclick="window.closeModal()" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-400 hover:text-white transition">Cancel</button>
                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-rose-500/20 transition">Log Expense</button>
            </div>
        </form>
    `;

    document.getElementById('expense-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        try {
            const data = await apiPost('save-expense.php', formData);
            if (data.success) { showToast('Expense recorded'); window.closeModal(); await refreshData(); }
            else { showToast(data.message || 'Error saving expense', 'error'); }
        } catch (err) { console.error(err); showToast('Network error while saving', 'error'); }
    });
};

window.deleteExpense = async (id) => {
    if (!confirm('Are you sure you want to delete this expense record?')) return;
    try {
        const data = await apiPost('delete-expense.php', { id });
        if (data.success) { showToast('Expense deleted'); await refreshData(); }
        else { showToast(data.error || 'Failed to delete expense', 'error'); }
    } catch (err) { showToast('Network error', 'error'); console.error(err); }
};

window.markInvoicePaid = async (id) => {
    if (!confirm('Mark this invoice as Paid?')) return;
    try {
        const data = await apiPost('update-invoice-status.php', { id, status: 'Paid' });
        if (data.success) { showToast('Invoice marked as Paid'); await refreshData(); }
        else { showToast(data.error || 'Failed to update invoice', 'error'); }
    } catch (err) { showToast('Network error', 'error'); console.error(err); }
};
