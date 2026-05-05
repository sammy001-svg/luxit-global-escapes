<?php
include_once 'db.php';
// Fetch settings for agency name, etc.
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html><html lang="en"><head>
    <!-- Character Encoding -->
	<meta charset="UTF-8">

	<!-- Responsive Design -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Title -->
	<title><?php echo $settings['agency_name'] ?? 'Luxit Global Escapes'; ?> - Travel & Tour Agency</title>

    <meta name="title" content="Luxit Global Escapes - Travel & Tour Tailwind CSS Template">
    <meta name="description" content="Luxit Global Escapes is a responsive Travel & Tour Tailwind CSS template designed for travel agencies.">
    <meta name="keywords" content="travel, tour booking, Tailwind, holiday booking">
    <meta name="author" content="Luxit Africa">
    <meta name="robots" content="index, follow">

    <!-- FAVICONS ICON -->
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

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
				<a href="tel:<?php echo str_replace(' ', '', $settings['contact_phone'] ?? '+254737800900'); ?>" class="hover:text-white transition duration-300"><?php echo $settings['contact_phone'] ?? '+254 737 800 900'; ?></a>
			</div>
			<div class="flex items-center">
				<i class="fas fa-envelope text-secondary mr-2 text-sm"></i>
				<a href="mailto:<?php echo $settings['contact_email'] ?? 'info@luxitglobalescapes.com'; ?>" class="hover:text-white transition duration-300 lowercase"><?php echo $settings['contact_email'] ?? 'info@luxitglobalescapes.com'; ?></a>
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

							<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
							<a class="lg:py-7.5 py-2 2xxl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="destinations.php">
								<span class="inline-block">Destinations</span>
								<i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-black float-end"></i>
							</a>
							<ul class="lg:absolute bg-white lg:rounded-xxl block lg:left-0 max-lg:border-t max-lg:border-gray-200 w-full lg:w-55 lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0 max-lg:hidden sub-menu">
								<li class="group/second relative border-b border-black/5 sub-menu-down"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25 after:content-['\f054'] after:inline-block after:font-black after:font-['Font_Awesome_5_Free'] after:text-tiny after:float-right after:duration-500 max-lg:after:size-7 max-lg:after:leading-7 max-lg:after:text-center max-lg:after:text-xs max-lg:after:bg-black max-lg:after:text-black" href="destinations.php"><span>Africa</span></a>
									<ul class="bg-white lg:w-55 2xl:left-55 lg:left-55 lg:top-0 lg:absolute lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover/second:opacity-100 lg:group-hover/second:visible lg:group-hover/second:translate-y-0 max-lg:hidden max-lg:pl-5 sub-menu">
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="kenya.php"><span>Kenya</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="uganda.php"><span>Uganda</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="tanzania.php"><span>Tanzania</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="seychelles.php"><span>Seychelles</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="madagascar.php"><span>Madagascar</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="zambia.php"><span>Zambia</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="zimbabwe.php"><span>Zimbabwe</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="rwanda.php"><span>Rwanda</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="south-africa.php"><span>South Africa</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="namibia.php"><span>Namibia</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="botswana.php"><span>Botswana</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="morocco.php"><span>Morocco</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-black font-semibold py-3 lg:px-5 duration-500 hover:text-black hover:pl-6.25" href="egypt.php"><span>Egypt</span></a></li>
									</ul>
								</li>
								<li class="group/second relative border-b border-black/5 sub-menu-down"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25 after:content-['\f054'] after:inline-block after:font-black after:font-['Font_Awesome_5_Free'] after:text-tiny after:float-right after:duration-500 max-lg:after:size-7 max-lg:after:leading-7 max-lg:after:text-center max-lg:after:text-xs max-lg:after:bg-black max-lg:after:text-white" href="destinations.php"><span>Middle East</span></a>
									<ul class="bg-white lg:w-55 2xl:left-55 lg:left-55 lg:top-0 lg:absolute lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover/second:opacity-100 lg:group-hover/second:visible lg:group-hover/second:translate-y-0 max-lg:hidden max-lg:pl-5 sub-menu">
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="dubai.php"><span>Dubai</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="oman.php"><span>Oman</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="jordan.php"><span>Jordan</span></a></li>
									</ul>
								</li>
								<li class="group/second relative border-b border-black/5 sub-menu-down"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25 after:content-['\f054'] after:inline-block after:font-black after:font-['Font_Awesome_5_Free'] after:text-tiny after:float-right after:duration-500 max-lg:after:size-7 max-lg:after:leading-7 max-lg:after:text-center max-lg:after:text-xs max-lg:after:bg-black max-lg:after:text-white" href="destinations.php"><span>Asia</span></a>
									<ul class="bg-white lg:w-55 2xl:left-55 lg:left-55 lg:top-0 lg:absolute lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover/second:opacity-100 lg:group-hover/second:visible lg:group-hover/second:translate-y-0 max-lg:hidden max-lg:pl-5 sub-menu">
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="thailand.php"><span>Thailand</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="singapore.php"><span>Singapore</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="philippines.php"><span>Philippines</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="maldives.php"><span>Maldives</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="china.php"><span>China</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="malaysia.php"><span>Malaysia</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="india.php"><span>India</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="indonesia.php"><span>Indonesia</span></a></li>
									</ul>
								</li>
								<li class="group/second relative border-b border-black/5 sub-menu-down"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25 after:content-['\f054'] after:inline-block after:font-black after:font-['Font_Awesome_5_Free'] after:text-tiny after:float-right after:duration-500 max-lg:after:size-7 max-lg:after:leading-7 max-lg:after:text-center max-lg:after:text-xs max-lg:after:bg-black max-lg:after:text-white" href="destinations.php"><span>Europe</span></a>
									<ul class="bg-white lg:w-55 2xl:left-55 lg:left-55 lg:top-0 lg:absolute lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover/second:opacity-100 lg:group-hover/second:visible lg:group-hover/second:translate-y-0 max-lg:hidden max-lg:pl-5 sub-menu">
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="spain.php"><span>Spain</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="france.php"><span>France</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="italy.php"><span>Italy</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="greece.php"><span>Greece</span></a></li>
										<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="turkey.php"><span>Turkey</span></a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
							<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="javascript:void(0);">
								<span class="inline-block">Tours</span>
								<i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-white float-end"></i>
							</a>
						<ul class="lg:absolute bg-white lg:rounded-xxl block lg:left-0 w-full lg:w-55 lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0 max-lg:hidden sub-menu">
							<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="local-packages.php"><span>Local Packages</span></a></li>
							<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="international-packages.php"><span>International Packages</span></a></li>
							<li class="group/second relative border-b border-black/5 sub-menu-down"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25 after:content-['\f054'] after:inline-block after:font-black after:font-['Font_Awesome_5_Free'] after:text-tiny after:float-right after:duration-500 max-lg:after:size-7 max-lg:after:leading-7 max-lg:after:text-center max-lg:after:text-xs max-lg:after:bg-black max-lg:after:text-white" href="javascript:void(0);"><span>Special Packages</span></a>
								<ul class="bg-white lg:w-55 2xl:left-55 lg:left-55 lg:top-0 lg:absolute lg:opacity-0 lg:invisible lg:translate-y-10 z-10 mt-0 text-left duration-500 lg:group-hover/second:opacity-100 lg:group-hover/second:visible lg:group-hover/second:translate-y-0 max-lg:hidden max-lg:pl-5 sub-menu">
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="halal-travel.php"><span>Halal Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="kosher-travel.php"><span>Kosher Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="accessible-travel.php"><span>Accessible Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="mice-travel.php"><span>Mice Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="romantic-travel.php"><span>Romantic Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="wellness-travel.php"><span>Wellness Travel</span></a></li>
									<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="faith-travel.php"><span>Faith Travel</span></a></li>
								</ul>
							</li>
							<li class="relative border-b border-black/5"><a class="block relative text-sm text-primary font-semibold py-3 lg:px-5 duration-500 hover:text-primary hover:pl-6.25" href="safari-packages.php"><span>Safari Packages</span></a></li>
						</ul>
						</li>
						<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group">
							<a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium lg:text-white text-black hover:text-secondary" href="contact.php">
								<span class="inline-block">Contact</span>
							</a>
						</li>
					</ul>
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
