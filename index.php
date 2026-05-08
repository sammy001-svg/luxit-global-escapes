<?php 
$page_title = "Luxit Global Escapes - Travel & Tour | Home";
include 'header.php'; 
?>
	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">
				<!-- Hero Carousel Start -->
				<style>
				/* ── Hero Carousel ── */
				.hero-carousel-wrap{position:relative;height:100vh;min-height:600px;max-height:960px;overflow:hidden;}
				.hero-carousel-wrap .trv-header-carousel{height:100%;}

				/* Slide */
				.hero-slide{position:relative;height:100%;}
				.hero-slide-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
				.hero-slide-overlay{position:absolute;inset:0;background:linear-gradient(105deg,rgba(4,28,22,.88) 0%,rgba(4,28,22,.55) 50%,rgba(4,28,22,.08) 100%);}

				/* Content */
				.hero-slide-content{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;text-align:center;padding:0 6% 130px;}

				/* Animated elements — start hidden, reveal on active slide */
				.hero-eyebrow,.hero-title,.hero-desc,.hero-ctas{opacity:0;transform:translateY(32px);transition:opacity .75s ease,transform .75s ease;}
				.swiper-slide-active .hero-eyebrow{opacity:1;transform:translateY(0);transition-delay:0ms;}
				.swiper-slide-active .hero-title  {opacity:1;transform:translateY(0);transition-delay:190ms;}
				.swiper-slide-active .hero-desc   {opacity:1;transform:translateY(0);transition-delay:370ms;}
				.swiper-slide-active .hero-ctas   {opacity:1;transform:translateY(0);transition-delay:540ms;}

				.hero-eyebrow{display:inline-flex;align-items:center;gap:10px;color:#FFD214;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin-bottom:18px;}
				.hero-eyebrow::before{content:'';display:block;width:30px;height:2px;background:#FFD214;flex-shrink:0;}

				.hero-title{color:#fff;line-height:1.08;font-size:clamp(2.2rem,5.2vw,5rem);font-weight:800;margin-bottom:22px;font-family:var(--font-display,Georgia,serif);}
				.hero-title em{color:#FFD214;font-style:normal;}

				.hero-desc{color:rgba(255,255,255,.72);font-size:clamp(.92rem,1.4vw,1.1rem);max-width:560px;line-height:1.72;margin:0 auto 36px;}

				.hero-ctas{display:flex;gap:14px;flex-wrap:wrap;align-items:center;justify-content:center;}
				.hero-cta-ghost{display:inline-flex;align-items:center;gap:9px;color:#fff;border:1px solid rgba(255,255,255,.45);border-radius:50px;padding:12px 26px;font-size:.86rem;font-weight:600;letter-spacing:.04em;transition:background .3s,border-color .3s;}
				.hero-cta-ghost:hover{background:#006A72;border-color:#006A72;color:#fff;}
				.hero-cta-ghost i{font-size:.75rem;transition:transform .3s;}
				.hero-cta-ghost:hover i{transform:translateX(4px);}

				/* Nav Bar */
				.hero-nav-bar{position:absolute;bottom:0;left:0;right:0;z-index:20;display:flex;align-items:center;justify-content:space-between;padding:20px 6% 26px;background:linear-gradient(0deg,rgba(4,28,22,.78) 0%,transparent 100%);}

				/* Progress bar — pinned to top of nav bar */
				.hero-progress-wrap{position:absolute;top:0;left:0;right:0;height:2px;background:rgba(255,255,255,.15);}
				.hero-progress-bar{height:100%;width:0%;background:#FFD214;transition:width 5s linear;}

				/* Counter */
				.hero-counter{color:rgba(255,255,255,.55);font-size:.76rem;letter-spacing:.12em;font-weight:600;min-width:60px;font-variant-numeric:tabular-nums;}
				.hero-counter strong{color:#fff;font-size:.92rem;}

				/* Arrows */
				.hero-arrows{display:flex;gap:10px;}
				.hero-arrows button{width:44px;height:44px;border-radius:50%;border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.07);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .3s,border-color .3s;font-size:.8rem;}
				.hero-arrows button:hover{background:#006A72;border-color:#006A72;}

				/* Thumbnails */
				.hero-thumbs{display:flex;gap:8px;}
				.hero-thumb{width:76px;height:48px;border-radius:8px;overflow:hidden;cursor:pointer;opacity:.4;border:2px solid transparent;transition:opacity .35s,border-color .35s,transform .35s;}
				.hero-thumb img{width:100%;height:100%;object-fit:cover;}
				.hero-thumb.active{opacity:1;border-color:#FFD214;transform:scale(1.06);}
				.hero-thumb:hover:not(.active){opacity:.7;}

				/* Scroll indicator */
				.hero-scroll-indicator{position:absolute;right:28px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;align-items:center;gap:12px;z-index:20;}
				.hero-scroll-indicator span{color:rgba(255,255,255,.45);font-size:.58rem;letter-spacing:.2em;text-transform:uppercase;writing-mode:vertical-rl;}
				.hero-scroll-line{width:1px;height:60px;background:rgba(255,255,255,.18);position:relative;overflow:hidden;}
				.hero-scroll-line::after{content:'';position:absolute;top:-60px;left:0;width:100%;height:60px;background:#FFD214;animation:heroScrollDown 2s ease-in-out infinite;}
				@keyframes heroScrollDown{0%{top:-60px}100%{top:60px}}

				/* Responsive */
				@media(max-width:1023px){.hero-scroll-indicator{display:none;}.hero-slide-content{padding:0 5% 110px;}}
				@media(max-width:640px){.hero-thumbs{display:none;}.hero-counter{display:none;}.hero-slide-content{padding:0 5% 90px;}.hero-title{font-size:clamp(1.9rem,8vw,2.6rem);}}
				</style>

				<div class="hero-carousel-wrap">
					<div class="trv-header-carousel swiper">
						<div class="swiper-wrapper">

							<!-- Slide 1: Kenya Safari -->
							<div class="swiper-slide hero-slide">
								<img src="assets/images/header-carousel/safari.png" alt="Kenya Safari" class="hero-slide-img">
								<div class="hero-slide-overlay"></div>
								<div class="hero-slide-content">
									<div>
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
									<div>
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
									<div>
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
									<div>
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
				
				<!-- SEARCH BAR START-->
				<div class="bg-lightturquoise xl:pt-17.5 pt-12.5 px-5">
					<div class="max-w-250 mx-auto lg:h-25 p-1.75 bg-paleaqua lg:rounded-25xl rounded-2xl">
						<div class="bg-white lg:rounded-25xl rounded-2xl sm:pt-3 sm:pr-3.25 sm:pb-2.25 sm:pl-10 p-5 h-full">
							<form>
								<div class="flex justify-between items-center max-lg:flex-wrap max-sm:flex-col">
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="custom-select style-1" data-label="Color">
											<label class="pb-1.25 flex items-center">
												<i class="inline-block mr-5">
													<img src="assets/images/search-icon/icon1.png" alt="Image" class="h-5 w-full">
												</i>Location
											</label>
											<select class="dynamic-select" id="sortingSelect">
												<option value="Zealand" selected="">New Zealand</option>
												<option value="Paris">Paris</option>
												<option value="Bali">Bali</option>
												<option value="Indonesia">Indonesia</option>
											</select>
										</div>
									</div>
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="custom-select style-1">
											<label class="pb-1.25 flex items-center">
												<i class="inline-block mr-5"><img src="assets/images/search-icon/icon2.png" alt="Image" class="h-5 w-full"></i>
												Activity Type
											</label>
											<select class="dynamic-select" aria-label="Default select example">
												<option selected="">Adventure</option>
												<option value="1">Beyond the Edge</option>
												<option value="2">Whispering Peaks</option>
												<option value="3">Wave Riders</option>
											</select>
										</div>
									</div>
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="">
											<label class="pb-1.25 flex items-center"><i class="inline-block mr-5"><img src="assets/images/search-icon/icon3.png" class="h-5 w-full" alt="Image"></i>Date</label>
											<div class="relative">
												<input class="outline-none h-8.5 p-0 font-title font-bold text-xl text-primary bg-transparent placeholder:!text-primary flatpickr1" placeholder="Date">
												<span class="absolute right-3.75 bottom-0 text-input text-lg pointer-events-none">
													<i class="fa fa-solid fa-calendar-days"></i>
												</span>
											</div>
										</div>
									</div>
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="">
											<label for="travelerCount" class="pb-1.25 flex items-center"><i class="inline-block mr-5"><img src="assets/images/search-icon/icon4.png" class="h-5 w-full" alt="Image"></i>Traveler</label>
											<div class="input-group">
												<span class="flex gap-2.5 w-full">
													<input id="travelerCount" type="number" step="1" value="1" name="quantity" class="touchspin h-6 lg:w-17.5 w-full leading-6.75 text-primary font-bold outline-none font-title text-xl">
													<button type="button" aria-label="Decrease traveler count" value="-" data-field="quantity" class="button-minus cursor-pointer size-6 leading-6.75 text-center text-base text-input"><i class="las la-minus text-lg" aria-hidden="true"></i></button>
													<button type="button" aria-label="Increase traveler count" value="+" data-field="quantity" class="button-plus cursor-pointer size-6 leading-6.75 text-center text-base text-input"><i class="las la-plus text-lg" aria-hidden="true"></i></button>
												</span>
											</div>
										</div>
									</div>
									<div class="lg:w-auto w-full">
										<div class="trv-search-st1-search-btn">
											<button aria-label="Search" class="text-28 text-white rounded-full size-15 bg-primary max-lg:!w-full duration-500 cursor-pointer"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- SEARCH BAR END--> 
				
				<!--EXPLORE POPULAR TOUR START-->
				<div class="bg-lightturquoise sm:mx-15 xl:pb-26.5 pb-5 xl:pt-30 pt-12.5">
					<div class="container-fluid">
						<!-- TITLE START-->
						<div class="text-center max-w-150 mx-auto md:mb-15 mb-7.5">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Explore Popular<span class="text-citrusyellow"> Tours!</span></h2>
							<p class="text-base">Destinations worth exploring! Here are a few popular spots</p>
							<div class="-mt-7">
								<img src="assets/images/background/Title-Separator.png" alt="Image" class="w-117.5 inline-block" width="470" height="70" loading="lazy">
							</div>
						</div>
						<!-- TITLE END-->
						<div>
							<div class="swiper trv-tours-st1 xl:!pb-29 !pb-22.5">
								<div class="swiper-wrapper">
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic1.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span class="block">8 days , 3 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															Bali, Indonesia
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$59</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">Nusa Penida is a stunning island located just southeast of Bali</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic2.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span class="block">4 days , 2 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															South Korea
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$75</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">Deogyusan  mountain. Its highest peak is 1,614 m. above sea level</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic3.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span class="block">6 days , 3 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															Tokyo City Japan
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$99</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">The bridge offers panoramic views of Tokyo Tower, the skyline.</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic4.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span class="block">8 days , 3 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															Plateau in Slovenia
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$149</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">Nusa Penida is a stunning island located just southeast of Bali</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic5.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span>4 days , 2 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															Switzerland Tour Package
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$129</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">Deogyusan  mountain. Its highest peak is 1,614 m. above sea level</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php"><img src="assets/images/tour/style1/pic6.jpg" alt="Image" class="xl:h-105 h-80 w-full object-cover object-center" width="309" height="500" loading="lazy"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex itmes-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span class="block">6 days , 3 Nights</span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur duration-500">
													<h3 class="2xl:text-28 text-2xl font-medium">
														<a href="tour-detail.php" class="text-white">
														   <i class="fa-solid fa-location-dot"></i>
															Tokyo City Japan
														</a>
													</h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-[0px_18px_18px_rgba(0,106,114,0.15)]">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28/[1.3] font-black block">$79</span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl/[1.3] font-title font-medium">
														<a href="tour-detail.php" class="text-primary hover:text-citrusyellow duration-500">The bridge offers panoramic views of Tokyo Tower, the skyline.</a>
													</div>
												</div>
												<div class="flex itmes-center justify-between">
													<div class="trv-book">
														<a href="#" class="site-button outline quick-book-btn">Book Now</a>
													</div>
													<div>
														<span>(4.8 Review)</span>
														<div class="text-citrusyellow">
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
															<i class="fa-solid fa-star"></i>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="swiper-button-next"></div>
								<div class="swiper-button-prev"></div>
							</div>
						</div> 
					</div>
				</div>
				<!--EXPLORE POPULAR TOUR END-->
				
				<!--WE RECOMMEND SECTION START-->
				<div class="bg-paleaqua lg:pt-30">
					<div class="container">
						<div class="grid grid-cols-12">
							<div class="xl:col-span-5 lg:col-span-7 col-span-12 px-3.75 max-lg:mb-40">
								<div class="2xl:mb-30 mb-10 relative">
									<!-- TITLE START-->
									<div class="text-left 2xl:mb-15 mb-10">
										<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">We <span class="text-citrusyellow">Recommend </span>Beautiful Destinations Every Month</h2>
										<p class="2xl:mb-12.5 mb-7 sm:pr-8.75 text-base">Luxit Global Escapes is a multi-award-winning strategy and content creation
											agency that specializes in travel marketing. They have one of the
											world's largest and most influential online travel communities,
											helping brands and tourism.
										</p>
									</div>
									<!-- TITLE END-->
									<div class="mb-5 flex max-sm:flex-wrap">
										<div class="sm:w-35 w-full sm:h-51 h-40 p-2.25 bg-white rounded-3xl flex min-w-35 sm:mr-7.5 max-sm:mb-10">
											<div class="bg-primary shadow-[0px_4px_4px_rgba(0,0,0,0.25)] p-2.5 rounded-2xxl text-center flex flex-col items-center justify-center w-full">
												<span class="text-38 text-citrusyellow font-black block">24/7</span>
												<span class="text-white text-2xl font-title font-medium block">Guide Support</span>
											</div> 
										</div>
										<div>
											<div class="flex mb-7.5 trv-icon-bx-st1">
												<div class="bg-no-repeat size-21.25 min-w-21.25 bg-cover mr-7.5 flex items-center justify-center bg-[url(../images/trv-icon/Icon-Bg.png)] trv-icon-bx-media">
													<span><img src="assets/images/trv-icon/travel-guide.png" alt="travel-guide" width="48" height="49" loading="lazy" class="max-w-12"></span>
												</div>
												<div>
													<h2 class="mb-3 font-medium text-2xl">Trusted travel guide</h2>
													<p>Provides reliable information to help travelers plan their trips efficiently and safely.</p>   
												</div>
											</div>
											<div class="flex mb-7.5 trv-icon-bx-st1">
												<div class="bg-no-repeat size-21.25 min-w-21.25 bg-cover mr-7.5 flex items-center justify-center bg-[url(../images/trv-icon/Icon-Bg.png)] trv-icon-bx-media">
													<span><img src="assets/images/trv-icon/mission-icon.png" alt="mission-icon" width="48" height="49" loading="lazy" class="max-w-12"></span>
												</div>
												<div>
													<h2 class="mb-3 font-medium text-2xl">Mission & Vision</h2>
													<p>Aims to connect people to positive experience through travel, helping them see the world differently.</p>   
												</div>
											</div>
										</div>
									</div>
									<div class="sm:flex items-center">
										<div class="mr-3">
											<a href="destinations.php" class="site-button butn-bg-shape">Discover More</a>
										</div>
										<div class="flex max-sm:pt-2.5">
											<div class="flex items-center mr-5">
												<span class="size-9 inline-flex rounded-full overflow-hidden border border-white ml-0">
													<img src="assets/images/hpy-cus/pic1.jpg" alt="img" width="34" height="34" loading="lazy">
												</span>
												<span class="size-9 inline-flex rounded-full overflow-hidden border border-white -ml-2.5">
													<img src="assets/images/hpy-cus/pic2.jpg" alt="img" width="34" height="34" loading="lazy">
												</span>
												<span class="size-9 inline-flex rounded-full overflow-hidden border border-white -ml-2.5">
													<img src="assets/images/hpy-cus/pic3.jpg" alt="img" width="34" height="34" loading="lazy">
												</span>
											</div>
											<div>
												<span class="block font-black text-22 text-primary">3.5k</span>
												<p class="mb-0 uppercase font-medium text-xs">Happy Customer</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="xl:col-span-7 lg:col-span-5 col-span-12 relative px-3.75">
								<div class="absolute z-1 lg:top-7.5 -top-40.5 3xl:-right-36.25 lg:-right-0.25 -right-6.25 animate-slide-top max-md:hidden">
									<div class="absolute lg:-left-27.5 -left-22.5 bottom-1.25">
										<img src="assets/images/plane1.png" alt="image" class="max-lg:max-w-80" width="384" height="193" loading="lazy">
									</div>
									<div class="bg-white rounded-xl py-1 lg:pl-13.75 pl-10.75 pr-6 inline-flex items-center lg:w-88 w-70">
										<h2 class="!font-display !font-black lg:!text-83 !text-6xl leading-none !text-secondary !mb-0 !text-shadow-[0px_4px_0px_var(--primary)] !mr-6.75">25</h2>
										<span class="font-black lg:text-28 text-2xl leading-[1.2] text-primary">Years of Experience</span>
									</div>
								</div>
								<div class="mt-3 xl:size-105 sm:size-90 size-70 border-[20px] border-white rounded-full
								shadow-[0px_27px_35.9px_rgba(41,137,145,0.2)] max-xl:relative max-xl:-bottom-41.25 max-lg:bottom-1/2 max-lg:translate-x-1/2 max-md:translate-x-0">
									<img src="assets/images/we-rec-pic2.jpg" alt="image" class="w-full rounded-full" width="380" height="380" loading="lazy">
								</div>
								<div class="absolute 3xl:-right-43.75 right-0.25 bottom-0 max-lg:w-[80%]">
									<img src="assets/images/we-rec-pic.png" alt="Image" width="764" height="586" loading="lazy">
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--WE RECOMMEND SECTION END-->

				<!-- MARKETING TOUR CAROUSEL START -->
				<div class="bg-paleaqua pb-15 relative overflow-hidden">
					<div class="container">
						<div class="text-center max-w-150 mx-auto md:mb-15 mb-7.5">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Exclusive <span class="text-citrusyellow">Marketing </span>Highlights</h2>
							<p class="text-base text-primary">Grab these limited-time deals and embark on your next great adventure!</p>
							<div class="-mt-7">
								<img src="assets/images/background/Title-Separator.png" alt="Image" class="w-117.5 inline-block" width="470" height="70" loading="lazy">
							</div>
						</div>
						<div class="swiper trv-marketing-tour-swiper pb-10">
							<div class="swiper-wrapper">
								<!-- SLIDE 1: Iceland -->
								<div class="swiper-slide">
									<div class="mx-3.75 group">
										<div class="relative rounded-3xl overflow-hidden shadow-lg transform transition duration-500 group-hover:scale-105">
											<img src="assets/images/tour/style1/pic1.jpg" alt="Iceland Tours" class="w-full h-96 object-cover">
											<div class="absolute top-5 left-5 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider z-10">
												Flash Sale: 40% OFF
											</div>
											<div class="absolute bottom-0 left-0 right-0 p-6 bg-white text-center">
												<h3 class="text-primary text-2xl font-bold mb-2">Majestic Iceland</h3>
												<p class="text-secondary text-sm font-semibold italic">"The Land of Fire and Ice awaits you."</p>
												<a href="#" class="mt-4 inline-block bg-citrusyellow text-primary px-5 py-2 rounded-full font-black text-sm hover:bg-primary hover:text-white transition-colors duration-300 shadow-md quick-book-btn">BOOK NOW</a>
											</div>
										</div>
									</div>
								</div>
								<!-- SLIDE 2: Japan -->
								<div class="swiper-slide">
									<div class="mx-3.75 group">
										<div class="relative rounded-3xl overflow-hidden shadow-lg transform transition duration-500 group-hover:scale-105">
											<img src="assets/images/tour/style1/pic2.jpg" alt="Japan Tours" class="w-full h-96 object-cover">
											<div class="absolute top-5 left-5 bg-secondary text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider z-10">
												Exclusive Guide
											</div>
											<div class="absolute bottom-0 left-0 right-0 p-6 bg-white text-center">
												<h3 class="text-primary text-2xl font-bold mb-2">Spirit of Japan</h3>
												<p class="text-secondary text-sm font-semibold italic">"Experience tradition with private local guides."</p>
												<a href="#" class="mt-4 inline-block bg-citrusyellow text-primary px-5 py-2 rounded-full font-black text-sm hover:bg-primary hover:text-white transition-colors duration-300 shadow-md quick-book-btn">EXPLORE</a>
											</div>
										</div>
									</div>
								</div>
								<!-- SLIDE 3: Maldives -->
								<div class="swiper-slide">
									<div class="mx-3.75 group">
										<div class="relative rounded-3xl overflow-hidden shadow-lg transform transition duration-500 group-hover:scale-105">
											<img src="assets/images/tour/style1/pic3.jpg" alt="Maldives Tours" class="w-full h-96 object-cover">
											<div class="absolute top-5 left-5 bg-citrusyellow text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider z-10">
												Early Bird: SAVE $200
											</div>
											<div class="absolute bottom-0 left-0 right-0 p-6 bg-white text-center">
												<h3 class="text-primary text-2xl font-bold mb-2">Maldives Paradise</h3>
												<p class="text-secondary text-sm font-semibold italic">"Book for 2027 and lock in today's prices."</p>
												<a href="#" class="mt-4 inline-block bg-citrusyellow text-primary px-5 py-2 rounded-full font-black text-sm hover:bg-primary hover:text-white transition-colors duration-300 shadow-md quick-book-btn">CLAIM DEAL</a>
											</div>
										</div>
									</div>
								</div>
								<!-- SLIDE 4: Switzerland -->
								<div class="swiper-slide">
									<div class="mx-3.75 group">
										<div class="relative rounded-3xl overflow-hidden shadow-lg transform transition duration-500 group-hover:scale-105">
											<img src="assets/images/tour/style1/pic4.jpg" alt="Switzerland Tours" class="w-full h-96 object-cover">
											<div class="absolute top-5 left-5 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider z-10">
												Last Minute Deal
											</div>
											<div class="absolute bottom-0 left-0 right-0 p-6 bg-white text-center">
												<h3 class="text-primary text-2xl font-bold mb-2">Swiss Alpine Magic</h3>
												<p class="text-secondary text-sm font-semibold italic">"Unbeatable escapes starting at $499."</p>
												<a href="#" class="mt-4 inline-block bg-citrusyellow text-primary px-5 py-2 rounded-full font-black text-sm hover:bg-primary hover:text-white transition-colors duration-300 shadow-md quick-book-btn">SNAG IT</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="swiper-pagination !-bottom-5"></div>
							<!-- Navigation buttons removed per user request -->
						</div>
					</div>
				</div>
				<!-- MARKETING TOUR CAROUSEL END -->
				
				<!-- CLIENT LOGO SECTION START -->
				<div class="p-5 bg-paleaqua">
					<div class="py-5 bg-white border-4 border-dashed border-paleaqua">
						<div class="container">
							<div class="grid grid-cols-12 items-center">
								<div class="xl:col-span-3 col-span-12">
									<div>
										<h2 class="!font-black !leading-[1] xl:text-46 md:text-40 text-3xl max-xl:mb-5 max-xl:text-center"><span class="!font-base text-citrusyellow block text-5xl !font-black">1K+ </span>Brands Trust Us</h2>
									</div>
								</div>
								<div class="xl:col-span-9 col-span-12">
									<div class="swiper brand-swiper">
										<div class="swiper-wrapper">
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/1.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="339" height="82" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/2.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="232" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/3.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="389" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/4.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="219" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/5.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="211" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/1.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="339" height="81" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/2.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="232" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/3.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="339" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/4.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="219" height="91" loading="lazy"></a>
												</div>
											</div>
											<div class="swiper-slide">
												<div class="text-center">
													<a href="about.php"><img src="assets/images/client-logo/dark/5.png" class="w-auto h-11.25 mx-auto opacity-40" alt="img" width="211" height="91" loading="lazy"></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- CLIENT LOGO  SECTION End -->
				
				<!-- VIDEO WITH ACHIVMENT SECTION REMOVED PER USER REQUEST -->
				
				<!--WE OFFER SERVICES SECTION START-->
				<div class="bg-primary relative overflow-hidden sm:pb-22.5 pb-10 sm:pt-30 pt-17.5 before:absolute before:left-0 before:right-0 before:bottom-0 before:z-0 2xl:before:h-55 sm:before:h-40 before:h-0 before:bg-lightturquoise">
					<div class="container">
						<div class="relative z-1">
							<div class="grid grid-cols-12">
								<div class="lg:col-span-5 col-span-12 px-3.75">
									<div class="relative z-1">
										<img class="block sm:ml-21.5 pb-17.5 animate-slide-left max-xl:-bottom-73 max-lg:bottom-0 max-xl:relative max-lg:[position:inherit] size-auto" src="assets/images/Left-Man-Image.png" alt="image" width="50" height="127" loading="lazy">
										<img class="absolute xl:bottom-0 lg:-bottom-60 bottom-10 left-0 size-auto" src="assets/images/travel-sites.png" alt="image" width="50" height="127" loading="lazy">
										<div class="img-bg-shade"></div>
									</div>
								</div>
								<div class="lg:col-span-7 col-span-12 px-3.75">
									<div class="trv-we-off-content">
										<!-- TITLE START-->
											<div class="lg:mr-22.5 text-left mb-15">
												<h2 class="!text-white xl:text-46 md:text-40 text-3xl mb-2.5">We offer Best <span class="text-citrusyellow">Services </span></h2>
												<p class="text-paleaqua mb-12.5 text-base">Luxit Global Escapes is a multi-award-winning strategy and content creation agency that specializes in travel marketing. They have one of the world's largest and most influential online travel communities, helping brands and tourism.</p>
											</div>
										<!-- TITLE END-->
										<!--servce box-->
										<div class="grid grid-cols-12 gap-7.5">
											<div class="xl:col-span-4 md:col-span-6 col-span-12 mb-10">
												<div class="relative text-center bg-white ml-3 rounded-tl-8xl rounded-br-8xl rounded-tr-2xl rounded-bl-2xl pt-8.75 px-4 pb-15">
													<div>
														<span class="inline-block mb-7.5">
															<img src="assets/images/trv-icon/ship.png" alt="Image" class="max-w-16 w-full inline-block" style="filter: brightness(0) saturate(100%) invert(53%) sepia(72%) saturate(1711%) hue-rotate(188deg) brightness(100%) contrast(94%);" width="64" height="64" loading="lazy">
														</span>
													</div>
													<div class="trv-icon-content">
														<h2 class="mb-3.75 text-2xl">Exclusive Trip</h2>
														<p class="p-text">We pay attention to every quality in the service we provide to you</p>
													</div>
													<div class="relative after:absolute after:-left-7 after:-top-10.25 after:size-0 after:border-t-[12px] after:border-t-transparent after:border-r-[12px] after:border-r-[#297BD4] after:border-b-[12px] after:border-b-transparent">
														<div class="absolute -left-7 -bottom-15 size-0 border-b-[90px] border-b-[#489CF6] border-r-[80px] border-r-transparent">
															<span class="text-xs/[1.5] font-medium block text-white font-title pt-9 px-2.5 pb-0">Step</span>
															<div class="text-2xl font-black text-white pl-2.5">01</div>
														</div>
													</div>
												</div>
											</div>
											<div class="xl:col-span-4 md:col-span-6 col-span-12 mb-10">
												<div class="relative text-center bg-white ml-3 rounded-tl-8xl rounded-br-8xl rounded-tr-2xl rounded-bl-2xl pt-8.75 px-4 pb-15">
													<div class="inline-block mb-7.5">
														<span>
															<img src="assets/images/trv-icon/plane-booking.png" alt="Image" class="max-w-16 w-full inline-block" style="filter: brightness(0) saturate(100%) invert(64%) sepia(100%) saturate(1157%) hue-rotate(350deg) brightness(103%) contrast(103%);" width="64" height="64" loading="lazy">
															
														</span>
													</div>
													<div class="trv-icon-content">
														<h2 class="mb-3.75 text-2xl">Easy Booking</h2>
														<p class="p-text">Booking process and full support service assistance from us.</p>
													</div>
													<div class="relative after:absolute after:-left-7 after:-top-10.25 after:size-0 after:border-t-[12px] after:border-t-transparent after:border-r-[12px] after:border-r-[#BA7D0A] after:border-b-[12px] after:border-b-transparent">
														<div class="absolute -left-7 -bottom-15 size-0 border-b-[90px] border-b-[#FFAA0D] border-r-[80px] border-r-transparent">
															<span class="text-xs/[1.5] font-medium block text-white font-title pt-9 px-2.5 pb-0">Step</span>
															<div class="text-2xl font-black text-white pl-2.5">02</div>
														</div>
													</div>
												</div>
											</div>
											<div class="xl:col-span-4 col-span-12 mb-10">
												<div class="relative text-center bg-white ml-3 rounded-tl-8xl rounded-br-8xl rounded-tr-2xl rounded-bl-2xl pt-8.75 px-4 pb-15">
													<div class="inline-block mb-7.5">
														<span>
															<img src="assets/images/trv-icon/guide-icon.png" alt="Image" class="max-w-16 w-full inline-block" style="filter: brightness(0) saturate(100%) invert(67%) sepia(36%) saturate(2222%) hue-rotate(38deg) brightness(102%) contrast(101%);" width="64" height="64" loading="lazy">
														</span>
													</div>
													<div class="trv-icon-content">
														<h2 class="mb-3.75 text-2xl">Professional Guide</h2>
														<p class="p-text">While on vacation will be guided by our professional guide</p>
													</div>
													<div class="relative after:absolute after:-left-7 after:-top-10.25 after:size-0 after:border-t-[12px] after:border-t-transparent after:border-r-[12px] after:border-r-[#568603] after:border-b-[12px] after:border-b-transparent">
														<div class="absolute -left-7 -bottom-15 size-0 border-b-[90px] border-b-[#85D200] border-r-[80px] border-r-transparent">
															<span class="text-xs/[1.5] font-medium block text-white font-title pt-9 px-2.5 pb-0">Step</span>
															<div class="text-2xl font-black text-white pl-2.5">03</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="rounded-full 2xl:h-50 2xl:mb-7.5 overflow-hidden ">
											<img src="assets/images/landscape-pic.jpg" alt="image" class="w-full" width="726" height="199" loading="lazy">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="absolute left-0 2xl:bottom-55 bottom-40"><img src="assets/images/building-1.png" alt="Image" width="301" height="640" loading="lazy"></div>
					<div class="absolute md:right-0 sm:-right-20 -right-38 md:top-0 sm:-top-3 -top-12"><img src="assets/images/Right-top-plane.png" alt="Image" width="257" height="342" loading="lazy"></div>
				</div>
				<!--WE OFFER SERVICES SECTION START-->

				<!--POPULAR SEARCH DESTINATION START-->
				<div class="bg-eggshell">
					<div class="pt-10 pb-1.25">
						<div class="swiper popular-places-logo-swiper">
							<div class="swiper-wrapper">
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>250 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">Paris</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>65 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">Thailand</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>175 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">Bangkok</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>130 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">Dubai</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>140 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">France</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>350 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">London</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>250 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">Paris</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>65 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">Thailand</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>175 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">Bangkok</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>130 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">Dubai</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>140 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-citrusyellow leading-14.5 block hover:text-secondary duration-500">France</a>
									</div>
								</div>
								<div class="swiper-slide">
									<div class="text-center">
										<div class="text-xs leading-3.5 text-primary inline-flex py-1.5 px-2.5 bg-white border border-primary/20 rounded-2lg">
											<i class="fa-regular fa-flag mr-1"></i>350 Tour 
										</div>
										<a href="destinations.php" class="font-display text-40 text-primary leading-14.5 block hover:text-secondary duration-500">London</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="container">
						<div>
							<div class="grid grid-cols-12">
								<div class="md:col-span-5 col-span-12 px-3.75">
									<div class="media">
										<img src="assets/images/pop-search-left-pic.png" alt="Image" width="510" height="184" loading="lazy">
									</div>
								</div>
								<div class="md:col-span-7 col-span-12 px-3.75">
									<div class="md:ml-12.5">
										<span class="text-primary block font-title font-bold text-28 mb-3.75">Popular!</span>
										<h2 class="2xl:!text-40xl lg:!text-30xl sm:!text-90 !text-6xl !leading-[0.75] uppercase !text-eggshelllight !font-black !font-base mb-2.5">Search</h2>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--POPULAR SEARCH DESTINATION END-->
				
				<!--POPULAR DESTINATION SECTION START-->
				<div class="relative overflow-hidden md:pb-22.5 pb-10 md:pt-30 pt-17.5 bg-contain bg-[bottom_center] bg-repeat-x bg-[url(../images/background/Cloud-bg.png)]">
					<div class="container">
						<!-- TITLE START-->
						<div class="text-center max-w-150 mx-auto md:mb-15 mb-7.5">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5"><span class="text-citrusyellow">Popular </span>Destination</h2>
							<p class="text-base">Destinations worth exploring! Here are a few popular spots</p>
							<div class="-mt-7">
								<img src="assets/images/background/Title-Separator.png" alt="Image" class="w-117.5 inline-block" width="470" height="70">
							</div>
						</div>
						<!-- TITLE END-->
						<div>
							<div class="swiper reviewtwo-slider !relative !z-1 !-mt-7.5 xl:!pb-29 !pb-22.5">
								<div class="swiper-wrapper pt-7.5">
									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic1.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Paris</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic2.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Maldives</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic3.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Hong Kong</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic4.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Thailand</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic5.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Bangkok</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic6.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Tokyo</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic7.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">Spain</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="relative z-1 group">
											<div class="rounded-3xl overflow-hidden">
												<a href="destinations.php"><img src="assets/images/destinations/style1/pic8.jpg" width="309" height="500" alt="Image" class="w-full h-125 object-cover object-center"></a>
											</div>
											<div>
												<h3 class="text-28"><a href="destinations.php" class="block text-primary bg-white text-center p-5 rounded-3xl absolute left-0 right-0 -bottom-px duration-500 group-hover:text-white group-hover:bg-primary">California</a></h3>
											</div>
											<div class="absolute -z-1 top-0 left-1/2 -translate-x-1/2 duration-500 group-hover:-top-7.5">
												<img src="assets/images/destinations/hotballon-right.png" alt="image" width="155" height="233" class="w-full max-w-75 mx-auto block duration-500">
											</div>
										</div>
									</div>
								</div>
								<div class="swiper-button-next"></div>
								<div class="swiper-button-prev"></div>
							</div>
						</div> 
					</div>
					<div class="absolute -left-28.75 top-2/5 w-57.5 opacity-50 animate-slide-top2"><img src="assets/images/hotballon-Left.png" alt="image" width="233" height="333"></div>
					<div class="absolute -right-13.75 top-2/5 w-27.5 animate-slide-top"><img src="assets/images/hotballon-right.png" alt="image" width="110" height="166"></div>
				</div>
				<!--POPULAR DESTINATION SECTION END-->
				
				<!--3 STEP SECTION START-->
				<div class="bg-white md:pt-30 pt-17.5 relative overflow-hidden">
					<div class="absolute w-150 h-137.5 -left-75 top-1/2 bg-amber [filter:blur(100px)]"></div>
					<div class="absolute w-150 h-125 -right-75 top-1/2 bg-bluelight [filter:blur(100px)]"></div>
					<div class="absolute w-150 h-125 left-1/2 -translate-x-1/2 -top-50 bg-amber [filter:blur(100px)]"></div>
					<div class="container">
						<div class="section-content">
							<div class="relative z-1 mb-30">
								<div class="grid grid-cols-12">
									<div class="xl:col-span-7 col-span-12 px-3.75">
										<div>
											<!-- TITLE START-->
											<div class="md:max-w-100 max-w-[inherit] text-left lg:mb-15 mb-7.5">
												<h2 class="xl:text-46 md:text-40 text-3xl mb-3.5"><span class="text-citrusyellow">3 Easy Steps </span>for Book Your Next Trip</h2>
											</div>
											<!-- TITLE END-->
											<div class="md:flex max-2xl:justify-center">
												<div class="md:mr-6 md:min-w-61.5 min-w-[inherit]">
													<div class="media">
														<img src="assets/images/offer/pic1.jpg" alt="Image" class="md:w-59 w-[calc(100%_-_12px)] h-66.5 object-cover object-center rounded-xl" width="236" height="266" loading="lazy">
													</div>
													<div class="rounded-xl md:max-w-59 max-w-[inherit] p-5 pt-15 ml-3 -mt-11 bg-citrusyellow max-md:text-center max-md:mb-7.5">
														<span class="text-primary font-semibold text-lg leading-6 block pb-2.5">Get Special Offer</span>
														<div class="flex max-md:justify-center">
															<h2 class="!text-white !text-95 !leading-[0.75] !font-black !font-base">48</h2>
															<div class="block text-xl text-primary uppercase font-black leading-6">%<span class="block">Off</span></div>
														</div>
													</div>
												</div>
												<div class="md:mr-6.25">
													<div class="mb-5 bg-white border border-paleaqua p-2.5 sm:pl-12.5 pl-10.5 shadow-[0px_27px_35.9px_rgba(41,137,145,0.2)] rounded-tr-50xl rounded-br-50xl relative sm:ml-12.5 ml-6.5">
														<div class="flex items-center justify-between">
															<div class="sm:size-20 size-15 sm:min-w-20 min-w-15 items-center justify-center flex bg-primary rounded-xl font-base sm:text-42 text-36 text-white font-black absolute sm:-left-12.5 -left-8.5 top-1/2 -translate-y-1/2">
																01
															</div>
															<div>
																<div class="font-title text-primary lg:text-2xl text-xl font-medium leading-[1.2] mb-2.5">Choose Destination</div>
																<p class="text-primary">First, select your preferred destination and proceed further</p>
															</div>
															<div class="sm:size-25 size-15 sm:min-w-25 min-w-15 bg-citrusyellow rounded-full flex items-center justify-center mr-0">
																<div class="bg-white sm:size-22.5 size-12.5 sm:min-w-22.5 min-w-12.5 flex items-center justify-center rounded-full">
																	<img src="assets/images/trv-icon/destination.png" alt="image" class="sm:max-w-12 max-w-7 w-full" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="48" height="48" loading="lazy">
																</div>
															</div>
														</div>
													</div>
													<div class="mb-5 bg-white border border-paleaqua p-2.5 sm:pl-12.5 pl-10.5 shadow-[0px_27px_35.9px_rgba(41,137,145,0.2)] rounded-tr-50xl rounded-br-50xl relative sm:ml-12.5 ml-6.5">
														<div class="flex items-center justify-between">
															<div class="sm:size-20 size-15 sm:min-w-20 min-w-15 items-center justify-center flex bg-primary rounded-xl font-base sm:text-42 text-36 text-white font-black absolute sm:-left-12.5 -left-8.5 top-1/2 -translate-y-1/2">
																02
															</div>
															<div>
																<div class="font-title text-primary lg:text-2xl text-xl font-medium leading-[1.2] mb-2.5">Make Payment</div>
																<p class="text-primary">We pay attention to every quality in the service we provide to you</p>
															</div>
															<div class="sm:size-25 size-15 sm:min-w-25 min-w-15 bg-citrusyellow rounded-full flex items-center justify-center mr-0">
																<div class="bg-white sm:size-22.5 size-12.5 sm:min-w-22.5 min-w-12.5 flex items-center justify-center rounded-full">
																	<img src="assets/images/trv-icon/make-payment.png" alt="image" class="sm:max-w-12 max-w-7 w-full" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="48" height="48" loading="lazy">
																</div>
															</div>
														</div>
													</div>
													<div class="mb-5 bg-white border border-paleaqua p-2.5 sm:pl-12.5 pl-10.5 shadow-[0px_27px_35.9px_rgba(41,137,145,0.2)] rounded-tr-50xl rounded-br-50xl relative sm:ml-12.5 ml-6.5">
														<div class="flex items-center justify-between">
															<div class="sm:size-20 size-15 sm:min-w-20 min-w-15 items-center justify-center flex bg-primary rounded-xl font-base sm:text-42 text-36 text-white font-black absolute sm:-left-12.5 -left-8.5 top-1/2 -translate-y-1/2">
																03
															</div>
															<div>
																<div class="font-title text-primary lg:text-2xl text-xl font-medium leading-[1.2] mb-2.5">Ready For Travelling</div>
																<p class="text-primary">We pay attention to every quality in the service we provide to you</p>
															</div>
															<div class="sm:size-25 size-15 sm:min-w-25 min-w-15 bg-citrusyellow rounded-full flex items-center justify-center mr-0">
																<div class="bg-white sm:size-22.5 size-12.5 sm:min-w-22.5 min-w-12.5 flex items-center justify-center rounded-full">
																	<img src="assets/images/trv-icon/travelling.png" alt="image" class="sm:max-w-12 max-w-7 w-full" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="48" height="48" loading="lazy">
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="xl:col-span-5 col-span-12 px-3.75">
										<div class="relative z-1 pt-22.5 max-2xl:flex max-2xl:justify-center">
											<img src="assets/images/hotballon-Left.png" alt="img" class="w-17 absolute left-0 2xl:-bottom-106.25 bottom-0 animate-slide-top2" width="68" height="98" loading="lazy">
											<img src="assets/images/hotballon-right.png" alt="img" class="sm:w-32 w-25 absolute sm:right-15 right-0 top-2.5 z-1" width="128" height="198" loading="lazy">
											<img src="assets/images/cloud-1.png" alt="Image" class="absolute z-2 w-2/5 -left-7.5 top-40 animate-smooth-up-down" width="204" height="169" loading="lazy">
											<img src="assets/images/cloud-2.png" alt="Image" class="absolute right-0 top-10 z-3 animate-smooth-up-down2" width="297" height="225" loading="lazy">
											<div class="2xl:absolute after:absolute after:bottom-[-30%] after:left-1/2 after:-translate-1/2 sm:after:size-125 after:size-80 after:rounded-full after:bg-eggshell after:-z-1">
												<img src="assets/images/Girl-Image.png" alt="img" class="mr-20 relative z-5" width="440" height="577" loading="lazy">
												<span class="text-primary font-display sm:text-40 text-36 flex items-baseline absolute 2xl:left-[85%] sm:left-[90%] left-[78%] text-left rotate-[-90deg] origin-[0_0] sm:pl-12.5">For <b class="text-citrusyellow text-98 font-normal leading-[1]">Summer!</b></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="overflow-hidden rounded-br-25xl rounded-bl-25xl bg-primary pt-27.5 pb-17.5 bg-[url(../images/w-cho-top.png),url(../images/w-cho-btm.png)] bg-no-repeat [background-position:top_left,bottom_left] flex flex-wrap items-center justify-between">
							<div class="max-w-122.5 xl:ml-45 ml-10 mb-7.5">
								<h3 class="!font-display !text-40 mb-7.5 !text-white">
									Why Choose Us!
								</h3>
								<div class="mb-3.75">
									<ul class="sm:flex text-white flex-wrap">
										<li class="font-title font-medium text-lg text-white sm:w-1/2 pr-2.5 flex mb-5">
											<i class="size-7.5 max-w-7.5 mr-6 rounded-full bg-citrusyellow !flex items-center justify-center text-white fa-solid fa-check"></i>
											<span>Personalized travel experiences</span>
										</li>
										<li class="font-title font-medium text-lg text-white sm:w-1/2 pr-2.5 flex mb-5">
											<i class="size-7.5 max-w-7.5 mr-6 rounded-full bg-citrusyellow !flex items-center justify-center text-white fa-solid fa-check"></i>
											<span>Expert local knowledge</span>
										</li>
										<li class="font-title font-medium text-lg text-white sm:w-1/2 pr-2.5 flex mb-5">
											<i class="size-7.5 max-w-7.5 mr-6 rounded-full bg-citrusyellow !flex items-center justify-center text-white fa-solid fa-check"></i>
											<span>Seamless end-to-end planning</span>
										</li>
										<li class="font-title font-medium text-lg text-white sm:w-1/2 pr-2.5 flex mb-5">
											<i class="size-7.5 max-w-7.5 mr-6 rounded-full bg-citrusyellow !flex items-center justify-center text-white fa-solid fa-check"></i>
											<span>Reliable responsive support</span>
										</li>
                                        <li class="font-title font-medium text-lg text-white sm:w-1/2 pr-2.5 flex mb-5">
											<i class="size-7.5 max-w-7.5 mr-6 rounded-full bg-citrusyellow !flex items-center justify-center text-white fa-solid fa-check"></i>
											<span>Impact driven luxury journeys</span>
										</li>
									</ul>
								</div>
								<a href="destinations.php" class="site-button butn-bg-shape">Discover More</a>
							</div>
							<div class="mx-auto lg:mr-40 max-lg:text-center">
								<div class="mb-2.5">
									<img src="assets/images/24-Image.png" alt="image" width="147" height="163" loading="lazy">
								</div>
								<div>
									<h3 class="!font-black xl:!text-58 !text-40 !leading-[0.75] mb-2.5 uppercase !text-white !font-base">Call Us</h3>
									<span class="block font-bold xl:text-38 text-28 leading-[1.2] uppercase text-secondary">+254 737 800900</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--3 STEP SECTION END-->
				
				<!--TESTIMONIAL SECTION START-->
				<div class="sm:py-22.5 py-10 relative overflow-hidden bg-white">
					<div class="container">
						<!-- TITLE START-->
						<div class="text-center max-w-150 mx-auto xl:mb-15 mb-0">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Our Client<span class="text-citrusyellow"> Says!</span></h2>
							<p class="text-base">Destinations worth exploring! Here are a few popular spots</p>
							<div class="-mt-7">
								<img src="assets/images/background/Title-Separator.png" alt="Image" class="w-117.5 inline-block" width="470" height="70" loading="lazy">
							</div>
						</div>
						<!-- TITLE END-->
						<div>
							<div class="text-center relative font-bold 2xl:text-40xl/48 xl:text-30xl/48 lg:text-80 max-lg:mb-7.5 sm:text-6xl text-40 tracking-[0.12em] uppercase bg-primary-gradient bg-clip-text 
							[-webkit-text-fill-color:transparent] 
							bg-[linear-gradient(to_bottom,#066168_15%,rgba(255,170,13,0.3019607843)_60%,#fff_85%)]">
								Testimonial
								<img src="assets/images/airplane-takeoff1.png" alt="Image" class="absolute xl:-top-5 sm:-top-6 top-0 left-1/2 -translate-x-1/2 max-xl:w-[40%] max-md:w-[60%]" width="493" height="116" loading="lazy">
							</div>

							<div class="relative">
								<!--Image Slider-->
								<div class="swiper testimonial-content-sld">
									<div class="swiper-wrapper">
										<!--1-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic1.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Amelia Warner</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Tourist</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="img" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-full" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														Once the travel bug bites, there is no known antidote, and I know that I shall be happily infected until the end of my life. A journey is best measured in friends.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--2-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic2.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Kavin Martin</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Travler</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														I was very impressed with the high-quality services and professional guides. Every detail of my trip was perfectly organized, making it an unforgettable experience.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--3-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic3.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Antonio</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Tourist</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														Travel bug bites, there is no known antidote, and I know that I shall be happily infected until the end of my life. A journey is best measured in friends.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
									   <!--1-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic1.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Amelia Warner</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Tourist</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														Once the travel bug bites, there is no known antidote, and I know that I shall be happily infected until the end of my life. A journey is best measured in friends.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--2-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic2.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Kavin Martin</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Travler</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														I was very impressed with the high-quality services and professional guides. Every detail of my trip was perfectly organized, making it an unforgettable experience.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--3-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic3.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28lg:!text-36 !text-28 !font-normal text-primary mb-0">Antonio</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Tourist</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														Travel bug bites, there is no known antidote, and I know that I shall be happily infected until the end of my life. A journey is best measured in friends.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--3-->
										<!--2-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic2.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Kavin Martin</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Travler</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														I was very impressed with the high-quality services and professional guides. Every detail of my trip was perfectly organized, making it an unforgettable experience.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
										<!--3-->
										<div class="swiper-slide">
											<div class="md:flex items-center bg-white">
												<div class="mb-0 relative lg:max-w-93.75 max-w-62.5 z-1 lg:mr-23.75 md:mr-7.5 max-md:mx-auto max-md:mb-7.5 before:absolute before:size-95 before:rounded-full before:bg-citrusyellow before:opacity-10 before:-right-23.5 before:top-1/2 before:-translate-y-1/2 before:-z-1 after:absolute after:size-82.5 after:rounded-full after:bg-primary after:-right-10.5 after:top-1/2 after:-translate-y-1/2 after:-z-1 max-lg:after:hidden max-lg:before:hidden">
													<img src="assets/images/trv-testimonial2/pic3.jpg" alt="Image" class="rounded-3xl" width="375" height="489" loading="lazy">
												</div>
												<div class="lg:max-w-122.5 max-w-85 ml-auto max-lg:mr-auto max-md:mx-auto">
													<div class="flex items-center justify-between mb-6.25 max-sm:flex-col max-sm:text-center sm:items-start">
														<div>
															<h2 class="!font-display lg:!text-36 !text-28 !font-normal text-primary mb-0">Antonio</h2>
															<span class="font-title text-xl font-medium text-citrusyellow inline-block">Tourist</span>
														</div>
														<div>
															<img src="assets/images/trv-icon/Quote.png" alt="image" class="lg:max-w-17.5 max-w-11 max-h-14.5 w-fulll" style="filter: brightness(0) saturate(100%) invert(25%) sepia(21%) saturate(4469%) hue-rotate(154deg) brightness(93%) contrast(95%)" width="70" height="58" loading="lazy">
														</div>
													</div>
													<p class="font-title lg:text-2xl text-lg text-primary mb-5 max-lg:pr-7.5 max-md:pr-0">
														Travel bug bites, there is no known antidote, and I know that I shall be happily infected until the end of my life. A journey is best measured in friends.
													</p>
													<div class="md:float-right text-citrusyellow text-base mr-0.75">
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
														<i class="fa-solid fa-star"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="testimonial-content-sld-control">
										<div class="swiper-button-next"></div>
										<div class="swiper-button-prev"></div>
									</div>
								</div>
								<!--Thumbnail Slider-->
								<div class="swiper testimonial-thum-sld xl:h-92.5 h-auto !overflow-hidden xl:w-28.75 w-62.5 xl:!absolute xl:top-1/2 top-auto xl:left-128.75 left-0 xl:-translate-y-1/2 transform-none max-xl:mt-5 xl:!mx-auto md:!mx-0 !mx-auto !relative">
									<div class="swiper-wrapper xl:flex-col">
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic1.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic2.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic3.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
									   </div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic4.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>

										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic1.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic2.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic3.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
										<div class="swiper-slide">
											<div class="duration-500">
												<img src="assets/images/trv-testimonial2/pic4.jpg" alt="Image" class="duration-500 size-19 object-cover object-center rounded-xl cursor-pointer" width="76" height="76" loading="lazy">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="absolute -right-13.75 top-2/5 w-27.5 animate-slide-top"><img src="assets/images/hotballon-right.png" alt="image"></div>
				</div>
				<!--TESTIMONIAL SECTION End-->
				
				
				<!--ALL BLOGS SECTION START-->
				<div class="sm:pt-22.5 pt-10 pb-40 bg-paleaqua">
					<div class="container">
						<!-- TITLE START-->
						<div class="md:mb-15 mb-7.5 grid grid-cols-12">
							<div class="xl:col-span-4 lg:col-span-6 col-span-12">
								<div class="text-left">
									<h2 class="xl:text-46 md:text-40 text-3xl mb-3.5">Explore<span class="text-citrusyellow"> Latest News</span></h2>
									<div class="text-base max-lg:mb-7.5">Maybe for a travel blog, wildlife site, or web development project here are a few sample templates you can use to simulate real-time news updates:</div>
								</div>
							</div>
							<div class="xl:col-span-8 lg:col-span-6 col-span-12">
								<div class="lg:text-right">
									<a href="blog-detail.php" class="site-button butn-bg-shape">See More Articles</a>
								</div>
							</div>
						</div>
						<!-- TITLE END-->
						<div>
							<div class="grid grid-cols-12 md:gap-7.5">
								<div class="xl:col-span-4 md:col-span-6 col-span-12">
									<div class="relative flex mb-7.5">
										<div class="mr-2.5 relative z-1 rounded-xxl overflow-hidden min-w-25 w-25 h-32.75">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-sm/pic1.jpg" alt="Image" class="size-full" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="bg-white py-6.25 lg:px-5 px-3 rounded-xxl w-full">
											<div class="size-10 text-white text-sm leading-none bg-primary text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5">
												<span class="block text-xl leading-none font-bold text-white">14</span>June
											</div>
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none  pb-3.75">Aidan Butler</div>
											<div>
												<h2><a href="blog-detail.php" class="duration-500 2xl:text-xl/6 sm:text-lg text-sm font-title font-medium text-primary block hover:text-citrusyellow">Resources for your first trip to overseas vacation</a></h2>
											</div>
										</div>                                
									</div>
									<div class="relative flex mb-7.5">
										<div class="mr-2.5 relative z-1 rounded-xxl overflow-hidden min-w-25 w-25 h-32.75">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-sm/pic2.jpg" alt="Image" class="size-full" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="bg-white py-6.25 lg:px-5 px-3 rounded-xxl w-full">
											<div class="size-10 text-white text-sm leading-none bg-primary text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5">
												<span class="block text-xl leading-none font-bold text-white">26</span>June
											</div>
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none  pb-3.75">Ricardo Bell</div>
											<div>
												<h2><a href="blog-detail.php" class="duration-500 2xl:text-xl/6 sm:text-lg text-sm font-title font-medium text-primary block hover:text-citrusyellow">How to get acquainted with natives in a strange land</a></h2>
											</div>
										</div>                                
									</div>
									<div class="relative flex mb-7.5">
										<div class="mr-2.5 relative z-1 rounded-xxl overflow-hidden min-w-25 w-25 h-32.75">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-sm/pic3.jpg" alt="Image" class="size-full" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="bg-white py-6.25 lg:px-5 px-3 rounded-xxl w-full">
											<div class="size-10 text-white text-sm leading-none bg-primary text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5">
												<span class="block text-xl leading-none font-bold text-white">20</span>June
											</div>
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none  pb-3.75">Martin Hicks</div>
											<div>
												<h2><a href="blog-detail.php" class="duration-500 2xl:text-xl/6 sm:text-lg text-sm font-title font-medium text-primary block hover:text-citrusyellow">Resources for your first trip to overseas vacation</a></h2>
											</div>
										</div>                                
									</div>
								</div>
								<div class="xl:col-span-4 md:col-span-6 col-span-12">
									<div class="relative flex mb-7.5">
										<div class="mr-2.5 relative z-1 rounded-xxl overflow-hidden min-w-25 w-25 h-32.75">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-sm/pic4.jpg" alt="Image" class="size-full" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="bg-white py-6.25 lg:px-5 px-3 rounded-xxl w-full">
											<div class="size-10 text-white text-sm leading-none bg-primary text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5">
												<span class="block text-xl leading-none font-bold text-white">28</span>June
											</div>
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none  pb-3.75">Poul Ward</div>
											<div>
												<h2><a href="blog-detail.php" class="duration-500 2xl:text-xl/6 sm:text-lg text-sm font-title font-medium text-primary block hover:text-citrusyellow">Step by step guide to planning your ideal holiday</a></h2>
											</div>
										</div>                                
									</div>
									<div class="relative mb-7.5">
										<div class="relative z-1 rounded-xl overflow-hidden">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-md/pic1.jpg" alt="Image" class="object-cover object-center h-52.5 w-full" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="size-10 text-primary text-sm leading-none bg-paleaqua text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5 z-1">
											<span class="block text-xl leading-none font-bold">28</span>June
										</div>                                      
										<div class="bg-white py-6.25 px-5 rounded-xxl -mt-12.5 md:mx-5 z-1 absolute max-xl:w-[91%] max-md:w-full">
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none  pb-3.75">Poul Ward</div>
											<div>
												<h2 class="post-title"><a href="blog-detail.php" class="duration-500 2xl:text-xl/6 sm:text-lg text-sm font-title font-medium text-primary block hover:text-citrusyellow">Step by step guide to planning your ideal holiday</a></h2>
											</div>
										</div>                                
									</div>
								</div>
								<div class="xl:col-span-4 md:col-span-6 col-span-12">
									<div class="relative mb-7.5 max-xl:left-1/2 max-md:left-0 max-md:-bottom-15">
										<div class="relative z-1 rounded-xl overflow-hidden">
											<a href="blog-detail.php"><img src="assets/images/trv-blog/blog-lg/pic1.jpg" alt="Image" class="w-full  object-cover h-113.25" width="100" height="131" loading="lazy"></a>
										</div>
										<div class="size-20 text-primary text-sm leading-none bg-paleaqua text-center flex flex-col items-center justify-center rounded-md absolute top-2.5 right-2.5 z-1"><span class="block text-36 leading-none font-extrabold">08</span>June</div>   
										<div class="pt-15 p-7.5 absolute rounded-xxl z-1 bottom-0 left-0 w-full 
										bg-[linear-gradient(to_bottom,rgba(0,0,0,0)_0%,rgba(0,0,0,0.76)_73%)]">
											<div class="text-lg font-medium text-citrusyellow whitespace-nowrap table font-title leading-none pb-3.75">By Joey Peterson</div>
											<div class="trv-post-title ">
												<h3><a href="blog-detail.php" class="2xl:text-28 text-2xl font-title font-medium text-white block">The Top Travel Destinations for Photography Enthusiasts</a></h3>
											</div>
										</div>                                
									</div>
								</div>
							</div>
						</div> 
					</div>
				</div>
				<!--ALL BLOGS SECTION END-->
			</div>
			<!-- CONTENT END -->

			<!-- ═══════════════════════════════════════════
			     QUICK BOOKING MODAL
			     ═══════════════════════════════════════════ -->
			<div id="qb-overlay" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px;">
				<div id="qb-panel" style="background:#fff;border-radius:24px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 32px 80px rgba(0,106,114,.25);transform:translateY(20px);opacity:0;transition:transform .35s ease,opacity .35s ease;">
					<!-- Panel header -->
					<div style="background:#006A72;padding:24px 32px;display:flex;align-items:flex-start;justify-content:space-between;">
						<div>
							<p style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.55);margin-bottom:5px;">Quick Booking</p>
							<h3 id="qb-tour-name" style="color:#fff;font-size:1.4rem;font-weight:800;line-height:1.2;max-width:380px;"></h3>
						</div>
						<button id="qb-close" aria-label="Close" style="color:rgba(255,255,255,.6);background:none;border:none;font-size:1.8rem;line-height:1;cursor:pointer;margin-top:-2px;padding:0 0 0 16px;flex-shrink:0;" onmouseenter="this.style.color='#fff'" onmouseleave="this.style.color='rgba(255,255,255,.6)'">&times;</button>
					</div>
					<!-- Form -->
					<div id="qb-form-wrap" style="padding:28px 32px;">
						<form id="qb-form" novalidate autocomplete="off">
							<input type="hidden" name="tour_name" id="qb-hidden-tour">
							<input type="hidden" name="type" id="qb-hidden-type" value="book">
							<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
								<div style="grid-column:span 2;">
									<input name="full_name" required type="text" placeholder="Your Full Name *" style="width:100%;height:52px;padding:0 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;box-sizing:border-box;transition:border-color .2s;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'">
								</div>
								<div>
									<input name="email" required type="email" placeholder="Email Address *" style="width:100%;height:52px;padding:0 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;box-sizing:border-box;transition:border-color .2s;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'">
								</div>
								<div>
									<input name="phone" type="text" placeholder="Phone Number" style="width:100%;height:52px;padding:0 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;box-sizing:border-box;transition:border-color .2s;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'">
								</div>
								<div>
									<input name="travel_date" type="date" id="qb-date" style="width:100%;height:52px;padding:0 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;color:#555;box-sizing:border-box;transition:border-color .2s;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'">
								</div>
								<div>
									<input name="travelers" type="number" min="1" max="99" value="1" placeholder="Travelers" style="width:100%;height:52px;padding:0 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;box-sizing:border-box;transition:border-color .2s;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'">
								</div>
								<div style="grid-column:span 2;">
									<textarea name="message" rows="3" placeholder="Special requests or questions? (optional)" style="width:100%;padding:14px 18px;border-radius:14px;border:2px solid #f0f4f4;outline:none;font-size:.9rem;resize:none;box-sizing:border-box;transition:border-color .2s;font-family:inherit;" onfocus="this.style.borderColor='#006A72'" onblur="this.style.borderColor='#f0f4f4'"></textarea>
								</div>
							</div>
							<p id="qb-error" style="color:#e53e3e;font-size:.82rem;margin-bottom:10px;display:none;"></p>
							<div style="display:flex;gap:10px;">
								<button type="submit" data-type="book" id="qb-btn-book" style="flex:1;height:52px;background:#006A72;color:#fff;border:none;border-radius:14px;font-weight:700;font-size:.88rem;cursor:pointer;transition:background .2s;letter-spacing:.03em;">
									<i class="fa fa-calendar-check" style="margin-right:7px;"></i>Book Now
								</button>
								<button type="submit" data-type="enquire" id="qb-btn-enquire" style="flex:1;height:52px;background:#fff;color:#006A72;border:2px solid #006A72;border-radius:14px;font-weight:700;font-size:.88rem;cursor:pointer;transition:background .2s,color .2s;letter-spacing:.03em;" onmouseenter="this.style.background='#006A72';this.style.color='#fff'" onmouseleave="this.style.background='#fff';this.style.color='#006A72'">
									<i class="fa fa-envelope" style="margin-right:7px;"></i>Send Enquiry
								</button>
							</div>
						</form>
					</div>
					<!-- Success state -->
					<div id="qb-success" style="padding:48px 32px;text-align:center;display:none;">
						<div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
							<i class="fa fa-check" style="color:#16a34a;font-size:1.5rem;"></i>
						</div>
						<h4 id="qb-success-msg" style="color:#006A72;font-size:1.1rem;font-weight:700;margin-bottom:8px;"></h4>
						<p style="color:#6b7280;font-size:.85rem;margin-bottom:24px;">Our team will be in touch with you shortly.</p>
						<button id="qb-close-success" class="site-button butn-bg-shape">Close</button>
					</div>
				</div>
			</div>

			<script>
			(function () {
				var overlay    = document.getElementById('qb-overlay');
				var panel      = document.getElementById('qb-panel');
				var tourNameEl = document.getElementById('qb-tour-name');
				var hiddenTour = document.getElementById('qb-hidden-tour');
				var hiddenType = document.getElementById('qb-hidden-type');
				var form       = document.getElementById('qb-form');
				var errorEl    = document.getElementById('qb-error');
				var formWrap   = document.getElementById('qb-form-wrap');
				var successEl  = document.getElementById('qb-success');
				var successMsg = document.getElementById('qb-success-msg');
				var dateInput  = document.getElementById('qb-date');
				var lastType   = 'book'; // track which submit button was clicked

				if (dateInput) dateInput.min = new Date().toISOString().split('T')[0];

				function getTourName(btn) {
					var slide = btn.closest('.swiper-slide');
					if (!slide) return 'Selected Tour';
					var h3 = slide.querySelector('h3');
					if (!h3) return 'Selected Tour';
					return h3.textContent.replace(/\s+/g, ' ').trim() || 'Selected Tour';
				}

				function resetButtons() {
					var b = document.getElementById('qb-btn-book');
					var e = document.getElementById('qb-btn-enquire');
					b.disabled = false;
					b.innerHTML = '<i class="fa fa-calendar-check" style="margin-right:7px;"></i>Book Now';
					e.disabled = false;
					e.innerHTML = '<i class="fa fa-envelope" style="margin-right:7px;"></i>Send Enquiry';
				}

				function openModal(tourName) {
					form.reset();
					hiddenTour.value = tourName;
					if (dateInput) dateInput.min = new Date().toISOString().split('T')[0];
					tourNameEl.textContent = tourName;
					errorEl.style.display = 'none';
					formWrap.style.display = 'block';
					successEl.style.display = 'none';
					resetButtons();
					lastType = 'book';
					panel.style.transform = 'translateY(20px)';
					panel.style.opacity = '0';
					overlay.style.display = 'flex';
					document.body.style.overflow = 'hidden';
					requestAnimationFrame(function () {
						requestAnimationFrame(function () {
							panel.style.transform = 'translateY(0)';
							panel.style.opacity = '1';
						});
					});
				}

				function closeModal() {
					panel.style.transform = 'translateY(20px)';
					panel.style.opacity = '0';
					setTimeout(function () {
						overlay.style.display = 'none';
						document.body.style.overflow = '';
					}, 350);
				}

				// ── Event delegation: catches original AND Swiper-cloned buttons ──
				document.addEventListener('click', function (e) {
					var btn = e.target.closest('.quick-book-btn');
					if (!btn) return;
					e.preventDefault();
					openModal(getTourName(btn));
				});

				// Track which submit button was last clicked
				document.getElementById('qb-btn-book').addEventListener('click', function () { lastType = 'book'; });
				document.getElementById('qb-btn-enquire').addEventListener('click', function () { lastType = 'enquire'; });

				// Close handlers
				document.getElementById('qb-close').addEventListener('click', closeModal);
				document.getElementById('qb-close-success').addEventListener('click', closeModal);
				overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });
				document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

				// Form submit
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					hiddenType.value = lastType;
					hiddenTour.value = tourNameEl.textContent;

					var bookBtn    = document.getElementById('qb-btn-book');
					var enquireBtn = document.getElementById('qb-btn-enquire');
					bookBtn.disabled = true;
					enquireBtn.disabled = true;
					(lastType === 'book' ? bookBtn : enquireBtn).textContent = 'Submitting…';
					errorEl.style.display = 'none';

					fetch('booking-handler.php', { method: 'POST', body: new FormData(form) })
						.then(function (r) { return r.json(); })
						.then(function (res) {
							if (res.success) {
								formWrap.style.display = 'none';
								successMsg.textContent = res.message;
								successEl.style.display = 'block';
							} else {
								errorEl.textContent = res.error || 'Something went wrong. Please try again.';
								errorEl.style.display = 'block';
								resetButtons();
							}
						})
						.catch(function () {
							errorEl.textContent = 'Network error. Please try again.';
							errorEl.style.display = 'block';
							resetButtons();
						});
				});
			})();
			</script>
			
			<!-- FOOTER START -->
<?php include 'footer.php'; ?>
