<?php
// Footer links. Destinations and styles come from the same lists as the menu.
$ftDest = [['Africa', 'africa.php'], ['Asia', 'asia.php'], ['Middle East', 'middle-east.php'], ['Europe', 'europe.php'],
           ['Kenya', 'kenya.php'], ['Tanzania', 'tanzania.php'], ['Maldives', 'maldives.php'], ['Dubai', 'dubai.php']];
$ftTours = [['Safari Packages', 'safari-packages.php'], ['Local Packages', 'local-packages.php'], ['International Packages', 'international-packages.php'],
            ['Romantic Travel', 'romantic-travel.php'], ['Wellness Travel', 'wellness-travel.php'], ['Halal Travel', 'halal-travel.php'],
            ['MICE Travel', 'mice-travel.php']];
$ftCompany = [['About us', 'about.php'], ['All destinations', 'destinations.php'], ['Travel journal', 'blog.php'], ['Contact us', 'contact.php'],
              ['Plan a trip', 'contact.php?topic=Plan+a+trip']];
?>
			<footer class="ft" aria-labelledby="ft-heading">
				<h2 id="ft-heading" class="sr-only">Site footer</h2>

				<div class="ft-wrap ft-top">
					<div class="ft-news">
						<div>
							<span class="ft-eyebrow">Travel inspiration</span>
							<h3>Ideas for your next journey, once a month</h3>
							<p>New packages, seasonal offers and destination guides from our team. No spam; unsubscribe any time.</p>
						</div>
						<form class="ft-news-form" id="ft-news-form" novalidate>
							<input type="text" name="website" class="ft-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
							<label for="ft-email" class="sr-only">Email address</label>
							<div class="ft-news-row">
								<input type="email" name="email" id="ft-email" autocomplete="email" placeholder="Your email address" required maxlength="100">
								<button type="submit" id="ft-news-btn">Subscribe</button>
							</div>
							<p class="ft-news-msg" id="ft-news-msg" role="status" aria-live="polite"></p>
						</form>
					</div>
				</div>

				<div class="ft-wrap ft-main">
					<div class="ft-brand">
						<a href="index.php" class="ft-logo"><img src="assets/images/luxit-africa-logo.png" alt="Luxit Global Escapes" width="193" height="81" loading="lazy"></a>
						<p>Tailor-made safaris, island escapes and journeys worldwide, planned by a travel team based in Nairobi, Kenya.</p>
						<ul class="ft-contact">
							<li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:+254737800900">+254 737 800 900</a></li>
							<li><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><a href="https://wa.me/254737800900" target="_blank" rel="noopener">WhatsApp us</a></li>
							<li><i class="fa-solid fa-envelope" aria-hidden="true"></i><a href="mailto:info@luxitglobalescapes.com">info@luxitglobalescapes.com</a></li>
							<li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>Tana House, Karen, Nairobi, Kenya</span></li>
						</ul>
					</div>

					<nav class="ft-col" aria-label="Destinations">
						<h3>Destinations</h3>
						<ul><?php foreach ($ftDest as [$t, $u]) : ?><li><a href="<?php echo $u; ?>"><?php echo $t; ?></a></li><?php endforeach; ?></ul>
					</nav>
					<nav class="ft-col" aria-label="Tours">
						<h3>Tours</h3>
						<ul><?php foreach ($ftTours as [$t, $u]) : ?><li><a href="<?php echo $u; ?>"><?php echo $t; ?></a></li><?php endforeach; ?></ul>
					</nav>
					<nav class="ft-col" aria-label="Company">
						<h3>Company</h3>
						<ul><?php foreach ($ftCompany as [$t, $u]) : ?><li><a href="<?php echo $u; ?>"><?php echo $t; ?></a></li><?php endforeach; ?></ul>
					</nav>

					<div class="ft-col ft-plan">
						<h3>Plan with us</h3>
						<p>Tell us where and when; a specialist replies within 24 hours. Quotes are free.</p>
						<a class="ft-btn" href="contact.php?topic=Plan+a+trip">Start planning <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
						<div class="ft-social" aria-label="Follow us">
							<a href="https://www.facebook.com/luxitafrica" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
							<a href="https://x.com/Luxitafrica" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
							<a href="https://www.instagram.com/p/DIoeX4NuF4S/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
							<a href="https://www.tiktok.com/@luxitglobalescapes" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
						</div>
					</div>
				</div>

				<div class="ft-bottom">
					<div class="ft-wrap ft-bottom-row">
						<p>&copy; <?php echo date('Y'); ?> Luxit Global Escapes. All rights reserved.</p>
						<p class="ft-bottom-links"><a href="contact.php">Contact</a><a href="destinations.php">Destinations</a><a href="blog.php">Journal</a></p>
					</div>
				</div>
			</footer>
			<!-- FOOTER END -->
		</div>
	</div>
	
	<!-- BUTTON TOP START -->
	<button class="scroltop" aria-label="Scroll to top"><span class="fa fa-angle-up  relative" id="btn-vibrate" aria-hidden="true"></span></button>
	<div id="offcanvas-right" class="fixed top-0 right-0 z-9999999 h-screen overflow-y-auto transition-transform translate-x-full bg-black/20 backdrop-blur-[5px] sm:max-w-125 max-w-88 custom-scroll">
		<button type="button" class="absolute right-0 top-6 text-[#443935] bg-citrusyellow text-base size-7 cursor-pointer rounded m-6.25" data-drawer-hide="offcanvas-right" aria-label="Close">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>
		<div class="pt-30 pb-10 px-8">
			<div class="">
				<div class="mb-15">
					<img src="assets/images/luxit-africa-logo.png" alt="Image" class="max-w-33.5">
				</div>
				<div class="trv-side-pnl-content">
					<div class="mb-15">
						<h3 class="text-28 !text-white mb-5">It's Time to Traveling</h3>
						<h3 class="!text-secondary text-5xl mb-5 uppercase">Plan Your Next Holiday</h3>
						<p class="text-base !text-white/50">Luxit Global Escapes is a premier luxury tour operator specializing in bespoke safari packages, curated international journeys, and custom-tailored travel experiences across the globe.</p>   
					</div>
					<h3 class="text-28 !text-white mb-5">Don't miss out on these offers!</h3>
					<ul class="flex justify-between pb-10 mb-10 relative after:w-75 after:bg-white/15 after:h-px after:absolute after:left-1/2 after:bottom-0 after:-translate-x-1/2">
						<li>
							<h2 class="!text-secondary text-34 mb-2">$199</h2>
							<span class="!text-white text-lg font-medium font-title">Basic Plan</span>
						</li>
						<li>
							<h2 class="!text-secondary text-34 mb-2">$299</h2>
							<span class="!text-white text-lg font-medium font-title">Pro Plan</span>
						</li>
						<li>
							<h2 class="!text-secondary text-34 mb-2">$399</h2>
							<span class="!text-white text-lg font-medium font-title">Full Plan</span>
						</li>
					</ul>
					<ul class="mt-5">
						<li class="inline-flex size-11.5 bg-citrusyellow mr-2.5 rounded-4xl justify-center items-center duration-500 hover:rounded-2lg group">
							<a class="inline-flex size-9 bg-white rounded-full justify-center items-center duration-500 text-primary text-lg" href="https://x.com/Luxitafrica/status/1915682543342387701">
								<i class="fa-brands fa-x-twitter group-hover:rotate-y-[360deg] group-hover:scale-[1.2] !inline-block duration-[0.5s] group-hover:text-citrusyellow"></i>
							</a>
						</li>
						<li class="inline-flex size-11.5 bg-citrusyellow mr-2.5 rounded-4xl justify-center items-center duration-500 hover:rounded-2lg group">
							<a class="inline-flex size-9 bg-white rounded-full justify-center items-center duration-500 text-primary text-lg" href="https://www.facebook.com/luxitafrica">
								<i class="fa-brands fa-facebook group-hover:rotate-y-[360deg] group-hover:scale-[1.2] !inline-block duration-[0.5s] group-hover:text-citrusyellow"></i>
							</a>
						</li>
						<li class="inline-flex size-11.5 bg-citrusyellow mr-2.5 rounded-4xl justify-center items-center duration-500 hover:rounded-2lg group">
							<a class="inline-flex size-9 bg-white rounded-full justify-center items-center duration-500 text-primary text-lg" href="https://www.instagram.com/p/DIoeX4NuF4S/">
								<i class="fa-brands fa-instagram group-hover:rotate-y-[360deg] group-hover:scale-[1.2] !inline-block duration-[0.5s] group-hover:text-citrusyellow"></i>
							</a>
						</li>
						<li class="inline-flex size-11.5 bg-citrusyellow mr-2.5 rounded-4xl justify-center items-center duration-500 hover:rounded-2lg group">
							<a class="inline-flex size-9 bg-white rounded-full justify-center items-center duration-500 text-primary text-lg" href="https://www.tiktok.com/@luxitglobalescapes">
								<i class="fa-brands fa-tiktok group-hover:rotate-y-[360deg] group-hover:scale-[1.2] !inline-block duration-[0.5s] group-hover:text-citrusyellow"></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
    
<!-- JAVASCRIPT  FILES ========================================= --> 
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/vendor/gsap/gsap.min.js"></script>
<script src="assets/vendor/gsap/ScrollSmoother.js"></script>
<script src="assets/vendor/gsap/ScrollTrigger.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/masonry/masonry-4.2.2.js"></script>
<script src="assets/vendor/masonry/isotope.pkgd.min.js"></script>
<script src="assets/vendor/flatpickr/js/flatpickr.js"></script>
<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="assets/vendor/nouislider/nouislider.min.js"></script>
<script src="assets/vendor/group-slide/group-loop.js"></script>
<script src="assets/vendor/xmenu/xmenu.js"></script>
<script src="assets/vendor/lightgallery/dist/lightgallery.min.js"></script>
<script src="assets/vendor/lightgallery/dist/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="assets/vendor/lightgallery/dist/plugins/zoom/lg-zoom.min.js"></script>
<script src="assets/js/imagesloaded.pkgd.min.js"></script>
<script src="assets/js/theia-sticky-sidebar.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/isotope.pkgd.min.js"></script>
<script src="assets/js/dz.carousel.js"></script>
<script src="assets/js/lc_lightbox.lite.js"></script>
<script src="assets/js/dz.ajax.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/menu.js"></script>
<?php
// Page-specific scripts, printed after the shared libraries they rely on.
if (!empty($page_scripts)) echo $page_scripts;
?>
<script>
// Footer newsletter (newsletter-handler.php). Replaces the template's
// mailchamp.php form, which posted to a file that never existed.
(function () {
	var form = document.getElementById('ft-news-form');
	if (!form) return;
	var msg = document.getElementById('ft-news-msg');
	var btn = document.getElementById('ft-news-btn');
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var email = form.email.value.trim();
		msg.className = 'ft-news-msg';
		if (!/^\S+@\S+\.\S+$/.test(email)) { msg.textContent = 'Please enter a valid email address.'; msg.classList.add('is-error'); form.email.focus(); return; }
		btn.disabled = true; btn.textContent = 'Subscribing…';
		var data = new FormData(form);
		data.append('source', location.pathname.split('/').pop() || 'index.php');
		fetch('newsletter-handler.php', { method: 'POST', body: data })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				msg.textContent = res.success ? res.message : (res.error || 'Something went wrong. Please try again.');
				msg.classList.add(res.success ? 'is-ok' : 'is-error');
				if (res.success) form.reset();
			})
			.catch(function () { msg.textContent = 'We could not reach our server. Please try again.'; msg.classList.add('is-error'); })
			.then(function () { btn.disabled = false; btn.textContent = 'Subscribe'; });
	});
})();
</script>

</body></html>
