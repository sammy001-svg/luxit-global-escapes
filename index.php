<?php
require_once 'includes/db.php';
function homeQuery(PDO $pdo, string $sql): array {
    try { return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC); } catch (PDOException $e) { return []; }
}
require_once 'includes/blog.php';
// "From the journal": the featured post first, then the newest. Falls back to
// newest-only if migrate.php has not yet added the featured column.
$blogCols  = "id, title, slug, excerpt, content, image, author, category, created_at";
$homeBlogs = homeQuery($pdo, "SELECT $blogCols FROM blog_posts WHERE status = 'Published' ORDER BY featured DESC, created_at DESC LIMIT 3")
          ?: homeQuery($pdo, "SELECT $blogCols FROM blog_posts WHERE status = 'Published' ORDER BY created_at DESC LIMIT 3");

require_once 'includes/packages.php';
require_once 'includes/destinations.php';
require_once 'includes/collections.php';
// Package sections are chosen in the admin Homepage tab (includes/homepage.php).
require_once 'includes/homepage.php';
$page_title = "Luxit Global Escapes | Safaris, Local & International Holidays";
$page_css = ['assets/css/tour.css', 'assets/css/destination.css', 'assets/css/blog.css', 'assets/css/home.css'];
$page_description = 'Luxit Global Escapes plans tailor-made safaris, local escapes and international holidays from Nairobi, Kenya. Browse packages or ask a specialist; we reply within 24 hours.';
include 'header.php';
?>
	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">
				<!-- Hero Carousel Start -->
				<style>
				/* ── Hero Carousel ──
				   The site header is absolutely positioned over the hero, so the copy
				   must start below it. --hero-top is the header's measured bottom edge
				   (set by the script after the hero); the fallbacks match its usual size. */
				.hero-carousel-wrap{--hero-top:130px;--hero-gutter:20px;--hero-bottom:118px;position:relative;height:100vh;height:100svh;min-height:620px;max-height:980px;overflow:hidden;background:#041c16;}
				@media(min-width:1024px){.hero-carousel-wrap{--hero-top:170px;--hero-gutter:70px;--hero-bottom:132px;min-height:660px;}}
				.hero-carousel-wrap .trv-header-carousel{height:100%;}

				/* Slide */
				.hero-slide{position:relative;height:100%;}
				.hero-slide-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transform:scale(1.06);transition:transform 7s ease-out;}
				.swiper-slide-active .hero-slide-img{transform:scale(1);}
				/* Left-weighted shade behind the copy, plus a top band so the logo and
				   menu stay legible whatever the photo looks like. */
				.hero-slide-overlay{position:absolute;inset:0;background:
					linear-gradient(180deg,rgba(4,28,22,.62) 0%,rgba(4,28,22,0) 30%),
					linear-gradient(95deg,rgba(4,28,22,.86) 0%,rgba(4,28,22,.6) 42%,rgba(4,28,22,.12) 78%,rgba(4,28,22,0) 100%);}

				/* Content: vertically centred in the space between header and nav bar */
				.hero-slide-content{position:absolute;inset:0;display:flex;align-items:center;padding:calc(var(--hero-top) + 24px) var(--hero-gutter) calc(var(--hero-bottom) + 16px);}
				.hero-copy{max-width:640px;text-align:left;}

				/* Animated elements — start hidden, reveal on active slide */
				.hero-eyebrow,.hero-title,.hero-desc,.hero-ctas{opacity:0;transform:translateY(24px);transition:opacity .8s ease,transform .8s cubic-bezier(.2,.7,.2,1);}
				.swiper-slide-active .hero-eyebrow{opacity:1;transform:none;transition-delay:100ms;}
				.swiper-slide-active .hero-title  {opacity:1;transform:none;transition-delay:250ms;}
				.swiper-slide-active .hero-desc   {opacity:1;transform:none;transition-delay:400ms;}
				.swiper-slide-active .hero-ctas   {opacity:1;transform:none;transition-delay:550ms;}
				@media(prefers-reduced-motion:reduce){.hero-eyebrow,.hero-title,.hero-desc,.hero-ctas,.hero-slide-img{transition:none;transform:none;}}

				.hero-eyebrow{display:inline-flex;align-items:center;gap:12px;color:#FFD214;font-size:.72rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;margin-bottom:20px;}
				.hero-eyebrow::before{content:'';display:block;width:36px;height:1px;background:currentColor;flex-shrink:0;}

				/* Sized by width AND height so short laptop screens get a smaller
				   headline rather than one that runs into the header. */
				.hero-title{color:#fff;font-family:'Playfair Display',Georgia,'Times New Roman',serif;font-weight:600;line-height:1.08;letter-spacing:-.01em;font-size:clamp(2.2rem,min(5vw,8.4vh),4.4rem);margin:0 0 22px;text-wrap:balance;text-shadow:0 2px 24px rgba(0,0,0,.25);}
				.hero-title em{color:#FFD214;font-style:italic;font-weight:500;}

				.hero-desc{color:rgba(255,255,255,.82);font-size:clamp(.95rem,1.2vw,1.08rem);max-width:500px;line-height:1.7;margin:0 0 34px;}

				.hero-ctas{display:flex;gap:14px;flex-wrap:wrap;align-items:center;}
				.hero-cta-ghost{display:inline-flex;align-items:center;gap:9px;color:#fff;border:1px solid rgba(255,255,255,.5);border-radius:50px;padding:12px 26px;font-size:.86rem;font-weight:600;letter-spacing:.04em;backdrop-filter:blur(4px);background:rgba(255,255,255,.06);transition:background .3s,border-color .3s;}
				.hero-cta-ghost:hover{background:#006A72;border-color:#006A72;color:#fff;}
				.hero-cta-ghost i{font-size:.75rem;transition:transform .3s;}
				.hero-cta-ghost:hover i{transform:translateX(4px);}

				/* Nav Bar */
				.hero-nav-bar{position:absolute;bottom:0;left:0;right:0;z-index:20;display:flex;align-items:center;gap:24px;height:var(--hero-bottom);padding:0 var(--hero-gutter);background:linear-gradient(0deg,rgba(4,28,22,.8) 0%,rgba(4,28,22,0) 100%);}

				/* Progress bar — runs across the top of the nav bar, inside the gutters */
				.hero-progress-wrap{position:absolute;top:28px;left:var(--hero-gutter);right:var(--hero-gutter);height:2px;background:rgba(255,255,255,.18);}
				.hero-progress-bar{height:100%;width:0%;background:#FFD214;transition:width 5s linear;}

				/* Counter */
				.hero-counter{color:rgba(255,255,255,.55);font-size:.76rem;letter-spacing:.12em;font-weight:600;min-width:60px;font-variant-numeric:tabular-nums;padding-top:28px;}
				.hero-counter strong{color:#fff;font-size:.92rem;}

				/* Arrows */
				.hero-arrows{display:flex;gap:10px;padding-top:28px;}
				.hero-arrows button{width:44px;height:44px;border-radius:50%;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.07);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .3s,border-color .3s;font-size:.8rem;}
				.hero-arrows button:hover{background:#006A72;border-color:#006A72;}
				.hero-arrows button:focus-visible{outline:2px solid #FFD214;outline-offset:3px;}

				/* Thumbnails */
				.hero-thumbs{display:flex;gap:10px;margin-left:auto;padding-top:28px;}
				.hero-thumb{width:76px;height:48px;border-radius:8px;overflow:hidden;cursor:pointer;opacity:.45;border:2px solid transparent;transition:opacity .35s,border-color .35s;}
				.hero-thumb img{width:100%;height:100%;object-fit:cover;}
				.hero-thumb.active{opacity:1;border-color:#FFD214;}
				.hero-thumb:hover:not(.active){opacity:.75;}

				/* Scroll indicator */
				.hero-scroll-indicator{position:absolute;right:28px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;align-items:center;gap:12px;z-index:20;}
				.hero-scroll-indicator span{color:rgba(255,255,255,.45);font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;writing-mode:vertical-rl;}
				.hero-scroll-line{width:1px;height:60px;background:rgba(255,255,255,.18);position:relative;overflow:hidden;}
				.hero-scroll-line::after{content:'';position:absolute;top:-60px;left:0;width:100%;height:60px;background:#FFD214;animation:heroScrollDown 2s ease-in-out infinite;}
				@keyframes heroScrollDown{0%{top:-60px}100%{top:60px}}

				/* Responsive */
				@media(max-width:1279px){.hero-scroll-indicator{display:none;}}
				/* Phones: hide thumbnails and keep the arrows clear of the fixed scroll-to-top button. */
				@media(max-width:767px){.hero-thumbs{display:none;}.hero-arrows{margin-left:auto;}.hero-nav-bar{padding-right:84px;}}
				/* Short laptop screens: tighten the stack so it never meets the header. */
				@media(min-width:1024px) and (max-height:800px){
					.hero-carousel-wrap{--hero-bottom:112px;}
					.hero-title{font-size:clamp(2rem,7.6vh,3.5rem);margin-bottom:16px;}
					.hero-eyebrow{margin-bottom:14px;}
					.hero-desc{margin-bottom:24px;}
				}
				@media(max-width:639px){
					.hero-carousel-wrap{--hero-bottom:96px;}
					.hero-title{font-size:clamp(2rem,9.5vw,2.6rem);}
					.hero-desc{font-size:.95rem;margin-bottom:28px;}
				}
				</style>

				<div class="hero-carousel-wrap">
					<div class="trv-header-carousel swiper">
						<div class="swiper-wrapper">

							<!-- Slide 1: Kenya Safari -->
							<div class="swiper-slide hero-slide">
								<img src="assets/images/header-carousel/safari.png" alt="Kenya Safari" class="hero-slide-img">
								<div class="hero-slide-overlay"></div>
								<div class="hero-slide-content">
									<div class="hero-copy">
										<span class="hero-eyebrow">Kenya Safari Experience</span>
										<h1 class="hero-title">Experience the <em>Wild Heart</em><br>of Africa</h1>
										<p class="hero-desc">Discover our curated safari packages designed for nature lovers and adventurers seeking the ultimate wilderness encounter.</p>
										<div class="hero-ctas">
											<a href="safari-packages.php" class="site-button butn-bg-shape">View Safaris</a>
											<a href="contact.php" class="hero-cta-ghost">Get In Touch <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
								</div>
							</div>

							<!-- Slide 2: Maldives Escape -->
							<div class="swiper-slide hero-slide">
								<img src="assets/images/header-carousel/maldives.png" alt="Maldives Escape" class="hero-slide-img">
								<div class="hero-slide-overlay"></div>
								<div class="hero-slide-content">
									<div class="hero-copy">
										<span class="hero-eyebrow">Luxury Island Retreats</span>
										<h1 class="hero-title">Paradise Found<br>in the <em>Maldives</em></h1>
										<p class="hero-desc">Float above turquoise lagoons in overwater villas — curated luxury escapes for travellers who demand the extraordinary.</p>
										<div class="hero-ctas">
											<a href="international-packages.php" class="site-button butn-bg-shape">Explore Deals</a>
											<a href="contact.php" class="hero-cta-ghost">Inquire Now <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
								</div>
							</div>

							<!-- Slide 3: Mountain Adventure -->
							<div class="swiper-slide hero-slide">
								<img src="assets/images/header-carousel/mountains.png" alt="Mountain Adventure" class="hero-slide-img">
								<div class="hero-slide-overlay"></div>
								<div class="hero-slide-content">
									<div class="hero-copy">
										<span class="hero-eyebrow">Adventure &amp; Trekking</span>
										<h1 class="hero-title">Reach <em>New Heights</em><br>with Luxit</h1>
										<p class="hero-desc">From the peaks of Kilimanjaro to misty highland trails — push your limits on expeditions crafted for true explorers.</p>
										<div class="hero-ctas">
											<a href="destinations.php" class="site-button butn-bg-shape">Discover Routes</a>
											<a href="contact.php" class="hero-cta-ghost">Join the Journey <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
								</div>
							</div>

							<!-- Slide 4: African Heritage -->
							<div class="swiper-slide hero-slide">
								<img src="assets/images/background/adv-bg.jpg" alt="African Heritage" class="hero-slide-img">
								<div class="hero-slide-overlay"></div>
								<div class="hero-slide-content">
									<div class="hero-copy">
										<span class="hero-eyebrow">Cultural Heritage Tours</span>
										<h1 class="hero-title">Uncover Africa's<br><em>Rich Heritage</em></h1>
										<p class="hero-desc">Walk ancient trade routes, meet local communities and immerse yourself in the vibrant cultures that define the African continent.</p>
										<div class="hero-ctas">
											<a href="local-packages.php" class="site-button butn-bg-shape">Explore Culture</a>
											<a href="contact.php" class="hero-cta-ghost">Plan My Trip <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
								</div>
							</div>

						</div><!-- /.swiper-wrapper -->
					</div><!-- /.trv-header-carousel -->

					<!-- Bottom Navigation Bar -->
					<div class="hero-nav-bar">
						<div class="hero-progress-wrap">
							<div class="hero-progress-bar"></div>
						</div>
						<span class="hero-counter"><strong>01</strong>&nbsp;/&nbsp;04</span>
						<div class="hero-arrows">
							<button class="hero-prev" aria-label="Previous slide"><i class="fa fa-arrow-left"></i></button>
							<button class="hero-next" aria-label="Next slide"><i class="fa fa-arrow-right"></i></button>
						</div>
						<div class="hero-thumbs">
							<div class="hero-thumb active" data-index="0"><img src="assets/images/header-carousel/safari.png" alt="Safari"></div>
							<div class="hero-thumb" data-index="1"><img src="assets/images/header-carousel/maldives.png" alt="Maldives"></div>
							<div class="hero-thumb" data-index="2"><img src="assets/images/header-carousel/mountains.png" alt="Mountains"></div>
							<div class="hero-thumb" data-index="3"><img src="assets/images/background/adv-bg.jpg" alt="Heritage"></div>
						</div>
					</div>

					<!-- Scroll Indicator -->
					<div class="hero-scroll-indicator">
						<span>Scroll</span>
						<div class="hero-scroll-line"></div>
					</div>
				</div>
				<!-- Hero Carousel End -->
				<script>
				// Keep the hero copy clear of the header: measure where the header
				// actually ends (its height changes with the top bar and breakpoints).
				(function () {
					var wrap = document.querySelector('.hero-carousel-wrap');
					var header = document.querySelector('.site-header');
					if (!wrap || !header) return;
					function measure() {
						// Once scrolled the header turns fixed and collapses; only its
						// resting position over the hero matters.
						if (header.classList.contains('is-fixed')) return;
						var gap = header.getBoundingClientRect().bottom - wrap.getBoundingClientRect().top;
						if (gap > 0) wrap.style.setProperty('--hero-top', Math.ceil(gap) + 'px');
					}
					measure();
					window.addEventListener('resize', measure);
					window.addEventListener('load', measure);
				})();
				</script>
				
				<div class="dst">

				<!-- TRIP FINDER START -->
				<div class="hm-finder-wrap">
					<div class="dst-wrap">
						<form class="hm-finder" id="hm-finder" action="destinations.php" aria-label="Find a trip">
							<label>Where to?
								<select id="hm-where">
									<option value="">Any destination</option>
									<?php foreach (DEST_REGIONS as $rk => $r) : ?>
									<optgroup label="<?php echo htmlspecialchars($r['name']); ?>">
										<option value="<?php echo $r['page']; ?>">All of <?php echo htmlspecialchars($r['name']); ?></option>
										<?php foreach (destinationCountries($rk) as $ck => $c) : ?>
										<option value="<?php echo $ck; ?>.php"><?php echo htmlspecialchars($c['name']); ?></option>
										<?php endforeach; ?>
									</optgroup>
									<?php endforeach; ?>
								</select>
							</label>
							<label>Type of trip
								<select id="hm-type">
									<option value="">Any type</option>
									<?php foreach (TOUR_COLLECTIONS as $ck => $c) : ?>
									<option value="<?php echo $c['page']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
							<button type="submit" class="dst-btn dst-btn--primary"><i class="fa-solid fa-magnifying-glass"></i> Find trips</button>
							<a class="dst-btn dst-btn--ghost" href="contact.php?topic=Plan+a+trip">Plan a custom trip</a>
						</form>
						<p class="hm-quick">Popular: <a href="safari-packages.php">Safaris</a><a href="kenya.php">Kenya</a><a href="maldives.php">Maldives</a><a href="dubai.php">Dubai</a><a href="romantic-travel.php">Honeymoons</a></p>
					</div>
				</div>
				<!-- TRIP FINDER END -->

				<?php
				// One section per homepage area chosen in the admin Homepage tab.
				// A section with no packages, or switched off, is not shown.
				$hmTint = false;
				foreach (HOME_SECTIONS as $hsKey => $hs) :
					if (!homeSectionEnabled($pdo, $hsKey)) continue;
					$hsPackages = homeSectionPackages($pdo, $hsKey);
					if (!$hsPackages) continue;
					$hmTint = !$hmTint;
				?>
				<!-- <?php echo strtoupper($hsKey); ?> PACKAGES START -->
				<section class="dst-section hm-pk <?php echo $hmTint ? 'dst-section--tint' : ''; ?>" id="home-<?php echo $hsKey; ?>" aria-labelledby="home-<?php echo $hsKey; ?>-title">
					<div class="dst-wrap">
						<div class="dst-section-head">
							<div>
								<span class="dst-eyebrow"><?php echo htmlspecialchars($hs['eyebrow']); ?></span>
								<h2 class="dst-h2" id="home-<?php echo $hsKey; ?>-title"><?php echo htmlspecialchars($hs['title']); ?></h2>
								<p><?php echo htmlspecialchars($hs['intro']); ?></p>
							</div>
							<?php if ($hsKey !== 'offers') : ?>
							<a class="dst-btn dst-btn--ghost hm-viewall" href="<?php echo $hs['link']; ?>">View all <?php echo htmlspecialchars(strtolower($hs['title'])); ?> <i class="fa-solid fa-arrow-right"></i></a>
							<?php endif; ?>
						</div>
						<div class="pk-grid">
							<?php foreach ($hsPackages as $t) :
								if ($hsKey !== 'offers') { packageTile($t); continue; }
								$badge = trim((string)($t['promo_badge'] ?? ''));
								$tag   = trim((string)($t['promo_tagline'] ?? ''));
								$style = in_array($t['promo_style'] ?? '', ['primary', 'secondary', 'citrus', 'red'], true) ? $t['promo_style'] : 'primary';
							?>
							<a class="pk-tile hm-offer" href="tour-detail.php?id=<?php echo (int)$t['id']; ?>">
								<div class="pk-tile-img">
									<img src="<?php echo packageImage($t); ?>" alt="" loading="lazy">
									<?php if (!empty($t['duration'])) : ?><span class="pk-tile-dur"><?php echo htmlspecialchars($t['duration']); ?></span><?php endif; ?>
									<?php if ($badge !== '') : ?><span class="hm-badge hm-badge--<?php echo $style; ?>"><?php echo htmlspecialchars($badge); ?></span><?php endif; ?>
								</div>
								<div class="pk-tile-body">
									<span class="pk-tile-loc"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($t['location']); ?></span>
									<h3 class="pk-tile-title"><?php echo htmlspecialchars($t['title']); ?></h3>
									<?php if ($tag !== '') : ?><p class="hm-tagline">&ldquo;<?php echo htmlspecialchars($tag); ?>&rdquo;</p><?php endif; ?>
									<span class="pk-tile-price">From <b>$<?php echo number_format((float)$t['price']); ?></b> per person</span>
								</div>
							</a>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
				<!-- <?php echo strtoupper($hsKey); ?> PACKAGES END -->
				<?php endforeach; ?>

				<!-- REGIONS START -->
				<section class="dst-section <?php echo $hmTint ? '' : 'dst-section--tint'; ?>" aria-labelledby="home-regions-title">
					<div class="dst-wrap">
						<div class="dst-section-head">
							<div>
								<span class="dst-eyebrow">Where we travel</span>
								<h2 class="dst-h2" id="home-regions-title">Explore by region</h2>
								<p><?php echo count(DEST_COUNTRIES); ?> destinations across four regions, each planned by specialists who know them.</p>
							</div>
							<a class="dst-btn dst-btn--ghost hm-viewall" href="destinations.php">All destinations <i class="fa-solid fa-arrow-right"></i></a>
						</div>
						<div class="hm-regions">
							<?php foreach (DEST_REGIONS as $rk => $r) : ?>
							<a class="hm-region" href="<?php echo $r['page']; ?>">
								<img src="<?php echo htmlspecialchars($r['image']); ?>" alt="" loading="lazy" width="640" height="640">
								<span class="hm-region-text">
									<strong><?php echo htmlspecialchars($r['name']); ?></strong>
									<span><?php echo count(destinationCountries($rk)); ?> destinations · <?php echo htmlspecialchars($r['tagline']); ?></span>
								</span>
							</a>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
				<!-- REGIONS END -->

				<!-- HOW IT WORKS START -->
				<section class="dst-section <?php echo $hmTint ? 'dst-section--tint' : ''; ?>" aria-labelledby="home-how-title">
					<div class="dst-wrap">
						<div class="dst-section-head">
							<div>
								<span class="dst-eyebrow">How it works</span>
								<h2 class="dst-h2" id="home-how-title">Your trip, planned in three steps</h2>
							</div>
						</div>
						<div class="hm-steps">
							<div class="hm-step"><span class="hm-step-n">1</span><h3>Tell us your plans</h3><p>Pick a package or send us your dates, group and wishes. It takes a minute and costs nothing.</p></div>
							<div class="hm-step"><span class="hm-step-n">2</span><h3>Get a tailored proposal</h3><p>A specialist replies within 24 hours with availability, an itinerary and a clear quote.</p></div>
							<div class="hm-step"><span class="hm-step-n">3</span><h3>Travel with support</h3><p>Once you are happy we confirm everything, and our Nairobi team is a call or WhatsApp away throughout.</p></div>
						</div>
					</div>
				</section>
				<!-- HOW IT WORKS END -->

				</div><!-- /.dst -->

				<!--ALL BLOGS SECTION START-->
				<?php if (!empty($homeBlogs)) : ?>
				<section class="hj" aria-labelledby="hj-title">
					<div class="ba-wrap">
						<div class="hj-head">
							<div>
								<span class="bl-eyebrow">From the journal</span>
								<h2 id="hj-title">Travel stories &amp; advice</h2>
								<p>Destination guides and planning tips from our team.</p>
							</div>
							<a href="blog.php">Read the journal <i class="fa-solid fa-arrow-right"></i></a>
						</div>
						<div class="ba-grid">
							<?php foreach ($homeBlogs as $hb) blogCard($hb); ?>
						</div>
					</div>
				</section>
				<?php endif; ?>
				<!--ALL BLOGS SECTION END-->
			</div>
			<!-- CONTENT END -->

<?php ob_start(); ?>
<script>
// Trip finder: a destination wins; otherwise the trip type; otherwise everything.
(function () {
	var form = document.getElementById('hm-finder');
	if (!form) return;
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var where = document.getElementById('hm-where').value;
		var type = document.getElementById('hm-type').value;
		window.location.href = where || type || 'destinations.php';
	});
})();
</script>
<?php $page_scripts = ob_get_clean(); ?>

			<!-- FOOTER START -->
<?php include 'footer.php'; ?>
