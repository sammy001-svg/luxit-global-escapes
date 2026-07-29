<?php
/**
 * Gate for every admin API endpoint.
 *
 * require_once this as the FIRST statement of an endpoint — before any output
 * and before touching $_POST. It establishes the session, rejects anonymous
 * callers, and verifies the CSRF token on state-changing requests.
 *
 *     require_once __DIR__ . '/_guard.php';
 *     require_once __DIR__ . '/../../includes/db.php';
 */

require_once __DIR__ . '/../includes/auth.php';

adminSessionStart();
header('Content-Type: application/json');

// Never let a browser or proxy cache an authenticated API response.
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!adminIsLoggedIn()) {
    adminJsonError('Not authenticated', 401);
}

// Writes must carry the session's CSRF token. Reads (GET) are exempt because
// they change nothing — but every mutating endpoint is POST-only by design.
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !adminVerifyCsrf()) {
    adminJsonError('Invalid or missing security token. Reload the page and try again.', 403);
}
