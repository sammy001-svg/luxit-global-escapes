<?php
/**
 * Renders a tour collection page. The page file sets $collectionKey
 * (e.g. 'safari', 'halal'); content lives in includes/collections.php.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/packages.php';
require_once __DIR__ . '/destinations.php';
require_once __DIR__ . '/collections.php';

$c = TOUR_COLLECTIONS[$collectionKey];
$isStyle  = $c['kind'] === 'style';
$packages = collectionPackages($pdo, $c);
$minPrice = $packages ? min(array_map(fn($t) => (float)$t['price'], $packages)) : null;

$page_title       = $c['name'] . ' | Luxit Global Escapes';
$page_description = mb_substr($c['tagline'] . '. ' . $c['intro'], 0, 300);
$page_image       = $c['image'];
$page_css         = ['assets/css/tour.css', 'assets/css/destination.css', 'assets/css/collection.css'];
$header_variant   = 'solid';

$planLink = 'contact.php?' . http_build_query([
    'topic' => $collectionKey === 'mice' ? 'Group or corporate travel' : 'Plan a trip',
]);
$waLink = 'https://wa.me/254737800900?text=' . rawurlencode('Hello Luxit Global Escapes, I am interested in ' . strtolower($c['name']) . '.');
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
// Leading number of days in "6 Days / 5 Nights", for sorting by length.
$days = fn($d) => preg_match('/(\d+)\s*day/i', (string)$d, $m) ? (int)$m[1] : 0;

include __DIR__ . '/../header.php';
?>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">
        <div class="dst">

            <header class="dst-hero">
                <div class="dst-wrap dst-hero-grid">
                    <div>
                        <nav class="dst-crumbs" aria-label="Breadcrumb">
                            <a href="index.php">Home</a><span aria-hidden="true">/</span>
                            <span><?php echo $isStyle ? 'Travel styles' : 'Tours'; ?></span><span aria-hidden="true">/</span>
                            <span aria-current="page"><?php echo $h($c['name']); ?></span>
                        </nav>
                        <span class="dst-eyebrow"><?php echo $isStyle ? 'Travel style' : 'Tour packages'; ?></span>
                        <h1 class="dst-title"><?php echo $h($c['name']); ?></h1>
                        <p class="dst-tagline"><?php echo $h($c['tagline']); ?></p>
                        <div class="dst-facts">
                            <?php if ($packages) : ?>
                            <div class="dst-fact"><i class="fa-solid fa-suitcase-rolling"></i><div><small>Ready-made trips</small><span><?php echo count($packages); ?> package<?php echo count($packages) === 1 ? '' : 's'; ?></span></div></div>
                            <div class="dst-fact"><i class="fa-solid fa-tag"></i><div><small>Starting from</small><span>$<?php echo number_format($minPrice); ?> per person</span></div></div>
                            <?php else : ?>
                            <div class="dst-fact"><i class="fa-solid fa-pen-ruler"></i><div><small>How it works</small><span>Tailor-made around you</span></div></div>
                            <?php endif; ?>
                            <div class="dst-fact"><i class="fa-regular fa-clock"></i><div><small>Our reply</small><span>Within 24 hours</span></div></div>
                        </div>
                        <div class="dst-ctas">
                            <?php if ($packages) : ?><a class="dst-btn dst-btn--primary" href="#col-list" data-col-jump><i class="fa-solid fa-list"></i> Browse packages</a><?php endif; ?>
                            <a class="dst-btn <?php echo $packages ? 'dst-btn--ghost' : 'dst-btn--primary'; ?>" href="<?php echo $h($planLink); ?>"><?php echo $collectionKey === 'mice' ? 'Send us your brief' : 'Plan a tailor-made trip'; ?></a>
                        </div>
                    </div>
                    <figure class="dst-hero-photo">
                        <img src="<?php echo $h($c['image']); ?>" alt="<?php echo $h($c['name']); ?>" width="640" height="640">
                        <figcaption><i class="fa-solid <?php echo $h($c['icon'] ?? 'fa-route'); ?>"></i> <?php echo $h($c['short']); ?></figcaption>
                    </figure>
                </div>
            </header>

            <?php if ($isStyle) : ?>
            <section class="dst-section dst-section--tint" aria-labelledby="col-how-title">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">How we plan it</span>
                            <h2 class="dst-h2" id="col-how-title"><?php echo $h($c['name']); ?>, done properly</h2>
                            <p><?php echo $h($c['intro']); ?></p>
                        </div>
                    </div>
                    <div class="col-how">
                        <?php foreach ($c['how'] as $title => $text) : ?>
                        <div class="dst-hl">
                            <span class="dst-hl-n" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
                            <h3><?php echo $h($title); ?></h3>
                            <p><?php echo $h($text); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <section class="dst-section <?php echo $isStyle ? '' : 'dst-section--tint'; ?>" id="col-list" aria-labelledby="col-list-title">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow"><?php echo $isStyle ? 'Ready-made trips' : 'Our ' . $h(strtolower($c['short'])) . ' collection'; ?></span>
                            <h2 class="dst-h2" id="col-list-title"><?php echo $isStyle ? $h($c['short']) . ' packages' : 'Choose your trip'; ?></h2>
                            <?php if (!$isStyle) : ?><p><?php echo $h($c['intro']); ?></p><?php endif; ?>
                        </div>
                    </div>

                    <?php if ($packages) : ?>
                    <div class="col-bar">
                        <span class="col-count" id="col-count"><?php echo count($packages); ?> <span>package<?php echo count($packages) === 1 ? '' : 's'; ?></span></span>
                        <?php if (count($packages) > 1) : ?>
                        <div class="col-controls">
                            <label class="col-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                <span class="sr-only">Search packages</span>
                                <input class="col-input" type="search" id="col-q" placeholder="Search by destination or name">
                            </label>
                            <label><span class="sr-only">Sort packages</span>
                                <select class="col-input" id="col-sort">
                                    <option value="recommended">Recommended</option>
                                    <option value="price-asc">Price: low to high</option>
                                    <option value="price-desc">Price: high to low</option>
                                    <option value="days-asc">Duration: shortest first</option>
                                    <option value="days-desc">Duration: longest first</option>
                                </select>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="pk-grid" id="col-grid">
                        <?php foreach ($packages as $i => $t) : ?>
                        <div class="col-item" data-order="<?php echo $i; ?>" data-price="<?php echo (float)$t['price']; ?>" data-days="<?php echo $days($t['duration']); ?>"
                             data-text="<?php echo $h(mb_strtolower($t['title'] . ' ' . $t['location'])); ?>">
                            <?php packageTile($t); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-none" id="col-none" role="status">
                        No packages match your search. <br><button type="button" id="col-clear">Show all packages</button>
                    </div>
                    <?php else : ?>
                    <div class="dst-empty">
                        <span class="dst-empty-icon"><i class="fa-solid fa-pen-ruler"></i></span>
                        <div>
                            <h3>Every <?php echo $h(strtolower($c['short'])); ?> trip is tailor-made</h3>
                            <p>There is no ready-made package listed here right now. Tell us your dates, group and wishes and a specialist will design an itinerary for you, usually within 24 hours.</p>
                        </div>
                        <a class="dst-btn dst-btn--primary" href="<?php echo $h($planLink); ?>"><?php echo $collectionKey === 'mice' ? 'Send us your brief' : 'Request a tailor-made trip'; ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="dst-section" aria-labelledby="col-dest-title">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">Where to go</span>
                            <h2 class="dst-h2" id="col-dest-title">Popular destinations for <?php echo $h(strtolower($c['short'])); ?> trips</h2>
                        </div>
                        <a class="dst-btn dst-btn--ghost" href="destinations.php">All destinations</a>
                    </div>
                    <div class="col-dests">
                        <?php foreach ($c['destinations'] as $dk) : if (!isset(DEST_COUNTRIES[$dk])) continue; ?>
                        <a class="col-dest" href="<?php echo $dk; ?>.php"><span><i class="fa-solid fa-location-dot"></i></span><?php echo $h(DEST_COUNTRIES[$dk]['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="dst-section" aria-label="Why book with Luxit Global Escapes" style="padding-top:0">
                <div class="dst-wrap">
                    <div class="col-why">
                        <div><i class="fa-solid fa-pen-ruler"></i><span><b>Tailor-made</b>Every trip adjusted to your dates, group and budget.</span></div>
                        <div><i class="fa-solid fa-hand-holding-dollar"></i><span><b>No payment to ask</b>Quotes and advice are free; pay once you are happy.</span></div>
                        <div><i class="fa-regular fa-clock"></i><span><b>Fast replies</b>A specialist answers within 24 hours.</span></div>
                        <div><i class="fa-solid fa-location-dot"></i><span><b>Based in Nairobi</b>A local team that knows the places we sell.</span></div>
                    </div>
                </div>
            </section>

            <section class="dst-section dst-section--tint" aria-labelledby="col-more-title">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">Keep exploring</span>
                            <h2 class="dst-h2" id="col-more-title"><?php echo $isStyle ? 'Other ways to travel' : 'More tour collections'; ?></h2>
                        </div>
                    </div>
                    <div class="col-others">
                        <?php
                        $others = array_filter(TOUR_COLLECTIONS, fn($o, $k) => $k !== $collectionKey, ARRAY_FILTER_USE_BOTH);
                        // Package pages first, then styles; five cards fill one desktop row.
                        uasort($others, fn($a, $b) => ($a['kind'] === 'packages' ? 0 : 1) <=> ($b['kind'] === 'packages' ? 0 : 1));
                        foreach (array_slice($others, 0, 5, true) as $o) : ?>
                        <a class="col-other" href="<?php echo $o['page']; ?>">
                            <span class="col-other-img"><img src="<?php echo $h($o['image']); ?>" alt="" loading="lazy"></span>
                            <b><?php echo $h($o['name']); ?></b>
                            <small><?php echo $h($o['tagline']); ?></small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <div class="dst-cta-wrap" style="padding-top:0">
                <div class="dst-wrap">
                    <div class="dst-cta">
                        <div>
                            <h2><?php echo $collectionKey === 'mice' ? 'Planning a meeting or incentive trip?' : 'Can\'t see the perfect trip?'; ?></h2>
                            <p>Tell us your dates, who is travelling and what matters to you. A specialist will reply within 24 hours, and there is no charge for a quote.</p>
                        </div>
                        <div class="dst-ctas">
                            <a class="dst-btn dst-btn--primary" href="<?php echo $h($planLink); ?>">Start planning</a>
                            <a class="dst-btn dst-btn--wa" href="<?php echo $h($waLink); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div><!-- /page-content -->

<?php ob_start(); ?>
<script>
(function () {
    // Search + sort the package grid in place.
    var grid = document.getElementById('col-grid');
    if (grid) {
        var items = Array.prototype.slice.call(grid.querySelectorAll('.col-item'));
        var q = document.getElementById('col-q');
        var sort = document.getElementById('col-sort');
        var count = document.getElementById('col-count');
        var none = document.getElementById('col-none');
        var apply = function () {
            var term = q ? q.value.trim().toLowerCase() : '';
            var mode = sort ? sort.value : 'recommended';
            var num = function (el, k) { return parseFloat(el.dataset[k]) || 0; };
            var sorted = items.slice().sort(function (a, b) {
                switch (mode) {
                    case 'price-asc':  return num(a, 'price') - num(b, 'price');
                    case 'price-desc': return num(b, 'price') - num(a, 'price');
                    case 'days-asc':   return num(a, 'days') - num(b, 'days');
                    case 'days-desc':  return num(b, 'days') - num(a, 'days');
                    default:           return num(a, 'order') - num(b, 'order');
                }
            });
            var shown = 0;
            sorted.forEach(function (el) {
                var match = !term || el.dataset.text.indexOf(term) !== -1;
                el.hidden = !match;
                if (match) shown++;
                grid.appendChild(el);
            });
            count.innerHTML = shown + ' <span>package' + (shown === 1 ? '' : 's') + (term ? ' found' : '') + '</span>';
            none.classList.toggle('is-on', shown === 0);
            if (window.ScrollTrigger) ScrollTrigger.refresh();
        };
        if (q) q.addEventListener('input', apply);
        if (sort) sort.addEventListener('change', apply);
        var clear = document.getElementById('col-clear');
        if (clear) clear.addEventListener('click', function () { if (q) q.value = ''; apply(); if (q) q.focus(); });
    }

    // "Browse packages": ScrollSmoother needs its own scrollTo, below the fixed header.
    document.querySelectorAll('[data-col-jump]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(a.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            e.stopPropagation();
            var smoother = window.ScrollSmoother && ScrollSmoother.get && ScrollSmoother.get();
            if (smoother) smoother.scrollTo(Math.max(0, smoother.offset(target, 'top top') - 110), true);
            else window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 110, behavior: 'smooth' });
        });
    });
})();
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include __DIR__ . '/../footer.php'; ?>
