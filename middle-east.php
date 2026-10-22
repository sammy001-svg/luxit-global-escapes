<?php
$page_title = 'Middle East Destinations - Luxit Global Escapes';
$page_description = 'Explore our Middle East destinations — from Dubai\'s modern luxury and Oman\'s Arabian heritage to Jordan\'s ancient wonders.';
$header_variant = 'solid';
include 'header.php';
?>
	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">

                <!-- INNER PAGE BANNER -->
                <div class="relative bg-cover bg-center w-full bg-[url(assets/destination/images/background/inr-banner.jpg)] overflow-hidden">
                    <div class="opacity-60 absolute left-0 top-0 size-full bg-primary"></div>
                    <div class="flex w-full lg:h-120 md:h-100 h-80 pb-10 items-baseline mx-auto">
                        <div class="relative md:mt-52 mt-36 flex items-center justify-center w-full flex-col z-5">
                            <h2 class="lg:text-60 md:text-52 text-28 relative text-white text-center">Middle East <span class="text-citrusyellow">Destinations</span></h2>
                            <div class="mt-4">
                                <ul class="inline-block">
                                    <li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal"><a href="index.php">Home</a></li>
                                    <li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal"><a href="destinations.php">Destinations</a></li>
                                    <li class="relative inline-block text-base font-semibold text-citrusyellow">Middle East</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="h-50 w-full absolute top-50 left-0 z-1">
                        <div class="inline-block whitespace-nowrap animate-moveCloud">
                            <img src="assets/destination/images/inr-banner-cloud.png" alt="" class="h-47.5">
                        </div>
                    </div>
                    <div class="absolute right-11.25 bottom-16.25 animate-slide-top2"><img src="assets/destination/images/hotballon-Left.png" alt="" class="md:w-21 w-10" width="84" height="121"></div>
                </div>
                <!-- INNER PAGE BANNER END -->

                <!-- DESTINATIONS GRID -->
                <div class="relative overflow-hidden z-1 bg-white xl:pt-24 pt-12 xl:pb-24 pb-12">
                    <div class="container">
                        <div class="mb-12 text-center max-w-2xl mx-auto">
                            <h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Explore the <span class="text-citrusyellow">Middle East</span></h2>
                            <p class="text-base text-gray-500">3 captivating destinations where ancient heritage meets modern luxury</p>
                        </div>
                        <div class="grid grid-cols-12 gap-6 justify-center">

                            <!-- Dubai -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic1.jpg" alt="Dubai" class="w-full h-64 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="dubai.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Dubai</a>
                                        <span class="text-sm text-white/60">Modern Luxury &amp; Desert</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Oman -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic2.jpg" alt="Oman" class="w-full h-64 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="oman.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Oman</a>
                                        <span class="text-sm text-white/60">Arabian Heritage</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Jordan -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic3.jpg" alt="Jordan" class="w-full h-64 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="jordan.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Jordan</a>
                                        <span class="text-sm text-white/60">Petra &amp; Desert Wonders</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="text-center mt-12">
                            <a href="destinations.php" class="site-button btn-sm outline">
                                <i class="fas fa-globe mr-2"></i> View All Destinations
                            </a>
                        </div>
                    </div>
                </div>
                <!-- DESTINATIONS GRID END -->

			</div>
			<!-- CONTENT END -->

<?php include 'footer.php'; ?>
