<?php include 'includes/header.php'; ?>

	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">
				<!-- Banner Style One -->
				<div class="trv-header-carousel swiper relative overflow-hidden">
					<div class="swiper-wrapper">
						<?php
						$stmt = $pdo->query("SELECT * FROM tours WHERE show_on_home = 1 AND home_section = 'Main Header Slider'");
						while($tour = $stmt->fetch()):
						?>
						<div class="swiper-slide">
							<div class="h-170 sm:h-192 2xl:h-225 3xl:h-237.5 relative">
								<img src="<?php echo $tour['image']; ?>" alt="<?php echo $tour['title']; ?>" class="absolute inset-0 object-cover w-full h-full">
								<div class="absolute inset-0 flex items-center" style="background-color: rgba(0,0,0,0.6);">
									<div class="container mx-auto px-5 lg:px-20">
										<div class="max-w-3xl mx-auto text-center">
											<span class="text-white text-2xl lg:text-4xl font-display block mb-4"><?php echo $tour['category']; ?></span>
											<h1 class="text-white text-5xl lg:text-8xl font-display mb-6" style="color: white !important;"><?php echo $tour['title']; ?></h1>
											<p class="text-white text-lg lg:text-2xl mb-8"><?php echo $tour['description']; ?></p>
											<div class="flex gap-4 justify-center">
												<a href="tour-detail.php?id=<?php echo $tour['id']; ?>" class="site-button butn-bg-shape">View Details</a>
												<a href="contact.php" class="site-button outline !text-white !border-white">Get In Touch</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php endwhile; ?>
					</div>
					<div class="swiper-pagination"></div>
				</div>
				
				<!-- SEARCH BAR START-->
				<div class="bg-lightturquoise xl:pt-17.5 pt-12.5 px-5">
					<div class="max-w-250 mx-auto lg:h-25 p-1.75 bg-paleaqua lg:rounded-25xl rounded-2xl">
						<div class="bg-white lg:rounded-25xl rounded-2xl sm:pt-3 sm:pr-3.25 sm:pb-2.25 sm:pl-10 p-5 h-full">
							<form action="search-results.php" method="GET">
								<div class="flex justify-between items-center max-lg:flex-wrap max-sm:flex-col">
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="custom-select style-1">
											<label class="pb-1.25 flex items-center">
												<i class="inline-block mr-5"><img src="assets/images/search-icon/icon1.png" alt="Location" class="h-5 w-full"></i>Location
											</label>
											<select name="location" class="dynamic-select">
												<option value="">All Locations</option>
												<?php
												$locs = $pdo->query("SELECT DISTINCT name FROM destinations ORDER BY name ASC");
												while($loc = $locs->fetch()) {
													echo "<option value='{$loc['name']}'>{$loc['name']}</option>";
												}
												?>
											</select>
										</div>
									</div>
									<div class="lg:w-42.5 sm:w-[48%] w-full max-sm:mb-5 max-lg:mb-5 max-lg:border-b border-paleaqua">
										<div class="custom-select style-1">
											<label class="pb-1.25 flex items-center">
												<i class="inline-block mr-5"><img src="assets/images/search-icon/icon2.png" alt="Activity" class="h-5 w-full"></i>Activity
											</label>
											<select name="category" class="dynamic-select">
												<option value="">All Types</option>
												<option value="Safari">Safari</option>
												<option value="Luxury">Luxury</option>
												<option value="Adventure">Adventure</option>
											</select>
										</div>
									</div>
									<div class="lg:w-auto w-full">
										<button type="submit" class="text-28 text-white rounded-full size-15 bg-primary max-lg:!w-full duration-500 cursor-pointer"><i class="fa-solid fa-magnifying-glass"></i></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<!--EXPLORE POPULAR TOUR START-->
				<div class="bg-lightturquoise sm:mx-15 xl:pb-26.5 pb-5 xl:pt-30 pt-12.5">
					<div class="container-fluid">
						<div class="text-center max-w-150 mx-auto md:mb-15 mb-7.5">
							<h2 class="xl:text-46 md:text-40 text-3xl mb-2.5">Explore Popular<span class="text-citrusyellow"> Tours!</span></h2>
							<p class="text-base">Destinations worth exploring! Here are a few popular spots</p>
							<div class="-mt-7"><img src="assets/images/background/Title-Separator.png" alt="Separator" class="w-117.5 inline-block"></div>
						</div>
						<div>
							<div class="swiper trv-tours-st1 xl:!pb-29 !pb-22.5">
								<div class="swiper-wrapper">
									<?php
									$stmt = $pdo->query("SELECT * FROM tours WHERE show_on_home = 1 AND home_section = 'Explore Popular Tours' ORDER BY id DESC");
									while($tour = $stmt->fetch()):
									?>
									<div class="swiper-slide">
										<div class="mx-3.75">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="tour-detail.php?id=<?php echo $tour['id']; ?>"><img src="<?php echo $tour['image']; ?>" alt="<?php echo $tour['title']; ?>" class="xl:h-105 h-80 w-full object-cover"></a>
												<div class="absolute top-7.5 left-0 py-2.5 px-5 bg-primary text-white font-semibold text-sm rounded-tr-5xl rounded-br-5xl flex items-center">
													<i class="text-xl mr-2.5 fa-regular fa-calendar-days"></i>
													<span><?php echo $tour['duration']; ?></span>
												</div>
												<div class="absolute bottom-0 left-0 right-0 py-3.75 px-7.5 bg-caribbeanlight backdrop-blur">
													<h3 class="2xl:text-28 text-2xl font-medium text-white"><i class="fa-solid fa-location-dot mr-2"></i><?php echo $tour['location']; ?></h3>
												</div>
											</div>
											<div class="bg-white p-7.5 rounded-bl-3xl rounded-br-3xl shadow-lg">
												<div class="mb-7.5 flex">
													<div class="w-20">
														<span class="text-citrusyellow text-28 font-black block">$<?php echo number_format($tour['price'] / 8, 0); ?></span>
														<span class="text-base block">Per Day</span>
													</div>
													<div class="w-[calc(100%_-_90px)] text-xl font-title font-medium">
														<a href="tour-detail.php?id=<?php echo $tour['id']; ?>" class="text-primary hover:text-citrusyellow duration-500"><?php echo $tour['title']; ?></a>
													</div>
												</div>
												<div class="flex items-center justify-between">
													<a href="tour-detail.php?id=<?php echo $tour['id']; ?>" class="site-button outline">Book Now</a>
													<div>
														<span>(<?php echo $tour['rating']; ?> Review)</span>
														<div class="text-citrusyellow">
															<?php for($i=0; $i<5; $i++) echo ($i < floor($tour['rating'])) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php endwhile; ?>
								</div>
								<div class="swiper-button-next"></div>
								<div class="swiper-button-prev"></div>
							</div>
						</div> 
					</div>
				</div>

				<!-- RECOMMEND SECTION -->
				<div class="bg-paleaqua lg:pt-30 pb-30">
					<div class="container">
						<div class="grid grid-cols-12 gap-8">
							<div class="lg:col-span-6 col-span-12">
								<h2 class="xl:text-46 text-3xl mb-6">We <span class="text-citrusyellow">Recommend</span> Beautiful Destinations</h2>
								<p class="text-lg mb-8">Discover curated experiences designed to help you see the world differently. From safaris to luxury escapes.</p>
								<div class="grid grid-cols-2 gap-4">
									<div class="bg-white p-6 rounded-3xl text-center shadow-md">
										<span class="text-4xl text-primary font-black block">24/7</span>
										<span class="text-slate-500 font-medium">Support</span>
									</div>
									<div class="bg-white p-6 rounded-3xl text-center shadow-md">
										<span class="text-4xl text-secondary font-black block">100+</span>
										<span class="text-slate-500 font-medium">Packages</span>
									</div>
								</div>
							</div>
							<div class="lg:col-span-6 col-span-12">
								<img src="assets/images/we-rec-pic.png" alt="Recommend" class="w-full">
							</div>
						</div>
					</div>
				</div>
			</div>
<?php include 'includes/footer.php'; ?>
