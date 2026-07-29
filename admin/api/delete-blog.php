<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    adminJsonError('Invalid request method', 405);
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    adminJsonError('Blog post ID is required');
}

try {
    // Capture the label before the row goes away so the activity log
    // reads as a name rather than a bare id.
    $label = $pdo->prepare("SELECT `title` FROM blog_posts WHERE id = ?");
    $label->execute([$id]);
    $name = (string)($label->fetchColumn() ?: ("#" . $id));

    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);

    logActivity($pdo, 'deleted the blog post', $name);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    adminJsonDbError($e, 'delete-blog');
}
