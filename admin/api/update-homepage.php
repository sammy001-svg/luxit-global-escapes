<?php
// Homepage tab: show/hide a package in a section, switch a whole section on
// or off, and save the order of packages in a section.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/homepage.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminJsonError('Invalid request method', 405);

$action  = $_POST['action'] ?? '';
$section = $_POST['section'] ?? '';
if (!isset(HOME_SECTIONS[$section])) adminJsonError('Unknown homepage section.');
$label = HOME_SECTIONS[$section]['title'];

try {
    switch ($action) {
        case 'toggle': {
            $id = (int)($_POST['id'] ?? 0);
            $on = ($_POST['on'] ?? '') === '1';
            $st = $pdo->prepare("SELECT title, home_sections, package_pages FROM tours WHERE id = ?");
            $st->execute([$id]);
            $tour = $st->fetch(PDO::FETCH_ASSOC);
            if (!$tour) adminJsonError('Package not found.', 404);

            $page = HOME_SECTIONS[$section]['page'];
            $pages = array_map('trim', explode(',', (string)$tour['package_pages']));
            if ($on && $page !== null && !in_array($page, $pages, true)) {
                adminJsonError("This package is not on the $page page, so it can't appear under $label. Tick \"$page\" in the package's Package Pages first.");
            }

            $secs = array_values(array_filter(array_map('trim', explode(',', (string)$tour['home_sections']))));
            $secs = $on ? array_values(array_unique([...$secs, $section])) : array_values(array_diff($secs, [$section]));
            // Keep the legacy flags in step for anything still reading them.
            $pdo->prepare("UPDATE tours SET home_sections = ?, show_on_home = ? WHERE id = ?")
                ->execute([implode(',', $secs) ?: null, $secs ? 1 : 0, $id]);
            logActivity($pdo, ($on ? 'added to' : 'removed from') . " homepage $label", $tour['title']);
            echo json_encode(['success' => true, 'home_sections' => implode(',', $secs)]);
            break;
        }
        case 'enable': {
            $on = ($_POST['on'] ?? '') === '1';
            $pdo->prepare("INSERT INTO site_settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)")
                ->execute(["home_{$section}_enabled", $on ? '1' : '0']);
            logActivity($pdo, ($on ? 'showed' : 'hid') . ' the homepage section', $label);
            echo json_encode(['success' => true]);
            break;
        }
        case 'order': {
            $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
            $upd = $pdo->prepare("UPDATE tours SET home_order = ? WHERE id = ?");
            $pdo->beginTransaction();
            foreach ($ids as $i => $id) $upd->execute([$i + 1, $id]);
            $pdo->commit();
            echo json_encode(['success' => true]);
            break;
        }
        default:
            adminJsonError('Unknown action.');
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if (in_array($e->getCode(), ['42S22', '42S02'], true)) {
        adminJsonError('Your database schema is out of date. Run migrate.php in the site root, then try again.', 500);
    }
    adminJsonDbError($e, 'update-homepage');
}
