<?php
/**
 * Admin authentication and CSRF helpers.
 *
 * Single place that decides whether a request is an authenticated admin, so
 * pages and API endpoints cannot drift apart on the answer.
 */

if (!function_exists('adminSessionStart')) {
    function adminSessionStart(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Harden the session cookie before it is issued.
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                      || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => $secure,
            ]);
            session_start();
        }
    }
}

if (!function_exists('adminIsLoggedIn')) {
    function adminIsLoggedIn(): bool {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
}

if (!function_exists('adminCsrfToken')) {
    /**
     * The session's CSRF token, created on first use.
     */
    function adminCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('adminVerifyCsrf')) {
    /**
     * Constant-time comparison of the submitted token against the session's.
     * Accepts the token from a form field or the X-CSRF-Token header.
     */
    function adminVerifyCsrf(): bool {
        $sent = $_POST['csrf_token']
             ?? $_SERVER['HTTP_X_CSRF_TOKEN']
             ?? '';
        $known = $_SESSION['csrf_token'] ?? '';
        return $known !== '' && is_string($sent) && hash_equals($known, $sent);
    }
}

if (!function_exists('adminJsonError')) {
    /**
     * Emit a JSON error and stop. Never exposes internal detail to the client;
     * pass $logMessage for anything that belongs in the error log instead.
     */
    function adminJsonError(string $message, int $status = 400, string $logMessage = ''): void {
        if ($logMessage !== '') error_log('Admin API: ' . $logMessage);
        http_response_code($status);
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}

if (!function_exists('adminJsonDbError')) {
    /**
     * Standard handling for a PDOException in an API endpoint: log the real
     * error, return something safe and actionable.
     */
    function adminJsonDbError(PDOException $e, string $context): void {
        error_log("Admin API ($context): " . $e->getMessage());

        // Missing table/column means the database predates a feature.
        if ($e->getCode() === '42S02' || $e->getCode() === '42S22') {
            adminJsonError('Your database schema is out of date. Run migrate.php in the site root, then try again.', 500);
        }
        // Foreign key / constraint violation.
        if ($e->getCode() === '23000') {
            adminJsonError('That record is still referenced by other data and cannot be changed.', 409);
        }
        adminJsonError('The operation could not be completed. Please try again.', 500);
    }
}
