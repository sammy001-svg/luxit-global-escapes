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
