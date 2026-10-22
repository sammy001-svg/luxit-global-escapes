<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/blog.php';
require_once __DIR__ . '/../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

function blogFail(string $error): void {
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

$id       = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$title    = trim($_POST['title']    ?? '');
$excerpt  = trim($_POST['excerpt']  ?? '');
$content  = trim($_POST['content']  ?? '');
$author   = trim($_POST['author']   ?? '') ?: 'Luxit Global Escapes';
$category = trim($_POST['category'] ?? '') ?: 'Travel Tips';
$tags     = trim($_POST['tags']     ?? '');
$status   = in_array(trim($_POST['status'] ?? ''), ['Published', 'Draft'], true) ? trim($_POST['status']) : 'Draft';
$featured = isset($_POST['featured']) && $_POST['featured'] === 'on' ? 1 : 0;
$imageRaw = $_POST['image'] ?? '';

if ($title === '') blogFail('Please give the post a title.');

// The editor produces HTML; store it already cleaned (scripts, event
// handlers and unclosed tags removed) rather than relying only on the
// clean-up at display time. An editor left empty yields "<p><br></p>".
$content = blogRenderContent($content)['html'];
$plain   = blogPlainText($content);
if ($plain === '') $content = '';
if ($status === 'Published' && $content === '') {
    blogFail('Write the article before publishing it, or save it as a draft.');
}

// Listings need a summary; write one from the opening of the article if the
// author left it blank.
if ($excerpt === '' && $plain !== '') {
    $excerpt = mb_strlen($plain) > 180
        ? rtrim(mb_substr($plain, 0, mb_strrpos(mb_substr($plain, 0, 180), ' ') ?: 180), ' ,.;:') . '…'
        : $plain;
}

$limits = ['Title' => [$title, 200], 'Summary' => [$excerpt, 400], 'Author' => [$author, 100],
           'Category' => [$category, 100], 'Tags' => [$tags, 255]];
foreach ($limits as $label => [$value, $max]) {
    if (mb_strlen($value) > $max) blogFail("$label is too long (maximum $max characters).");
}

function makeSlug(string $title): string {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-') ?: 'post';
}

// A post keeps its address once created: regenerating the slug from an edited
// title would break every link already shared to the old one.
$slug = null;
if ($id) {
    $s = $pdo->prepare("SELECT slug FROM blog_posts WHERE id = ?");
    $s->execute([$id]);
    $slug = $s->fetchColumn() ?: null;
}
if (!$slug) {
    $base = makeSlug($title);
    $slug = $base;
    $check = $pdo->prepare("SELECT 1 FROM blog_posts WHERE slug = ?" . ($id ? " AND id != ?" : ""));
    for ($n = 2; ; $n++) {
        $check->execute($id ? [$slug, $id] : [$slug]);
        if (!$check->fetchColumn()) break;
        $slug = $base . '-' . $n;
    }
}

// ── Image ─────────────────────────────────────────────────────────────────────
$imagePath = '';
if (str_starts_with($imageRaw, 'data:image')) {
    $decoded = base64_decode(substr($imageRaw, strpos($imageRaw, ',') + 1), true);
    if ($decoded === false) blogFail('The image could not be read. Please choose it again.');
    if (strlen($decoded) > 5 * 1024 * 1024) blogFail('Image is too large (max 5 MB).');
    // Trust the bytes, not the data-URL prefix the browser sent.
    $info = @getimagesizefromstring($decoded);
    $mimeToExt = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($info === false || !isset($mimeToExt[$info['mime']])) {
        blogFail('The uploaded file is not a valid JPG, PNG or WEBP image.');
    }
    $uploadDir = __DIR__ . '/../../assets/images/blog/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $filename = uniqid('blog_', true) . '.' . $mimeToExt[$info['mime']];
    if (file_put_contents($uploadDir . $filename, $decoded) === false) {
        error_log('save-blog: could not write ' . $uploadDir . $filename);
        blogFail('The image could not be saved on the server. Check that assets/images/blog/uploads is writable.');
    }
    $imagePath = 'assets/images/blog/uploads/' . $filename;
} elseif ($imageRaw !== '' && str_starts_with($imageRaw, 'assets/')) {
    $imagePath = $imageRaw;
}
// An empty value on update means "unchanged" unless the author removed it.
if ($imagePath === '' && $id && ($_POST['image_removed'] ?? '') !== '1') {
    $s = $pdo->prepare("SELECT image FROM blog_posts WHERE id = ?");
    $s->execute([$id]);
    $imagePath = (string)($s->fetchColumn() ?: '');
}

try {
    $pdo->beginTransaction();
    // One lead story at a time.
    if ($featured) {
        $pdo->prepare("UPDATE blog_posts SET featured = 0 WHERE featured = 1" . ($id ? " AND id != ?" : ""))
            ->execute($id ? [$id] : []);
    }
    if ($id) {
        $stmt = $pdo->prepare(
            "UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, image=?, author=?, category=?, tags=?, status=?, featured=?, updated_at=NOW()
             WHERE id=?"
        );
        $stmt->execute([$title, $slug, $excerpt, $content, $imagePath, $author, $category, $tags, $status, $featured, $id]);
        $newId = $id;
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO blog_posts (title, slug, excerpt, content, image, author, category, tags, status, featured)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $slug, $excerpt, $content, $imagePath, $author, $category, $tags, $status, $featured]);
        $newId = (int)$pdo->lastInsertId();
    }
    $pdo->commit();

    logActivity($pdo, $id ? 'updated the blog post' : ($status === 'Published' ? 'published the blog post' : 'drafted the blog post'), $title);
    echo json_encode(['success' => true, 'id' => $newId, 'slug' => $slug, 'image' => $imagePath, 'status' => $status]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('save-blog error: ' . $e->getMessage());

    // 42S02 = table missing, 42S22 = column missing (featured arrives with
    // migrate.php). Either way the fix is the same.
    if (in_array($e->getCode(), ['42S02', '42S22'], true)) {
        blogFail('Your database is missing part of the blog schema. Run migrate.php in the site root, then try again.');
    }
    blogFail('Could not save the post. Please try again.');
}
