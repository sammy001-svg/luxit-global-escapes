<?php
$page_title = 'Contact Luxit Global Escapes - Plan Your Luxury Trip';
$page_description = 'Get in touch with Luxit Global Escapes. Our travel experts are ready to help you plan your next luxury adventure. Reach out to us for personalized travel consultation.';
$header_variant = 'solid';
include 'header.php';
?>
	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">
				<div class="relative bg-cover bg-center w-full bg-white overflow-hidden" style="background-image: url('assets/Contact/images/background/inr-banner.jpg');">
						<div class="bg-black/60 absolute left-0 top-0 size-full"></div>
						<div class="inr-banner-height flex w-full lg:h-160 md:h-135 h-100 pb-10 items-baseline mx-auto">
							<div class="relative md:mt-60 mt-45 flex items-center justify-center w-full flex-col z-5">
								<div>
									<h2 class="lg:text-60 md:text-52 text-28 relative text-white">Contact Us</h2>
								</div>
								<!-- BREADCRUMB ROW -->                            
								<div>
									<ul class="inline-block">
										<li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal">
											<a href="index.php" class="text-white">Home</a></li>
										<li class="relative inline-block text-base font-semibold text-white">Contact Us</li>
									</ul>
								</div>
							</div>
							<!-- BREADCRUMB ROW END -->                        
						</div>
						<div class="h-50 w-full absolute top-50 left-0 z-1">            
							<div class="inline-block whitespace-nowrap animate-moveCloud">
								<img src="assets/Contact/images/inr-banner-cloud.png" alt="Image" class="h-47.5">
							</div>
						</div>
						<div class="inr-plane-wrap absolute w-1/2 right-0 top-0 bottom-0 z-1">
							<div class="mt-60 animate-slide-right"><img src="assets/Contact/images/airplane.png" alt="Image" class="animate-slide-top-fast" width="378" height="146"></div>
						</div>
						<div class="absolute right-11.25 bottom-16.25 animate-slide-top2"><img src="assets/Contact/images/hotballon-Left.png" alt="Image" class="md:w-21 w-10" width="84" height="121"></div>
						<div class="inr-balloon-right absolute md:-right-15 -right-10 top-41.25 animate-slide-top"><img src="assets/Contact/images/hotballon-right.png" alt="Image" class="md:w-37.5 w-20" width="230" height="333"></div>
					</div>
				<!-- INNER PAGE BANNER END -->
				
				<!-- CONTACT SECTION START -->
				<div class="xl:pt-30 pt-12.5 bg-white">
					<div class="container">
						<div class="grid grid-cols-12 gap-7.5 items-center">
							<div class="lg:col-span-6 col-span-12">
								<div class="relative rounded-6xl overflow-hidden shadow-2xl">
									<img src="assets/Contact/images/Image-cont.png" alt="Contact Image" class="w-full h-auto object-cover">
								</div>
							</div>
							<div class="lg:col-span-6 col-span-12">
								<div class="cf-panel bg-paleaqua sm:p-16 p-7 rounded-6xl flex flex-col justify-center gap-y-8 sm:gap-y-12">
									<div>
										<h2 class="xl:text-64 md:text-52 sm:text-4xl text-3xl mb-4 font-bold">Get In <span class="text-citrusyellow">Touch</span></h2>
										<p class="sm:text-xl text-base text-primary/70 max-w-lg leading-relaxed">We are here to help you. Send us a message and we will get back to you as soon as possible.</p>
									</div>
									<form class="dzForm" action="assets/script/contact.php">
										<div class="dzFormMsg"></div>
										<div class="grid grid-cols-12 xl:gap-y-8 gap-x-6 gap-y-4 sm:gap-y-6">
											<div class="sm:col-span-6 col-span-12">
												<input name="dzName" required type="text" class="cf-input w-full sm:h-20 h-14 sm:px-8 px-5 rounded-3xl bg-white border-2 border-primary/5 outline-none focus:border-citrusyellow duration-300 placeholder:text-primary/40 sm:text-xl text-base shadow-sm" placeholder="Your Name">
											</div>
											<div class="sm:col-span-6 col-span-12">
												<input name="dzEmail" required type="email" class="cf-input w-full sm:h-20 h-14 sm:px-8 px-5 rounded-3xl bg-white border-2 border-primary/5 outline-none focus:border-citrusyellow duration-300 placeholder:text-primary/40 sm:text-xl text-base shadow-sm" placeholder="Email Address">
											</div>
											<div class="col-span-12">
												<input name="dzPhoneNumber" required type="tel" class="cf-input w-full sm:h-20 h-14 sm:px-8 px-5 rounded-3xl bg-white border-2 border-primary/5 outline-none focus:border-citrusyellow duration-300 placeholder:text-primary/40 sm:text-xl text-base shadow-sm" placeholder="Phone Number">
											</div>
											<div class="col-span-12">
												<textarea name="dzMessage" required class="cf-textarea w-full sm:h-52 h-36 sm:p-8 p-4 rounded-3xl bg-white border-2 border-primary/5 outline-none focus:border-citrusyellow duration-300 placeholder:text-primary/40 sm:text-xl text-base shadow-sm" placeholder="Your Message"></textarea>
											</div>
											<div class="col-span-12 mt-2">
												<button type="submit" name="submit" class="cf-submit site-button butn-bg-shape w-full sm:h-20 h-14 sm:text-2xl text-lg font-bold shadow-lg justify-center flex">Send Message</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- MAP SECTION START -->
				<div class="xl:pt-30 pt-12.5 md:pb-30 pb-12.5">
					<div class="container">
						<div class="cf-map relative rounded-6xl overflow-hidden shadow-xl border-8 border-paleaqua" style="height: clamp(280px, 55vw, 750px);">
							<iframe 
								width="100%" 
								height="100%" 
								id="gmap_canvas" 
								src="https://maps.google.com/maps?q=Tana%20house,%20karen,%20nairobi,%20kenya&t=&z=15&ie=UTF8&iwloc=&output=embed" 
								frameborder="0" 
								scrolling="no" 
								marginheight="0" 
								marginwidth="0">
							</iframe>
						</div>
					</div>
				</div>
				<!-- MAP SECTION END -->
				<!-- CONTACT SECTION END -->
				
				<!--POPULAR DESTINATION SECTION START-->
				<div class="relative overflow-hidden md:pb-22.5 pb-10 md:pt-30 pt-17.5 bg-contain bg-[bottom_center] bg-repeat-x bg-[url(assets/about/images/background/Cloud-bg.png)]">
					<div class="container">
						<!-- TITLE START-->
						<div class="text-center max-w-150 mx-auto md:mb-15 mb-7.5">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5"><span class="text-citrusyellow">Popular </span>Destination</h2>
							<p class="text-base">Destinations worth exploring! Here are a few popular spots</p>
							<div class="-mt-7">
								<img src="assets/about/images/background/Title-Separator.png" alt="Image" class="w-117.5 inline-block" width="470" height="70">
							</div>
						</div>
						<!-- TITLE END-->
						<div>
							<div class="swiper reviewtwo-slider !relative !z-1 !-mt-7.5 xl:!pb-29 !pb-22.5">
								<div class="swiper-wrapper pt-7.5">
									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic1.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Paris</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic2.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Maldives</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic3.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Hong Kong</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic4.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Thailand</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic5.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Bangkok</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic6.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Tokyo</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic7.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">Spain</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/about/images/destinations/style1/pic8.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-black bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-secondary group-hover:bg-primary">California</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/about/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>
								</div>
								<div class="swiper-button-next"></div>
								<div class="swiper-button-prev"></div>
							</div>
						</div> 
					</div>
					<div class="absolute -left-28.75 top-2/5 w-57.5 opacity-50 animate-slide-top2"><img src="assets/about/images/hotballon-Left.png" alt="image" width="233" height="333"></div>
					<div class="absolute -right-13.75 top-2/5 w-27.5 animate-slide-top"><img src="assets/about/images/hotballon-right.png" alt="image" width="110" height="166"></div>
				</div>
				<!--POPULAR DESTINATION SECTION END-->

			</div>
			<!-- CONTENT END -->

<?php include 'footer.php'; ?>
