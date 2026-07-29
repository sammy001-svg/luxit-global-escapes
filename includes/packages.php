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

if (!function_exists('packagePromoClasses')) {
    /**
     * Tailwind classes for a promo badge colour chosen in the admin panel.
     * Kept server-side so the badge cannot be styled with arbitrary input.
     */
    function packagePromoClasses(?string $style): string {
        switch ($style) {
            case 'secondary': return 'bg-secondary text-primary';
            case 'citrus':    return 'bg-citrusyellow text-primary';
            case 'red':       return 'bg-red-600 text-white';
            case 'primary':
            default:          return 'bg-primary text-white';
        }
    }
}
