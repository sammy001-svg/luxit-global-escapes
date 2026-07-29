<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$id          = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$title       = trim($_POST['title']       ?? '');
$location    = trim($_POST['location']    ?? '');
$price       = (float)($_POST['price']    ?? 0);
$duration    = trim($_POST['duration']    ?? '');
$category    = trim($_POST['category']    ?? 'Adventure');
$status      = in_array(trim($_POST['status'] ?? ''), ['Active', 'Draft', 'Inactive']) ? trim($_POST['status']) : 'Active';
$description = trim($_POST['description'] ?? '');
$rating      = (float)($_POST['rating']   ?? 5.0);
$imageRaw    = $_POST['image'] ?? '';
$showOnHome  = isset($_POST['showOnHome']) && $_POST['showOnHome'] === 'on' ? 1 : 0;
$homeSection = trim($_POST['homeSection'] ?? '');

// Which public package pages this tour appears on. Stored as a comma-separated
// list so a single package can sit on more than one page (a Maasai Mara trip
// is both Safari and Local).
$allowedPages = ['International', 'Local', 'Safari'];
$pagesInput   = $_POST['packagePages'] ?? [];
if (!is_array($pagesInput)) $pagesInput = [$pagesInput];
$packagePages = implode(',', array_values(array_intersect($allowedPages, $pagesInput)));

// Optional promotion for the homepage Marketing Highlights carousel.
$promoBadge   = trim($_POST['promoBadge']   ?? '');
$promoTagline = trim($_POST['promoTagline'] ?? '');
$promoStyle   = in_array($_POST['promoStyle'] ?? '', ['primary', 'secondary', 'citrus', 'red'], true)
                ? $_POST['promoStyle'] : 'primary';

if (empty($title) || empty($location) || $price <= 0) {
    echo json_encode(['success' => false, 'error' => 'Title, destination, and price are required.']);
    exit;
}

if ($packagePages === '') {
    echo json_encode(['success' => false, 'error' => 'Select at least one package page for this tour, otherwise it will not appear anywhere on the site.']);
    exit;
}

// ── Image handling ────────────────────────────────────────────────────────────
$imagePath = '';

if (str_starts_with($imageRaw, 'data:image')) {
    // Decode base64 data URL and save to uploads directory
    if (preg_match('/^data:image\/(\w+);base64,/', $imageRaw, $matches)) {
        $ext      = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            echo json_encode(['success' => false, 'error' => 'Unsupported image format.']);
            exit;
        }
        $base64Data = substr($imageRaw, strpos($imageRaw, ',') + 1);
        $decoded    = base64_decode($base64Data);
        if ($decoded === false) {
            echo json_encode(['success' => false, 'error' => 'Invalid image data.']);
            exit;
        }
        if (strlen($decoded) > 3 * 1024 * 1024) { // 3 MB limit
            echo json_encode(['success' => false, 'error' => 'Image is too large (max 3 MB).']);
            exit;
        }
        $uploadDir = __DIR__ . '/../../assets/images/tour/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename  = uniqid('tour_', true) . '.' . $ext;
        file_put_contents($uploadDir . $filename, $decoded);
        $imagePath = 'assets/images/tour/uploads/' . $filename;
    }
} elseif (!empty($imageRaw) && str_starts_with($imageRaw, 'assets/')) {
    // Existing relative path — use as-is
    $imagePath = $imageRaw;
}

// ── Preserve existing image if none supplied for an update ────────────────────
if (empty($imagePath) && $id) {
    $stmt = $pdo->prepare("SELECT image FROM tours WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetchColumn();
    $imagePath = $existing ?: '';
}

try {
    if ($id) {
        $stmt = $pdo->prepare(
            "UPDATE tours SET title=?, location=?, price=?, duration=?, category=?, status=?, description=?,
                    rating=?, image=?, show_on_home=?, home_section=?, package_pages=?,
                    promo_badge=?, promo_style=?, promo_tagline=?
             WHERE id=?"
        );
        $stmt->execute([$title, $location, $price, $duration, $category, $status, $description,
                        $rating, $imagePath, $showOnHome, $homeSection, $packagePages,
                        $promoBadge, $promoStyle, $promoTagline, $id]);
        $newId = $id;
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO tours (title, location, price, duration, category, status, description,
                    rating, image, show_on_home, home_section, package_pages,
                    promo_badge, promo_style, promo_tagline)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $location, $price, $duration, $category, $status, $description,
                        $rating, $imagePath, $showOnHome, $homeSection, $packagePages,
                        $promoBadge, $promoStyle, $promoTagline]);
        $newId = (int)$pdo->lastInsertId();
    }

    echo json_encode(['success' => true, 'id' => $newId, 'image' => $imagePath]);
} catch (PDOException $e) {
    error_log('save-tour error: ' . $e->getMessage());

    // Unknown column means the database predates a feature — the exact class of
    // failure that made tour saves fail silently before migrate.php existed.
    if ($e->getCode() === '42S22' || $e->getCode() === '42S02') {
        echo json_encode([
            'success' => false,
            'error'   => 'Your database schema is out of date. Run migrate.php in the site root, then try again.'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Could not save the package. Please try again.']);
    }
}
