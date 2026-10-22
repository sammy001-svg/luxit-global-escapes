<?php
$page_title = 'Europe Destinations - Luxit Global Escapes';
$page_description = 'Explore our Europe destinations — from Spain\'s vibrant culture and France\'s art and cuisine to Italy\'s romance, Greece\'s ancient history, and Turkey\'s east-meets-west charm.';
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
                            <h2 class="lg:text-60 md:text-52 text-28 relative text-white text-center">Europe <span class="text-citrusyellow">Destinations</span></h2>
                            <div class="mt-4">
                                <ul class="inline-block">
                                    <li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal"><a href="index.php">Home</a></li>
                                    <li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal"><a href="destinations.php">Destinations</a></li>
                                    <li class="relative inline-block text-base font-semibold text-citrusyellow">Europe</li>
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
                            <h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Explore <span class="text-citrusyellow">Europe</span></h2>
                            <p class="text-base text-gray-500">5 iconic destinations rich in history, culture, and unforgettable landscapes</p>
                        </div>
                        <div class="grid grid-cols-12 gap-6 justify-center">

                            <!-- Spain -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic1.jpg" alt="Spain" class="w-full h-60 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="spain.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Spain</a>
                                        <span class="text-sm text-white/60">Culture &amp; Beaches</span>
                                    </div>
                                </div>
                            </div>

                            <!-- France -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic2.jpg" alt="France" class="w-full h-60 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="france.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">France</a>
                                        <span class="text-sm text-white/60">Art &amp; Cuisine</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Italy -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic3.jpg" alt="Italy" class="w-full h-60 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="italy.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Italy</a>
                                        <span class="text-sm text-white/60">History &amp; Romance</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Greece -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic4.jpg" alt="Greece" class="w-full h-60 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="greece.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Greece</a>
                                        <span class="text-sm text-white/60">Ancient History</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Turkey -->
                            <div class="xl:col-span-4 lg:col-span-4 md:col-span-6 col-span-12">
                                <div class="bg-primary rounded-2xl overflow-hidden border border-white/20 duration-500 hover:shadow-2xl hover:-translate-y-1 group">
                                    <div class="overflow-hidden">
                                        <img src="assets/images/destinations/style1/pic5.jpg" alt="Turkey" class="w-full h-60 object-cover duration-500 group-hover:scale-105" loading="lazy">
                                    </div>
                                    <div class="p-5 text-center">
                                        <a href="turkey.php" class="text-2xl font-semibold text-white group-hover:text-citrusyellow duration-500 block mb-1">Turkey</a>
                                        <span class="text-sm text-white/60">East Meets West</span>
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
