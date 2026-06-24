// import MOCK_DATA removed for global script compatibility
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
let state = {
    currentTab: 'dashboard',
    version: '5.0',
    data: (() => {
        try {
            if (!window.MOCK_DATA) {
                console.error('window.MOCK_DATA NOT FOUND!');
                return { tours: [], destinations: [], bookings: [], customers: [] };
            }
            // Always start from fresh DB data; only carry over UI-only state from localStorage
            const saved = (() => { try { return JSON.parse(localStorage.getItem('adminData')); } catch(e) { return null; } })();
            if (!saved || saved.version !== '5.0') {
                localStorage.removeItem('adminData');
                return { ...window.MOCK_DATA };
            }
            // Keep DB sources fresh; only tours are briefly cached for instant re-render after saves
            return {
                ...window.MOCK_DATA,
                bookings:    window.MOCK_DATA.bookings,
                destinations: window.MOCK_DATA.destinations,
                customers:   window.MOCK_DATA.customers,
                blogPosts:   window.MOCK_DATA.blogPosts,
                tours:       Array.isArray(saved.tours) ? saved.tours : window.MOCK_DATA.tours,
            };
        } catch (e) {
            console.error('State initialization failed:', e);
            return window.MOCK_DATA || {};
        }
    })(),
    searchQuery: '',
    tourStatusFilter: 'All',
    bookingFilter: 'All',
    financeTab: 'quotations'
};

// Persistence
const saveState = () => {
    try {
        localStorage.setItem('adminData', JSON.stringify(state.data));
    } catch (e) {
        console.error('Failed to save state:', e);
    }
};

window.hardReset = () => {
    if (confirm('CRITICAL: This will delete ALL your local data and restore defaults. Proceed?')) {
        localStorage.removeItem('adminData');
        location.reload();
    }
};

// ── Toast Notification System ─────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const existing = document.getElementById('admin-toast');
    if (existing) existing.remove();

    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const colors = { success: 'bg-emerald-500', error: 'bg-rose-500', info: 'bg-primary' };

    const toast = document.createElement('div');
    toast.id = 'admin-toast';
    toast.className = `fixed bottom-6 right-6 z-[99999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-sm font-bold text-white ${colors[type] || colors.success} translate-y-0 opacity-100 transition-all duration-300`;
    toast.innerHTML = `<i class="fas ${icons[type] || icons.success} text-base"></i><span>${message}</span>`;
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
        sidebarLinks.forEach(l => {
            l.classList.toggle('active', l.getAttribute('data-tab') === tab);
        });
        if (tabs[tab]) {
            console.log(`Executing tab function for ${tab}`);
            tabs[tab]();
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
                        <h2 class="text-xl font-bold">Revenue Performance</h2>
                        <span class="text-xs text-emerald-400 font-bold bg-emerald-400/10 px-2 py-1 rounded-md">+12.5% vs last month</span>
                    </div>
                    <canvas id="revenueChart" class="w-full h-80"></canvas>
                </div>
                
                <div class="glass-card rounded-2xl p-6 flex flex-col">
                    <h2 class="text-xl font-bold mb-6">Recent Activity</h2>
                    <div class="flex-1 space-y-6">
                        ${activityFeed.map(item => `
                            <div class="flex space-x-4">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas ${getActivityIcon(item.action)} text-primary text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium"><span class="text-primary">${item.user}</span> ${item.action}</p>
                                    <p class="text-xs text-slate-400 font-bold opacity-80 mt-0.5">${item.target} • ${item.time}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <button class="w-full mt-6 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-semibold transition">View System Logs</button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold">Calendar of Events</h2>
                        <div class="flex space-x-2">
                            <button class="p-1.5 hover:bg-white/5 rounded-lg text-slate-400"><i class="fas fa-chevron-left"></i></button>
                            <button class="p-1.5 hover:bg-white/5 rounded-lg text-slate-400"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-2 text-center text-[10px] text-slate-500 font-bold mb-4 uppercase tracking-tighter">
                        <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        ${renderCalendarDays()}
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6">Upcoming Schedule</h2>
                    <div class="space-y-4">
                        ${state.data.events.slice(0, 4).map(event => `
                            <div class="flex items-center p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition group">
                                <div class="w-12 h-12 rounded-xl ${getEventTypeColor(event.type)} flex flex-col items-center justify-center mr-4">
                                    <span class="text-[10px] font-bold uppercase opacity-80">${new Date(event.date).toLocaleString('default', { month: 'short' })}</span>
                                    <span class="text-lg font-bold leading-none">${new Date(event.date).getDate()}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold group-hover:text-primary transition">${event.title}</h4>
                                    <p class="text-xs text-slate-500">${event.type.charAt(0).toUpperCase() + event.type.slice(1)} • All Day</p>
                                </div>
                                <i class="fas fa-chevron-right text-slate-700 text-xs"></i>
                            </div>
                        `).join('')}
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

function renderTours() {
    const { tours } = state.data;
    const filteredTours = tours.filter(t => {
        const matchesSearch = t.title.toLowerCase().includes(state.searchQuery) || t.location.toLowerCase().includes(state.searchQuery);
        const matchesStatus = state.tourStatusFilter === 'All' || t.status === state.tourStatusFilter;
        return matchesSearch && matchesStatus;
    });

    const stats = {
        total: tours.length,
        active: tours.filter(t => t.status === 'Active').length,
        value: tours.filter(t => t.status === 'Active').reduce((sum, t) => sum + t.price, 0)
    };

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Tour Packages</h1>
                    <p class="text-slate-500 mt-1">Manage your travel products and portfolio</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-slate-800/50 p-1 rounded-xl border border-white/5">
                        ${['All', 'Active', 'Draft', 'Inactive'].map(s => `
                            <button onclick="window.setTourStatusFilter('${s}')" 
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition ${state.tourStatusFilter === s ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-white'}">
                                ${s}
                            </button>
                        `).join('')}
                    </div>
                    <button onclick="window.openTourModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                        <i class="fas fa-plus mr-2"></i> Create Package
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-6 rounded-2xl border border-white/5 bg-gradient-to-br from-primary/5 to-transparent">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Portfolio Value</p>
                    <p class="text-2xl font-bold">$${stats.value.toLocaleString()}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Sum of all active package prices</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Active Packages</p>
                    <p class="text-2xl font-bold text-emerald-400">${stats.active}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Draft Mode</p>
                    <p class="text-2xl font-bold text-slate-400">${stats.total - stats.active}</p>
                </div>
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
                                    <span class="font-bold text-secondary text-lg">$${tour.price}</span>
                                </div>
                                <div class="flex items-center text-xs text-slate-500">
                                    <i class="fas fa-tag mr-2 opacity-50"></i>
                                    <span>${tour.category || 'Adventure'}</span>
                                    <span class="mx-2">•</span>
                                    <div class="flex gap-1 text-secondary">
                                        ${renderStars(tour.rating)}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-5 border-t border-white/5">
                                <button onclick="window.viewTourPublic('${tour.id}')" class="text-xs font-bold text-slate-400 hover:text-white transition">Preview Link</button>
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

function renderBookings() {
    const { bookings } = state.data;
    const filteredBookings = bookings.filter(b => {
        const matchesSearch = b.user.toLowerCase().includes(state.searchQuery) || 
                              b.tour.toLowerCase().includes(state.searchQuery) || 
                              b.id.toLowerCase().includes(state.searchQuery);
        const matchesFilter = state.bookingFilter === 'All' || b.status === state.bookingFilter;
        return matchesSearch && matchesFilter;
    });

    const stats = {
        total: bookings.length,
        pending: bookings.filter(b => b.status === 'Pending').length,
        revenue: bookings.filter(b => b.status === 'Confirmed').reduce((sum, b) => sum + b.amount, 0)
    };

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Bookings Management</h1>
                    <p class="text-slate-500 mt-1">Review and manage your incoming travel requests</p>
                </div>
                <div class="flex items-center space-x-2 bg-slate-800/50 p-1 rounded-xl border border-white/5">
                    ${['All', 'Pending', 'Confirmed', 'Cancelled'].map(f => `
                        <button onclick="window.setBookingFilter('${f}')" 
                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition ${state.bookingFilter === f ? 'bg-primary text-white shadow-lg' : 'text-slate-400 hover:text-white'}">
                            ${f}
                        </button>
                    `).join('')}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Total Bookings</p>
                    <p class="text-2xl font-bold">${stats.total}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Pending Action</p>
                    <p class="text-2xl font-bold text-amber-400">${stats.pending}</p>
                </div>
                <div class="glass-card p-6 rounded-2xl border border-white/5">
                    <p class="text-xs text-slate-400 font-bold uppercase mb-2">Confirmed Revenue</p>
                    <p class="text-2xl font-bold text-emerald-400">$${stats.revenue.toLocaleString()}</p>
                </div>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
                <table class="w-full admin-table text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Booking Details</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Tour & Date</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Amount</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500">Status</th>
                            <th class="p-4 text-xs font-bold uppercase text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filteredBookings.length > 0 ? filteredBookings.map(booking => `
                            <tr class="border-t border-white/5 hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-primary">${booking.user.charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white group-hover:text-primary transition">${booking.user}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">${booking.id}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-medium">${booking.tour}</div>
                                    <div class="text-xs text-slate-500">${booking.date}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-bold text-secondary text-base">$${booking.amount}</div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold ${getBookingStatusClass(booking.status)}">
                                        <i class="fas fa-circle mr-1.5 text-[6px]"></i>
                                        ${booking.status}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        ${booking.status === 'Pending' ? `
                                            <button onclick="window.confirmBooking('${booking.id}')" class="p-2 hover:bg-emerald-500/20 text-emerald-500 rounded-lg transition" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="window.cancelBooking('${booking.id}')" class="p-2 hover:bg-rose-500/20 text-rose-500 rounded-lg transition" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        ` : ''}
                                        <button onclick="window.viewBookingDetails('${booking.id}')" class="p-2 hover:bg-white/10 text-slate-400 rounded-lg transition" title="Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="window.deleteBooking('${booking.id}')" class="p-2 hover:bg-rose-500/20 text-rose-500 rounded-lg transition" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('') : `
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-slate-700 mb-4"></i>
                                        <p class="text-slate-500 font-bold">No bookings found matching your criteria</p>
                                        <button onclick="window.setBookingFilter('All')" class="text-primary text-sm mt-2 hover:underline">Clear Filters</button>
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
                                            <span class="text-xs font-bold text-primary">${cust.name.charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white group-hover:text-primary transition">${cust.name}</div>
                                            <div class="text-[10px] text-slate-500 font-medium tracking-tight">${cust.email}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm">${cust.country || 'Not Set'}</td>
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
        
        // Calculate dynamic insights
        const totalConfirmed = bookings.filter(b => b.status === 'Confirmed').length;
        const avgBookingValue = Math.round(analytics.totalRevenue / analytics.totalBookings);
        
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
                ${renderAnalyticsStatCard('Total Revenue', `$${analytics.totalRevenue.toLocaleString()}`, 'fa-dollar-sign', 'text-emerald-400', '+18.4%')}
                ${renderAnalyticsStatCard('Conversion Rate', '4.2%', 'fa-bullseye', 'text-amber-400', '+0.8%')}
                ${renderAnalyticsStatCard('Avg. Order Value', `$${avgBookingValue}`, 'fa-receipt', 'text-blue-400', '-2.1%')}
                ${renderAnalyticsStatCard('Customer LTV', '$2,840', 'fa-user-graduate', 'text-purple-400', '+12%')}
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
                        ${[
                            { label: 'Africa', value: '35%', color: 'bg-[#006A72]' },
                            { label: 'Asia', value: '25%', color: 'bg-[#FFD214]' },
                            { label: 'Europe', value: '20%', color: 'bg-[#10B981]' }
                        ].map(item => `
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
                                    <th class="p-4 text-[10px] font-black uppercase text-slate-500 tracking-wider">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                ${analytics.popularTours.map(tour => `
                                    <tr class="hover:bg-white/5 transition group">
                                        <td class="p-4 flex items-center">
                                            <div class="w-8 h-8 rounded-lg bg-primary/10 mr-3 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <i class="fas fa-route text-primary text-[10px]"></i>
                                            </div>
                                            <span class="text-sm font-bold group-hover:text-primary transition truncate max-w-[150px]">${tour.name}</span>
                                        </td>
                                        <td class="p-4 text-sm font-medium">${tour.bookings}</td>
                                        <td class="p-4 text-sm font-bold text-secondary">$${parseFloat(tour.revenue || 0).toLocaleString()}</td>
                                        <td class="p-4">
                                            <span class="text-emerald-400 text-xs font-bold"><i class="fas fa-caret-up mr-1"></i>${Math.floor(Math.random()*15)+5}%</span>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <h2 class="text-xl font-bold mb-6">Device & Source Traffic</h2>
                    <div class="space-y-8">
                        ${[
                            { label: 'Direct Traffic', percent: 65, color: 'bg-primary' },
                            { label: 'Social Media', percent: 22, color: 'bg-secondary' },
                            { label: 'Referral', percent: 13, color: 'bg-emerald-400' }
                        ].map(source => `
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-400 uppercase tracking-widest">${source.label}</span>
                                    <span class="font-bold">${source.percent}%</span>
                                </div>
                                <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="${source.color} h-full rounded-full" style="width: ${source.percent}%"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="mt-12 p-4 rounded-xl bg-primary/5 border border-primary/10">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-lightbulb text-primary mt-1"></i>
                            <div>
                                <h4 class="text-sm font-bold text-white">Insight: Mobile users are growing</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Bookings from mobile devices have increased by 24% this month. Consider optimizing the checkout flow for smaller screens.</p>
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

function renderAnalyticsStatCard(title, value, icon, iconColor, trend) {
    const isPositive = trend.startsWith('+');
    return `
        <div class="glass-card rounded-2xl p-6 border border-white/5 hover:border-white/10 transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-dark-900 flex items-center justify-center">
                    <i class="fas ${icon} ${iconColor} text-lg"></i>
                </div>
                <span class="text-[10px] font-black px-2 py-1 rounded-lg ${isPositive ? 'bg-emerald-400/10 text-emerald-400' : 'bg-rose-500/10 text-rose-500'}">
                    ${trend}
                </span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-1">${title}</p>
                <p class="text-2xl font-bold">${value}</p>
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
                            <td class="p-4 font-bold text-sm text-white">${q.customer_name}</td>
                            <td class="p-4 text-sm text-slate-400">${q.tour_name}</td>
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
                            <td class="p-4 font-bold text-sm text-white">${i.customer_name}</td>
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
    const blogs = (state.data.blogPosts || []).filter(b =>
        b.title.toLowerCase().includes(state.searchQuery) ||
        (b.author || '').toLowerCase().includes(state.searchQuery) ||
        (b.category || '').toLowerCase().includes(state.searchQuery)
    );

    const published = (state.data.blogPosts || []).filter(b => b.status === 'Published').length;
    const drafts    = (state.data.blogPosts || []).filter(b => b.status === 'Draft').length;

    contentArea.innerHTML = `
        <div class="space-y-8 animate-fade-in pb-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Blog Posts</h1>
                    <p class="text-slate-500 mt-1">Create and manage articles that appear on your website</p>
                </div>
                <button onclick="window.openBlogModal()" class="bg-primary hover:bg-opacity-90 text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-pen-nib mr-2"></i> Write New Post
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Total Posts</p>
                    <p class="text-3xl font-black text-white">${(state.data.blogPosts || []).length}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Published</p>
                    <p class="text-3xl font-black text-emerald-400">${published}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-white/5">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Drafts</p>
                    <p class="text-3xl font-black text-amber-400">${drafts}</p>
                </div>
            </div>

            <!-- Blog Table -->
            <div class="glass-card rounded-2xl overflow-hidden border border-white/5 shadow-2xl">
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
                                    ${b.image ? `<img src="${b.image.startsWith('assets') ? '../' + b.image : b.image}" class="w-12 h-12 rounded-xl object-cover border border-white/10 flex-shrink-0">` : `<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-newspaper text-primary text-sm"></i></div>`}
                                    <div>
                                        <p class="font-bold text-sm text-white leading-tight line-clamp-2 max-w-xs">${b.title}</p>
                                        <p class="text-[10px] text-slate-600 mt-0.5 font-mono">${b.slug || ''}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-slate-400">${b.author || 'Admin'}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-primary/10 text-primary">${b.category || 'Travel'}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase ${b.status === 'Published' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-400/10 text-amber-400'}">${b.status}</span>
                            </td>
                            <td class="p-4 text-xs text-slate-500">${new Date(b.created_at).toLocaleDateString('en-GB', { day:'numeric', month:'short', year:'numeric' })}</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="../blog-detail.php?slug=${b.slug}" target="_blank" class="p-2 hover:bg-white/10 text-slate-400 hover:text-white rounded-lg transition" title="Preview"><i class="fas fa-eye text-xs"></i></a>
                                    <button onclick="window.openBlogModal(${b.id})" class="p-2 hover:bg-primary/10 text-slate-400 hover:text-primary rounded-lg transition" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                                    <button onclick="window.deleteBlog(${b.id})" class="p-2 hover:bg-rose-500/10 text-slate-400 hover:text-rose-500 rounded-lg transition" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        `).join('')}
                    </tbody>
                </table>
                ` : `
                <div class="py-20 text-center">
                    <i class="fas fa-newspaper text-6xl text-slate-800 mb-6 block"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No Blog Posts Yet</h3>
                    <p class="text-slate-500 mb-6">Start writing to engage your audience and drive traffic to your site.</p>
                    <button onclick="window.openBlogModal()" class="bg-primary text-white px-8 py-3 rounded-2xl font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">Write Your First Post</button>
                </div>
                `}
            </div>
        </div>
    `;
}

window.openBlogModal = (blogId = null) => {
    const blog = blogId ? (state.data.blogPosts || []).find(b => b.id === blogId) : null;

    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <form id="blog-form" class="p-8 space-y-6 max-h-[90vh] overflow-y-auto custom-scroll">
            <div class="flex items-center justify-between sticky top-0 bg-dark-800 z-10 pb-4 border-b border-white/5">
                <div>
                    <h2 class="text-2xl font-bold">${blog ? 'Edit Blog Post' : 'Write New Post'}</h2>
                    <p class="text-slate-500 text-xs mt-1">${blog ? 'Update your article details below' : 'Craft your article and publish when ready'}</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white"><i class="fas fa-times text-xl"></i></button>
            </div>

            <div class="grid grid-cols-2 gap-6 text-sm">
                <!-- Title -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Post Title *</label>
                    <input type="text" name="title" required value="${blog ? blog.title.replace(/"/g, '&quot;') : ''}"
                        class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition"
                        placeholder="e.g. Top 10 Safari Destinations in Africa">
                </div>

                <!-- Featured Image -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Featured Image</label>
                    <div id="blog-image-dropzone" class="relative group cursor-pointer border-2 border-dashed border-white/10 rounded-2xl p-4 hover:border-primary/50 transition bg-dark-900/50">
                        <input type="file" id="blog-image-input" accept="image/*" class="hidden">
                        <div id="blog-image-preview-placeholder" class="flex flex-col items-center justify-center space-y-2 ${blog && blog.image ? 'hidden' : ''}">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-400">Click or drag to upload featured image</p>
                            <p class="text-[10px] text-slate-600">JPG, PNG or WEBP (Max 5MB)</p>
                        </div>
                        <div id="blog-image-preview" class="relative ${blog && blog.image ? '' : 'hidden'}">
                            <img src="${blog && blog.image ? (blog.image.startsWith('assets') ? '../' + blog.image : blog.image) : ''}" id="blog-preview-img" class="w-full h-48 object-cover rounded-xl border border-white/5 shadow-2xl">
                            <button type="button" id="blog-remove-img" class="absolute top-2 right-2 bg-rose-500 text-white size-8 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="image" id="blog-image-hidden" value="${blog ? (blog.image || '') : ''}">
                </div>

                <!-- Author & Category -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Author</label>
                    <input type="text" name="author" value="${blog ? (blog.author || 'Admin').replace(/"/g, '&quot;') : 'Admin'}"
                        class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition"
                        placeholder="Your name">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Category</label>
                    <select name="category" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        ${['Travel Tips', 'Culture', 'Planning', 'Photography', 'Adventure', 'Luxury', 'Food & Cuisine', 'News'].map(c => `
                            <option value="${c}" ${blog && blog.category === c ? 'selected' : ''}>${c}</option>
                        `).join('')}
                    </select>
                </div>

                <!-- Tags & Status -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tags <span class="text-slate-600 normal-case">(comma-separated)</span></label>
                    <input type="text" name="tags" value="${blog ? (blog.tags || '').replace(/"/g, '&quot;') : ''}"
                        class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition"
                        placeholder="safari, kenya, wildlife">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Status</label>
                    <div class="flex p-1 bg-dark-900 border border-white/10 rounded-xl">
                        <label class="flex-1 text-center py-2.5 rounded-lg cursor-pointer text-xs font-bold transition ${(!blog || blog.status === 'Published') ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:text-white'}">
                            <input type="radio" name="status" value="Published" class="hidden" ${(!blog || blog.status === 'Published') ? 'checked' : ''}
                                onchange="this.parentElement.parentElement.querySelectorAll('label').forEach(l=>{l.classList.remove('bg-emerald-500','bg-slate-700','text-white');l.classList.add('text-slate-500');}); this.parentElement.classList.remove('text-slate-500'); this.parentElement.classList.add('bg-emerald-500','text-white')">
                            <i class="fas fa-globe mr-1"></i> Published
                        </label>
                        <label class="flex-1 text-center py-2.5 rounded-lg cursor-pointer text-xs font-bold transition ${blog && blog.status === 'Draft' ? 'bg-slate-700 text-white' : 'text-slate-500 hover:text-white'}">
                            <input type="radio" name="status" value="Draft" class="hidden" ${blog && blog.status === 'Draft' ? 'checked' : ''}
                                onchange="this.parentElement.parentElement.querySelectorAll('label').forEach(l=>{l.classList.remove('bg-emerald-500','bg-slate-700','text-white');l.classList.add('text-slate-500');}); this.parentElement.classList.remove('text-slate-500'); this.parentElement.classList.add('bg-slate-700','text-white')">
                            <i class="fas fa-file-alt mr-1"></i> Draft
                        </label>
                    </div>
                </div>

                <!-- Excerpt -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Excerpt <span class="text-slate-600 normal-case">(short summary shown in listings)</span></label>
                    <textarea name="excerpt" rows="2" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition resize-none" placeholder="A brief summary of the post (1-2 sentences)...">${blog ? (blog.excerpt || '') : ''}</textarea>
                </div>

                <!-- Full Content -->
                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Full Content <span class="text-slate-600 normal-case">(supports basic HTML)</span></label>
                    <textarea name="content" rows="10" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition font-mono text-xs" placeholder="Write your article here. You can use HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;h2&gt;, etc.">${blog ? (blog.content || '') : ''}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-white/5 sticky bottom-0 bg-dark-800 z-10">
                <button type="button" onclick="window.closeModal()" class="px-8 py-3 rounded-xl text-slate-500 hover:text-white font-bold transition">Discard</button>
                <button type="submit" class="bg-primary px-10 py-3 rounded-xl text-white font-bold shadow-xl shadow-primary/20 hover:scale-105 transition">
                    ${blog ? 'Update Post' : 'Publish Post'}
                </button>
            </div>
        </form>
    `;

    // Image upload handlers
    const dropzone   = document.getElementById('blog-image-dropzone');
    const fileInput  = document.getElementById('blog-image-input');
    const placeholder = document.getElementById('blog-image-preview-placeholder');
    const previewDiv  = document.getElementById('blog-image-preview');
    const previewImg  = document.getElementById('blog-preview-img');
    const hiddenInput = document.getElementById('blog-image-hidden');
    const removeBtn   = document.getElementById('blog-remove-img');

    dropzone.onclick = (e) => {
        if (e.target !== removeBtn && !removeBtn.contains(e.target)) fileInput.click();
    };
    fileInput.onchange = (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            previewImg.src = ev.target.result;
            hiddenInput.value = ev.target.result;
            placeholder.classList.add('hidden');
            previewDiv.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    };
    removeBtn.onclick = (e) => {
        e.stopPropagation();
        previewImg.src = '';
        hiddenInput.value = '';
        fileInput.value = '';
        placeholder.classList.remove('hidden');
        previewDiv.classList.add('hidden');
    };

    // Form submit
    document.getElementById('blog-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (blog) formData.append('id', blog.id);

        const submitBtn = e.target.querySelector('[type="submit"]');
        const origLabel = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

        try {
            const res  = await fetch('api/save-blog.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Save failed');

            const blogData = {
                id:         data.id,
                title:      formData.get('title'),
                slug:       data.slug,
                excerpt:    formData.get('excerpt'),
                content:    formData.get('content'),
                image:      data.image || formData.get('image'),
                author:     formData.get('author'),
                category:   formData.get('category'),
                tags:       formData.get('tags'),
                status:     formData.get('status'),
                created_at: blog ? blog.created_at : new Date().toISOString(),
            };

            if (!state.data.blogPosts) state.data.blogPosts = [];
            if (blog) {
                state.data.blogPosts = state.data.blogPosts.map(b => b.id === blog.id ? blogData : b);
            } else {
                state.data.blogPosts.unshift(blogData);
            }

            showToast(blog ? 'Post updated successfully' : 'Post published successfully');
            renderBlogs();
            window.closeModal();
        } catch (err) {
            showToast('Error saving post: ' + err.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = origLabel;
        }
    };
};

window.deleteBlog = async (id) => {
    if (!confirm('Delete this blog post permanently? This cannot be undone.')) return;
    try {
        const res  = await fetch(`api/delete-blog.php?id=${id}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Delete failed');
        state.data.blogPosts = (state.data.blogPosts || []).filter(b => b.id !== id);
        showToast('Post deleted');
        renderBlogs();
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
    if (action.includes('Booking')) return 'fa-shopping-cart';
    if (action.includes('tour')) return 'fa-plus';
    return 'fa-comment-alt';
}

function getEventTypeColor(type) {
    switch (type) {
        case 'tour': return 'bg-primary/20 text-primary';
        case 'meeting': return 'bg-amber-400/20 text-amber-400';
        case 'promo': return 'bg-emerald-400/20 text-emerald-400';
        default: return 'bg-slate-400/20 text-slate-400';
    }
}

function renderCalendarDays() {
    const days = [];
    const date = new Date();
    const month = date.getMonth();
    const year = date.getFullYear();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Adjust for Monday start (default is Sunday=0)
    const offset = firstDay === 0 ? 6 : firstDay - 1;

    for (let i = 0; i < offset; i++) {
        days.push(`<div class="h-10 border border-white/5 rounded-lg opacity-20"></div>`);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const currentDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const hasEvent = state.data.events.some(e => e.date === currentDate);
        const isToday = d === date.getDate();
        
        days.push(`
            <div class="h-10 relative group border border-white/5 rounded-lg flex items-center justify-center text-xs font-bold ${isToday ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'hover:bg-white/5'} transition cursor-pointer">
                ${d}
                ${hasEvent ? `<span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-secondary"></span>` : ''}
                ${hasEvent ? `
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 p-2 bg-slate-900 border border-white/10 rounded-lg text-[9px] whitespace-nowrap opacity-0 group-hover:opacity-100 transition pointer-events-none z-50">
                        ${state.data.events.find(e => e.date === currentDate).title}
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
    
    new Chart(ctx, {
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
    });
}

function initAnalyticsCharts() {
    if (typeof Chart === 'undefined') return;

    const revCtx = document.getElementById('analyticsRevenueChart');
    const distCtx = document.getElementById('bookingsDoughnut');
    
    if (revCtx) {
        const gradient = revCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 106, 114, 0.4)');
        gradient.addColorStop(1, 'rgba(0, 106, 114, 0)');

        new Chart(revCtx, {
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
        });
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

        new Chart(distCtx, {
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
        });
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
                    <input type="text" name="title" required value="${tour ? tour.title : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. Serengeti Luxury Safari">
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
                            <p class="text-[10px] text-slate-600">JPG, PNG or WEBP (Max 2MB)</p>
                        </div>
                        <div id="image-preview" class="relative ${tour && tour.image ? '' : 'hidden'}">
                            <img src="${tour && tour.image ? (tour.image.startsWith('assets') ? '../' + tour.image : tour.image) : ''}" id="preview-img" class="w-full h-48 object-cover rounded-xl border border-white/5 shadow-2xl">
                            <button type="button" id="remove-img" class="absolute top-2 right-2 bg-rose-500 text-white size-8 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="image" id="image-path-hidden" value="${tour ? (tour.image || '') : ''}">
                </div>

                <!-- Destination & Price Row -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Destination</label>
                    <select name="location" required class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        <option value="" disabled ${!tour ? 'selected' : ''}>Select Destination</option>
                        ${destinations.map(d => `
                            <option value="${d.name}" ${tour && tour.location === d.name ? 'selected' : ''}>${d.name}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Price ($)</label>
                    <input type="number" name="price" required value="${tour ? tour.price : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="1200">
                </div>

                <!-- Duration & Category Row -->
                <div class="space-y-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Duration</label>
                    <input type="text" name="duration" required value="${tour ? tour.duration : ''}" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="e.g. 5 Days / 4 Nights">
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
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Home Page Visibility</label>
                    <div class="flex items-center space-x-3 p-3.5 bg-dark-900 border border-white/10 rounded-xl">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="showOnHome" class="sr-only peer" ${tour && (tour.show_on_home || tour.showOnHome) ? 'checked' : ''} onchange="document.getElementById('home-section-container').classList.toggle('opacity-50', !this.checked); document.getElementById('home-section-container').classList.toggle('pointer-events-none', !this.checked)">
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                        <span class="text-xs font-bold text-slate-400">Featured on Home</span>
                    </div>
                </div>

                <div id="home-section-container" class="space-y-2 col-span-2 transition-opacity ${(!tour || (!tour.show_on_home && !tour.showOnHome)) ? 'opacity-50 pointer-events-none' : ''}">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Home Page Section</label>
                    <select name="homeSection" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition appearance-none">
                        ${['Explore Popular Tours', 'We Recommend', 'Marketing Carousel', 'Main Header Slider'].map(s => `
                            <option value="${s}" ${tour && tour.homeSection === s ? 'selected' : ''}>${s}</option>
                        `).join('')}
                    </select>
                </div>

                <div class="space-y-2 col-span-2">
                    <label class="text-xs text-slate-500 uppercase font-bold tracking-wider">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-dark-900 border border-white/10 rounded-xl p-3.5 outline-none focus:border-primary transition" placeholder="Short highlights and key features...">${tour ? (tour.description || '') : ''}</textarea>
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

    fileInput.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImg.src = event.target.result;
                pathHidden.value = event.target.result; // Store as data URL for prototype
                previewContainer.classList.add('hidden');
                previewDiv.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
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
            const res  = await fetch('api/save-tour.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Save failed');

            const tourData = {
                id:          data.id,
                title:       formData.get('title'),
                image:       data.image || formData.get('image'),
                price:       parseInt(formData.get('price')),
                location:    formData.get('location'),
                duration:    formData.get('duration'),
                category:    formData.get('category'),
                status:      formData.get('status'),
                description: formData.get('description'),
                show_on_home: formData.get('showOnHome') === 'on' ? 1 : 0,
                home_section: formData.get('homeSection'),
                rating:      tour ? tour.rating : 5.0
            };

            if (tour) {
                state.data.tours = state.data.tours.map(t => t.id === tour.id ? tourData : t);
            } else {
                state.data.tours.unshift(tourData);
            }

            saveState();
            showToast(tour ? 'Tour updated successfully' : 'Tour created successfully');
            renderTours();
            window.closeModal();
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

window.viewTourPublic = (id) => {
    window.open('../tour-detail.php?id=' + id, '_blank');
};

window.deleteTour = async (id) => {
    if (!confirm('Are you sure you want to delete this tour? This cannot be undone.')) return;
    try {
        const res  = await fetch(`api/delete-tour.php?id=${id}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Delete failed');
        state.data.tours = state.data.tours.filter(t => t.id !== id);
        saveState();
        showToast('Tour deleted');
        renderTours();
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
            const response = await fetch('api/save-destination.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                showToast(dest ? 'Destination updated' : 'Destination added');
                location.reload();
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
        const response = await fetch(`api/delete-destination.php?id=${id}`);
        const result = await response.json();
        if (result.success) {
            showToast('Destination deleted');
            location.reload();
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
};

window.confirmBooking = async (id) => {
    try {
        const res = await fetch('api/update-booking-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&status=Confirmed`
        });
        const data = await res.json();
        if (data.success) {
            state.data.bookings = state.data.bookings.map(b =>
                b.id == id ? { ...b, status: 'Confirmed' } : b
            );
            showToast('Booking confirmed successfully');
            renderBookings();
        } else {
            showToast(data.error || 'Failed to confirm booking', 'error');
        }
    } catch (e) { showToast('Network error. Please try again.', 'error'); }
};

window.cancelBooking = async (id) => {
    try {
        const res = await fetch('api/update-booking-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&status=Cancelled`
        });
        const data = await res.json();
        if (data.success) {
            state.data.bookings = state.data.bookings.map(b =>
                b.id == id ? { ...b, status: 'Cancelled' } : b
            );
            showToast('Booking cancelled');
            renderBookings();
        } else {
            showToast(data.error || 'Failed to cancel booking', 'error');
        }
    } catch (e) { showToast('Network error. Please try again.', 'error'); }
};

window.deleteBooking = async (id) => {
    if (!confirm('Delete this booking permanently?')) return;
    try {
        const res = await fetch('api/delete-booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}`
        });
        const data = await res.json();
        if (data.success) {
            state.data.bookings = state.data.bookings.filter(b => b.id != id);
            showToast('Booking deleted');
            renderBookings();
        } else {
            showToast(data.error || 'Failed to delete booking', 'error');
        }
    } catch (e) { showToast('Network error. Please try again.', 'error'); }
};

window.viewBookingDetails = (id) => {
    const booking = state.data.bookings.find(b => b.id === id);
    if (!booking) return;

    // Parse packed tour field: "[Booking] Tour Name | Phone: x | Travelers: N | Note: y"
    const raw = booking.tour || '';
    let type = 'Booking';
    let tourName = raw;
    let phone = '—';
    let travelers = '—';
    let note = '—';

    const prefixMatch = raw.match(/^\[(Booking|Enquiry)\]\s*/);
    if (prefixMatch) {
        type = prefixMatch[1];
        tourName = raw.slice(prefixMatch[0].length);
    }

    tourName.split('|').slice(1).map(s => s.trim()).forEach(seg => {
        if (seg.startsWith('Phone:'))     phone     = seg.slice(6).trim();
        else if (seg.startsWith('Travelers:')) travelers = seg.slice(10).trim();
        else if (seg.startsWith('Note:'))  note      = seg.slice(5).trim();
    });
    tourName = tourName.split('|')[0].trim();

    const statusClass = getBookingStatusClass(booking.status);
    const typeColor = type === 'Enquiry' ? 'text-sky-400 bg-sky-400/10' : 'text-amber-400 bg-amber-400/10';

    modalOverlay.classList.remove('hidden');
    modalContent.innerHTML = `
        <div class="p-8 space-y-6 max-w-lg w-full">
            <div class="flex items-start justify-between border-b border-white/5 pb-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold ${typeColor}">
                            <i class="fas fa-${type === 'Enquiry' ? 'question-circle' : 'calendar-check'} mr-1.5"></i>
                            ${type}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold ${statusClass}">
                            <i class="fas fa-circle mr-1.5 text-[6px]"></i>
                            ${booking.status}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-white">${tourName}</h2>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">${booking.id}</p>
                </div>
                <button type="button" onclick="window.closeModal()" class="text-slate-500 hover:text-white text-lg mt-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Client Name</p>
                    <p class="text-white font-medium">${booking.user || '—'}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Email</p>
                    <p class="text-white font-medium break-all">${booking.email || '—'}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Phone</p>
                    <p class="text-white font-medium">${phone}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Travelers</p>
                    <p class="text-white font-medium">${travelers}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Travel Date</p>
                    <p class="text-white font-medium">${booking.date || '—'}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Submitted</p>
                    <p class="text-white font-medium">${booking.created_at ? new Date(booking.created_at).toLocaleDateString() : '—'}</p>
                </div>
            </div>

            ${note !== '—' ? `
            <div class="bg-white/5 rounded-xl p-4 space-y-1">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Client Note</p>
                <p class="text-sm text-slate-300">${note}</p>
            </div>` : ''}

            ${booking.status === 'Pending' ? `
            <div class="flex gap-3 pt-2">
                <button onclick="window.confirmBooking('${booking.id}'); window.closeModal();"
                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition text-sm">
                    <i class="fas fa-check mr-2"></i> Confirm
                </button>
                <button onclick="window.cancelBooking('${booking.id}'); window.closeModal();"
                        class="flex-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 font-bold py-3 rounded-xl transition text-sm">
                    <i class="fas fa-times mr-2"></i> Cancel
                </button>
            </div>` : ''}
        </div>
    `;
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
            const response = await fetch('api/save-customer.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                showToast(cust ? 'Client profile updated' : 'New client registered');
                location.reload();
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
        const response = await fetch(`api/delete-customer.php?id=${id}`);
        const result = await response.json();
        if (result.success) {
            showToast('Client deleted');
            location.reload();
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
            const response = await fetch('api/add-booking.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                const newBooking = {
                    id: result.booking_id,
                    user: formData.get('user_name'),
                    email: formData.get('email'),
                    tour: formData.get('tour_name'),
                    date: formData.get('booking_date'),
                    amount: parseFloat(formData.get('amount')),
                    status: formData.get('status'),
                    created_at: new Date().toISOString()
                };
                state.data.bookings.unshift(newBooking);
                saveState();
                showToast('Booking created successfully');
                renderBookings();
                window.closeModal();
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
                        ${customers.map(c => `<option value="${c.id}" ${quote && quote.customer_id === c.id ? 'selected' : ''}>${c.name} (${c.id})</option>`).join('')}
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
            const res = await fetch('api/save-quotation.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) { showToast(quoteId ? 'Quotation updated' : 'Quotation created'); window.closeModal(); location.reload(); }
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
                        ${customers.map(c => `<option value="${c.id}" ${inv && inv.customer_id === c.id ? 'selected' : ''}>${c.name} (${c.id})</option>`).join('')}
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
            const res = await fetch('api/save-invoice.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) { showToast(invoiceId ? 'Invoice updated' : 'Invoice created'); window.closeModal(); location.reload(); }
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
            const res = await fetch('api/save-expense.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) { showToast('Expense recorded'); window.closeModal(); location.reload(); }
            else { showToast(data.message || 'Error saving expense', 'error'); }
        } catch (err) { console.error(err); showToast('Network error while saving', 'error'); }
    });
};

window.deleteExpense = async (id) => {
    if (!confirm('Are you sure you want to delete this expense record?')) return;
    try {
        const res = await fetch(`api/delete-expense.php?id=${id}`);
        const data = await res.json();
        if (data.success) { showToast('Expense deleted'); location.reload(); }
        else { showToast(data.error || 'Failed to delete expense', 'error'); }
    } catch (err) { showToast('Network error', 'error'); console.error(err); }
};

window.markInvoicePaid = async (id) => {
    if (!confirm('Mark this invoice as Paid?')) return;
    try {
        const res = await fetch('api/update-invoice-status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&status=Paid`
        });
        const data = await res.json();
        if (data.success) { showToast('Invoice marked as Paid'); location.reload(); }
        else { showToast(data.error || 'Failed to update invoice', 'error'); }
    } catch (err) { showToast('Network error', 'error'); console.error(err); }
};
