<!DOCTYPE html><html lang="en"><head>
    <!-- Character Encoding -->
	<meta charset="UTF-8">

	<!-- Responsive Design -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Title -->
	<title><?php echo isset($page_title) ? $page_title : "Luxit Global Escapes - Luxury Travel & Tour Agency"; ?></title>

    <meta name="title" content="Luxit Global Escapes - Travel & Tour Tailwind CSS Template | DexignZone">
    <meta name="description" content="Luxit Global Escapes is a responsive Travel & Tour Tailwind CSS template designed for travel agencies, tour operators, holiday planners, and booking websites.">
    <meta name="keywords" content="travel HTML template, tour booking template, Tailwind travel website, tourism Tailwind template, holiday booking, responsive travel design, tour operator website, Luxit Global Escapes template, modern travel design, travel agency web design">
    <meta name="author" content="DexignZone">
    <meta name="robots" content="index, follow">

    <!-- CANONICAL URL -->
    <link rel="canonical" href="https://eGNfOZkBzy5m.com/tailwind/demo/index.php">

    <!-- FAVICONS ICON -->
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="Luxit Global Escapes - Luxury Travel & Tour Agency">
    <meta property="og:description" content="Luxit Global Escapes is a responsive Travel & Tour Tailwind CSS template designed for travel agencies, tour operators, holiday planners, and booking websites.">
    <meta property="og:image" content="assets/images/seo/og_index.png">
    <meta property="og:url" content="index.php">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Luxit Global Escapes">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Luxit Global Escapes - Luxury Travel & Tour Agency">
    <meta name="twitter:description" content="Luxit Global Escapes is a responsive Travel & Tour Tailwind CSS template designed for travel agencies, tour operators, holiday planners, and booking websites.">
    <meta name="twitter:image" content="assets/images/seo/og_index.png">

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
	<style>
	/* ═══════════════════════════════════════
	   MEGA MENU — Desktop layout & hover
	═══════════════════════════════════════ */
	@media (min-width: 1024px) {
	  li.mm-parent { position: static !important; }
	  li.mm-parent > .mega-menu {
	    position: absolute;
	    top: 100%;
	    left: 0;
	    right: 0;
	    z-index: 99999;
	    padding-top: 8px;
	  }
	  .mm-panel {
	    background: #fff;
	    border-radius: 16px;
	    box-shadow: 0 20px 60px rgba(6,97,104,.13), 0 4px 20px rgba(0,0,0,.07);
	    border: 1px solid rgba(0,0,0,.07);
	    overflow: hidden;
	  }
	  .mm-topbar, .mm-botbar {
	    display: flex !important;
	    align-items: center;
	    justify-content: space-between;
	    padding: 11px 32px;
	  }
	  .mm-topbar {
	    background: rgba(6,97,104,.04);
	    border-bottom: 1px solid rgba(0,0,0,.06);
	  }
	  .mm-botbar {
	    background: #f9fafb;
	    border-top: 1px solid rgba(0,0,0,.06);
	  }
	  .mm-topbar-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.14em; }
	  .mm-topbar-link { font-size:12px; font-weight:600; color:#066168; display:flex; align-items:center; gap:4px; transition:gap .2s; }
	  .mm-topbar-link:hover { gap:8px; }
	  .mm-body { padding: 22px 8px; }
	  .mm-4col { display: grid; grid-template-columns: repeat(4, 1fr); }
	  .mm-3col { display: grid; grid-template-columns: 1fr 2fr; }
	  .mm-col { padding: 0 20px; }
	  .mm-col + .mm-col { border-left: 1px solid rgba(0,0,0,.07); }
	  .mm-col + .mm-col2 { border-left: 1px solid rgba(0,0,0,.07); }
	  .mm-col2 { padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 0 12px; }
	  .mm-col2 + .mm-col { border-left: 1px solid rgba(0,0,0,.07); }
	  .mm-col-head { display: flex !important; align-items: center; gap: 10px; margin-bottom: 10px; }
	  .mm-icon { width:30px; height:30px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0; }
	  .mm-col-title { font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:.14em; }
	  .mm-col-count { font-size:9.5px; color:#9ca3af; margin-top:1px; }
	  .mm-divider { display:block !important; width:18px; height:2px; border-radius:9999px; margin-bottom:12px; }
	  .mm-link { display:block; font-size:13.5px; font-weight:500; color:#374151; padding:5px 0; transition:color .15s, padding-left .15s; }
	  .mm-link:hover { color:#066168; padding-left:5px; }
	  .mm-plink { display:flex; align-items:flex-start; gap:12px; padding:7px 0; cursor:pointer; }
	  .mm-plink:hover .mm-picon { background:rgba(6,97,104,.12); }
	  .mm-plink:hover .mm-ptitle { color:#066168; }
	  .mm-picon { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; transition:background .15s; }
	  .mm-ptitle { font-size:13.5px; font-weight:600; color:#1f2937; transition:color .15s; display:block; line-height:1.3; }
	  .mm-pdesc { font-size:10.5px; color:#9ca3af; display:block; margin-top:2px; }
	  .mm-xlink { display:flex; align-items:center; gap:9px; padding:6px 0; font-size:13.5px; font-weight:500; color:#374151; transition:color .15s; }
	  .mm-xlink:hover { color:#066168; }
	  .mm-xicon { width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
	  .mm-btn { display:inline-flex; align-items:center; gap:7px; background:#066168; color:#fff !important; font-size:11.5px; font-weight:700; padding:7px 14px; border-radius:8px; transition:background .2s; }
	  .mm-btn:hover { background:#054f55; }
	  .mm-botbar-note { font-size:11.5px; color:#9ca3af; }
	}

	/* ═══════════════════════════════════════
	   MEGA MENU — Mobile overrides (simple list)
	═══════════════════════════════════════ */
	.mm-topbar, .mm-botbar, .mm-col-head, .mm-divider, .mm-pdesc { display: none; }
	.mm-moblabel { display:block; padding:9px 20px; font-size:10.5px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.1em; background:#f9fafb; border-bottom:1px solid rgba(0,0,0,.05); }
	@media (min-width: 1024px) {
	  .mm-moblabel { display: none; }
	  .mm-col-head { display: flex; }
	}
	@media (max-width: 1023px) {
	  .mm-col { padding: 0; }
	  .mm-col + .mm-col { border-left: none; }
	  .mm-col2 { padding: 0; display:block; }
	  .mm-link { padding:10px 20px; border-bottom:1px solid rgba(0,0,0,.05); }
	  .mm-plink { padding:10px 20px; border-bottom:1px solid rgba(0,0,0,.05); gap:0; }
	  .mm-picon { display:none; }
	  .mm-xlink { padding:10px 20px; border-bottom:1px solid rgba(0,0,0,.05); gap:0; }
	  .mm-xicon { display:none; }
	  .mm-body { padding:0; }
	  .mm-ptitle { font-size:14px; font-weight:500; color:#1f2937; }
	}
	</style>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Afacad:ital,wght@0,400..700;1,400..700&family=Figtree:ital,wght@0,300..900;1,300..900&family=Kaushan+Script&display=swap" rel="stylesheet">

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
	<div class="bg-white text-black py-3 block border-b border-black/5 relative" style="z-index: 1001;">
		<div class="container mx-auto px-8.75 flex flex-wrap justify-center items-center text-[12px] font-medium tracking-wide gap-x-48 gap-y-2">
			<div class="flex items-center">
				<i class="fas fa-map-marker-alt text-secondary mr-2 text-sm"></i>
				<span>Karen, Nairobi-Kenya</span>
			</div>
			<div class="flex items-center">
				<i class="fas fa-phone-alt text-secondary mr-2 text-sm"></i>
				<a href="tel:+254737800900" class="hover:text-white transition duration-300">+254 737 800 900</a>
			</div>
			<div class="flex items-center">
				<i class="fas fa-envelope text-secondary mr-2 text-sm"></i>
				<a href="mailto:info@luxitglobalescapes.com" class="hover:text-white transition duration-300 lowercase">info@luxitglobalescapes.com</a>
			</div>
		</div>
	</div>
	<header class="site-header sticky-header absolute lg:left-8.75 lg:right-8.75 lg:top-[44px] left-0 right-0 top-[44px] duration-500 z-999 [.site-header.is-fixed]:fixed [.site-header.is-fixed]:animate-header-scroll-animation [.site-header.is-fixed]:bg-primary [.site-header.is-fixed]:rounded-b-3xl [.site-header.is-fixed]:top-0">
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

							<!-- ══ DESTINATIONS MEGA MENU ══ -->
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group mm-parent">
								<a class="lg:py-7.5 py-2 2xxl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="destinations.php">
									<span class="inline-block">Destinations</span>
									<i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-black float-end"></i>
								</a>
								<div class="mega-menu max-lg:hidden lg:opacity-0 lg:invisible lg:translate-y-10 duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0">
									<div class="mm-panel">
										<!-- Top bar -->
										<div class="mm-topbar">
											<div style="display:flex;align-items:center;gap:8px;">
												<i class="fas fa-globe-africa" style="color:#066168;font-size:14px;"></i>
												<span class="mm-topbar-label">Explore World Destinations</span>
											</div>
											<a href="destinations.php" class="mm-topbar-link">
												All Destinations <i class="fas fa-long-arrow-alt-right" style="font-size:11px;"></i>
											</a>
										</div>
										<!-- Columns -->
										<div class="mm-body mm-4col">

											<!-- Africa -->
											<div class="mm-col">
												<div class="mm-col-head">
													<span class="mm-icon" style="background:#f0fdf4;border:1px solid #bbf7d0;">
														<i class="fas fa-leaf" style="color:#16a34a;"></i>
													</span>
													<div>
														<p class="mm-col-title" style="color:#15803d;">Africa</p>
														<p class="mm-col-count">13 destinations</p>
													</div>
												</div>
												<div class="mm-divider" style="background:#86efac;"></div>
												<span class="mm-moblabel">Africa</span>
												<a class="mm-link" href="kenya.php">Kenya</a>
												<a class="mm-link" href="uganda.php">Uganda</a>
												<a class="mm-link" href="tanzania.php">Tanzania</a>
												<a class="mm-link" href="seychelles.php">Seychelles</a>
												<a class="mm-link" href="madagascar.php">Madagascar</a>
												<a class="mm-link" href="zambia.php">Zambia</a>
												<a class="mm-link" href="zimbabwe.php">Zimbabwe</a>
												<a class="mm-link" href="rwanda.php">Rwanda</a>
												<a class="mm-link" href="south-africa.php">South Africa</a>
												<a class="mm-link" href="namibia.php">Namibia</a>
												<a class="mm-link" href="botswana.php">Botswana</a>
												<a class="mm-link" href="morocco.php">Morocco</a>
												<a class="mm-link" href="egypt.php">Egypt</a>
											</div>

											<!-- Middle East -->
											<div class="mm-col">
												<div class="mm-col-head">
													<span class="mm-icon" style="background:#fffbeb;border:1px solid #fde68a;">
														<i class="fas fa-moon" style="color:#d97706;"></i>
													</span>
													<div>
														<p class="mm-col-title" style="color:#b45309;">Middle East</p>
														<p class="mm-col-count">3 destinations</p>
													</div>
												</div>
												<div class="mm-divider" style="background:#fcd34d;"></div>
												<span class="mm-moblabel">Middle East</span>
												<a class="mm-link" href="dubai.php">Dubai</a>
												<a class="mm-link" href="oman.php">Oman</a>
												<a class="mm-link" href="jordan.php">Jordan</a>
											</div>

											<!-- Asia -->
											<div class="mm-col">
												<div class="mm-col-head">
													<span class="mm-icon" style="background:#eff6ff;border:1px solid #bfdbfe;">
														<i class="fas fa-torii-gate" style="color:#2563eb;"></i>
													</span>
													<div>
														<p class="mm-col-title" style="color:#1d4ed8;">Asia</p>
														<p class="mm-col-count">8 destinations</p>
													</div>
												</div>
												<div class="mm-divider" style="background:#93c5fd;"></div>
												<span class="mm-moblabel">Asia</span>
												<a class="mm-link" href="thailand.php">Thailand</a>
												<a class="mm-link" href="singapore.php">Singapore</a>
												<a class="mm-link" href="philippines.php">Philippines</a>
												<a class="mm-link" href="maldives.php">Maldives</a>
												<a class="mm-link" href="china.php">China</a>
												<a class="mm-link" href="malaysia.php">Malaysia</a>
												<a class="mm-link" href="india.php">India</a>
												<a class="mm-link" href="indonesia.php">Indonesia</a>
											</div>

											<!-- Europe -->
											<div class="mm-col">
												<div class="mm-col-head">
													<span class="mm-icon" style="background:#faf5ff;border:1px solid #e9d5ff;">
														<i class="fas fa-landmark" style="color:#7c3aed;"></i>
													</span>
													<div>
														<p class="mm-col-title" style="color:#6d28d9;">Europe</p>
														<p class="mm-col-count">5 destinations</p>
													</div>
												</div>
												<div class="mm-divider" style="background:#c4b5fd;"></div>
												<span class="mm-moblabel">Europe</span>
												<a class="mm-link" href="spain.php">Spain</a>
												<a class="mm-link" href="france.php">France</a>
												<a class="mm-link" href="italy.php">Italy</a>
												<a class="mm-link" href="greece.php">Greece</a>
												<a class="mm-link" href="turkey.php">Turkey</a>
											</div>

										</div>
										<!-- Bottom bar -->
										<div class="mm-botbar">
											<span class="mm-botbar-note"><i class="fas fa-shield-alt" style="color:#066168;margin-right:6px;"></i>IATA accredited &amp; fully licensed agency</span>
											<a href="contact.php" class="mm-btn">
												<i class="fas fa-headset" style="font-size:11px;"></i> Talk to an Expert
											</a>
										</div>
									</div>
								</div>
							</li>

							<!-- ══ TOURS MEGA MENU ══ -->
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group mm-parent">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="javascript:void(0);">
									<span class="inline-block">Tours</span>
									<i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-white float-end"></i>
								</a>
								<div class="mega-menu max-lg:hidden lg:opacity-0 lg:invisible lg:translate-y-10 duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0">
									<div class="mm-panel">
										<!-- Top bar -->
										<div class="mm-topbar">
											<div style="display:flex;align-items:center;gap:8px;">
												<i class="fas fa-route" style="color:#066168;font-size:14px;"></i>
												<span class="mm-topbar-label">Our Tour Packages</span>
											</div>
											<a href="local-packages.php" class="mm-topbar-link">
												Browse all packages <i class="fas fa-long-arrow-alt-right" style="font-size:11px;"></i>
											</a>
										</div>
										<!-- Columns -->
										<div class="mm-body mm-3col">

											<!-- Package Types column -->
											<div class="mm-col">
												<div class="mm-col-head">
													<span class="mm-icon" style="background:rgba(6,97,104,.08);border:1px solid rgba(6,97,104,.2);">
														<i class="fas fa-suitcase-rolling" style="color:#066168;"></i>
													</span>
													<p class="mm-col-title" style="color:#066168;">Package Types</p>
												</div>
												<div class="mm-divider" style="background:rgba(6,97,104,.3);"></div>
												<span class="mm-moblabel">Package Types</span>

												<a href="local-packages.php" class="mm-plink">
													<span class="mm-picon" style="background:rgba(6,97,104,.06);">
														<i class="fas fa-map-marker-alt" style="color:#066168;"></i>
													</span>
													<span>
														<span class="mm-ptitle">Local Packages</span>
														<span class="mm-pdesc">East Africa &amp; beyond</span>
													</span>
												</a>
												<a href="international-packages.php" class="mm-plink">
													<span class="mm-picon" style="background:#eff6ff;">
														<i class="fas fa-plane" style="color:#2563eb;"></i>
													</span>
													<span>
														<span class="mm-ptitle">International Packages</span>
														<span class="mm-pdesc">Worldwide destinations</span>
													</span>
												</a>
												<a href="safari-packages.php" class="mm-plink">
													<span class="mm-picon" style="background:#fffbeb;">
														<i class="fas fa-paw" style="color:#d97706;"></i>
													</span>
													<span>
														<span class="mm-ptitle">Safari Packages</span>
														<span class="mm-pdesc">Wildlife &amp; game drives</span>
													</span>
												</a>
											</div>

											<!-- Special Experiences (2-col span) -->
											<div class="mm-col2">
												<div style="grid-column:span 2;" class="mm-col-head">
													<span class="mm-icon" style="background:#fefce8;border:1px solid #fef08a;">
														<i class="fas fa-star" style="color:#ca8a04;"></i>
													</span>
													<p class="mm-col-title" style="color:#92400e;">Special Experiences</p>
												</div>
												<div class="mm-divider" style="grid-column:span 2;background:#fcd34d;"></div>
												<span class="mm-moblabel" style="grid-column:span 2;">Special Packages</span>

												<!-- Left sub-column -->
												<div>
													<a href="halal-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#f0fdf4;"><span>🕌</span></span>
														Halal Travel
													</a>
													<a href="kosher-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#eff6ff;"><span>✡️</span></span>
														Kosher Travel
													</a>
													<a href="accessible-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#f0f9ff;"><span>♿</span></span>
														Accessible Travel
													</a>
													<a href="mice-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#f9fafb;"><span>🤝</span></span>
														MICE Travel
													</a>
												</div>
												<!-- Right sub-column -->
												<div>
													<a href="romantic-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#fff1f2;"><span>💑</span></span>
														Romantic Travel
													</a>
													<a href="wellness-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#f0fdf4;"><span>🌿</span></span>
														Wellness Travel
													</a>
													<a href="faith-travel.php" class="mm-xlink">
														<span class="mm-xicon" style="background:#fffbeb;"><span>✝️</span></span>
														Faith Travel
													</a>
												</div>
											</div>

										</div>
										<!-- Bottom bar -->
										<div class="mm-botbar">
											<span class="mm-botbar-note"><i class="fas fa-award" style="color:#066168;margin-right:6px;"></i>Award-winning tours since 2010</span>
											<a href="contact.php" class="mm-btn">
												<i class="fas fa-calendar-check" style="font-size:11px;"></i> Book a Tour
											</a>
										</div>
									</div>
								</div>
							</li>
							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
								<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="javascript:void(0);">
									<span class="inline-block">Blogs</span>
									<i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-white float-end"></i>
								</a>
								<ul class="lg:absolute bg-white lg:rounded-xxl block lg:left-0 w-full lg:w-55 lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0 max-lg:hidden sub-menu">
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="blog-grid.php"><span>Blog Grid</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="blog-grid-left.php"><span>Blog Grid Left</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="blog-list-left.php"><span>Blog List</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="blog-detail.php"><span>Blog Detail</span></a></li>
								</ul>
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
