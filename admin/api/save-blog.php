<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$id       = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$title    = trim($_POST['title']    ?? '');
$excerpt  = trim($_POST['excerpt']  ?? '');
$content  = trim($_POST['content']  ?? '');
$author   = trim($_POST['author']   ?? 'Admin');
$category = trim($_POST['category'] ?? 'Travel');
$tags     = trim($_POST['tags']     ?? '');
$status   = in_array(trim($_POST['status'] ?? ''), ['Published', 'Draft']) ? trim($_POST['status']) : 'Draft';
$imageRaw = $_POST['image'] ?? '';

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Blog title is required.']);
    exit;
}

// Auto-generate slug from title
function makeSlug(string $title): string {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

$baseSlug = makeSlug($title);
$slug     = $baseSlug;

// Ensure slug is unique (exclude current record on update)
try {
    $checkSql  = $id ? "SELECT id FROM blog_posts WHERE slug = ? AND id != ?" : "SELECT id FROM blog_posts WHERE slug = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $id ? $checkStmt->execute([$slug, $id]) : $checkStmt->execute([$slug]);
    if ($checkStmt->fetch()) {
        $slug = $baseSlug . '-' . time();
    }
} catch (PDOException $e) {
    // Ignore slug check failure; DB unique key will catch true duplicates
}

// Image handling
$imagePath = '';
if (str_starts_with($imageRaw, 'data:image')) {
    if (preg_match('/^data:image\/(\w+);base64,/', $imageRaw, $matches)) {
        $ext     = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            echo json_encode(['success' => false, 'error' => 'Unsupported image format.']);
            exit;
        }
        $decoded = base64_decode(substr($imageRaw, strpos($imageRaw, ',') + 1));
        if ($decoded === false || strlen($decoded) > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'Invalid or too-large image (max 5 MB).']);
            exit;
        }
        $uploadDir = __DIR__ . '/../../assets/images/blog/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename  = uniqid('blog_', true) . '.' . $ext;
        file_put_contents($uploadDir . $filename, $decoded);
        $imagePath = 'assets/images/blog/uploads/' . $filename;
    }
} elseif (!empty($imageRaw) && str_starts_with($imageRaw, 'assets/')) {
    $imagePath = $imageRaw;
}

// Preserve existing image on update if none supplied
if (empty($imagePath) && $id) {
    try {
        $s = $pdo->prepare("SELECT image FROM blog_posts WHERE id = ?");
        $s->execute([$id]);
        $imagePath = (string)($s->fetchColumn() ?: '');
    } catch (PDOException $e) {
        $imagePath = '';
    }
}

try {
    if ($id) {
        $stmt = $pdo->prepare(
            "UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, image=?, author=?, category=?, tags=?, status=?, updated_at=NOW()
             WHERE id=?"
        );
        $stmt->execute([$title, $slug, $excerpt, $content, $imagePath, $author, $category, $tags, $status, $id]);
        $newId = $id;
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO blog_posts (title, slug, excerpt, content, image, author, category, tags, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $slug, $excerpt, $content, $imagePath, $author, $category, $tags, $status]);
        $newId = (int)$pdo->lastInsertId();
    }

    echo json_encode(['success' => true, 'id' => $newId, 'slug' => $slug, 'image' => $imagePath]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
