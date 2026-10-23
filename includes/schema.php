<?php
/**
 * What the current code expects the database to contain, beyond the original
 * release. migrate.php applies it; the admin panel checks it on every load so
 * an out-of-date database is reported (with a one-click fix) instead of every
 * save failing with "schema is out of date".
 *
 * When a new column is added to database.sql, add it here too.
 */

const SCHEMA_COLUMNS = [
    ['tours', 'show_on_home',  "tinyint(1) DEFAULT 0"],
    ['tours', 'home_section',  "varchar(100) DEFAULT NULL"],
    ['tours', 'package_pages', "varchar(100) DEFAULT NULL"],
    ['tours', 'promo_badge',   "varchar(100) DEFAULT NULL"],
    ['tours', 'promo_style',   "varchar(20) DEFAULT 'primary'"],
    ['tours', 'promo_tagline', "varchar(255) DEFAULT NULL"],
    ['tours', 'highlights',    "text DEFAULT NULL"],
    ['tours', 'itinerary',     "text DEFAULT NULL"],
    ['tours', 'inclusions',    "text DEFAULT NULL"],
    ['tours', 'exclusions',    "text DEFAULT NULL"],
    ['tours', 'travel_styles', "varchar(150) DEFAULT NULL"],
    ['tours', 'home_sections', "varchar(100) DEFAULT NULL"],
    ['tours', 'home_order',    "int(11) DEFAULT 0"],
    ['bookings', 'type',       "enum('Booking','Enquiry') DEFAULT 'Booking'"],
    ['bookings', 'tour_id',    "int(11) DEFAULT NULL"],
    ['bookings', 'phone',      "varchar(40) DEFAULT NULL"],
    ['bookings', 'travelers',  "int(11) DEFAULT 1"],
    ['bookings', 'message',    "text DEFAULT NULL"],
    ['bookings', 'admin_note', "text DEFAULT NULL"],
    ['bookings', 'updated_at', "timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP"],
    ['blog_posts', 'featured', "tinyint(1) DEFAULT 0"],
];

/** Tables added after the original release (created from database.sql). */
const SCHEMA_TABLES = ['blog_posts', 'newsletter_subscribers', 'site_settings'];

if (!function_exists('schemaMissing')) {
    /**
     * Tables and columns the code needs that the database does not have yet,
     * as "table" / "table.column" strings. Empty when up to date.
     */
    function schemaMissing(PDO $pdo): array {
        try {
            $tables = array_map('strtolower', $pdo->query(
                "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()"
            )->fetchAll(PDO::FETCH_COLUMN));
            $cols = [];
            foreach ($pdo->query(
                "SELECT LOWER(table_name), LOWER(column_name) FROM information_schema.columns WHERE table_schema = DATABASE()"
            )->fetchAll(PDO::FETCH_NUM) as [$t, $c]) {
                $cols["$t.$c"] = true;
            }
        } catch (PDOException $e) {
            error_log('schemaMissing: ' . $e->getMessage());
            return [];
        }
        $missing = [];
        foreach (SCHEMA_TABLES as $t) {
            if (!in_array($t, $tables, true)) $missing[] = $t;
        }
        foreach (SCHEMA_COLUMNS as [$t, $c]) {
            if (in_array($t, $tables, true) && !isset($cols["$t.$c"])) $missing[] = "$t.$c";
        }
        return $missing;
    }
}
