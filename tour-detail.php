<?php
require_once 'includes/db.php';

$tour_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tour_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ? AND status = 'Active'");
    $stmt->execute([$tour_id]);
    $tour = $stmt->fetch();
}

if (empty($tour)) {
    $stmt = $pdo->query("SELECT * FROM tours WHERE status = 'Active' ORDER BY id ASC LIMIT 1");
    $tour = $stmt->fetch();
}

if (empty($tour)) {
    header('Location: destinations.php');
    exit;
}

// Related tours: same category first, then fill from others
$stmt = $pdo->prepare("SELECT * FROM tours WHERE status = 'Active' AND id != ? AND category = ? ORDER BY id ASC LIMIT 4");
$stmt->execute([$tour['id'], $tour['category']]);
$related = $stmt->fetchAll();

if (count($related) < 4) {
    $exclude = array_merge([$tour['id']], array_column($related, 'id'));
    $ph = implode(',', array_fill(0, count($exclude), '?'));
    $need = 4 - count($related);
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE status = 'Active' AND id NOT IN ($ph) ORDER BY id ASC LIMIT $need");
    $stmt->execute($exclude);
    $related = array_merge($related, $stmt->fetchAll());
}

$gallery_pool = [
    'assets/images/tour/style1/pic1.jpg',
    'assets/images/tour/style1/pic2.jpg',
    'assets/images/tour/style1/pic3.jpg',
    'assets/images/tour/style1/pic4.jpg',
    'assets/images/tour/style1/pic5.jpg',
    'assets/images/tour/style1/pic6.jpg',
];

$idx   = ($tour['id'] - 1) % 6;
$main_image = !empty($tour['image']) ? $tour['image'] : $gallery_pool[$idx];

// Generate a 4-image gallery cycling through the pool
$gallery = [];
for ($i = 0; $i < 4; $i++) {
    $gallery[] = $gallery_pool[($idx + $i) % 6];
}

// Parse duration string like "8 Days / 3 Nights" into day count for itinerary
$day_count = 3;
if (preg_match('/(\d+)\s*[Dd]ay/', $tour['duration'] ?? '', $m)) {
    $day_count = min((int)$m[1], 7);
}

$page_title = htmlspecialchars($tour['title']) . ' - Luxit Global Escapes';
include 'header.php';
?>
<style>
/* ═══════════════════════════════════════════════════
   TOUR DETAIL — fully responsive
   Breakpoints: sm=640 md=768 lg=1024 xl=1280
═══════════════════════════════════════════════════ */

/* ── Hero ── */
.td-hero{position:relative;height:55vh;min-height:340px;max-height:720px;overflow:hidden;}
@media(min-width:768px){.td-hero{height:65vh;min-height:480px;}}
.td-hero-img{width:100%;height:100%;object-fit:cover;object-position:center;}
.td-hero-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(4,28,22,.4) 0%,rgba(4,28,22,.88) 100%);}
.td-hero-content{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;padding:0 5% 40px;text-align:center;}
@media(min-width:768px){.td-hero-content{padding:0 5% 60px;}}

/* ── Gallery ── */
.td-gallery-main{height:240px;}
@media(min-width:640px){.td-gallery-main{height:320px;}}
@media(min-width:1024px){.td-gallery-main{height:420px;}}
.td-gallery-thumbs{height:64px;margin-top:8px;}
@media(min-width:640px){.td-gallery-thumbs{height:80px;}}
.td-thumb-slide{height:56px;}
@media(min-width:640px){.td-thumb-slide{height:72px;}}

/* ── Stats ── */
.td-stat{display:flex;align-items:center;gap:8px;padding:10px 12px;background:#f0fdf4;border-radius:12px;}
@media(min-width:640px){.td-stat{gap:10px;padding:14px 18px;}}
.td-stat-icon{width:36px;height:36px;border-radius:9px;background:#006A72;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
@media(min-width:640px){.td-stat-icon{width:40px;height:40px;border-radius:10px;}}
.td-stat-icon i{color:#fff;font-size:13px;}
@media(min-width:640px){.td-stat-icon i{font-size:14px;}}

/* ── Tabs — horizontally scrollable on mobile ── */
.td-tabs{display:flex;gap:0;border-bottom:2px solid #e5e7eb;margin-bottom:24px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;flex-wrap:nowrap;}
.td-tabs::-webkit-scrollbar{display:none;}
@media(min-width:640px){.td-tabs{margin-bottom:32px;}}
.td-tab{padding:10px 14px;font-weight:600;font-size:.82rem;color:#6b7280;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:color .2s,border-color .2s;white-space:nowrap;flex-shrink:0;background:none;border-top:none;border-left:none;border-right:none;}
@media(min-width:640px){.td-tab{padding:10px 22px;font-size:.88rem;}}
.td-tab.active{color:#006A72;border-color:#006A72;}
.td-tab-panel{display:none;}
.td-tab-panel.active{display:block;}

/* ── Sidebar ── */
.td-sidebar{position:static;}
@media(min-width:1024px){.td-sidebar{position:sticky;top:110px;}}

/* ── Booking card ── */
.td-book-card{background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,106,114,.12);overflow:hidden;}
.td-book-header{background:#006A72;padding:18px 20px;}
@media(min-width:640px){.td-book-header{padding:20px 24px;}}
.td-book-body{padding:18px 20px;}
@media(min-width:640px){.td-book-body{padding:24px;}}
.td-book-input{width:100%;padding:11px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:.9rem;color:#1f2937;outline:none;transition:border-color .2s;-webkit-appearance:none;}
.td-book-input:focus{border-color:#006A72;box-shadow:0 0 0 3px rgba(0,106,114,.1);}
.td-book-label{display:block;font-size:.78rem;font-weight:600;color:#6b7280;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;}

/* ── Highlights ── */
.highlight-item{display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;}
.highlight-item:last-child{border-bottom:none;}
.hi-dot{width:8px;height:8px;border-radius:50%;background:#FFD214;flex-shrink:0;margin-top:6px;}

/* ── Itinerary ── */
.itinerary-day{border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:12px;}
.itinerary-day-head{display:flex;align-items:center;gap:12px;padding:13px 14px;background:#f9fafb;cursor:pointer;-webkit-tap-highlight-color:transparent;}
@media(min-width:640px){.itinerary-day-head{gap:14px;padding:14px 18px;}}
.itinerary-day-num{width:34px;height:34px;border-radius:50%;background:#006A72;color:#fff;font-weight:700;font-size:.78rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
@media(min-width:640px){.itinerary-day-num{width:36px;height:36px;font-size:.82rem;}}
.itinerary-day-body{padding:14px 14px;font-size:.88rem;color:#4b5563;line-height:1.75;display:none;}
@media(min-width:640px){.itinerary-day-body{padding:16px 18px 16px 62px;}}
.itinerary-day.open .itinerary-day-body{display:block;}
.itinerary-day.open .itinerary-chevron{transform:rotate(180deg);}
.itinerary-chevron{transition:transform .25s;color:#9ca3af;font-size:.8rem;margin-left:auto;flex-shrink:0;}

/* ── Included checklist ── */
.included-item{display:flex;align-items:center;gap:10px;padding:8px 0;}
.included-item.yes i{color:#16a34a;}
.included-item.no i{color:#dc2626;}

/* ── Related tours ── */
.td-related-card{border-radius:20px;overflow:hidden;background:#fff;box-shadow:0 6px 24px rgba(0,106,114,.08);transition:transform .3s,box-shadow .3s;}
.td-related-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,106,114,.14);}
.td-related-card:hover .td-related-img{transform:scale(1.06);}
.td-related-img-wrap{overflow:hidden;height:200px;}
@media(min-width:640px){.td-related-img-wrap{height:220px;}}
.td-related-img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}

/* ── Toast — mobile full-width ── */
.toast-wrap{position:fixed;bottom:16px;left:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none;}
@media(min-width:640px){.toast-wrap{left:auto;right:28px;bottom:28px;width:360px;}}
.toast{padding:14px 18px;border-radius:12px;font-weight:600;font-size:.86rem;color:#fff;box-shadow:0 4px 20px rgba(0,0,0,.18);transform:translateY(120%);transition:transform .4s cubic-bezier(.34,1.56,.64,1);pointer-events:all;}
@media(min-width:640px){.toast{transform:translateX(120%);}}
.toast.show{transform:translateY(0);}
@media(min-width:640px){.toast.show{transform:translateX(0);}}
.toast.success{background:#006A72;}
.toast.error{background:#dc2626;}
</style>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

        <!-- HERO BANNER -->
        <div class="td-hero">
            <img src="<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="td-hero-img">
            <div class="td-hero-overlay"></div>
            <div class="td-hero-content">
                <span class="inline-flex items-center gap-2 text-citrusyellow font-bold text-xs tracking-widest uppercase mb-3">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($tour['location']); ?>
                </span>
                <h1 class="text-white font-display font-black lg:text-5xl md:text-4xl sm:text-3xl text-2xl mb-4 max-w-3xl leading-tight">
                    <?php echo htmlspecialchars($tour['title']); ?>
                </h1>
                <nav class="flex items-center flex-wrap justify-center gap-1.5 text-xs sm:text-sm text-white/70" aria-label="Breadcrumb">
                    <a href="index.php" class="hover:text-citrusyellow transition">Home</a>
                    <i class="fas fa-chevron-right text-xs opacity-50"></i>
                    <a href="destinations.php" class="hover:text-citrusyellow transition">Tours</a>
                    <i class="fas fa-chevron-right text-xs opacity-50"></i>
                    <span class="text-citrusyellow truncate max-w-[200px] sm:max-w-none"><?php echo htmlspecialchars($tour['title']); ?></span>
                </nav>
            </div>
        </div>
        <!-- HERO BANNER END -->

        <!-- MAIN CONTENT -->
        <div class="bg-eggshell xl:py-20 lg:py-16 py-10">
            <div class="container">
                <div class="grid grid-cols-12 gap-7.5">

                    <!-- LEFT: Tour Content -->
                    <div class="lg:col-span-7 xl:col-span-8 col-span-12">

                        <!-- Stats Row -->
                        <div class="grid sm:grid-cols-4 grid-cols-2 gap-3 sm:gap-4 mb-8 sm:mb-10">
                            <div class="td-stat">
                                <div class="td-stat-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Duration</p>
                                    <p class="font-bold text-primary text-sm"><?php echo htmlspecialchars($tour['duration'] ?? 'Custom'); ?></p>
                                </div>
                            </div>
                            <div class="td-stat">
                                <div class="td-stat-icon"><i class="fas fa-star"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Rating</p>
                                    <p class="font-bold text-primary text-sm"><?php echo number_format((float)($tour['rating'] ?? 4.5), 1); ?> / 5.0</p>
                                </div>
                            </div>
                            <div class="td-stat">
                                <div class="td-stat-icon"><i class="fas fa-tag"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Category</p>
                                    <p class="font-bold text-primary text-sm"><?php echo htmlspecialchars($tour['category'] ?? 'General'); ?></p>
                                </div>
                            </div>
                            <div class="td-stat">
                                <div class="td-stat-icon"><i class="fas fa-dollar-sign"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Starting From</p>
                                    <p class="font-bold text-primary text-sm">$<?php echo number_format((float)($tour['price'] ?? 0), 0); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Image Gallery -->
                        <div class="mb-8 sm:mb-10">
                            <div class="swiper td-gallery-main rounded-2xl overflow-hidden">
                                <div class="swiper-wrapper">
                                    <?php foreach ($gallery as $img): ?>
                                    <div class="swiper-slide">
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Tour Image" class="w-full h-full object-cover">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="swiper-button-next !text-white after:!text-sm"></div>
                                <div class="swiper-button-prev !text-white after:!text-sm"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <div class="swiper td-gallery-thumbs hidden sm:block">
                                <div class="swiper-wrapper">
                                    <?php foreach ($gallery as $img): ?>
                                    <div class="swiper-slide td-thumb-slide rounded-xl overflow-hidden cursor-pointer opacity-60 hover:opacity-100 transition">
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail" class="w-full h-full object-cover">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,106,114,.07)] p-4 sm:p-7">
                            <div class="td-tabs" role="tablist">
                                <button class="td-tab active" data-tab="overview" role="tab" aria-selected="true">Overview</button>
                                <button class="td-tab" data-tab="itinerary" role="tab" aria-selected="false">Itinerary</button>
                                <button class="td-tab" data-tab="included" role="tab" aria-selected="false">What's Included</button>
                                <button class="td-tab" data-tab="pricing" role="tab" aria-selected="false">Pricing</button>
                            </div>

                            <!-- OVERVIEW -->
                            <div class="td-tab-panel active" id="tab-overview">
                                <h3 class="text-xl font-bold text-primary mb-4">About This Tour</h3>
                                <p class="text-gray-600 leading-relaxed mb-6">
                                    <?php echo nl2br(htmlspecialchars($tour['description'] ?? 'Experience the journey of a lifetime with our expertly curated tour package. Every detail is taken care of so you can focus on making unforgettable memories.')); ?>
                                </p>
                                <p class="text-gray-600 leading-relaxed mb-8">
                                    Our dedicated team of local guides and travel specialists ensures a seamless, immersive experience tailored to your preferences. With 24/7 support, flexible itineraries, and handpicked accommodations, you travel with confidence and comfort at every step.
                                </p>
                                <h4 class="font-bold text-primary mb-4">Tour Highlights</h4>
                                <div>
                                    <?php
                                    $highlights_map = [
                                        'Safari'    => ['Big Five game drives at dawn & dusk','Expert Maasai guide accompanying your safari','Luxury tented camp with en-suite facilities','Sundowner cocktails on the savannah','Bush breakfast in the wild','Full-board meals using local produce'],
                                        'Adventure' => ['Guided trek with certified mountain guide','Professional safety equipment provided','Acclimatisation stops and support team','Scenic photography stops at panoramic viewpoints','Camp-cooked meals along the trail','Certificate of completion at summit'],
                                        'Luxury'    => ['5-star resort accommodation throughout','Private butler service & welcome amenity','Spa treatment included on arrival day','Private yacht or boat excursion','Gourmet dining with local-cuisine tasting menu','Airport transfers in luxury vehicle'],
                                        'Culture'   => ['Guided visits to UNESCO World Heritage sites','Private local expert historian','Traditional cooking class with a local family','Entry to exclusive cultural performances','Artisan market tours with purchasing guide','Small-group format (max 8 guests)'],
                                        'Beach'     => ['Overwater or beachfront villa accommodation','Daily snorkelling or diving excursion','Sunset cruise with champagne','Glass-bottom boat tour of coral gardens','All-inclusive meals & non-alcoholic beverages','Complimentary kayaks & paddleboards'],
                                    ];
                                    $cat = $tour['category'] ?? 'Luxury';
                                    $highlights = $highlights_map[$cat] ?? $highlights_map['Luxury'];
                                    foreach ($highlights as $h): ?>
                                    <div class="highlight-item">
                                        <div class="hi-dot"></div>
                                        <span class="text-gray-700 text-sm"><?php echo htmlspecialchars($h); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- ITINERARY -->
                            <div class="td-tab-panel" id="tab-itinerary">
                                <h3 class="text-xl font-bold text-primary mb-6">Day-by-Day Itinerary</h3>
                                <?php
                                $itinerary_templates = [
                                    1 => ['Arrival & Welcome', 'Arrive at your destination and transfer to your accommodation. Meet your personal guide for a welcome briefing and orientation. Enjoy a welcome dinner showcasing local cuisine.'],
                                    2 => ['Exploration Begins', 'After breakfast, embark on your first full day of exploration. Morning and afternoon activities are guided by your local expert. Return to your lodge for dinner and a recap of the day\'s highlights.'],
                                    3 => ['Cultural Immersion', 'Immerse yourself in local culture and traditions. Visit key landmarks and interact with local communities. Evening at leisure with an optional cultural performance.'],
                                    4 => ['Adventure Day', 'Today\'s schedule is dedicated to the core adventure experience this destination is famous for. All equipment and safety briefings are provided. Celebratory dinner in the evening.'],
                                    5 => ['Hidden Gems', 'Venture off the beaten path to discover hidden gems known only to locals. Enjoy a scenic picnic lunch in a stunning natural setting. Return for a relaxing evening.'],
                                    6 => ['Leisure & Optional Activities', 'A relaxed morning at your own pace — read, swim, or explore independently. Afternoon optional activities are available at your guide\'s recommendation. Farewell dinner tonight.'],
                                    7 => ['Departure', 'Enjoy a leisurely breakfast and complete any last-minute shopping. Transfer to the airport for your onward journey. Safe travels and thank you for choosing Luxit Global Escapes!'],
                                ];
                                for ($d = 1; $d <= $day_count; $d++):
                                    $t = $itinerary_templates[$d] ?? ["Day $d Activities", "Activities and experiences are planned for this day with your local guide."];
                                ?>
                                <div class="itinerary-day <?php echo $d === 1 ? 'open' : ''; ?>" data-day="<?php echo $d; ?>">
                                    <div class="itinerary-day-head" onclick="toggleDay(this)">
                                        <div class="itinerary-day-num">D<?php echo $d; ?></div>
                                        <div>
                                            <span class="font-semibold text-primary text-sm">Day <?php echo $d; ?></span>
                                            <span class="text-gray-500 text-xs block"><?php echo htmlspecialchars($t[0]); ?></span>
                                        </div>
                                        <i class="fas fa-chevron-down itinerary-chevron"></i>
                                    </div>
                                    <div class="itinerary-day-body"><?php echo htmlspecialchars($t[1]); ?></div>
                                </div>
                                <?php endfor; ?>
                            </div>

                            <!-- WHAT'S INCLUDED -->
                            <div class="td-tab-panel" id="tab-included">
                                <div class="grid sm:grid-cols-2 gap-8">
                                    <div>
                                        <h4 class="font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-check-circle text-green-600"></i> Included</h4>
                                        <?php
                                        $included = ['Professional licensed guide','All park / entry fees','Airport transfers (round trip)','Full-board meals as stated in itinerary','Accommodation as described','Bottled water throughout the tour','24/7 emergency support'];
                                        foreach ($included as $item): ?>
                                        <div class="included-item yes">
                                            <i class="fas fa-check-circle text-sm"></i>
                                            <span class="text-sm text-gray-700"><?php echo $item; ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-times-circle text-red-500"></i> Not Included</h4>
                                        <?php
                                        $excluded = ['International flights','Travel insurance (highly recommended)','Personal items & souvenirs','Visa fees (where applicable)','Tips & gratuities (discretionary)','Alcoholic beverages','Optional excursions not listed'];
                                        foreach ($excluded as $item): ?>
                                        <div class="included-item no">
                                            <i class="fas fa-times-circle text-sm"></i>
                                            <span class="text-sm text-gray-700"><?php echo $item; ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- PRICING -->
                            <div class="td-tab-panel" id="tab-pricing">
                                <h3 class="text-xl font-bold text-primary mb-6">Pricing Details</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left border-collapse">
                                        <thead>
                                            <tr class="bg-primary text-white">
                                                <th class="px-5 py-3 rounded-tl-xl">Package</th>
                                                <th class="px-5 py-3">Per Person</th>
                                                <th class="px-5 py-3">Group (4+)</th>
                                                <th class="px-5 py-3 rounded-tr-xl">Family Pack</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $base = (float)($tour['price'] ?? 500);
                                            $tiers = [
                                                ['Standard', $base, $base * 0.85, $base * 0.80],
                                                ['Comfort',  $base * 1.25, $base * 1.07, $base * 1.00],
                                                ['Premium',  $base * 1.60, $base * 1.36, $base * 1.28],
                                            ];
                                            foreach ($tiers as $i => [$name, $pp, $gp, $fp]):
                                                $row_bg = $i % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                                            ?>
                                            <tr class="<?php echo $row_bg; ?> border-b border-gray-100">
                                                <td class="px-5 py-3 font-semibold text-primary"><?php echo $name; ?></td>
                                                <td class="px-5 py-3">$<?php echo number_format($pp, 0); ?></td>
                                                <td class="px-5 py-3">$<?php echo number_format($gp, 0); ?> <span class="text-green-600 text-xs font-semibold">15% off</span></td>
                                                <td class="px-5 py-3">$<?php echo number_format($fp, 0); ?> <span class="text-green-600 text-xs font-semibold">20% off</span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-xs text-gray-500 mt-4"><i class="fas fa-info-circle mr-1"></i> Prices are per person and subject to availability. Contact us for custom group quotations.</p>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Booking Sidebar -->
                    <div class="lg:col-span-5 xl:col-span-4 col-span-12">
                        <div class="td-sidebar">
                            <!-- Price Summary Card -->
                            <div class="td-book-card mb-6">
                                <div class="td-book-header">
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <span class="text-white/70 text-xs uppercase tracking-wider font-semibold">Starting From</span>
                                            <div class="text-citrusyellow font-black text-3xl sm:text-4xl leading-none mt-1">
                                                $<?php echo number_format((float)($tour['price'] ?? 0), 0); ?>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="flex items-center justify-end gap-1 mb-1">
                                                <?php $r = (float)($tour['rating'] ?? 4.5);
                                                for ($s = 1; $s <= 5; $s++):
                                                    $cls = $s <= floor($r) ? 'text-citrusyellow' : 'text-white/30'; ?>
                                                <i class="fas fa-star text-xs <?php echo $cls; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-white/70 text-xs"><?php echo number_format($r, 1); ?> Rating</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-3">
                                        <span class="inline-flex items-center gap-1.5 text-white/80 text-xs bg-white/10 px-3 py-1.5 rounded-full">
                                            <i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($tour['duration'] ?? 'Custom Duration'); ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-white/80 text-xs bg-white/10 px-3 py-1.5 rounded-full">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($tour['category'] ?? 'General'); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="td-book-body">
                                    <h4 class="font-bold text-primary text-lg mb-5">Book This Tour</h4>
                                    <form id="booking-form" novalidate>
                                        <input type="hidden" name="tour_name" value="<?php echo htmlspecialchars($tour['title']); ?>">
                                        <input type="hidden" name="type" value="book">

                                        <div class="mb-4">
                                            <label class="td-book-label" for="bf-name">Full Name <span class="text-red-500">*</span></label>
                                            <input class="td-book-input" type="text" id="bf-name" name="full_name" placeholder="Your full name" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="td-book-label" for="bf-email">Email <span class="text-red-500">*</span></label>
                                            <input class="td-book-input" type="email" id="bf-email" name="email" placeholder="your@email.com" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="td-book-label" for="bf-phone">Phone Number</label>
                                            <input class="td-book-input" type="tel" id="bf-phone" name="phone" placeholder="+254 700 000 000">
                                        </div>
                                        <div class="mb-4">
                                            <label class="td-book-label" for="bf-date">Travel Date <span class="text-red-500">*</span></label>
                                            <input class="td-book-input flatpickr-td" type="text" id="bf-date" name="travel_date" placeholder="Select date" required readonly>
                                        </div>
                                        <div class="mb-4">
                                            <label class="td-book-label" for="bf-travelers">Number of Travelers</label>
                                            <input class="td-book-input" type="number" id="bf-travelers" name="travelers" min="1" max="50" value="1">
                                        </div>
                                        <div class="mb-5">
                                            <label class="td-book-label" for="bf-msg">Special Requests</label>
                                            <textarea class="td-book-input" id="bf-msg" name="message" rows="3" placeholder="Dietary needs, accessibility, special occasions..."></textarea>
                                        </div>

                                        <button type="submit" name="book" class="w-full site-button butn-bg-shape mb-3 justify-center flex">
                                            <i class="fas fa-calendar-check mr-2"></i> Book Now
                                        </button>
                                        <button type="button" id="enquire-btn" class="w-full site-button outline justify-center flex">
                                            <i class="fas fa-paper-plane mr-2"></i> Send Enquiry
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Quick Contact Card -->
                            <div class="bg-primary rounded-2xl p-6 text-white">
                                <h4 class="font-bold text-lg mb-2">Need Help Planning?</h4>
                                <p class="text-white/70 text-sm mb-5">Our travel experts are available 24/7 to craft your perfect itinerary.</p>
                                <a href="tel:+254737800900" class="flex items-center gap-3 mb-3 hover:text-citrusyellow transition group">
                                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-citrusyellow/20 transition">
                                        <i class="fas fa-phone text-sm"></i>
                                    </div>
                                    <span class="font-semibold text-sm">+254 737 800 900</span>
                                </a>
                                <a href="mailto:info@luxitglobalescapes.com" class="flex items-center gap-3 hover:text-citrusyellow transition group">
                                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-citrusyellow/20 transition">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </div>
                                    <span class="font-semibold text-sm">info@luxitglobalescapes.com</span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div><!-- /grid -->
            </div><!-- /container -->
        </div>
        <!-- MAIN CONTENT END -->

        <!-- RELATED TOURS -->
        <?php if (!empty($related)): ?>
        <div class="bg-lightturquoise xl:py-20 py-12">
            <div class="container">
                <div class="text-center max-w-150 mx-auto mb-12">
                    <h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">You May Also <span class="text-citrusyellow">Like</span></h2>
                    <p class="text-base">More handpicked tours you might enjoy</p>
                    <div class="-mt-7">
                        <img src="assets/images/background/Title-Separator.png" alt="" class="w-117.5 inline-block" width="470" height="70" loading="lazy">
                    </div>
                </div>
                <div class="swiper td-related-swiper xl:!pb-16 !pb-12">
                    <div class="swiper-wrapper">
                        <?php foreach ($related as $ri => $rt):
                            $ri_img = !empty($rt['image']) ? $rt['image'] : $gallery_pool[($rt['id'] - 1) % 6];
                        ?>
                        <div class="swiper-slide">
                            <div class="mx-2 td-related-card">
                                <div class="td-related-img-wrap relative">
                                    <a href="tour-detail.php?id=<?php echo $rt['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($ri_img); ?>" alt="<?php echo htmlspecialchars($rt['title']); ?>" class="td-related-img">
                                    </a>
                                    <div class="absolute top-4 left-0 py-2 px-4 bg-primary text-white text-xs font-semibold rounded-r-full">
                                        <?php echo htmlspecialchars($rt['duration'] ?? 'Custom'); ?>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="font-bold text-primary text-base leading-snug flex-1 pr-3">
                                            <a href="tour-detail.php?id=<?php echo $rt['id']; ?>" class="hover:text-citrusyellow transition">
                                                <?php echo htmlspecialchars($rt['title']); ?>
                                            </a>
                                        </h3>
                                        <span class="text-citrusyellow font-black text-lg whitespace-nowrap">$<?php echo number_format((float)($rt['price'] ?? 0), 0); ?></span>
                                    </div>
                                    <p class="text-gray-500 text-xs mb-4 flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <?php echo htmlspecialchars($rt['location']); ?>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1">
                                            <?php $rr = (float)($rt['rating'] ?? 4.5);
                                            for ($s = 1; $s <= 5; $s++): ?>
                                            <i class="fas fa-star text-xs <?php echo $s <= floor($rr) ? 'text-citrusyellow' : 'text-gray-300'; ?>"></i>
                                            <?php endfor; ?>
                                            <span class="text-xs text-gray-500 ml-1">(<?php echo number_format($rr, 1); ?>)</span>
                                        </div>
                                        <a href="tour-detail.php?id=<?php echo $rt['id']; ?>" class="site-button outline btn-sm">View Tour</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination !relative !bottom-auto mt-6 !pt-2"></div>
                    <div class="swiper-button-next !top-auto !bottom-0 !size-12 rounded-full bg-citrusyellow !text-white hover:!text-citrusyellow hover:!bg-white after:!text-sm hidden sm:flex !right-[calc(50%_-_64px)]"></div>
                    <div class="swiper-button-prev !top-auto !bottom-0 !size-12 rounded-full bg-citrusyellow !text-white hover:!text-citrusyellow hover:!bg-white after:!text-sm hidden sm:flex !left-[calc(50%_-_64px)]"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- RELATED TOURS END -->

        </div><!-- /page-content -->
    </div><!-- /smooth-content -->
</div><!-- /smooth-wrapper -->

<!-- Toast container -->
<div class="toast-wrap" id="toast-wrap" aria-live="polite"></div>

<script>
// ── Tabs ──────────────────────────────────────────────────────────────────
document.querySelectorAll('.td-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = this.dataset.tab;
        document.querySelectorAll('.td-tab').forEach(function(b) {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        document.querySelectorAll('.td-tab-panel').forEach(function(p) {
            p.classList.remove('active');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        var panel = document.getElementById('tab-' + target);
        if (panel) panel.classList.add('active');
    });
});

// ── Itinerary accordion ───────────────────────────────────────────────────
function toggleDay(head) {
    var card = head.closest('.itinerary-day');
    card.classList.toggle('open');
}

// ── Swiper inits ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Gallery thumbs (only on sm+, hidden on mobile via CSS)
    var thumbs = new Swiper('.td-gallery-thumbs', {
        spaceBetween: 8,
        slidesPerView: 3,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            768: { slidesPerView: 4 },
        },
    });
    // Gallery main
    new Swiper('.td-gallery-main', {
        spaceBetween: 0,
        loop: true,
        navigation: {
            nextEl: '.td-gallery-main .swiper-button-next',
            prevEl: '.td-gallery-main .swiper-button-prev',
        },
        pagination: {
            el: '.td-gallery-main .swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        thumbs: { swiper: thumbs },
        grabCursor: true,
    });

    // Related tours
    new Swiper('.td-related-swiper', {
        spaceBetween: 16,
        slidesPerView: 1,
        grabCursor: true,
        navigation: {
            nextEl: '.td-related-swiper .swiper-button-next',
            prevEl: '.td-related-swiper .swiper-button-prev',
        },
        pagination: {
            el: '.td-related-swiper .swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            500:  { slidesPerView: 1.3, spaceBetween: 12 },
            640:  { slidesPerView: 2,   spaceBetween: 16 },
            1024: { slidesPerView: 3,   spaceBetween: 20 },
        },
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
    });

    // Date picker for booking form
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr-td', {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
        });
    }
});

// ── Toast helper ──────────────────────────────────────────────────────────
function showToast(msg, type) {
    var wrap = document.getElementById('toast-wrap');
    var t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    wrap.appendChild(t);
    requestAnimationFrame(function() { t.classList.add('show'); });
    setTimeout(function() {
        t.classList.remove('show');
        setTimeout(function() { t.remove(); }, 400);
    }, 5000);
}

// ── Booking form submit ───────────────────────────────────────────────────
function submitForm(type) {
    var form  = document.getElementById('booking-form');
    var data  = new FormData(form);
    data.set('type', type);

    var name  = form.querySelector('[name="full_name"]').value.trim();
    var email = form.querySelector('[name="email"]').value.trim();
    var date  = form.querySelector('[name="travel_date"]').value.trim();

    if (!name || !email) {
        showToast('Please enter your name and email.', 'error');
        return;
    }
    if (!date && type === 'book') {
        showToast('Please select a travel date.', 'error');
        return;
    }

    var btn = type === 'book'
        ? form.querySelector('[name="book"]')
        : document.getElementById('enquire-btn');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

    fetch('booking-handler.php', { method: 'POST', body: data })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            btn.disabled = false;
            btn.innerHTML = orig;
            if (res.success) {
                showToast(res.message, 'success');
                form.reset();
                // Reset flatpickr
                if (typeof flatpickr !== 'undefined') {
                    var fp = document.querySelector('.flatpickr-td')._flatpickr;
                    if (fp) fp.clear();
                }
            } else {
                showToast(res.error || 'Something went wrong. Please try again.', 'error');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = orig;
            showToast('Network error. Please try again.', 'error');
        });
}

document.getElementById('booking-form').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm('book');
});
document.getElementById('enquire-btn').addEventListener('click', function() {
    submitForm('enquire');
});
</script>

<?php include 'footer.php'; ?>
