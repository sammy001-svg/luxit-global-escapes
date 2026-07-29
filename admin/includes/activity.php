<?php
/**
 * Activity logging.
 *
 * The dashboard's "Recent Activity" panel used to render three seed rows whose
 * timestamps were literal strings ("2 hours ago") — nothing ever wrote to the
 * table, so it showed the same three entries forever. Every write endpoint now
 * calls logActivity(), and the age is derived from created_at at render time.
 */

require_once __DIR__ . '/auth.php';

if (!function_exists('logActivity')) {
    /**
     * Record an admin action. Never throws: a failure to log must not break the
     * operation the admin actually asked for.
     *
     * @param string $action What happened, e.g. "created a package".
     * @param string $target What it happened to, e.g. the package title.
     */
    function logActivity(PDO $pdo, string $action, string $target): void {
        try {
            $user = $_SESSION['admin_username'] ?? 'System';

            $stmt = $pdo->prepare(
                "INSERT INTO activity_feed (user, action, target, activity_time)
                 VALUES (?, ?, ?, '')"
            );
            $stmt->execute([$user, $action, mb_substr($target, 0, 255)]);

            // Keep the table from growing without bound; the panel only ever
            // reads the most recent entries.
            $pdo->exec(
                "DELETE FROM activity_feed WHERE id NOT IN (
                     SELECT id FROM (
                         SELECT id FROM activity_feed ORDER BY created_at DESC LIMIT 200
                     ) keep
                 )"
            );
        } catch (PDOException $e) {
            error_log('logActivity failed: ' . $e->getMessage());
        }
    }
}
