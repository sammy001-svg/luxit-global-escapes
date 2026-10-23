<?php
// Newsletter subscribers as CSV, for import into a mailing service.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

adminSessionStart();
if (!adminIsLoggedIn()) {
    http_response_code(401);
    exit('Not authenticated');
}

try {
    $rows = $pdo->query("SELECT email, source, created_at FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    exit('The subscriber list is not available yet. Run migrate.php in the site root.');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
fputcsv($out, ['email', 'signed_up_on_page', 'signed_up_at']);
foreach ($rows as $r) {
    // Prefix cells a spreadsheet would treat as formulas.
    $r = array_map(fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v, $r);
    fputcsv($out, [$r['email'], $r['source'], $r['created_at']]);
}
fclose($out);
