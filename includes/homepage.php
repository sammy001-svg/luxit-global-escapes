<?php
/**
 * Homepage package sections, shared by index.php and the admin Homepage tab.
 *
 * A package appears in a section when its home_sections list contains the
 * section key (admin: Homepage tab, or the package form). A whole section can
 * be switched off in the Homepage tab (stored in site_settings).
 */

const HOME_SECTIONS = [
    'safari' => [
        'title'    => 'Safari Packages',
        'eyebrow'  => 'Into the wild',
        'intro'    => 'Game drives, private conservancies and nights under canvas across East and Southern Africa.',
        'link'     => 'safari-packages.php',
        'page'     => 'Safari',        // package_pages value a package must have to be eligible
    ],
    'local' => [
        'title'    => 'Local Packages',
        'eyebrow'  => 'Kenya & East Africa',
        'intro'    => 'Weekend escapes, coast holidays and safaris close to home, planned by people who live here.',
        'link'     => 'local-packages.php',
        'page'     => 'Local',
    ],
    'international' => [
        'title'    => 'International Packages',
        'eyebrow'  => 'Around the world',
        'intro'    => 'Islands, great cities and journeys abroad, with flights, transfers and hotels taken care of.',
        'link'     => 'international-packages.php',
        'page'     => 'International',
    ],
    'offers' => [
        'title'    => 'Special Offers',
        'eyebrow'  => 'Limited time',
        'intro'    => 'Seasonal deals and early-booking offers chosen by our team.',
        'link'     => 'international-packages.php',
        'page'     => null,            // any package can be an offer
    ],
];

const HOME_SECTION_LIMIT = 6;

if (!function_exists('homeSettings')) {
    /** All site settings as name => value; empty if the table is missing. */
    function homeSettings(PDO $pdo): array {
        static $cache = null;
        if ($cache !== null) return $cache;
        try {
            $cache = $pdo->query("SELECT name, value FROM site_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            $cache = [];
        }
        return $cache;
    }
}

if (!function_exists('homeSectionEnabled')) {
    /** Sections are on unless the admin switched them off. */
    function homeSectionEnabled(PDO $pdo, string $key): bool {
        return (homeSettings($pdo)["home_{$key}_enabled"] ?? '1') !== '0';
    }
}

if (!function_exists('homeSectionPackages')) {
    /** Active packages selected for a homepage section, in the admin's order. */
    function homeSectionPackages(PDO $pdo, string $key, int $limit = HOME_SECTION_LIMIT): array {
        try {
            $st = $pdo->prepare(
                "SELECT * FROM tours
                 WHERE status = 'Active' AND FIND_IN_SET(?, home_sections) > 0
                 ORDER BY home_order ASC, created_at DESC
                 LIMIT " . (int)$limit
            );
            $st->execute([$key]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // home_sections arrives with migrate.php.
            error_log('homeSectionPackages: ' . $e->getMessage());
            return [];
        }
    }
}
