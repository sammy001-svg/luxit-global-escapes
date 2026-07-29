<?php
/**
 * Luxit Global Escapes — Safe Schema Migration
 *
 * Creates any table defined in database.sql that is MISSING from the current
 * database, and seeds it if the schema file ships sample rows for it.
 *
 * Unlike setup_db.php this script is non-destructive: it never drops a table
 * and never touches a table that already exists, so it is safe to run against
 * a live database with real bookings and customers in it.
 *
 * Run from the browser (https://yoursite.com/migrate.php) or the CLI
 * (php migrate.php). DELETE OR RESTRICT THIS FILE once your schema is current.
 */

$isCli = (php_sapi_name() === 'cli');
if (!$isCli) header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/includes/db.php';

$schemaFile = __DIR__ . '/database.sql';
if (!file_exists($schemaFile)) {
    exit("Error: database.sql not found at $schemaFile\n");
}
$sql = file_get_contents($schemaFile);

echo "Luxit Global Escapes — schema migration\n";
echo str_repeat('=', 60) . "\n\n";

// ── Which tables exist right now? ────────────────────────────────────────────
$existing = $pdo->query(
    "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()"
)->fetchAll(PDO::FETCH_COLUMN);
$existing = array_map('strtolower', $existing);

// ── Pull every CREATE TABLE block out of the schema file ─────────────────────
preg_match_all(
    '/CREATE\s+TABLE\s+`?(\w+)`?\s*\((.*?)\)\s*ENGINE\s*=\s*\w+[^;]*;/is',
    $sql,
    $creates,
    PREG_SET_ORDER
);

if (empty($creates)) {
    exit("Error: no CREATE TABLE statements found in database.sql\n");
}

$created = [];
$skipped = [];
$failed  = [];

foreach ($creates as $block) {
    $table     = $block[1];
    $statement = $block[0];

    if (in_array(strtolower($table), $existing, true)) {
        $skipped[] = $table;
        continue;
    }

    try {
        $pdo->exec($statement);
        $created[] = $table;
        echo "CREATED   $table\n";
    } catch (PDOException $e) {
        $failed[$table] = $e->getMessage();
        echo "FAILED    $table — " . $e->getMessage() . "\n";
    }
}

// ── Seed only the tables we just created ─────────────────────────────────────
// A table that already existed keeps its real data untouched.
if (!empty($created)) {
    echo "\nSeeding newly created tables from database.sql sample data...\n";

    preg_match_all(
        '/INSERT\s+INTO\s+`?(\w+)`?\s*\(.*?;\s*(?=\n|$)/is',
        $sql,
        $inserts,
        PREG_SET_ORDER
    );

    foreach ($inserts as $ins) {
        $table = $ins[1];
        if (!in_array($table, $created, true)) continue;

        try {
            $pdo->exec($ins[0]);
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  seeded $table ($count rows)\n";
        } catch (PDOException $e) {
            echo "  could not seed $table — " . $e->getMessage() . "\n";
        }
    }
}

// ── Column migrations ────────────────────────────────────────────────────────
// Columns added to existing tables after the initial release. Each is applied
// only if absent, so this is safe to run repeatedly.
$columnMigrations = [
    ['tours', 'show_on_home',  "tinyint(1) DEFAULT 0"],
    ['tours', 'home_section',  "varchar(100) DEFAULT NULL"],
    ['tours', 'package_pages', "varchar(100) DEFAULT NULL"],
    ['tours', 'promo_badge',   "varchar(100) DEFAULT NULL"],
    ['tours', 'promo_style',   "varchar(20) DEFAULT 'primary'"],
    ['tours', 'promo_tagline', "varchar(255) DEFAULT NULL"],
];

echo "\nChecking for missing columns...\n";
$addedColumns = [];

foreach ($columnMigrations as [$table, $column, $definition]) {
    try {
        $exists = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?"
        );
        $exists->execute([$table, $column]);
        if ((int)$exists->fetchColumn() > 0) continue;

        $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        $addedColumns[] = "$table.$column";
        echo "  ADDED $table.$column\n";
    } catch (PDOException $e) {
        echo "  FAILED $table.$column — " . $e->getMessage() . "\n";
    }
}
if (empty($addedColumns)) echo "  (all columns present)\n";

// ── Backfill package_pages ───────────────────────────────────────────────────
// Existing tours have no page assignment. Without a backfill every package
// page would render empty after this migration, so reproduce the old implicit
// routing once: Safari by category, Local by location keyword, else International.
try {
    $unassigned = $pdo->query(
        "SELECT id, location, category FROM tours WHERE package_pages IS NULL OR package_pages = ''"
    )->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($unassigned)) {
        echo "\nBackfilling package_pages for " . count($unassigned) . " existing tours...\n";

        $localKeywords = ['Kenya','Tanzania','Uganda','Rwanda','Mombasa','Seychelles','Zanzibar',
                          'Madagascar','Zambia','Zimbabwe','Namibia','Botswana','Africa',
                          'South Africa','Morocco','Egypt','Safari','Nairobi'];

        $update = $pdo->prepare("UPDATE tours SET package_pages = ? WHERE id = ?");

        foreach ($unassigned as $t) {
            $pages = [];
            if (strcasecmp((string)$t['category'], 'Safari') === 0) $pages[] = 'Safari';

            foreach ($localKeywords as $kw) {
                if (stripos((string)$t['location'], $kw) !== false) { $pages[] = 'Local'; break; }
            }

            // Anything not clearly local/safari kept its old home on the
            // international page, which listed every active tour.
            if (empty($pages)) $pages[] = 'International';

            $update->execute([implode(',', array_unique($pages)), $t['id']]);
            echo "  #{$t['id']} {$t['location']} -> " . implode(', ', array_unique($pages)) . "\n";
        }
    }
} catch (PDOException $e) {
    echo "  Backfill skipped — " . $e->getMessage() . "\n";
}

// ── Summary ──────────────────────────────────────────────────────────────────
echo "\n" . str_repeat('=', 60) . "\n";
echo "Created:   " . (empty($created) ? '(nothing — schema already current)' : implode(', ', $created)) . "\n";
echo "Untouched: " . (empty($skipped) ? '(none)' : implode(', ', $skipped)) . "\n";
if (!empty($failed)) {
    echo "Failed:    " . implode(', ', array_keys($failed)) . "\n";
}

if (in_array('blog_posts', $created, true)) {
    echo "\nblog_posts now exists — posts published from the admin panel will\n";
    echo "appear on blog.php, blog-detail.php and the homepage blog section.\n";
}

echo "\nDone. Delete or restrict this file when you are finished.\n";
