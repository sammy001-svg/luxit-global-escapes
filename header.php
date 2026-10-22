<?php
/**
 * Shared page header: <head>, top contact bar and site navigation.
 *
 * Set before including:
 *   $page_title        <title>, also used for social cards
 *   $page_description  meta / social description (optional)
 *   $page_image        social-card image path (optional)
 *   $page_css          extra stylesheet paths for this page (optional)
 *   $header_variant    'overlay' (default) — transparent, white links, for
 *                      pages that open on a dark full-bleed hero (homepage);
 *                      'solid' — white floating bar with dark links, readable
 *                      over any inner-page banner.
 * Close the page with footer.php, which closes the wrappers opened here.
 */
$page_title       = $page_title ?? 'Luxit Global Escapes - Luxury Travel & Tour Agency';
$page_description = $page_description ?? 'Luxit Global Escapes crafts luxury safaris, international holidays and tailor-made journeys, with expert planning and 24/7 support from Nairobi, Kenya.';
$header_variant   = ($header_variant ?? 'overlay') === 'solid' ? 'solid' : 'overlay';
$hTitle = htmlspecialchars($page_title, ENT_QUOTES);
$hDesc  = htmlspecialchars($page_description, ENT_QUOTES);
$hImage = htmlspecialchars($page_image ?? 'assets/images/seo/og_index.png', ENT_QUOTES);
?>
<!DOCTYPE html><html lang="en"><head>
    <!-- Character Encoding -->
	<meta charset="UTF-8">

	<!-- Responsive Design -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Title -->
	<title><?php echo $hTitle; ?></title>

    <meta name="description" content="<?php echo $hDesc; ?>">
    <meta name="author" content="Luxit Global Escapes">
    <meta name="robots" content="index, follow">

    <!-- FAVICONS ICON -->
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="<?php echo $hTitle; ?>">
    <meta property="og:description" content="<?php echo $hDesc; ?>">
    <meta property="og:image" content="<?php echo $hImage; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Luxit Global Escapes">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $hTitle; ?>">
    <meta name="twitter:description" content="<?php echo $hDesc; ?>">
    <meta name="twitter:image" content="<?php echo $hImage; ?>">

    <!-- IE Compatibility -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="assets/icons/line-awesome/css/line-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/icons/flaticon/flaticon.css">
	<link rel="stylesheet" type="text/css" href="assets/icons/fontawesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="assets/icons/themify-icons/css/themify-icons.css">
	<link rel="stylesheet" type="text/css" href="assets/icons/feather/css/feather.css">
		
	<link rel="stylesheet" href="assets/vendor/swiper/swiper-bundle.min.css">
	<link rel="stylesheet" href="assets/vendor/flatpickr/css/flatpicker.css">
	<link rel="stylesheet" href="assets/css/lc_lightbox.css">
	<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css">
	<link rel="stylesheet" href="assets/vendor/lightgallery/dist/css/lightgallery-bundle.min.css">
	<link rel="stylesheet" href="assets/vendor/nouislider/nouislider.min.css">

	<!-- style -->
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/responsive.css">
	<link rel="stylesheet" href="assets/css/packages.css">
	<link rel="stylesheet" href="assets/css/site.css">
	<link rel="stylesheet" href="assets/css/menu.css">
<?php foreach (($page_css ?? []) as $css) : ?>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($css, ENT_QUOTES); ?>">
<?php endforeach; ?>
	<style>
	/* Top contact bar: the md:gap-x-48 / sm:gap-x-10 utilities are not in the
	   compiled build, which left the address, phone and email touching. */
	.site-topbar-inner { column-gap: clamp(18px, 4vw, 56px); }

	/* Solid header variant (inner pages): a white floating bar with dark links,
	   readable over light and dark banners alike. Once scrolled the header
	   turns fixed and teal (is-fixed), so these only apply at rest. */
	.site-header--solid:not(.is-fixed) .main-bar-wraper > div {
	  background: #fff;
	  box-shadow: 0 12px 32px rgba(6, 97, 104, .12);
	}
	@media (min-width: 1024px) {
	  .site-header--solid:not(.is-fixed) { top: 62px; }
	  .site-header--solid:not(.is-fixed) .main-bar-wraper > div { min-height: 88px; }
	  .site-header--solid:not(.is-fixed) .navbar-nav > li > a { color: #0d2f32; }
	  .site-header--solid:not(.is-fixed) .navbar-nav > li > a:hover { color: var(--color-secondary, #85D200); }
	  .site-header--solid:not(.is-fixed) .quick-search { color: #0d2f32; }
	  .site-header--solid:not(.is-fixed) .toggle-nav-btn > span { background: #0d2f32; }
	}
	.site-header--solid:not(.is-fixed) .xmenu-toggler { background: #066168; border-radius: 10px; }
	@media (max-width: 1023px) {
	  .site-header--solid:not(.is-fixed) { top: 54px; left: 10px; right: 10px; }
	}

	</style>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Afacad:ital,wght@0,400..700;1,400..700&family=Figtree:ital,wght@0,300..900;1,300..900&family=Kaushan+Script&family=Playfair+Display:ital,wght@0,500..700;1,500..700&display=swap" rel="stylesheet">

</head>
<body id="bg" class="selection:bg-[#484848] selection:text-white">
<!-- LOADING AREA START ===== -->
<div class="loading-area">
    <div class="loading-box"></div>
    <div class="loading-pic">
        <figure class="loader">
            <div class="dot white"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </figure>
    </div>
</div>
<!-- LOADING AREA  END ====== -->

<!-- Curser Pointer -->
<div class="cursor"></div>
<div class="cursor2"></div>
<div class="page-wraper">
	<div class="bg-white text-black py-3 block border-b border-black/5 relative" style="z-index: 998;">
		<div class="site-topbar-inner container mx-auto sm:px-8.75 px-4 flex flex-wrap justify-center items-center text-[12px] font-medium tracking-wide md:gap-x-48 sm:gap-x-10 gap-x-4 gap-y-1.5">
			<div class="flex items-center">
				<i class="fas fa-map-marker-alt text-secondary mr-2 text-sm"></i>
				<span>Karen, Nairobi-Kenya</span>
			</div>
			<div class="flex items-center">
				<i class="fas fa-phone-alt text-secondary mr-2 text-sm"></i>
				<a href="tel:+254737800900" class="hover:text-white transition duration-300">+254 737 800 900</a>
			</div>
			<div class="hidden sm:flex items-center">
				<i class="fas fa-envelope text-secondary mr-2 text-sm"></i>
				<a href="mailto:info@luxitglobalescapes.com" class="hover:text-white transition duration-300 lowercase">info@luxitglobalescapes.com</a>
			</div>
		</div>
	</div>
	<header class="site-header site-header--<?php echo $header_variant; ?> sticky-header absolute lg:left-8.75 lg:right-8.75 lg:top-[44px] left-0 right-0 top-[44px] duration-500 z-999 [.site-header.is-fixed]:fixed [.site-header.is-fixed]:animate-header-scroll-animation [.site-header.is-fixed]:bg-primary [.site-header.is-fixed]:rounded-b-3xl [.site-header.is-fixed]:top-0">
		<div class="main-bar-wraper">
			<div class="w-full lg:min-h-30 min-h-20 lg:ps-8.75 px-4 lg:pe-13.75 duration-500 rounded-5xl flex items-center justify-between">
				<div class="flex relative w-full">
					<div class="flex items-center relative z-9 h-20 lg:w-44 w-30">
						<a href="index.php" class="table-cell align-middle">
							<img src="assets/images/luxit-africa-logo.png" alt="logo" class="object-contain duration-500">
						</a>
					</div>
					<button class="xmenu-toggler lg:hidden float-right mt-4.5 mb-4 md:ml-7 ml-4 size-11 bg-dark-600 relative cursor-pointer max-lg:order-1" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
						<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-300 top-3.25 w-5.5"></span>
						<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-0 top-5.5 w-6.25"></span>
						<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-300 top-8 w-4"></span>
					</button>
					<div class="lg:hidden fixed top-0 left-0 bg-black size-full duration-300 z-999 opacity-0 visible pointer-events-none menu-close fade-overlay"></div>
					<div class="flex lg:justify-center lg:basis-auto lg:grow max-lg:flex-col justify-start font-base max-lg:fixed max-lg:h-screen max-lg:px-5 max-lg:top-0 max-lg:-left-75 max-lg:z-9999 max-lg:bg-white max-lg:w-72 max-lg:overflow-auto max-lg:duration-700 header-nav custom-scroll">
						<div class="flex items-center relative z-9 py-6.25 lg:hidden">
							<a href="index.php" class="table-cell align-middle">
								<img src="assets/images/luxit-africa-logo.png" alt="" class="object-contain duration-500">
							</a>
						</div>
						<ul class="lg:flex flex-wrap navbar-nav">
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="index.php">
									<span class="inline-block">Home</span>
								</a>
							</li>
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="about.php">
									<span class="inline-block">About</span>
								</a>
							</li>

							<?php
							// Mega-menu content. Add a destination by adding its page here.
							$mmRegions = [
								'africa' => ['Africa', 'africa.php', 'assets/images/header-banners/destination-africa.png',
									'Safaris, gorilla treks and Indian Ocean islands.',
									['Kenya' => 'kenya.php', 'Tanzania' => 'tanzania.php', 'Uganda' => 'uganda.php', 'Rwanda' => 'rwanda.php',
									 'South Africa' => 'south-africa.php', 'Botswana' => 'botswana.php', 'Namibia' => 'namibia.php', 'Zambia' => 'zambia.php',
									 'Zimbabwe' => 'zimbabwe.php', 'Seychelles' => 'seychelles.php', 'Madagascar' => 'madagascar.php', 'Morocco' => 'morocco.php',
									 'Egypt' => 'egypt.php']],
								'asia' => ['Asia', 'asia.php', 'assets/images/header-banners/destination-asia.png',
									'Island hideaways, temples and vibrant cities.',
									['Maldives' => 'maldives.php', 'Thailand' => 'thailand.php', 'Indonesia' => 'indonesia.php', 'Malaysia' => 'malaysia.php',
									 'Singapore' => 'singapore.php', 'Philippines' => 'philippines.php', 'India' => 'india.php', 'China' => 'china.php']],
								'middle-east' => ['Middle East', 'middle-east.php', 'assets/images/header-banners/destination-middle-east.png',
									'Desert luxury, ancient wonders and city breaks.',
									['Dubai' => 'dubai.php', 'Oman' => 'oman.php', 'Jordan' => 'jordan.php']],
								'europe' => ['Europe', 'europe.php', 'assets/images/header-banners/destination-europe.png',
									'Historic capitals, coastlines and fine food.',
									['France' => 'france.php', 'Italy' => 'italy.php', 'Spain' => 'spain.php', 'Greece' => 'greece.php', 'Turkey' => 'turkey.php']],
							];
							$mmPackages = [
								['Safari Packages', 'safari-packages.php', 'assets/images/header-carousel/safari.png', 'Game drives and wildlife camps'],
								['Local Packages', 'local-packages.php', 'assets/images/header-carousel/mountains.png', 'Kenya, East Africa and beyond'],
								['International Packages', 'international-packages.php', 'assets/images/header-carousel/maldives.png', 'Beach, city and island escapes'],
							];
							$mmStyles = [
								['Romantic Travel', 'romantic-travel.php', 'fa-heart', 'Honeymoons and anniversaries'],
								['Wellness Travel', 'wellness-travel.php', 'fa-spa', 'Retreats, spas and slow travel'],
								['Halal Travel', 'halal-travel.php', 'fa-moon', 'Halal dining and prayer-friendly stays'],
								['Kosher Travel', 'kosher-travel.php', 'fa-star-of-david', 'Kosher meals and Shabbat planning'],
								['Faith Travel', 'faith-travel.php', 'fa-place-of-worship', 'Pilgrimages and heritage journeys'],
								['Accessible Travel', 'accessible-travel.php', 'fa-wheelchair', 'Step-free stays and adapted transport'],
								['MICE Travel', 'mice-travel.php', 'fa-briefcase', 'Meetings, incentives and events'],
							];
							$mmLink = 'lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-flex lg:items-center block text-lg font-medium lg:text-white text-black hover:text-secondary';
							?>

							<!-- ══ DESTINATIONS MEGA MENU ══ -->
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 mm-item" data-mm>
								<a class="<?php echo $mmLink; ?>" href="destinations.php" aria-haspopup="true">
									<span class="inline-block">Destinations</span><span class="mm-caret" aria-hidden="true"></span>
								</a>
								<button type="button" class="mm-toggle" aria-expanded="false" aria-controls="mm-destinations" aria-label="Show destinations"></button>
								<div class="mm-panel" id="mm-destinations">
									<div class="mm-dest">
										<div class="mm-rail" role="tablist" aria-label="Regions">
											<p class="mm-label">Regions</p>
											<?php $first = true; foreach ($mmRegions as $key => [$name, $page, $img, $blurb, $places]) : ?>
											<a class="mm-region<?php echo $first ? ' is-active' : ''; ?>" href="<?php echo $page; ?>" role="tab" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" aria-controls="mm-region-<?php echo $key; ?>" data-mm-region="<?php echo $key; ?>">
												<span><?php echo $name; ?></span>
												<small><?php echo count($places); ?></small>
											</a>
											<?php $first = false; endforeach; ?>
										</div>
										<?php $first = true; foreach ($mmRegions as $key => [$name, $page, $img, $blurb, $places]) : ?>
										<div class="mm-region-pane<?php echo $first ? ' is-active' : ''; ?>" id="mm-region-<?php echo $key; ?>" role="tabpanel">
											<div class="mm-places">
												<p class="mm-label"><?php echo $name; ?> &middot; <?php echo count($places); ?> destinations</p>
												<div class="mm-place-grid">
													<?php foreach ($places as $place => $href) : ?>
													<a class="mm-place" href="<?php echo $href; ?>"><?php echo $place; ?></a>
													<?php endforeach; ?>
												</div>
											</div>
											<a class="mm-feature" href="<?php echo $page; ?>">
												<img src="<?php echo $img; ?>" alt="" loading="lazy" width="320" height="320">
												<span class="mm-feature-text">
													<strong>Explore <?php echo $name; ?></strong>
													<span><?php echo $blurb; ?></span>
												</span>
											</a>
										</div>
										<?php $first = false; endforeach; ?>
									</div>
									<div class="mm-foot">
										<span><i class="fa-solid fa-compass" aria-hidden="true"></i> Not sure where to go? Our specialists will suggest destinations for your dates and budget.</span>
										<span class="mm-foot-actions">
											<a href="destinations.php" class="mm-foot-link">All destinations <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
											<a href="contact.php" class="mm-btn">Talk to an expert</a>
										</span>
									</div>
								</div>
							</li>

							<!-- ══ TOURS MEGA MENU ══ -->
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 mm-item" data-mm>
								<a class="<?php echo $mmLink; ?>" href="safari-packages.php" aria-haspopup="true">
									<span class="inline-block">Tours</span><span class="mm-caret" aria-hidden="true"></span>
								</a>
								<button type="button" class="mm-toggle" aria-expanded="false" aria-controls="mm-tours" aria-label="Show tours"></button>
								<div class="mm-panel" id="mm-tours">
									<div class="mm-tours">
										<div>
											<p class="mm-label">Tour packages</p>
											<div class="mm-pack-grid">
												<?php foreach ($mmPackages as [$name, $href, $img, $desc]) : ?>
												<a class="mm-pack" href="<?php echo $href; ?>">
													<span class="mm-pack-img"><img src="<?php echo $img; ?>" alt="" loading="lazy" width="320" height="240"></span>
													<span class="mm-pack-text"><strong><?php echo $name; ?></strong><span><?php echo $desc; ?></span></span>
												</a>
												<?php endforeach; ?>
											</div>
										</div>
										<div class="mm-styles">
											<p class="mm-label">Travel styles</p>
											<?php foreach ($mmStyles as [$name, $href, $icon, $desc]) : ?>
											<a class="mm-style" href="<?php echo $href; ?>">
												<span class="mm-style-icon"><i class="fa-solid <?php echo $icon; ?>" aria-hidden="true"></i></span>
												<span><strong><?php echo $name; ?></strong><small><?php echo $desc; ?></small></span>
											</a>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="mm-foot">
										<span><i class="fa-solid fa-route" aria-hidden="true"></i> Every trip can be tailor-made around your dates, group and budget.</span>
										<span class="mm-foot-actions">
											<a href="international-packages.php" class="mm-foot-link">All packages <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
											<a href="contact.php" class="mm-btn">Plan my trip</a>
										</span>
									</div>
								</div>
							</li>
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="blog.php">
									<span class="inline-block">Blogs</span>
								</a>
							</li>
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="contact.php">
									<span class="inline-block">Contact</span>
								</a>
							</li>
						</ul>
						<div class="lg:hidden block max-lg:p-5 text-center mt-auto">
							<ul>
								<li class="inline-block mx-0.5">
									<a class="size-10 !leading-10 border border-black/10 text-center text-primary fab fa-facebook-f" target="_blank" href="https://www.facebook.com/luxitafrica/dexignzone"></a>
								</li>
								<li class="inline-block mx-0.5">
									<a class="size-10 !leading-10 border border-black/10 text-center text-primary fab fa-twitter" target="_blank" href="https://twitter.com/dexignzones"></a>
								</li>
								<li class="inline-block mx-0.5">
									<a class="size-10 !leading-10 border border-black/10 text-center text-primary fab fa-linkedin-in" target="_blank" href="https://www.linkedin.com/showcase/3686700/admin/"></a>
								</li>
								<li class="inline-block mx-0.5">
									<a class="size-10 !leading-10 border border-black/10 text-center text-primary fab fa-instagram" target="_blank" href="https://www.instagram.com/p/DIoeX4NuF4S//dexignzone/"></a>
								</li>
							</ul>
						</div>
					</div>
					<div class="flex lg:justify-end lg:items-center z-9 h-20 xl:pl-8 max-lg:ms-auto">
						<div class="flex items-center">
							<ul class="ml-5 flex items-center -mr-2.5">
								<li class="inline-block">
									<button type="button" aria-label="Open search" data-target="#searchOverlay1" aria-expanded="false" aria-controls="searchOverlay1" class="flex items-center justify-center size-14 px-4 quick-search cursor-pointer text-white">
										<i class="fa fa-search text-xl" aria-hidden="true"></i>
									</button>
								</li>
								<li class="inline-block" data-drawer="#offcanvas-right" data-drawer-placement="right">
									<button class="lg:mt-4.5 lg:mb-4 lg:ml-5 lg:size-11 bg-dark-600 relative cursor-pointer max-lg:order-1 max-md:ms-auto toggle-nav-btn" type="button" aria-label="Open information drawer" aria-expanded="false" aria-controls="offcanvas-right">
										<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-300 top-3.25 w-7 max-lg:hidden"></span>
										<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-0 top-5.5 w-7 max-lg:hidden"></span>
										<span class="block absolute left-2.5 h-0.5 rounded-px bg-white duration-300 top-8 w-7 max-lg:hidden"></span>
										<b class="lg:hidden uppercase fixed -rotate-90 -translate-y-1/2 -right-7.5 bg-primary px-5 rounded-t-2lg text-white tracking-[2px] top-1/2">Info</b>
									</button>
								</li>
							</ul>
						</div>
					</div>
					<div class="fixed -top-full left-0 size-full bg-body-bg z-999 flex items-center justify-center p-8 duration-500 xmenu-search" id="searchOverlay1">
						<form class="absolute top-1/2 left-1/2 -translate-1/2 w-[calc(100%_-_80px)] max-w-150 text-primary text-3xl font-light text-left outline-none p-1.5 duration-500 bg-paleaqua rounded-25xl" action="#">
							<div class="relative flex flex-wrap items-stretch w-full bg-white rounded-25xl overflow-hidden">
								<input name="search" value="" type="text" class="h-17.5 pr-3 pl-7.5 text-lg text-primary w-[1%] flex-1 outline-none duration-300 placeholder:text-primary focus:border-primary" placeholder="Search...">
								<span class="flex">
									<button type="button" aria-label="Submit search" class="px-2.5 outline-none size-17.5 bg-primary text-2xl text-white rounded-full flex-1 ml-2.5 duration-500 cursor-pointer"><i class="fa fa-search" aria-hidden="true"></i></button>
								</span>
							</div>
						</form>
						<button type="button" aria-label="Close search" class="absolute right-8 top-8 text-primary bg-citrusyellow text-base size-10 cursor-pointer rounded search-remove"><i class="fa fa-close" aria-hidden="true"></i></button>
					</div>
				</div>
			</div>
		</div>
	</header>
