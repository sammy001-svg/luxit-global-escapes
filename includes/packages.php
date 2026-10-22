<?php
/**
 * Shared package (tour) queries for the public site.
 *
 * Every public listing goes through here so the admin panel's "Package Pages"
 * checkboxes are the single source of truth for what appears where. Previously
 * each page guessed for itself — international-packages.php listed every active
 * tour, local-packages.php pattern-matched 18 hardcoded location keywords, and
 * safari-packages.php keyed off the category — which meant a package could
 * appear on the wrong page or on none at all with no way to tell.
 */

if (!function_exists('getPackagesForPage')) {
    /**
     * Active packages assigned to a given public page.
     *
     * package_pages holds a comma-separated list (e.g. "Safari,Local"), so the
     * match is done with FIND_IN_SET rather than LIKE — LIKE '%Local%' would
     * also match a hypothetical "LocalPlus" value.
     *
     * @param string $page One of International, Local, Safari.
     */
    function getPackagesForPage(PDO $pdo, string $page, ?int $limit = null): array {
        try {
            $sql = "SELECT * FROM tours
                    WHERE status = 'Active'
                      AND FIND_IN_SET(?, package_pages) > 0
                    ORDER BY created_at DESC";
            if ($limit !== null) $sql .= " LIMIT " . (int)$limit;

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$page]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Most likely cause: package_pages does not exist yet because
            // migrate.php has not been run against this database.
            error_log('getPackagesForPage failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('getHomepagePackages')) {
    /**
     * Packages the admin has flagged for a homepage section.
     *
     * @param string $section 'Explore Popular Tours' or 'Marketing Highlights'.
     */
    function getHomepagePackages(PDO $pdo, string $section, int $limit = 8): array {
        try {
            $stmt = $pdo->prepare(
                "SELECT * FROM tours
                 WHERE status = 'Active'
                   AND show_on_home = 1
                   AND home_section = ?
                 ORDER BY created_at DESC
                 LIMIT " . (int)$limit
            );
            $stmt->execute([$section]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getHomepagePackages failed: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('packageImagePath')) {
    /**
     * Image path for a package, falling back when the admin left the image
     * empty or the uploaded file has since gone missing: a broken image
     * collapses the card and throws the whole carousel row out of line.
     * Unescaped; use packageImage() when echoing into HTML.
     */
    function packageImagePath(array $tour, string $fallback = 'assets/images/tour/style1/pic1.jpg'): string {
        $img = trim((string)($tour['image'] ?? ''));
        if ($img === '' || !is_file(__DIR__ . '/../' . $img)) {
            $img = $fallback;
        }
        return $img;
    }
}

if (!function_exists('packageImage')) {
    /** HTML-escaped packageImagePath(), ready for a src attribute. */
    function packageImage(array $tour, string $fallback = 'assets/images/tour/style1/pic1.jpg'): string {
        return htmlspecialchars(packageImagePath($tour, $fallback));
    }
}

if (!function_exists('packageExcerpt')) {
    /**
     * Plain-text excerpt cut on a word boundary. Multibyte-safe, unlike the
     * substr() calls it replaces, which could split an accented character.
     */
    function packageExcerpt(?string $text, int $max = 100): string {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$text)));
        if (mb_strlen($text) <= $max) return $text;
        $cut = mb_substr($text, 0, $max);
        $space = mb_strrpos($cut, ' ');
        if ($space !== false && $space > $max * 0.6) $cut = mb_substr($cut, 0, $space);
        return rtrim($cut, " ,.;:-") . '…';
    }
}

if (!function_exists('renderPackageCard')) {
    /**
     * Swiper slide for the International / Local / Safari listing pages.
     *
     * One renderer for all three pages so a package looks the same wherever
     * it is posted. Every admin-supplied field is clamped (see
     * assets/css/packages.css), which keeps cards the same height no matter how
     * long the title, location or description is.
     */
    function renderPackageCard(array $tour, string $fallbackImage): void {
        $link     = 'tour-detail.php?id=' . (int)$tour['id'];
        $title    = htmlspecialchars($tour['title']);
        $location = htmlspecialchars($tour['location'] ?? '');
        $duration = htmlspecialchars(trim((string)($tour['duration'] ?? '')));
        $price    = (float)($tour['price'] ?? 0);
        $rating   = (float)($tour['rating'] ?? 0);
        $excerpt  = htmlspecialchars(packageExcerpt($tour['description'] ?? '', 100));
        $img      = packageImage($tour, $fallbackImage);
        ?>
									<div class="swiper-slide pkg-slide">
										<div class="mx-3.75 pkg-card">
											<div class="rounded-tl-3xl rounded-tr-3xl overflow-hidden relative">
												<a href="<?php echo $link; ?>"><img src="<?php echo $img; ?>" alt="<?php echo $title; ?>" class="w-full object-cover object-center pkg-listing-img" width="309" height="500" loading="lazy"></a>
												<?php if ($duration !== '') : ?>
												<div class="absolute top-7.5 left-0 py-2 px-4 bg-primary text-white font-semibold text-xs rounded-tr-5xl rounded-br-5xl pkg-badge"><?php echo $duration; ?></div>
												<?php endif; ?>
												<div class="absolute bottom-0 left-0 right-0 bg-caribbeanlight backdrop-blur pkg-overlay">
													<h3 class="text-xl font-medium text-white pkg-clamp-1" title="<?php echo $title; ?>"><?php echo $title; ?></h3>
												</div>
											</div>
											<div class="bg-white rounded-bl-3xl rounded-br-3xl shadow-[0px_10px_20px_rgba(0,106,114,0.1)] pkg-card-body pkg-listing-body">
												<div class="mb-4 flex items-center justify-between gap-3">
													<div class="pkg-min-0">
														<?php if ($price > 0) : ?>
														<span class="text-citrusyellow text-2xl font-black block">$<?php echo number_format($price); ?></span>
														<?php else : ?>
														<span class="text-citrusyellow text-2xl font-black block">On Request</span>
														<?php endif; ?>
														<span class="text-xs block pkg-clamp-1"><?php echo $location !== '' ? $location : 'Per Person'; ?></span>
													</div>
													<?php if ($rating > 0) : ?>
													<div class="text-citrusyellow text-sm pkg-nowrap">
														<i class="fa fa-star"></i> <?php echo number_format($rating, 1); ?>
													</div>
													<?php endif; ?>
												</div>
												<p class="text-sm mb-4 pkg-clamp-2 pkg-excerpt"><?php echo $excerpt; ?></p>
												<a href="<?php echo $link; ?>" class="site-button outline !py-2 !px-4 !text-sm pkg-cta">Book Now</a>
											</div>
										</div>
									</div>
        <?php
    }
}

if (!function_exists('packagePromoClasses')) {
    /**
     * CSS classes for a promo badge colour chosen in the admin panel.
     * Kept server-side so the badge cannot be styled with arbitrary input.
     */
    function packagePromoClasses(?string $style): string {
        // Styled in assets/css/packages.css: the compiled Tailwind build has
        // no bg-red-600, so the "Red" option used to render with no background.
        $allowed = ['primary', 'secondary', 'citrus', 'red'];
        $style   = in_array($style, $allowed, true) ? $style : 'primary';
        return 'pkg-promo-badge pkg-promo-badge--' . $style;
    }
}
