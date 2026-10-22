<?php
$page_title = 'Luxit Global Escapes - Madagascar';
$page_description = 'Discover Madagascar with Luxit Global Escapes: tailor-made itineraries, handpicked accommodation and expert local guides, with 24/7 support.';
$header_variant = 'solid';
include 'header.php';
?>
	<div id="smooth-wrapper">
		<div id="smooth-content">
			<!-- CONTENT START -->
			<div class="page-content">
				<!-- INNER PAGE BANNER -->
				<div class="relative bg-cover bg-center w-full bg-white overflow-hidden" style="background-image: url('assets/images/header-banners/destination-africa.png');">
						<div class="bg-black/60 absolute left-0 top-0 size-full"></div>
						<div class="inr-banner-height flex w-full lg:h-160 md:h-135 h-100 pb-10 items-baseline mx-auto">
							<div class="relative md:mt-60 mt-45 flex items-center justify-center w-full flex-col z-5">
								<div>
									<h2 class='lg:text-60 md:text-52 text-28 relative text-white'>Madagascar</h2>
								</div>
								<!-- BREADCRUMB ROW -->                            
								<div>
									<ul class="inline-block">
										<li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal">
											<a href="index.php" class="text-white">Home</a></li>
										<li class='relative inline-block text-base font-semibold text-white'>Madagascar</li>
									</ul>
								</div>
							</div>
							<!-- BREADCRUMB ROW END -->                        
						</div>
						<div class="h-50 w-full absolute top-50 left-0 z-1">            
							<div class="inline-block whitespace-nowrap animate-moveCloud">
								<img src="assets/destination/images/inr-banner-cloud.png" alt="Image" class="h-47.5">
							</div>
						</div>
						<div class="inr-plane-wrap absolute w-1/2 right-0 top-0 bottom-0 z-1">
							<div class="mt-60 animate-slide-right"><img src="assets/destination/images/airplane.png" alt="Image" class="animate-slide-top-fast" width="378" height="146"></div>
						</div>
						<div class="absolute right-11.25 bottom-16.25 animate-slide-top2"><img src="assets/destination/images/hotballon-Left.png" alt="Image" class="md:w-21 w-10" width="84" height="121"></div>
						<div class="inr-balloon-right absolute md:-right-15 -right-10 top-41.25 animate-slide-top"><img src="assets/destination/images/hotballon-right.png" alt="Image" class="md:w-37.5 w-20" width="230" height="333"></div>
					</div>
				<!-- INNER PAGE BANNER END -->
				<!-- MADAGASCAR CONTENT START -->
				<style>
					:root {
						--md-primary: #066168;
						--md-accent: #FFAA0D;
						--md-bg: #f8fafd;
						--md-radius: 60px;
						--md-shadow: 0 40px 80px rgba(6,97,104,0.12);
					}
					.md-full-section {
						width: 100%;
						background: var(--md-bg);
						padding: 100px 0;
						font-family: 'Figtree', sans-serif;
					}
					.md-wrap {
						max-width: 1280px;
						margin: 0 auto;
						padding: 0 32px;
					}
					.md-why-title {
						font-size: clamp(40px,5vw,72px);
						font-weight: 900;
						color: var(--md-primary);
						margin-bottom: 40px;
						line-height: 1;
						text-align: center;
					}
					.md-body {
						font-size: 18px;
						line-height: 1.85;
						color: rgba(0,0,0,.68);
						font-weight: 500;
						margin-bottom: 28px;
						text-align: justify;
					}
					.md-tours-label {
						display: inline-block;
						background: var(--md-accent);
						color: #fff;
						font-weight: 800;
						font-size: 14px;
						letter-spacing: 2px;
						text-transform: uppercase;
						padding: 8px 22px;
						border-radius: 30px;
						margin-bottom: 16px;
					}
					.md-section-title {
						font-size: clamp(32px,4vw,52px);
						font-weight: 900;
						color: var(--md-primary);
						text-align: center;
						line-height: 1.1;
						margin-bottom: 16px;
					}
					.md-section-sub {
						font-size: 18px;
						color: rgba(0,0,0,.5);
						text-align: center;
						font-weight: 600;
						margin-bottom: 60px;
					}
					.md-divider {
						border: 0;
						border-top: 2px solid #e8edf0;
						margin: 80px 0;
					}
					/* Tour cards overview grid */
					.md-tours-grid {
						display: grid;
						grid-template-columns: repeat(auto-fit, minmax(300px,1fr));
						gap: 32px;
						margin-bottom: 100px;
					}
					.md-tour-overview-card {
						background: #fff;
						border-radius: 32px;
						overflow: hidden;
						box-shadow: var(--md-shadow);
						border: 1px solid rgba(0,0,0,.05);
						display: flex;
						flex-direction: column;
					}
					.md-tour-overview-img {
						height: 220px;
						background: var(--md-primary);
						position: relative;
						overflow: hidden;
					}
					.md-tour-overview-img img {
						width: 100%;
						height: 100%;
						object-fit: cover;
					}
					.md-tour-overview-num {
						position: absolute;
						top: 16px;
						left: 20px;
						background: var(--md-accent);
						color: #fff;
						font-weight: 900;
						font-size: 13px;
						letter-spacing: 1px;
						padding: 6px 16px;
						border-radius: 20px;
					}
					.md-tour-overview-body {
						padding: 28px;
						flex: 1;
						display: flex;
						flex-direction: column;
					}
					.md-tour-overview-name {
						font-size: 22px;
						font-weight: 900;
						color: var(--md-primary);
						margin-bottom: 8px;
						line-height: 1.2;
					}
					.md-tour-overview-meta {
						font-size: 13px;
						font-weight: 700;
						color: var(--md-accent);
						text-transform: uppercase;
						letter-spacing: 1.5px;
						margin-bottom: 14px;
					}
					.md-tour-overview-best {
						font-size: 14px;
						color: rgba(0,0,0,.55);
						font-weight: 600;
						background: #f0f7f7;
						padding: 10px 16px;
						border-radius: 12px;
						margin-top: auto;
					}
					/* Detailed tour cards */
					.md-detail-card {
						background: #fff;
						border-radius: var(--md-radius);
						padding: 60px;
						margin-bottom: 60px;
						box-shadow: var(--md-shadow);
						border: 1px solid rgba(0,0,0,.04);
						position: relative;
						overflow: hidden;
					}
					.md-detail-card::before {
						content: attr(data-num);
						position: absolute;
						top: 30px;
						right: 50px;
						font-size: 130px;
						font-weight: 900;
						color: var(--md-accent);
						opacity: 0.07;
						line-height: 1;
						pointer-events: none;
					}
					.md-detail-header {
						border-bottom: 2px solid #eef1f3;
						padding-bottom: 32px;
						margin-bottom: 40px;
					}
					.md-detail-name {
						font-size: clamp(26px,3.5vw,42px);
						font-weight: 900;
						color: var(--md-primary);
						margin-bottom: 8px;
						line-height: 1.15;
					}
					.md-detail-meta {
						font-size: 15px;
						font-weight: 800;
						color: var(--md-accent);
						text-transform: uppercase;
						letter-spacing: 2px;
					}
					.md-best-tag {
						display: inline-block;
						background: #f0f7f7;
						color: var(--md-primary);
						padding: 10px 20px;
						border-radius: 14px;
						font-weight: 800;
						font-size: 15px;
						margin-bottom: 36px;
					}
					/* --- Highlights pills --- */
					.md-sec-title {
						font-size: 17px;
						font-weight: 900;
						color: var(--md-primary);
						display: flex;
						align-items: center;
						gap: 9px;
						margin-bottom: 18px;
					}
					.md-sec-title i { color: var(--md-accent); }
					.md-pills {
						display: flex;
						flex-wrap: wrap;
						gap: 10px;
						margin-bottom: 40px;
					}
					.md-pill {
						display: inline-flex;
						align-items: center;
						gap: 7px;
						background: #eef7f7;
						border: 1.5px solid rgba(6,97,104,0.14);
						color: var(--md-primary);
						padding: 9px 16px;
						border-radius: 50px;
						font-size: 13.5px;
						font-weight: 700;
						line-height: 1.3;
					}
					.md-pill::before {
						content: "\f00c";
						font-family: "Font Awesome 5 Free";
						font-weight: 900;
						color: var(--md-accent);
						font-size: 11px;
						flex-shrink: 0;
					}
					/* --- Itinerary day-card grid --- */
					.md-itin-grid {
						display: grid;
						grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
						gap: 14px;
						margin-bottom: 40px;
					}
					.md-itin-card {
						background: #f6fafb;
						border: 1.5px solid #e4eef0;
						border-radius: 20px;
						padding: 20px 18px;
						display: flex;
						flex-direction: column;
						gap: 10px;
					}
					.md-itin-card-head {
						display: flex;
						align-items: center;
						gap: 10px;
					}
					.md-itin-num {
						background: var(--md-accent);
						color: #fff;
						font-weight: 900;
						font-size: 12px;
						padding: 4px 10px;
						border-radius: 20px;
						white-space: nowrap;
						flex-shrink: 0;
					}
					.md-itin-card-title {
						font-size: 14px;
						font-weight: 900;
						color: var(--md-primary);
						line-height: 1.3;
					}
					.md-itin-card ul {
						list-style: disc;
						padding-left: 16px;
						margin: 0;
					}
					.md-itin-card ul li {
						font-size: 13px;
						color: rgba(0,0,0,.58);
						padding: 2px 0;
						line-height: 1.45;
					}
					.md-day-body ul {
						list-style: disc;
						padding-left: 18px;
						margin: 0;
					}
					.md-day-body ul li {
						font-size: 15px;
						color: rgba(0,0,0,.6);
						padding: 4px 0;
						line-height: 1.5;
					}
					.md-incl-grid {
						display: grid;
						grid-template-columns: 1fr 1fr;
						gap: 32px;
						background: #fafcfc;
						padding: 36px;
						border-radius: 36px;
						margin-top: 40px;
						border: 1px solid #eef1f3;
					}
					@media(max-width:640px){
						.md-incl-grid { grid-template-columns: 1fr; }
						.md-detail-card { padding: 32px 24px; }
						.md-detail-card::before { font-size: 80px; right: 20px; top: 16px; }
					}
					.md-incl-title {
						font-size: 16px;
						font-weight: 900;
						color: var(--md-primary);
						margin-bottom: 16px;
						display: flex;
						align-items: center;
						gap: 8px;
					}
					.md-incl-title i { color: var(--md-accent); }
					.md-incl-list {
						list-style: none;
						padding: 0;
						margin: 0;
					}
					.md-incl-list li {
						padding: 9px 0;
						border-bottom: 1px solid #f0f0f0;
						font-size: 14px;
						color: rgba(0,0,0,.6);
						display: flex;
						align-items: flex-start;
						gap: 10px;
						line-height: 1.4;
					}
					.md-incl-list li::before {
						content: "•";
						color: var(--md-accent);
						font-weight: 900;
						flex-shrink: 0;
					}
					.md-discover-section {
						background: linear-gradient(135deg,#f0f7f7 0%,#e8f4f5 100%);
						border-radius: 48px;
						padding: 70px 60px;
						margin-bottom: 80px;
						text-align: center;
					}
					@media(max-width:640px){
						.md-discover-section { padding: 40px 24px; border-radius: 28px; }
					}
					.md-cta-section {
						background: var(--md-primary);
						border-radius: var(--md-radius);
						padding: 90px 60px;
						text-align: center;
						color: #fff;
						margin-top: 60px;
						box-shadow: 0 40px 80px rgba(6,97,104,0.28);
					}
					@media(max-width:640px){
						.md-cta-section { padding: 50px 24px; border-radius: 32px; }
					}
					.md-cta-btn {
						display: inline-block;
						background: var(--md-accent);
						color: #fff;
						padding: 18px 48px;
						border-radius: 50px;
						font-weight: 900;
						font-size: 18px;
						text-decoration: none;
						margin-top: 40px;
						transition: transform .25s, box-shadow .25s;
						box-shadow: 0 8px 24px rgba(255,170,13,.4);
					}
					.md-cta-btn:hover {
						transform: translateY(-3px);
						box-shadow: 0 16px 40px rgba(255,170,13,.5);
					}
					.md-card-img {
						width: 100%;
						height: 320px;
						object-fit: cover;
						border-radius: 44px 44px 0 0;
						display: block;
						margin-bottom: 0;
					}
					@media(max-width:640px){
						.md-card-img { height: 220px; border-radius: 24px 24px 0 0; }
					}
					.md-card-has-img {
						padding-top: 0 !important;
						border-radius: var(--md-radius) !important;
						overflow: hidden;
					}
					.md-card-inner {
						padding: 50px 60px 50px;
					}
					@media(max-width:640px){
						.md-card-inner { padding: 28px 24px 28px; }
					}
					.md-book-row {
						display: flex;
						align-items: center;
						justify-content: space-between;
						flex-wrap: wrap;
						gap: 16px;
						margin-top: 40px;
						padding-top: 32px;
						border-top: 2px solid #eef1f3;
					}
					.md-book-btn {
						display: inline-flex;
						align-items: center;
						gap: 10px;
						background: var(--md-primary);
						color: #fff;
						padding: 16px 36px;
						border-radius: 50px;
						font-weight: 900;
						font-size: 16px;
						text-decoration: none;
						transition: background .25s, transform .25s, box-shadow .25s;
						box-shadow: 0 6px 20px rgba(6,97,104,0.3);
					}
					.md-book-btn:hover {
						background: var(--md-accent);
						transform: translateY(-2px);
						box-shadow: 0 12px 32px rgba(255,170,13,.4);
						color: #fff;
					}
					.md-enquire-btn {
						display: inline-flex;
						align-items: center;
						gap: 10px;
						background: transparent;
						color: var(--md-primary);
						padding: 15px 32px;
						border-radius: 50px;
						font-weight: 800;
						font-size: 16px;
						text-decoration: none;
						border: 2px solid var(--md-primary);
						transition: background .25s, color .25s;
					}
					.md-enquire-btn:hover {
						background: var(--md-primary);
						color: #fff;
					}
				</style>

				<div class="md-full-section">
					<div class="md-wrap">

						<!-- WHY VISIT MADAGASCAR -->
						<div style="max-width:900px;margin:0 auto 80px;text-align:center;">
							<span class="md-tours-label">Discover Madagascar</span>
							<h2 class="md-why-title">Why Visit Madagascar</h2>
							<p class="md-body">Floating in the Indian Ocean off the coast of East Africa, Madagascar is not just a destination — it is an entirely different world. Often referred to as the "eighth continent," Madagascar offers travelers a rare blend of untouched nature, unique wildlife, dramatic landscapes, vibrant culture, and idyllic beaches that cannot be found anywhere else on Earth.</p>
							<p class="md-body">For travelers seeking authentic adventure, extraordinary biodiversity, and unforgettable experiences away from mass tourism, Madagascar stands as one of Africa's most captivating destinations. From the iconic Avenue of the Baobabs and the surreal limestone forests of Tsingy de Bemaraha to lush rainforests echoing with the calls of lemurs and pristine islands fringed by turquoise waters, every corner of Madagascar feels like a discovery.</p>
							<p class="md-body">Madagascar is home to thousands of endemic species, meaning they exist nowhere else in the world. Visitors can encounter playful lemurs, colorful chameleons, rare birds, towering baobab trees, and breathtaking marine life while exploring national parks, tropical forests, and coastal paradises. Nature lovers, photographers, honeymooners, beach enthusiasts, and adventure travelers will all find something magical on this extraordinary island.</p>
							<p class="md-body">Beyond its wildlife, Madagascar offers rich cultural experiences shaped by African and Asian influences, warm hospitality, traditional villages, flavorful cuisine, and vibrant local traditions. Whether you dream of hiking through dramatic canyons, relaxing on untouched beaches in Nosy Be, whale watching along the coast, cruising through remote rivers, or immersing yourself in local Malagasy culture, Madagascar promises a journey unlike anywhere else in the world.</p>
							<p class="md-body">Unlike overcrowded tourist hotspots, Madagascar remains beautifully raw, authentic, and unspoiled — perfect for travelers searching for meaningful and immersive travel experiences. It is a destination where adventure meets luxury, where nature remains wild, and where every journey feels personal and transformative.</p>
							<p class="md-body">If you are looking for a destination filled with rare wildlife, stunning landscapes, unique experiences, and unforgettable memories, Madagascar should be at the very top of your Africa travel bucket list.</p>
						</div>

						<hr class="md-divider">

						<!-- POPULAR TOURS OVERVIEW -->
						<div style="text-align:center;margin-bottom:50px;">
							<span class="md-tours-label">Popular tours in Madagascar</span>
							<h2 class="md-section-title">Ultimate Madagascar Travel Guide</h2>
							<p class="md-section-sub">Best Places to Visit, Things to Do &amp; Luxury Tour Itineraries</p>
						</div>

						<div class="md-tours-grid">
							<!-- Card 1 -->
							<div class="md-tour-overview-card">
								<div class="md-tour-overview-img">
									<img src="assets/destination/images/tour/style2/pic1.jpg" alt="Nosy Be Luxury Island Escape" loading="lazy">
									<span class="md-tour-overview-num">Tour 01</span>
								</div>
								<div class="md-tour-overview-body">
									<div class="md-tour-overview-name">Nosy Be Luxury Island Escape</div>
									<div class="md-tour-overview-meta">5 Days / 4 Nights &mdash; Beaches, Marine Parks &amp; Island Culture</div>
									<div class="md-tour-overview-best"><strong>Best For:</strong> Luxury travelers, honeymooners, beach lovers, and island explorers.</div>
								</div>
							</div>
							<!-- Card 2 -->
							<div class="md-tour-overview-card">
								<div class="md-tour-overview-img">
									<img src="assets/destination/images/tour/style2/pic2.jpg" alt="Morondava & Baobab Adventure" loading="lazy">
									<span class="md-tour-overview-num">Tour 02</span>
								</div>
								<div class="md-tour-overview-body">
									<div class="md-tour-overview-name">Morondava &amp; Baobab Adventure</div>
									<div class="md-tour-overview-meta">5 Days / 4 Nights &mdash; Baobabs, Wildlife &amp; Cultural Discovery</div>
									<div class="md-tour-overview-best"><strong>Best For:</strong> Adventure travelers, wildlife lovers, photographers, and cultural explorers.</div>
								</div>
							</div>
							<!-- Card 3 -->
							<div class="md-tour-overview-card">
								<div class="md-tour-overview-img">
									<img src="assets/destination/images/tour/style2/pic3.jpg" alt="Masoala Rainforest & Eco-Adventure" loading="lazy">
									<span class="md-tour-overview-num">Tour 03</span>
								</div>
								<div class="md-tour-overview-body">
									<div class="md-tour-overview-name">Masoala Rainforest &amp; Eco-Adventure</div>
									<div class="md-tour-overview-meta">7 Days / 6 Nights &mdash; Rainforest, Marine Parks &amp; Rare Wildlife</div>
									<div class="md-tour-overview-best"><strong>Best For:</strong> Eco-tourists, wildlife enthusiasts, birdwatchers, and adventure seekers.</div>
								</div>
							</div>
						</div>

						<!-- DISCOVER THE MAGIC -->
						<div class="md-discover-section">
							<span class="md-tours-label" style="margin-bottom:24px;">Island Nation in the Indian Ocean</span>
							<h2 class="md-section-title" style="margin-bottom:24px;">Discover the Magic of Madagascar</h2>
							<p class="md-body" style="max-width:860px;margin:0 auto 20px;">Floating in the Indian Ocean off the coast of East Africa, Madagascar is not just a destination — it is an entirely different world. Often referred to as the "eighth continent," Madagascar offers travelers a rare blend of untouched nature, unique wildlife, dramatic landscapes, vibrant culture, and idyllic beaches that cannot be found anywhere else on Earth.</p>
							<p class="md-body" style="max-width:860px;margin:0 auto 20px;">For travelers seeking authentic adventure, extraordinary biodiversity, and unforgettable experiences away from mass tourism, Madagascar stands as one of Africa's most captivating destinations. From the iconic Avenue of the Baobabs and the surreal limestone forests of Tsingy de Bemaraha to lush rainforests echoing with the calls of lemurs and pristine islands fringed by turquoise waters, every corner of Madagascar feels like a discovery.</p>
							<p class="md-body" style="max-width:860px;margin:0 auto 20px;">Madagascar is home to thousands of endemic species, meaning they exist nowhere else in the world. Visitors can encounter playful lemurs, colorful chameleons, rare birds, towering baobab trees, and breathtaking marine life while exploring national parks, tropical forests, and coastal paradises.</p>
							<p class="md-body" style="max-width:860px;margin:0 auto 20px;">Whether you dream of hiking through dramatic canyons, relaxing on untouched beaches in Nosy Be, whale watching along the coast, cruising through remote rivers, or immersing yourself in local Malagasy culture, Madagascar promises a journey unlike anywhere else in the world.</p>
							<p class="md-body" style="max-width:860px;margin:0 auto 0;">Unlike overcrowded tourist hotspots, Madagascar remains beautifully raw, authentic, and unspoiled — perfect for travelers searching for meaningful and immersive travel experiences.</p>
						</div>

						<hr class="md-divider">

						<!-- POPULAR MADAGASCAR TOURS HEADING -->
						<div style="text-align:center;margin-bottom:70px;">
							<h2 class="md-section-title">Popular Madagascar Tours</h2>
						</div>

						<!-- TOUR 1: NOSY BE -->
						<div class="md-detail-card md-card-has-img" data-num="01">
							<img src="assets/destination/images/madagascar/nosybe.png" alt="Nosy Be Island Beach" class="md-card-img" loading="lazy">
							<div class="md-card-inner">
							<div class="md-detail-header">
								<h3 class="md-detail-name">Nosy Be Luxury Island Escape</h3>
								<span class="md-detail-meta">5 Days / 4 Nights &mdash; Beaches, Marine Parks &amp; Island Culture</span>
							</div>
							<div class="md-best-tag"><strong>Best For:</strong> Luxury travelers, honeymooners, beach lovers, and island explorers.</div>

							<div class="md-sec-title"><i class="fa fa-star"></i> Tour Highlights</div>
							<div class="md-pills">
								<span class="md-pill">Tropical paradise of Nosy Be</span>
								<span class="md-pill">Nosy Iranja's famous sandbank</span>
								<span class="md-pill">Lemurs in Nosy Komba</span>
								<span class="md-pill">Nosy Tanikely Marine Park snorkeling</span>
								<span class="md-pill">Local culture &amp; perfume plantations</span>
								<span class="md-pill">Sunset at Mont Passot</span>
							</div>

							<div class="md-sec-title"><i class="fa fa-map-marked-alt"></i> Detailed Itinerary</div>
							<div class="md-itin-grid">
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 1</span><div class="md-itin-card-title">Arrival in Nosy Be</div></div>
									<ul><li>Arrival at Fascene Airport</li><li>Meet &amp; greet service</li><li>Transfer to beach resort</li><li>Sunset relaxation by the Indian Ocean</li><li>Overnight in Nosy Be</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 2</span><div class="md-itin-card-title">Nosy Be Island Discovery Tour</div></div>
									<ul><li>Guided island tour</li><li>Visit local markets &amp; perfume plantations</li><li>Cultural immersion experience</li><li>Sunset excursion to Mont Passot</li><li>Overnight in Nosy Be</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 3</span><div class="md-itin-card-title">Nosy Iranja Excursion</div></div>
									<ul><li>Boat trip to Nosy Iranja</li><li>Walk across the white sandbank</li><li>Snorkeling and swimming</li><li>Seafood beach lunch</li><li>Return to Nosy Be</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 4</span><div class="md-itin-card-title">Nosy Komba &amp; Tanikely Marine Park</div></div>
									<ul><li>Visit Nosy Komba lemur reserve</li><li>Nosy Tanikely Marine Park</li><li>Snorkeling in coral reefs</li><li>Beach picnic lunch</li><li>Overnight in Nosy Be</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 5</span><div class="md-itin-card-title">Departure</div></div>
									<ul><li>Breakfast at leisure</li><li>Airport transfer for departure</li></ul>
								</div>
							</div>
							<div class="md-incl-grid">
								<div>
									<div class="md-incl-title"><i class="fa fa-plus-circle"></i> Tour Inclusions</div>
									<ul class="md-incl-list">
										<li>Accommodation on half-board basis</li>
										<li>Airport transfers</li>
										<li>Guided excursions mentioned in the itinerary</li>
										<li>Boat excursions and marine park visits</li>
										<li>Snorkeling activities</li>
										<li>Services of English-speaking guide</li>
										<li>Entrance fees to mentioned attractions</li>
									</ul>
								</div>
								<div>
									<div class="md-incl-title"><i class="fa fa-minus-circle"></i> Tour Exclusions</div>
									<ul class="md-incl-list">
										<li>International flights</li>
										<li>Madagascar visa fees</li>
										<li>Travel insurance</li>
										<li>Personal expenses</li>
										<li>Drinks and premium beverages</li>
										<li>Optional activities not mentioned</li>
										<li>Tips and gratuities</li>
									</ul>
								</div>
							</div>
							<div class="md-book-row">
								<div>
									<div style="font-size:13px;font-weight:700;color:rgba(0,0,0,.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:4px;">Ready to explore Nosy Be?</div>
									<div style="font-size:15px;font-weight:700;color:var(--md-primary);">5 Days &nbsp;|&nbsp; Luxury Island Experience</div>
								</div>
								<div style="display:flex;gap:12px;flex-wrap:wrap;">
									<button type="button" class="md-book-btn md-modal-trigger" data-tour="Nosy Be Luxury Island Escape" data-duration="5 Days / 4 Nights" data-type="book">
										<i class="fa fa-calendar-check"></i> Book Now
									</button>
									<button type="button" class="md-enquire-btn md-modal-trigger" data-tour="Nosy Be Luxury Island Escape" data-duration="5 Days / 4 Nights" data-type="enquire">
										<i class="fa fa-envelope"></i> Enquire
									</button>
								</div>
							</div>
							</div><!-- /.md-card-inner -->
						</div>

						<!-- TOUR 2: MORONDAVA -->
						<div class="md-detail-card md-card-has-img" data-num="02">
							<img src="assets/destination/images/madagascar/baobabs.png" alt="Avenue of the Baobabs Madagascar" class="md-card-img" loading="lazy">
							<div class="md-card-inner">
							<div class="md-detail-header">
								<h3 class="md-detail-name">Morondava &amp; Baobab Adventure</h3>
								<span class="md-detail-meta">5 Days / 4 Nights &mdash; Baobabs, Wildlife &amp; Cultural Discovery</span>
							</div>
							<div class="md-best-tag"><strong>Best For:</strong> Adventure travelers, wildlife lovers, photographers, and cultural explorers.</div>

							<div class="md-sec-title"><i class="fa fa-star"></i> Tour Highlights</div>
							<div class="md-pills">
								<span class="md-pill">Avenue of the Baobabs</span>
								<span class="md-pill">Kirindy Forest Reserve</span>
								<span class="md-pill">Lemurs, reptiles &amp; the famous fossa</span>
								<span class="md-pill">Belo-sur-Mer fishing village</span>
								<span class="md-pill">Spectacular Madagascar sunsets</span>
							</div>

							<div class="md-sec-title"><i class="fa fa-map-marked-alt"></i> Detailed Itinerary</div>
							<div class="md-itin-grid">
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 1</span><div class="md-itin-card-title">Arrival in Morondava</div></div>
									<ul><li>Airport arrival and transfer</li><li>Relaxation by the coast</li><li>Sunset walk in Morondava</li><li>Overnight stay</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 2</span><div class="md-itin-card-title">Kirindy Forest &amp; Night Safari</div></div>
									<ul><li>Drive to Kirindy Forest Reserve</li><li>Wildlife exploration</li><li>Spot lemurs, reptiles &amp; birds</li><li>Night walk for nocturnal wildlife</li><li>Overnight near Kirindy</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 3</span><div class="md-itin-card-title">Baobabs in Love &amp; Avenue of the Baobabs</div></div>
									<ul><li>Morning forest exploration</li><li>Return to Morondava</li><li>Visit Baobabs in Love</li><li>Sunset photography at the Baobabs</li><li>Overnight stay</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 4</span><div class="md-itin-card-title">Belo-sur-Mer Cultural Experience</div></div>
									<ul><li>Boat excursion to Belo-sur-Mer</li><li>Explore Vezo fisherman culture</li><li>Snorkeling and swimming</li><li>Visit local boatyards &amp; villages</li><li>Return to Morondava</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 5</span><div class="md-itin-card-title">Departure</div></div>
									<ul><li>Breakfast at the hotel</li><li>Airport transfer for onward flight</li></ul>
								</div>
							</div>
							<div class="md-incl-grid">
								<div>
									<div class="md-incl-title"><i class="fa fa-plus-circle"></i> Tour Inclusions</div>
									<ul class="md-incl-list">
										<li>Accommodation on half-board basis</li>
										<li>Domestic transfers mentioned in itinerary</li>
										<li>Guided forest and cultural excursions</li>
										<li>Park entrance fees</li>
										<li>Boat excursions</li>
										<li>Services of professional guide</li>
										<li>Sunset photography experiences</li>
									</ul>
								</div>
								<div>
									<div class="md-incl-title"><i class="fa fa-minus-circle"></i> Tour Exclusions</div>
									<ul class="md-incl-list">
										<li>International and domestic flights</li>
										<li>Visa fees</li>
										<li>Personal travel insurance</li>
										<li>Alcoholic beverages</li>
										<li>Optional activities</li>
										<li>Laundry and personal expenses</li>
										<li>Tips and gratuities</li>
									</ul>
								</div>
							</div>
							<div class="md-book-row">
								<div>
									<div style="font-size:13px;font-weight:700;color:rgba(0,0,0,.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:4px;">Explore the iconic Baobabs</div>
									<div style="font-size:15px;font-weight:700;color:var(--md-primary);">5 Days &nbsp;|&nbsp; Wildlife &amp; Cultural Discovery</div>
								</div>
								<div style="display:flex;gap:12px;flex-wrap:wrap;">
									<button type="button" class="md-book-btn md-modal-trigger" data-tour="Morondava & Baobab Adventure" data-duration="5 Days / 4 Nights" data-type="book">
										<i class="fa fa-calendar-check"></i> Book Now
									</button>
									<button type="button" class="md-enquire-btn md-modal-trigger" data-tour="Morondava & Baobab Adventure" data-duration="5 Days / 4 Nights" data-type="enquire">
										<i class="fa fa-envelope"></i> Enquire
									</button>
								</div>
							</div>
							</div><!-- /.md-card-inner -->
						</div>

						<!-- TOUR 3: MASOALA -->
						<div class="md-detail-card md-card-has-img" data-num="03">
							<img src="assets/destination/images/madagascar/lemur.png" alt="Masoala Rainforest Lemurs Madagascar" class="md-card-img" loading="lazy">
							<div class="md-card-inner">
							<div class="md-detail-header">
								<h3 class="md-detail-name">Masoala Rainforest &amp; Eco-Adventure</h3>
								<span class="md-detail-meta">7 Days / 6 Nights &mdash; Rainforest, Marine Parks &amp; Rare Wildlife</span>
							</div>
							<div class="md-best-tag"><strong>Best For:</strong> Eco-tourists, wildlife enthusiasts, birdwatchers, and adventure seekers.</div>

							<div class="md-sec-title"><i class="fa fa-star"></i> Tour Highlights</div>
							<div class="md-pills">
								<span class="md-pill">Masoala National Park</span>
								<span class="md-pill">Endemic lemurs, orchids &amp; tropical birds</span>
								<span class="md-pill">Fishing villages &amp; local communities</span>
								<span class="md-pill">Tampolo Marine Park snorkeling</span>
								<span class="md-pill">Night safari in Nosy Mangabe Reserve</span>
							</div>

							<div class="md-sec-title"><i class="fa fa-map-marked-alt"></i> Detailed Itinerary</div>
							<div class="md-itin-grid">
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 1</span><div class="md-itin-card-title">Arrival in Antananarivo</div></div>
									<ul><li>Meet &amp; greet service</li><li>Overnight stay in Antananarivo</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 2</span><div class="md-itin-card-title">Flight to Maroantsetra</div></div>
									<ul><li>Domestic flight to Maroantsetra</li><li>Guided city exploration</li><li>Cultural orientation experience</li><li>Overnight in Maroantsetra</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 3</span><div class="md-itin-card-title">Masoala National Park Expedition</div></div>
									<ul><li>Boat transfer to Masoala</li><li>Guided rainforest trekking</li><li>Wildlife exploration</li><li>Overnight eco-lodge</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 4</span><div class="md-itin-card-title">Tampolo Marine Park Adventure</div></div>
									<ul><li>Snorkeling &amp; marine exploration</li><li>Visit fishing villages</li><li>Cultural immersion</li><li>Overnight stay</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 5</span><div class="md-itin-card-title">Nosy Mangabe Reserve</div></div>
									<ul><li>Boat excursion to Nosy Mangabe</li><li>Guided wildlife exploration</li><li>Night safari for aye-aye lemurs</li><li>Overnight tented eco-camp</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 6</span><div class="md-itin-card-title">Return to Maroantsetra</div></div>
									<ul><li>Morning reserve exploration</li><li>Return by boat</li><li>Local market visit</li><li>Overnight stay</li></ul>
								</div>
								<div class="md-itin-card">
									<div class="md-itin-card-head"><span class="md-itin-num">Day 7</span><div class="md-itin-card-title">Departure</div></div>
									<ul><li>Flight back to Antananarivo</li><li>International departure assistance</li></ul>
								</div>
							</div>
							<div class="md-incl-grid">
								<div>
									<div class="md-incl-title"><i class="fa fa-plus-circle"></i> Tour Inclusions</div>
									<ul class="md-incl-list">
										<li>Accommodation on half-board basis</li>
										<li>Domestic airport transfers</li>
										<li>Boat transfers and excursions</li>
										<li>National park entrance fees</li>
										<li>Guided rainforest and marine activities</li>
										<li>English-speaking guide services</li>
										<li>Snorkeling activities</li>
									</ul>
								</div>
								<div>
									<div class="md-incl-title"><i class="fa fa-minus-circle"></i> Tour Exclusions</div>
									<ul class="md-incl-list">
										<li>International flights</li>
										<li>Madagascar visa fees</li>
										<li>Travel and medical insurance</li>
										<li>Alcoholic beverages and soft drinks</li>
										<li>Personal expenses</li>
										<li>Optional activities not listed</li>
										<li>Tips and porterage</li>
									</ul>
								</div>
							</div>
							<div class="md-book-row">
								<div>
									<div style="font-size:13px;font-weight:700;color:rgba(0,0,0,.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:4px;">Discover pristine rainforest wilderness</div>
									<div style="font-size:15px;font-weight:700;color:var(--md-primary);">7 Days &nbsp;|&nbsp; Rainforest &amp; Marine Eco-Adventure</div>
								</div>
								<div style="display:flex;gap:12px;flex-wrap:wrap;">
									<button type="button" class="md-book-btn md-modal-trigger" data-tour="Masoala Rainforest & Eco-Adventure" data-duration="7 Days / 6 Nights" data-type="book">
										<i class="fa fa-calendar-check"></i> Book Now
									</button>
									<button type="button" class="md-enquire-btn md-modal-trigger" data-tour="Masoala Rainforest & Eco-Adventure" data-duration="7 Days / 6 Nights" data-type="enquire">
										<i class="fa fa-envelope"></i> Enquire
									</button>
								</div>
							</div>
							</div><!-- /.md-card-inner -->
						</div>

						<!-- CLOSING CTA -->
						<div class="md-cta-section">
							<h3 style="font-size:clamp(26px,3.5vw,40px);font-weight:900;margin-bottom:28px;line-height:1.2;">Why Choose Madagascar for Your Next African Adventure?</h3>
							<p style="font-size:18px;line-height:1.85;opacity:.9;max-width:860px;margin:0 auto 20px;">Madagascar is one of the world's last truly untouched travel destinations. From luxury beach escapes and rainforest expeditions to wildlife safaris and authentic cultural encounters, the island offers unforgettable travel experiences unlike anywhere else on Earth.</p>
							<p style="font-size:18px;line-height:1.85;opacity:.9;max-width:860px;margin:0 auto;">Whether you are planning a honeymoon, luxury getaway, eco-tour, wildlife safari, family vacation, or adventure holiday, Madagascar delivers exceptional beauty, biodiversity, and authenticity.</p>
							<a href="contact.php" class="md-cta-btn">Inquire About Madagascar</a>
						</div>

					</div>
				</div>
				<!-- MADAGASCAR CONTENT END -->

			</div>
			<!-- CONTENT END -->

<?php ob_start(); ?>
<script>
(function($){
	var overlay   = $('#md-booking-overlay');
	var modal     = $('#md-booking-modal');
	var formView  = $('#md-form-view');
	var resultView= $('#md-result-view');
	var form      = $('#md-booking-form');

	// Minimum travel date = tomorrow
	var tomorrow = new Date();
	tomorrow.setDate(tomorrow.getDate() + 1);
	$('#md-travel-date').attr('min', tomorrow.toISOString().split('T')[0]);

	function openModal(tour, duration, type) {
		var isBook = (type === 'book');

		// Reset form
		form[0].reset();
		$('#md-form-error').hide().text('');
		formView.show();
		resultView.hide();
		$('#md-submit-btn').prop('disabled', false);

		// Fill tour name
		$('#md-tour-name').val(tour);
		$('#md-type-field').val(type);

		// Adapt labels & colours
		if (isBook) {
			$('#md-head-icon').attr('class', 'md-modal-head-icon book');
			$('#md-head-icon-i').attr('class', 'fa fa-calendar-check');
			$('#md-modal-title-text').text('Book Your Tour');
			$('#md-modal-subtitle').text(tour + ' — ' + duration);
			$('#md-message-label').text('Special Requests / Notes');
			$('#md-travelers-group').show();
			$('#md-submit-btn').attr('class', 'md-submit-btn book');
			$('#md-submit-icon').attr('class', 'fa fa-calendar-check');
			$('#md-submit-label').text('Confirm Booking');
		} else {
			$('#md-head-icon').attr('class', 'md-modal-head-icon enquire');
			$('#md-head-icon-i').attr('class', 'fa fa-envelope');
			$('#md-modal-title-text').text('Send an Enquiry');
			$('#md-modal-subtitle').text(tour + ' — ' + duration);
			$('#md-message-label').text('Your Message / Questions');
			$('#md-travelers-group').hide();
			$('#md-submit-btn').attr('class', 'md-submit-btn enquire');
			$('#md-submit-icon').attr('class', 'fa fa-paper-plane');
			$('#md-submit-label').text('Send Enquiry');
		}

		overlay.addClass('active');
		$('body').css('overflow', 'hidden');
		$('#md-full-name').focus();
	}

	function closeModal() {
		overlay.removeClass('active');
		$('body').css('overflow', '');
	}

	// Open on trigger buttons
	$(document).on('click', '.md-modal-trigger', function(){
		openModal(
			$(this).data('tour'),
			$(this).data('duration'),
			$(this).data('type')
		);
	});

	// Close handlers
	$('#md-close-btn, #md-cancel-btn, #md-result-close').on('click', closeModal);
	overlay.on('click', function(e){
		if ($(e.target).is(overlay)) closeModal();
	});
	$(document).on('keydown', function(e){
		if (e.key === 'Escape') closeModal();
	});

	// Form submit
	form.on('submit', function(e){
		e.preventDefault();
		var $btn = $('#md-submit-btn');
		var $err = $('#md-form-error');

		// Client-side validation
		var name  = $.trim($('#md-full-name').val());
		var email = $.trim($('#md-email').val());
		var tour  = $.trim($('#md-tour-name').val());

		if (!name || !email || !tour) {
			$err.text('Please fill in your full name and email address.').show();
			return;
		}
		var emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailRx.test(email)) {
			$err.text('Please enter a valid email address.').show();
			return;
		}
		$err.hide();

		// Loading state
		$btn.prop('disabled', true);
		$('#md-submit-label').text('Sending…');
		$('#md-submit-icon').attr('class', 'fa fa-spinner fa-spin');

		$.ajax({
			url: 'booking-handler.php',
			method: 'POST',
			data: form.serialize(),
			dataType: 'json',
			success: function(res) {
				formView.hide();
				resultView.show();

				if (res.success) {
					$('#md-result-icon').attr('class', 'md-result-icon success');
					$('#md-result-icon-i').attr('class', 'fa fa-check');
					$('#md-result-title').text(res.type === 'enquire' ? 'Enquiry Received!' : 'Booking Submitted!');
					$('#md-result-msg').text(res.message);
					if (res.booking_id && res.type === 'book') {
						$('#md-result-ref').text('Reference: ' + res.booking_id).show();
					} else {
						$('#md-result-ref').hide();
					}
					$('#md-result-close').attr('class', res.type === 'enquire' ? 'md-submit-btn enquire' : 'md-submit-btn book');
				} else {
					$('#md-result-icon').attr('class', 'md-result-icon error');
					$('#md-result-icon-i').attr('class', 'fa fa-times');
					$('#md-result-title').text('Something went wrong');
					$('#md-result-msg').text(res.error || 'Please try again or contact us directly.');
					$('#md-result-ref').hide();
					$('#md-result-close').attr('class', 'md-submit-btn book');
				}
			},
			error: function() {
				formView.hide();
				resultView.show();
				$('#md-result-icon').attr('class', 'md-result-icon error');
				$('#md-result-icon-i').attr('class', 'fa fa-times');
				$('#md-result-title').text('Connection Error');
				$('#md-result-msg').text('Could not reach the server. Please check your connection and try again.');
				$('#md-result-ref').hide();
			}
		});
	});

})(jQuery);
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include 'footer.php'; ?>
