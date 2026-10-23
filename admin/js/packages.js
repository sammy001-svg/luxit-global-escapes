/*
 * Package manager: the Tour Packages list and the full-page package editor.
 *
 * Loaded after main.js and uses its globals (state, contentArea, apiPost,
 * refreshData, showToast, escHtml, PACKAGE_PAGES, TRAVEL_STYLES,
 * HOMEPAGE_SECTIONS, tourPages, tourHomeSections, prepareTourImage).
 * Saving posts the same fields as before to api/save-tour.php.
 */

const PKG_CATEGORIES = ['Safari', 'Beach', 'Luxury', 'Adventure', 'Culture', 'City', 'Honeymoon', 'Family', 'Wellness'];
const PKG_STATUSES = [
    { key: 'Active',   label: 'Live',     cls: 'bg-emerald-500/15 text-emerald-400', hint: 'Visible on the website' },
    { key: 'Draft',    label: 'Draft',    cls: 'bg-amber-400/15 text-amber-400',     hint: 'Hidden while you work on it' },
    { key: 'Inactive', label: 'Archived', cls: 'bg-slate-500/20 text-slate-400',     hint: 'Hidden, kept for later' },
];
const pkgStatus = (s) => PKG_STATUSES.find(x => x.key === s) || PKG_STATUSES[1];
const pkgImg = (src) => escHtml(String(src || '').startsWith('assets') ? '../' + src : (src || ''));
const pkgLines = (v) => String(v || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
const pkgMoney = (n) => '$' + Number(n || 0).toLocaleString('en-US', { maximumFractionDigits: 0 });

/** What a package still needs to look complete on its public page. */
function pkgChecklist(t) {
    return [
        { ok: !!t.image,                          label: 'Cover photo' },
        { ok: String(t.description || '').trim().length >= 80, label: 'Description (80+ characters)' },
        { ok: pkgLines(t.highlights).length >= 3, label: 'At least 3 highlights' },
        { ok: pkgLines(t.itinerary).length >= 1,  label: 'Day-by-day itinerary' },
        { ok: pkgLines(t.inclusions).length >= 1, label: "What's included" },
        { ok: tourPages(t).length >= 1,           label: 'Listed on a package page' },
    ];
}

state.pkgSort = state.pkgSort || 'updated';
state.editingTour = null;   // set while the editor is open (blocks background refresh)

// ════════════════════════════════════════════════════════════════════════
// List
// ════════════════════════════════════════════════════════════════════════
function renderTours() {
    if (state.editingTour) return renderTourEditor();

    const tours = state.data.tours || [];
    const q = state.searchQuery || '';
    let list = tours.filter(t => {
        const hay = `${t.title} ${t.location} ${t.category}`.toLowerCase();
        return (!q || hay.includes(q))
            && (state.tourStatusFilter === 'All' || t.status === state.tourStatusFilter)
            && (state.tourPageFilter === 'All' || tourPages(t).includes(state.tourPageFilter));
    });
    const sorters = {
        updated:   (a, b) => Number(b.id) - Number(a.id),
        title:     (a, b) => String(a.title).localeCompare(String(b.title)),
        priceAsc:  (a, b) => Number(a.price) - Number(b.price),
        priceDesc: (a, b) => Number(b.price) - Number(a.price),
    };
    list = list.slice().sort(sorters[state.pkgSort] || sorters.updated);

    const live = tours.filter(t => t.status === 'Active');
    const needs = live.filter(t => pkgChecklist(t).some(c => !c.ok));
    const orphans = live.filter(t => tourPages(t).length === 0);
    const stats = [
        ['Live packages', live.length, 'text-emerald-400', 'fa-globe'],
        ['Drafts', tours.filter(t => t.status === 'Draft').length, 'text-amber-400', 'fa-pen'],
        ['On the homepage', live.filter(t => tourHomeSections(t).length).length, 'text-secondary', 'fa-house'],
        ['Need attention', needs.length, needs.length ? 'text-rose-400' : 'text-slate-400', 'fa-circle-exclamation'],
    ];
    const pill = (active, label, onclick) =>
        `<button onclick="${onclick}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition ${active ? 'bg-primary text-white' : 'text-slate-400 hover:text-white'}">${label}</button>`;

    contentArea.innerHTML = `
        <div class="space-y-6 animate-fade-in pb-12">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold">Packages</h1>
                    <p class="text-slate-500 mt-1">Create, update and publish the tours shown on your website.</p>
                </div>
                <button onclick="window.openTourModal()" class="bg-primary hover:bg-primary/80 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i>New package
                </button>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                ${stats.map(([label, n, cls, icon]) => `
                <div class="glass-card p-5 rounded-2xl border border-white/5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center ${cls}"><i class="fas ${icon}"></i></div>
                    <div><p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">${label}</p><p class="text-2xl font-bold ${cls}">${n}</p></div>
                </div>`).join('')}
            </div>

            ${orphans.length ? `
            <div class="rounded-2xl p-4 bg-amber-500/10 border border-amber-500/30 text-amber-200 text-sm">
                <i class="fas fa-triangle-exclamation mr-2"></i><b>${orphans.length} live package${orphans.length > 1 ? 's are' : ' is'} not on any package page</b>, so ${orphans.length > 1 ? 'they do' : 'it does'} not appear on the website:
                ${orphans.map(o => `<button onclick="window.openTourModal(${Number(o.id)})" class="underline hover:text-white ml-1">${escHtml(o.title)}</button>`).join(', ')}
            </div>` : ''}

            <div class="glass-card rounded-2xl border border-white/5 p-3 flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 flex-1 min-w-56 bg-dark-900 border border-white/10 rounded-xl px-3">
                    <i class="fas fa-search text-slate-500 text-sm"></i>
                    <input id="pkg-search" type="search" value="${escHtml(q)}" placeholder="Search by name, place or category" class="bg-transparent outline-none text-sm py-2.5 w-full">
                </label>
                <div class="flex items-center bg-dark-900 border border-white/10 rounded-xl p-1">
                    ${pill(state.tourStatusFilter === 'All', 'All', "window.setTourStatusFilter('All')")}
                    ${PKG_STATUSES.map(s => pill(state.tourStatusFilter === s.key, s.label, `window.setTourStatusFilter('${s.key}')`)).join('')}
                </div>
                <div class="flex items-center bg-dark-900 border border-white/10 rounded-xl p-1">
                    ${pill(state.tourPageFilter === 'All', 'All pages', "window.setTourPageFilter('All')")}
                    ${PACKAGE_PAGES.map(p => pill(state.tourPageFilter === p.key, p.label, `window.setTourPageFilter('${p.key}')`)).join('')}
                </div>
                <select id="pkg-sort" class="bg-dark-900 border border-white/10 rounded-xl px-3 py-2.5 text-sm outline-none">
                    ${[['updated', 'Newest first'], ['title', 'Name A–Z'], ['priceAsc', 'Price: low to high'], ['priceDesc', 'Price: high to low']]
                        .map(([v, l]) => `<option value="${v}" ${state.pkgSort === v ? 'selected' : ''}>${l}</option>`).join('')}
                </select>
            </div>

            <div class="glass-card rounded-2xl border border-white/5 overflow-hidden">
                ${list.length ? `
                <div class="hidden lg:grid grid-cols-[minmax(0,2.4fr)_minmax(0,0.9fr)_minmax(0,1.2fr)_minmax(0,1.4fr)_auto] gap-4 px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-white/5">
                    <span>Package</span><span>Price</span><span>Status</span><span>Appears on</span><span class="text-right pr-1">Actions</span>
                </div>
                <ul>
                ${list.map(t => {
                    const st = pkgStatus(t.status);
                    const missing = pkgChecklist(t).filter(c => !c.ok).length;
                    const where = [...tourPages(t), ...new Set(tourHomeSections(t).map(k => k === 'offers' ? 'Offer' : 'Home'))];
                    return `
                    <li class="grid lg:grid-cols-[minmax(0,2.4fr)_minmax(0,0.9fr)_minmax(0,1.2fr)_minmax(0,1.4fr)_auto] gap-3 lg:gap-4 items-center px-5 py-4 border-t border-white/5 hover:bg-white/[0.03] transition">
                        <button class="flex items-center gap-4 text-left min-w-0" onclick="window.openTourModal(${Number(t.id)})">
                            ${t.image ? `<img src="${pkgImg(t.image)}" alt="" class="w-16 h-12 rounded-lg object-cover shrink-0">`
                                      : `<div class="w-16 h-12 rounded-lg bg-white/5 shrink-0 flex items-center justify-center text-slate-600" title="No photo"><i class="fas fa-image"></i></div>`}
                            <span class="min-w-0">
                                <span class="block font-bold text-white truncate hover:text-primary">${escHtml(t.title)}</span>
                                <span class="block text-xs text-slate-500 truncate"><i class="fas fa-location-dot mr-1"></i>${escHtml(t.location)}${t.duration ? ' · ' + escHtml(t.duration) : ''}</span>
                                ${missing ? `<span class="inline-block mt-1 text-[10px] font-bold text-rose-300"><i class="fas fa-circle-exclamation mr-1"></i>${missing} thing${missing > 1 ? 's' : ''} to complete</span>` : ''}
                            </span>
                        </button>
                        <span class="text-sm font-bold text-secondary">${pkgMoney(t.price)}<span class="text-[10px] text-slate-500 font-normal"> pp</span></span>
                        <span>
                            <select onchange="window.setTourStatus(${Number(t.id)}, this.value)" class="text-xs font-bold rounded-lg px-2.5 py-1.5 border border-white/10 outline-none cursor-pointer ${st.cls}" title="Change status">
                                ${PKG_STATUSES.map(s => `<option value="${s.key}" ${s.key === t.status ? 'selected' : ''} class="bg-dark-800 text-white">${s.label}</option>`).join('')}
                            </select>
                        </span>
                        <span class="flex flex-wrap gap-1">
                            ${where.length ? where.map(w => `<span class="px-2 py-0.5 rounded-md text-[10px] font-bold ${w === 'Home' || w === 'Offer' ? 'bg-secondary/15 text-secondary' : 'bg-primary/15 text-teal-300'}">${escHtml(w)}</span>`).join('')
                                           : '<span class="text-[11px] text-amber-400">Not listed</span>'}
                        </span>
                        <span class="flex items-center justify-start lg:justify-end gap-1">
                            <button onclick="window.openTourModal(${Number(t.id)})" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-primary/20 text-xs font-bold transition">Edit</button>
                            <a href="../tour-detail.php?id=${Number(t.id)}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-400 flex items-center justify-center" title="View on website"><i class="fas fa-arrow-up-right-from-square text-xs"></i></a>
                            <button onclick="window.duplicateTour(${Number(t.id)})" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-400" title="Duplicate"><i class="fas fa-copy text-xs"></i></button>
                            <button onclick="window.deleteTour(${Number(t.id)})" class="w-8 h-8 rounded-lg hover:bg-rose-500/15 text-slate-400 hover:text-rose-400" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                        </span>
                    </li>`;
                }).join('')}
                </ul>` : `
                <div class="py-16 text-center">
                    <i class="fas fa-suitcase-rolling text-5xl text-slate-700 mb-4 block"></i>
                    <p class="font-bold">${tours.length ? 'No packages match these filters' : 'No packages yet'}</p>
                    ${tours.length ? `<button onclick="window.setTourStatusFilter('All'); window.setTourPageFilter('All'); document.getElementById('pkg-search').value=''; state.searchQuery=''; renderTours();" class="text-primary text-sm mt-2 hover:underline">Clear filters</button>`
                                   : `<button onclick="window.openTourModal()" class="mt-4 bg-primary text-white px-6 py-2.5 rounded-xl font-bold">Create your first package</button>`}
                </div>`}
            </div>
            <p class="text-xs text-slate-500">${list.length} of ${tours.length} packages</p>
        </div>`;

    const search = document.getElementById('pkg-search');
    search.addEventListener('input', () => {
        state.searchQuery = search.value.toLowerCase();
        const pos = search.selectionStart;
        renderTours();
        const s2 = document.getElementById('pkg-search');
        s2.focus(); s2.setSelectionRange(pos, pos);
    });
    document.getElementById('pkg-sort').addEventListener('change', (e) => { state.pkgSort = e.target.value; renderTours(); });
}

window.setTourStatusFilter = (s) => { state.tourStatusFilter = s; renderTours(); };
window.setTourPageFilter = (p) => { state.tourPageFilter = p; renderTours(); };

window.setTourStatus = async (id, status) => {
    try {
        const data = await apiPost('update-tour-status.php', { id, status });
        if (!data.success) throw new Error(data.error || 'Could not change the status');
        showToast(`Status changed to ${pkgStatus(status).label}`);
    } catch (err) {
        showToast(err.message, 'error');
    }
    await refreshData({ silent: true });
};

window.deleteTour = async (id) => {
    const t = state.data.tours.find(x => Number(x.id) === Number(id));
    if (!confirm(`Delete "${t ? t.title : 'this package'}" permanently?\n\nTip: set it to Archived instead to hide it but keep it for later.`)) return;
    try {
        const data = await apiPost('delete-tour.php', { id });
        if (!data.success) throw new Error(data.error || 'Delete failed');
        showToast('Package deleted');
        state.editingTour = null;
        await refreshData();
    } catch (err) {
        showToast('Could not delete the package: ' + err.message, 'error');
    }
};

window.duplicateTour = (id) => {
    const t = state.data.tours.find(x => Number(x.id) === Number(id));
    if (!t) return;
    openEditor({ ...t, id: null, title: 'Copy of ' + t.title, status: 'Draft', home_sections: '' }, true);
    showToast('Copy created as a draft. Change what you need and save.', 'info');
};

// ════════════════════════════════════════════════════════════════════════
// Editor (full page)
// ════════════════════════════════════════════════════════════════════════
window.openTourModal = (tourId = null) => {
    const t = tourId ? state.data.tours.find(x => Number(x.id) === Number(tourId)) : null;
    openEditor(t ? { ...t } : {
        id: null, title: '', location: '', price: '', duration: '', category: 'Safari', status: 'Draft', rating: 5,
        description: '', image: '', highlights: '', itinerary: '', inclusions: '', exclusions: '',
        package_pages: '', travel_styles: '', home_sections: '', promo_badge: '', promo_style: 'primary', promo_tagline: '',
    }, !t);
};

function openEditor(tour, isNew) {
    if (state.currentTab !== 'tours') {
        state.currentTab = 'tours';
        document.querySelectorAll('.sidebar-link').forEach(l => l.classList.toggle('active', l.dataset.tab === 'tours'));
    }
    state.editingTour = { tour, isNew, dirty: !!isNew && !!tour.title, image: tour.image || '', imageChanged: false };
    renderTourEditor();
    contentArea.scrollTop = 0;
}

/** Ask before discarding unsaved edits. Returns true if it is fine to leave. */
window.confirmLeaveEditor = () => {
    const ed = state.editingTour;
    if (!ed || !ed.dirty) return true;
    return confirm('You have unsaved changes to this package. Leave without saving?');
};

window.closeTourEditor = () => {
    if (!window.confirmLeaveEditor()) return;
    state.editingTour = null;
    renderTours();
    contentArea.scrollTop = 0;
};

function renderTourEditor() {
    const ed = state.editingTour;
    const t = ed.tour;
    const pages = tourPages(t);
    const styles = String(t.travel_styles || '').split(',').map(s => s.trim());
    const homes = tourHomeSections(t);
    const destOptions = [...new Set([...(window.ADMIN_DESTINATIONS || []), ...(state.data.tours || []).map(x => x.location).filter(Boolean)])];
    const categories = [...new Set([...PKG_CATEGORIES, ...(state.data.tours || []).map(x => x.category).filter(Boolean)])];
    const input = 'w-full bg-dark-900 border border-white/10 rounded-xl px-3.5 py-3 text-sm outline-none focus:border-primary transition';
    const label = 'block text-xs font-bold text-slate-300 mb-1.5';
    const hint = 'block text-[11px] text-slate-500 mt-1.5';
    const card = (id, icon, title, sub, body) => `
        <section id="${id}" class="glass-card rounded-2xl border border-white/5 scroll-mt-24">
            <div class="px-6 pt-5 pb-4 border-b border-white/5">
                <h2 class="font-bold flex items-center gap-2"><i class="fas ${icon} text-primary"></i>${title}</h2>
                ${sub ? `<p class="text-xs text-slate-500 mt-1">${sub}</p>` : ''}
            </div>
            <div class="p-6 space-y-5">${body}</div>
        </section>`;
    const chip = (name, value, on, text, icon, onchange = '') => `
        <label class="cursor-pointer">
            <input type="checkbox" name="${name}" value="${value}" class="sr-only peer" ${on ? 'checked' : ''} ${onchange}>
            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold transition border-white/10 bg-dark-900 text-slate-400 peer-checked:border-primary peer-checked:bg-primary/15 peer-checked:text-white hover:border-white/25">
                <i class="fas ${icon}"></i>${text}
            </span>
        </label>`;

    contentArea.innerHTML = `
    <form id="tour-form" class="animate-fade-in pb-16" novalidate>
        <div class="sticky -top-4 sm:-top-6 lg:-top-8 z-20 -mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8 mb-6 px-4 sm:px-6 lg:px-8 py-4 bg-dark-900 border-b border-white/10 shadow-lg shadow-black/20 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="window.closeTourEditor()" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 shrink-0" title="Back to packages"><i class="fas fa-arrow-left"></i></button>
                <div class="min-w-0">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">${ed.isNew ? 'New package' : 'Edit package'}</p>
                    <h1 id="ed-heading" class="text-lg font-bold truncate">${escHtml(t.title || 'Untitled package')}</h1>
                </div>
                <span id="ed-dirty" class="hidden text-[11px] font-bold text-amber-400"><i class="fas fa-circle text-[6px] align-middle mr-1"></i>Unsaved changes</span>
            </div>
            <div class="flex items-center gap-2">
                ${t.id ? `<a href="../tour-detail.php?id=${Number(t.id)}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-bold"><i class="fas fa-arrow-up-right-from-square mr-2"></i>View</a>` : ''}
                <button type="submit" data-close="1" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-sm font-bold">Save &amp; close</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/80 text-white text-sm font-bold shadow-lg shadow-primary/20" id="ed-save"><i class="fas fa-check mr-2"></i>Save</button>
            </div>
        </div>

        <div id="ed-error" class="hidden mb-6 rounded-xl p-4 bg-rose-500/10 border border-rose-500/30 text-rose-200 text-sm"></div>

        <div class="grid xl:grid-cols-[minmax(0,1fr)_340px] gap-6 items-start">
            <div class="space-y-6 min-w-0">
                ${card('ed-basics', 'fa-circle-info', 'Basics', 'The essentials shown on cards across the website.', `
                    <div>
                        <label class="${label}" for="ed-title">Package name *</label>
                        <input id="ed-title" name="title" class="${input} text-base font-semibold" maxlength="120" required value="${escHtml(t.title)}" placeholder="e.g. Maasai Mara Migration Safari">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="${label}" for="ed-location">Destination *</label>
                            <input id="ed-location" name="location" list="ed-dest-list" class="${input}" maxlength="255" required value="${escHtml(t.location)}" placeholder="e.g. Maasai Mara, Kenya">
                            <datalist id="ed-dest-list">${destOptions.map(d => `<option value="${escHtml(d)}">`).join('')}</datalist>
                            <span class="${hint}">Include the country name so the package shows on that destination page.</span>
                        </div>
                        <div>
                            <label class="${label}" for="ed-duration">Duration *</label>
                            <input id="ed-duration" name="duration" class="${input}" maxlength="40" required value="${escHtml(t.duration)}" placeholder="e.g. 6 Days / 5 Nights">
                        </div>
                        <div>
                            <label class="${label}" for="ed-price">Price per person (USD) *</label>
                            <div class="relative"><span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-sm">$</span>
                            <input id="ed-price" name="price" type="number" min="1" step="1" class="${input} pl-7" required value="${escHtml(t.price === '' ? '' : Math.round(Number(t.price)))}" placeholder="3200"></div>
                        </div>
                        <div class="grid grid-cols-[1fr_110px] gap-3">
                            <div>
                                <label class="${label}" for="ed-category">Category</label>
                                <input id="ed-category" name="category" list="ed-cat-list" class="${input}" maxlength="50" value="${escHtml(t.category)}">
                                <datalist id="ed-cat-list">${categories.map(c => `<option value="${escHtml(c)}">`).join('')}</datalist>
                            </div>
                            <div>
                                <label class="${label}" for="ed-rating">Rating</label>
                                <input id="ed-rating" name="rating" type="number" min="0" max="5" step="0.1" class="${input}" value="${escHtml(t.rating ?? 5)}">
                            </div>
                        </div>
                    </div>`)}

                ${card('ed-photo', 'fa-image', 'Cover photo', 'Used on the package page and every card. Landscape photos work best; large files are resized automatically.', `
                    <div id="ed-drop" class="relative rounded-2xl border-2 border-dashed border-white/10 hover:border-primary/60 transition cursor-pointer overflow-hidden bg-dark-900/50">
                        <input type="file" id="ed-file" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <img id="ed-preview" src="${pkgImg(ed.image)}" alt="" class="w-full aspect-[16/7] object-cover ${ed.image ? '' : 'hidden'}">
                        <div id="ed-placeholder" class="py-14 text-center ${ed.image ? 'hidden' : ''}">
                            <div class="w-14 h-14 rounded-full bg-primary/10 text-primary mx-auto flex items-center justify-center mb-3"><i class="fas fa-cloud-arrow-up text-xl"></i></div>
                            <p class="text-sm font-bold">Click or drop a photo here</p>
                            <p class="text-xs text-slate-500 mt-1">JPG, PNG or WEBP</p>
                        </div>
                    </div>
                    <div class="flex gap-2 ${ed.image ? '' : 'hidden'}" id="ed-photo-actions">
                        <button type="button" id="ed-replace" class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-bold"><i class="fas fa-arrows-rotate mr-2"></i>Replace</button>
                        <button type="button" id="ed-remove" class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 text-xs font-bold"><i class="fas fa-trash mr-2"></i>Remove</button>
                    </div>`)}

                ${card('ed-content', 'fa-align-left', 'Package page content', 'What travellers read before booking. One item per line in the lists; anything left empty is simply not shown.', `
                    <div>
                        <label class="${label}" for="ed-desc">Description</label>
                        <textarea id="ed-desc" name="description" rows="5" class="${input} leading-relaxed" placeholder="A short, inviting overview of the trip.">${escHtml(t.description)}</textarea>
                        <span class="${hint}" id="ed-desc-count"></span>
                    </div>
                    <div>
                        <label class="${label}" for="ed-hl">Highlights</label>
                        <textarea id="ed-hl" name="highlights" rows="4" class="${input}" placeholder="Twice-daily game drives in a private 4x4&#10;Two nights in a tented conservancy camp">${escHtml(t.highlights)}</textarea>
                    </div>
                    <div>
                        <label class="${label}" for="ed-it">Day-by-day itinerary</label>
                        <textarea id="ed-it" name="itinerary" rows="6" class="${input} font-mono text-[13px]" placeholder="Arrival in Nairobi: Meet your guide and transfer to your hotel.&#10;Into the Maasai Mara: Scenic drive and an afternoon game drive.">${escHtml(t.itinerary)}</textarea>
                        <span class="${hint}">One line per day as <b class="text-slate-300">Day title: what happens</b>. Days are numbered automatically.</span>
                        <div id="ed-it-preview" class="mt-3 space-y-1"></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="${label}" for="ed-inc">Included</label>
                            <textarea id="ed-inc" name="inclusions" rows="5" class="${input}" placeholder="Park entry fees&#10;All game drives">${escHtml(t.inclusions)}</textarea>
                        </div>
                        <div>
                            <label class="${label}" for="ed-exc">Not included</label>
                            <textarea id="ed-exc" name="exclusions" rows="5" class="${input}" placeholder="International flights&#10;Travel insurance">${escHtml(t.exclusions)}</textarea>
                        </div>
                    </div>`)}
            </div>

            <aside class="space-y-6 xl:sticky xl:top-20">
                ${card('ed-publish', 'fa-globe', 'Publishing', '', `
                    <div>
                        <span class="${label}">Status</span>
                        <div class="grid grid-cols-3 gap-1 p-1 bg-dark-900 border border-white/10 rounded-xl">
                            ${PKG_STATUSES.map(s => `
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="${s.key}" class="sr-only peer" ${t.status === s.key ? 'checked' : ''}>
                                <span class="block text-center py-2 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-primary peer-checked:text-white transition" title="${s.hint}">${s.label}</span>
                            </label>`).join('')}
                        </div>
                        <span class="${hint}" id="ed-status-hint"></span>
                    </div>
                    <div>
                        <span class="${label}">Package pages *</span>
                        <div class="flex flex-wrap gap-2">${PACKAGE_PAGES.map(p => chip('packagePages[]', p.key, pages.includes(p.key), p.label, p.icon, 'data-page')).join('')}</div>
                        <span class="${hint}">Where the package is listed. Pick at least one.</span>
                    </div>
                    <div>
                        <span class="${label}">Travel styles <span class="text-slate-500 font-normal">(optional)</span></span>
                        <div class="flex flex-wrap gap-2">${TRAVEL_STYLES.map(s => chip('travelStyles[]', s.key, styles.includes(s.key), s.label, s.icon)).join('')}</div>
                    </div>
                    <div>
                        <span class="${label}">Homepage <span class="text-slate-500 font-normal">(optional)</span></span>
                        <div class="flex flex-wrap gap-2" id="home-sections">${HOMEPAGE_SECTIONS.map(s => chip('homeSections[]', s.key, homes.includes(s.key), s.label, s.icon, `data-home="${s.page || ''}"`)).join('')}</div>
                        <span class="${hint}" id="ed-home-hint">Safari, Local and International need the matching package page.</span>
                    </div>
                    <div id="promo-fields" class="space-y-3 rounded-xl border border-white/10 bg-dark-900/60 p-4 ${homes.includes('offers') ? '' : 'hidden'}">
                        <p class="text-xs font-bold text-slate-300"><i class="fas fa-tag mr-1.5 text-secondary"></i>Special offer</p>
                        <div class="grid grid-cols-[1fr_110px] gap-2">
                            <input name="promoBadge" maxlength="30" class="${input} py-2.5" value="${escHtml(t.promo_badge)}" placeholder="Badge, e.g. Save 15%">
                            <select name="promoStyle" class="${input} py-2.5">
                                ${[['primary', 'Teal'], ['secondary', 'Green'], ['citrus', 'Orange'], ['red', 'Red']].map(([v, l]) => `<option value="${v}" ${t.promo_style === v ? 'selected' : ''}>${l}</option>`).join('')}
                            </select>
                        </div>
                        <input name="promoTagline" maxlength="90" class="${input} py-2.5" value="${escHtml(t.promo_tagline)}" placeholder="Tagline, e.g. Book by 31 March">
                    </div>`)}

                <section class="glass-card rounded-2xl border border-white/5 p-6">
                    <h2 class="font-bold flex items-center gap-2 mb-1"><i class="fas fa-list-check text-primary"></i>Completeness</h2>
                    <p class="text-xs text-slate-500 mb-4">Complete packages look more professional and sell better.</p>
                    <div class="h-2 rounded-full bg-white/5 overflow-hidden mb-4"><div id="ed-progress" class="h-full bg-primary transition-all"></div></div>
                    <ul id="ed-checklist" class="space-y-2 text-sm"></ul>
                </section>

                ${t.id ? `
                <section class="glass-card rounded-2xl border border-rose-500/20 p-6">
                    <h2 class="font-bold text-rose-300 mb-1">Delete package</h2>
                    <p class="text-xs text-slate-500 mb-3">To hide it but keep it, set the status to Archived instead.</p>
                    <button type="button" onclick="window.deleteTour(${Number(t.id)})" class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 text-xs font-bold"><i class="fas fa-trash mr-2"></i>Delete permanently</button>
                </section>` : ''}
            </aside>
        </div>
    </form>`;

    wireEditor();
}

function wireEditor() {
    const ed = state.editingTour;
    const form = document.getElementById('tour-form');
    const $ = (id) => document.getElementById(id);

    const markDirty = () => { ed.dirty = true; $('ed-dirty').classList.remove('hidden'); };
    if (ed.dirty) $('ed-dirty').classList.remove('hidden');

    // Snapshot of the form as a tour-like object, for the checklist.
    const current = () => {
        const fd = new FormData(form);
        return {
            image: ed.image, description: fd.get('description'), highlights: fd.get('highlights'),
            itinerary: fd.get('itinerary'), inclusions: fd.get('inclusions'),
            package_pages: fd.getAll('packagePages[]').join(','),
        };
    };
    const refreshChecklist = () => {
        const items = pkgChecklist(current());
        const done = items.filter(i => i.ok).length;
        $('ed-progress').style.width = Math.round(done / items.length * 100) + '%';
        $('ed-checklist').innerHTML = items.map(i => `
            <li class="flex items-center gap-2 ${i.ok ? 'text-slate-400' : 'text-slate-200'}">
                <i class="fas ${i.ok ? 'fa-circle-check text-emerald-400' : 'fa-circle text-slate-600 text-[10px] w-3.5 text-center'}"></i>${i.label}
            </li>`).join('');
        const d = $('ed-desc').value.trim().length;
        $('ed-desc-count').textContent = d ? `${d} characters` : '';
        const days = pkgLines($('ed-it').value);
        $('ed-it-preview').innerHTML = days.slice(0, 14).map((line, i) => {
            const p = line.indexOf(':');
            const title = p > 0 && p <= 80 ? line.slice(0, p) : `Day ${i + 1}`;
            return `<p class="text-[11px] text-slate-400"><span class="inline-block w-12 font-bold text-primary">Day ${i + 1}</span>${escHtml(title)}</p>`;
        }).join('');
        const st = form.querySelector('[name="status"]:checked');
        $('ed-status-hint').textContent = st ? pkgStatus(st.value).hint + '.' : '';
    };

    // Homepage sections need the matching package page; offers show promo fields.
    const syncHome = () => {
        const pages = new Set([...form.querySelectorAll('[name="packagePages[]"]:checked')].map(i => i.value));
        let unticked = [];
        form.querySelectorAll('[name="homeSections[]"]').forEach(i => {
            const need = i.dataset.home;
            const ok = !need || pages.has(need);
            i.disabled = !ok;
            i.closest('label').classList.toggle('opacity-40', !ok);
            if (!ok && i.checked) { i.checked = false; unticked.push(i.value); }
        });
        const offers = form.querySelector('[name="homeSections[]"][value="offers"]');
        $('promo-fields').classList.toggle('hidden', !(offers && offers.checked));
        if (unticked.length) showToast('Removed from the homepage section that needs that package page.', 'info');
    };

    form.addEventListener('input', (e) => {
        markDirty();
        if (e.target.id === 'ed-title') $('ed-heading').textContent = e.target.value || 'Untitled package';
        refreshChecklist();
    });
    form.addEventListener('change', (e) => {
        markDirty();
        if (e.target.name === 'packagePages[]' || e.target.name === 'homeSections[]') syncHome();
        refreshChecklist();
    });
    syncHome();
    refreshChecklist();

    // ── Photo ──
    const drop = $('ed-drop'), file = $('ed-file');
    const showImage = (src) => {
        $('ed-preview').src = src ? (src.startsWith('data:') ? src : pkgImg(src)) : '';
        $('ed-preview').classList.toggle('hidden', !src);
        $('ed-placeholder').classList.toggle('hidden', !!src);
        $('ed-photo-actions').classList.toggle('hidden', !src);
    };
    const useFile = async (f) => {
        if (!f) return;
        try {
            const dataUrl = await prepareTourImage(f);
            ed.image = dataUrl; ed.imageChanged = true;
            showImage(dataUrl); markDirty(); refreshChecklist();
        } catch (err) { showToast(err.message, 'error'); }
        file.value = '';
    };
    drop.addEventListener('click', () => file.click());
    $('ed-replace').addEventListener('click', () => file.click());
    $('ed-remove').addEventListener('click', () => { ed.image = ''; ed.imageChanged = true; showImage(''); markDirty(); refreshChecklist(); });
    file.addEventListener('change', () => useFile(file.files[0]));
    drop.addEventListener('dragover', (e) => { e.preventDefault(); drop.classList.add('border-primary'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('border-primary'));
    drop.addEventListener('drop', (e) => { e.preventDefault(); drop.classList.remove('border-primary'); useFile(e.dataTransfer.files[0]); });

    // ── Save ──
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const closeAfter = e.submitter && e.submitter.dataset.close === '1';
        const err = $('ed-error');
        err.classList.add('hidden');

        const fd = new FormData(form);
        const problems = [];
        if (!fd.get('title').trim()) problems.push(['ed-title', 'Give the package a name.']);
        if (!fd.get('location').trim()) problems.push(['ed-location', 'Add the destination.']);
        if (!fd.get('duration').trim()) problems.push(['ed-duration', 'Add the duration, e.g. 6 Days / 5 Nights.']);
        if (!(Number(fd.get('price')) > 0)) problems.push(['ed-price', 'Add a price per person.']);
        if (!fd.getAll('packagePages[]').length) problems.push(['ed-publish', 'Choose at least one package page, otherwise it will not appear anywhere.']);
        if (problems.length) {
            err.innerHTML = '<b>Please fix the following:</b><ul class="list-disc ml-5 mt-1">' + problems.map(p => `<li>${escHtml(p[1])}</li>`).join('') + '</ul>';
            err.classList.remove('hidden');
            const first = $(problems[0][0]);
            first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (first.focus) first.focus({ preventScroll: true });
            return;
        }

        // Image: a new upload is a data URL; an unchanged one is its stored path.
        fd.set('image', ed.image || '');
        if (ed.tour.id) fd.append('id', ed.tour.id);
        if (ed.imageChanged && !ed.image) fd.set('image_removed', '1'); // otherwise the server keeps the old photo

        const btn = $('ed-save');
        const original = btn.innerHTML;
        form.querySelectorAll('[type="submit"]').forEach(b => b.disabled = true);
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving…';
        try {
            const data = await apiPost('save-tour.php', fd);
            if (!data.success) throw new Error(data.error || 'Save failed');
            const wasNew = !ed.tour.id;
            ed.dirty = false;
            await refreshData({ silent: true });
            showToast(wasNew ? 'Package created' : 'Changes saved');
            if (closeAfter) {
                state.editingTour = null;
                renderTours();
            } else {
                const saved = state.data.tours.find(x => Number(x.id) === Number(data.id));
                if (saved) openEditor({ ...saved }, false);
            }
        } catch (ex) {
            err.innerHTML = '<b>Could not save:</b> ' + escHtml(ex.message);
            err.classList.remove('hidden');
            err.scrollIntoView({ behavior: 'smooth', block: 'center' });
            form.querySelectorAll('[type="submit"]').forEach(b => b.disabled = false);
            btn.innerHTML = original;
        }
    });
}

// Unsaved edits: warn before closing or reloading the browser tab.
window.addEventListener('beforeunload', (e) => {
    if (state.editingTour && state.editingTour.dirty) { e.preventDefault(); e.returnValue = ''; }
});
