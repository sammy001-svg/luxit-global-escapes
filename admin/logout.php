<?php
require_once __DIR__ . '/includes/auth.php';
adminSessionStart();

$_SESSION = [];

// Expire the session cookie itself, not just the server-side data, so the
// browser stops presenting the old session ID.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();
header("Location: login.php");
exit;
