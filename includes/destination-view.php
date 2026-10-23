<?php
/**
 * Renders a destination page. The page file sets one of:
 *   $destCountry = 'kenya';     a country page (kenya.php, …)
 *   $destRegion  = 'africa';    a region page (africa.php, …)
 *   neither                     the overview (destinations.php)
 * Content lives in includes/destinations.php.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/packages.php';
require_once __DIR__ . '/destinations.php';

$country = isset($destCountry) ? (DEST_COUNTRIES[$destCountry] ?? null) : null;
$regionKey = $country ? $country['region'] : ($destRegion ?? null);
$region = $regionKey ? (DEST_REGIONS[$regionKey] ?? null) : null;
$mode = $country ? 'country' : ($region ? 'region' : 'overview');

if ($mode === 'country') {
    $name     = $country['name'];
    $packages = destinationPackages($pdo, [$destCountry], 6);
    $nearby   = array_diff_key(destinationCountries($regionKey), [$destCountry => 1]);
    $photo    = destinationImage($destCountry);
    $page_title       = "$name Holidays & Tours | Luxit Global Escapes";
    $page_description = $country['tagline'] . '. ' . $country['intro'];
} elseif ($mode === 'region') {
    $name      = $region['name'];
    $countries = destinationCountries($regionKey);
    $packages  = destinationPackages($pdo, array_keys($countries), 6);
    $photo     = $region['image'];
    $page_title       = "$name Holidays & Tours | Luxit Global Escapes";
    $page_description = $region['tagline'] . '. ' . $region['intro'];
} else {
    $name = 'Destinations';
    $page_title       = 'Destinations | Luxit Global Escapes';
    $page_description = 'Explore our destinations across Africa, Asia, the Middle East and Europe, from Kenyan safaris to Maldives islands, with tailor-made trips planned by our specialists.';
}
$page_description = mb_substr($page_description, 0, 300);
$page_image       = $photo ?? DEST_REGIONS['africa']['image'];
$page_css         = ['assets/css/tour.css', 'assets/css/destination.css'];
$header_variant   = 'solid';

$waNumber   = '254737800900';
$planLink   = 'contact.php?' . http_build_query(['topic' => 'Plan a trip', 'destination' => $mode === 'overview' ? '' : $name]);
$waLink     = 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Hello Luxit Global Escapes, I would like help planning a trip' . ($mode === 'overview' ? '.' : " to $name."));
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);

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
                            <?php if ($mode === 'overview') : ?>
                                <span aria-current="page">Destinations</span>
                            <?php else : ?>
                                <a href="destinations.php">Destinations</a><span aria-hidden="true">/</span>
                                <?php if ($mode === 'country') : ?>
                                    <a href="<?php echo $region['page']; ?>"><?php echo $h($region['name']); ?></a><span aria-hidden="true">/</span>
                                <?php endif; ?>
                                <span aria-current="page"><?php echo $h($name); ?></span>
                            <?php endif; ?>
                        </nav>

                        <span class="dst-eyebrow"><?php echo $mode === 'country' ? $h($region['name']) : ($mode === 'region' ? count($countries) . ' destinations' : 'Where will you go next?'); ?></span>
                        <h1 class="dst-title"><?php echo $mode === 'overview' ? 'Destinations we know &amp; love' : $h($name); ?></h1>
                        <p class="dst-tagline"><?php
                            echo $mode === 'country' ? $h($country['tagline'])
                               : ($mode === 'region' ? $h($region['tagline'])
                               : 'Safaris, islands, ancient wonders and great cities, each planned around you by a team that has travelled them.');
                        ?></p>

                        <?php if ($mode === 'country') : ?>
                        <div class="dst-facts">
                            <div class="dst-fact"><i class="fa-regular fa-calendar"></i><div><small>Best time to visit</small><span><?php echo $h($country['best']); ?></span></div></div>
                            <div class="dst-fact"><i class="fa-regular fa-heart"></i><div><small>Ideal for</small><span><?php echo $h(implode(' · ', $country['ideal'])); ?></span></div></div>
                        </div>
                        <?php endif; ?>

                        <div class="dst-ctas">
                            <a class="dst-btn dst-btn--primary" href="<?php echo $h($planLink); ?>"><i class="fa-solid fa-route"></i> Plan a trip<?php echo $mode === 'overview' ? '' : ' to ' . $h($name); ?></a>
                            <?php if ($mode !== 'overview') : ?>
                            <a class="dst-btn dst-btn--ghost" href="#dst-packages">See packages</a>
                            <?php endif; ?>
                        </div>
                        <?php if ($mode === 'overview') : ?>
                        <nav class="dst-jump" aria-label="Regions">
                            <?php foreach (DEST_REGIONS as $rk => $r) : ?><a href="#region-<?php echo $rk; ?>" data-dst-jump><?php echo $h($r['name']); ?></a><?php endforeach; ?>
                        </nav>
                        <?php endif; ?>
                    </div>

                    <?php if ($mode !== 'overview') : ?>
                    <figure class="dst-hero-photo">
                        <img src="<?php echo $h($photo); ?>" alt="<?php echo $h($name); ?>" width="640" height="640">
                        <figcaption><i class="fa-solid fa-location-dot"></i> <?php echo $h($name); ?></figcaption>
                    </figure>
                    <?php else : ?>
                    <figure class="dst-hero-photo">
                        <img src="<?php echo $h(DEST_REGIONS['africa']['image']); ?>" alt="Safari at sunset in East Africa" width="640" height="640">
                        <figcaption><i class="fa-solid fa-earth-africa"></i> <?php echo count(DEST_COUNTRIES); ?> destinations, 4 regions</figcaption>
                    </figure>
                    <?php endif; ?>
                </div>
            </header>

            <?php if ($mode === 'country') : ?>
            <section class="dst-section" aria-labelledby="dst-about">
                <div class="dst-wrap dst-about">
                    <div>
                        <span class="dst-eyebrow">Why visit</span>
                        <h2 class="dst-h2" id="dst-about">Discover <?php echo $h($name); ?></h2>
                        <p class="dst-lead"><?php echo $h($country['intro']); ?></p>
                    </div>
                    <div class="dst-hl-grid">
                        <?php $n = 1; foreach ($country['highlights'] as $title => $text) : ?>
                        <div class="dst-hl">
                            <span class="dst-hl-n" aria-hidden="true"><?php echo $n++; ?></span>
                            <h3><?php echo $h($title); ?></h3>
                            <p><?php echo $h($text); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php elseif ($mode === 'region') : ?>
            <section class="dst-section" aria-labelledby="dst-about">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">Explore <?php echo $h($name); ?></span>
                            <h2 class="dst-h2" id="dst-about">Where to go in <?php echo $h($name); ?></h2>
                            <p><?php echo $h($region['intro']); ?></p>
                        </div>
                    </div>
                    <div class="dst-cards">
                        <?php foreach ($countries as $ck => $c) : ?>
                        <a class="dst-card" href="<?php echo $ck; ?>.php">
                            <span class="dst-card-name"><?php echo $h($c['name']); ?></span>
                            <p><?php echo $h($c['tagline']); ?></p>
                            <span class="dst-card-meta"><i class="fa-regular fa-calendar"></i> Best: <?php echo $h($c['best']); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php else : ?>
            <section class="dst-section" aria-label="All destinations">
                <div class="dst-wrap">
                    <?php foreach (DEST_REGIONS as $rk => $r) : $rc = destinationCountries($rk); ?>
                    <div class="dst-region" id="region-<?php echo $rk; ?>">
                        <div class="dst-region-aside">
                            <div class="dst-region-photo"><img src="<?php echo $h($r['image']); ?>" alt="<?php echo $h($r['name']); ?>" loading="lazy" width="640" height="480"></div>
                            <h2><?php echo $h($r['name']); ?></h2>
                            <p><?php echo $h($r['tagline']); ?>.</p>
                            <a class="dst-link" href="<?php echo $r['page']; ?>">Explore <?php echo $h($r['name']); ?> <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                        <div class="dst-cards">
                            <?php foreach ($rc as $ck => $c) : ?>
                            <a class="dst-card" href="<?php echo $ck; ?>.php">
                                <span class="dst-card-name"><?php echo $h($c['name']); ?></span>
                                <p><?php echo $h($c['tagline']); ?></p>
                                <span class="dst-card-meta"><i class="fa-regular fa-calendar"></i> Best: <?php echo $h($c['best']); ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($mode !== 'overview') : ?>
            <section class="dst-section dst-section--tint" id="dst-packages" aria-labelledby="dst-pk-title">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">Ready-made trips</span>
                            <h2 class="dst-h2" id="dst-pk-title"><?php echo $h($name); ?> packages</h2>
                            <p><?php echo $packages ? 'Start from one of our packages, then make it yours. Every trip can be adjusted to your dates and budget.' : ''; ?></p>
                        </div>
                    </div>
                    <?php if ($packages) : ?>
                    <div class="pk-grid">
                        <?php foreach ($packages as $t) packageTile($t); ?>
                    </div>
                    <?php else : ?>
                    <div class="dst-empty">
                        <span class="dst-empty-icon"><i class="fa-solid fa-pen-ruler"></i></span>
                        <div>
                            <h3>We tailor-make <?php echo $h($name); ?> trips</h3>
                            <p>There is no ready-made <?php echo $h($name); ?> package online right now, but our specialists design trips here around your dates, budget and interests. Tell us what you have in mind.</p>
                        </div>
                        <a class="dst-btn dst-btn--primary" href="<?php echo $h($planLink); ?>">Request a tailor-made trip</a>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($mode === 'country' && $nearby) : ?>
            <section class="dst-section" aria-labelledby="dst-near">
                <div class="dst-wrap">
                    <div class="dst-section-head">
                        <div>
                            <span class="dst-eyebrow">Combine your trip</span>
                            <h2 class="dst-h2" id="dst-near">More of <?php echo $h($region['name']); ?></h2>
                        </div>
                        <a class="dst-btn dst-btn--ghost" href="<?php echo $region['page']; ?>">All <?php echo $h($region['name']); ?> destinations</a>
                    </div>
                    <div class="dst-cards">
                        <?php foreach (array_slice($nearby, 0, 6, true) as $ck => $c) : ?>
                        <a class="dst-card" href="<?php echo $ck; ?>.php">
                            <span class="dst-card-name"><?php echo $h($c['name']); ?></span>
                            <p><?php echo $h($c['tagline']); ?></p>
                            <span class="dst-card-meta"><i class="fa-regular fa-calendar"></i> Best: <?php echo $h($c['best']); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <div class="dst-cta-wrap">
                <div class="dst-wrap">
                    <div class="dst-cta">
                        <div>
                            <h2><?php echo $mode === 'overview' ? 'Not sure where to go?' : 'Talk to our ' . $h($name) . ' specialists'; ?></h2>
                            <p>Tell us your dates, who is travelling and what you love. We will reply within 24 hours with ideas, and there is no charge for a quote.</p>
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
// In-page links (region jump links, "See packages"): ScrollSmoother needs its
// own scrollTo, and the header is fixed, so land the target just below it.
document.querySelectorAll('a[href^="#region-"], a[href="#dst-packages"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
        var target = document.querySelector(a.getAttribute('href'));
        if (!target) return;
        e.preventDefault();
        e.stopPropagation(); // keep animation.js's document-level handler out
        var smoother = window.ScrollSmoother && ScrollSmoother.get && ScrollSmoother.get();
        if (smoother) smoother.scrollTo(Math.max(0, smoother.offset(target, 'top top') - 110), true);
        else window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 110, behavior: 'smooth' });
    });
});
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include __DIR__ . '/../footer.php'; ?>
