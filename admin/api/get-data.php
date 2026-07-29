<?php
/**
 * Partial-refresh endpoint.
 *
 * Returns the same payload the dashboard boots with, so the panel can pull
 * fresh data after a write instead of doing a full page reload.
 */
session_start();
header('Content-Type: application/json');

// This endpoint exposes every record in the panel (customers, finance,
// bookings) — it must never answer an unauthenticated request.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/dashboard-data.php';

try {
    echo json_encode([
        'success' => true,
        'data'    => getAdminDashboardData($pdo),
    ]);
} catch (Throwable $e) {
    error_log('Admin get-data error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not load dashboard data']);
}
