<?php
// Rejects anonymous callers and verifies the CSRF token on writes.
require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

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
// rating is DECIMAL(2,1): anything above 9.9 is a database error, and the
// public cards draw five stars, so keep it on a 0–5 scale.
$rating      = round(max(0, min(5, (float)($_POST['rating'] ?? 5.0))), 1);
$imageRaw    = $_POST['image'] ?? '';
$showOnHome  = isset($_POST['showOnHome']) && $_POST['showOnHome'] === 'on' ? 1 : 0;

// A featured package must name a section the homepage actually renders,
// otherwise it is flagged "on the homepage" yet shows up nowhere.
$allowedSections = ['Explore Popular Tours', 'Marketing Highlights'];
$homeSection     = trim($_POST['homeSection'] ?? '');
if (!$showOnHome) {
    $homeSection = '';
} elseif (!in_array($homeSection, $allowedSections, true)) {
    $homeSection = $allowedSections[0];
}

// Which public package pages this tour appears on. Stored as a comma-separated
// list so a single package can sit on more than one page (a Maasai Mara trip
// is both Safari and Local).
$allowedPages = ['International', 'Local', 'Safari'];
$pagesInput   = $_POST['packagePages'] ?? [];
if (!is_array($pagesInput)) $pagesInput = [$pagesInput];
$packagePages = implode(',', array_values(array_intersect($allowedPages, $pagesInput)));

// Travel-style pages (romantic-travel.php etc.) this package is listed on.
$allowedStyles = ['romantic', 'wellness', 'halal', 'kosher', 'faith', 'accessible', 'mice'];
$stylesInput   = $_POST['travelStyles'] ?? [];
if (!is_array($stylesInput)) $stylesInput = [$stylesInput];
$travelStyles  = implode(',', array_values(array_intersect($allowedStyles, $stylesInput)));

// Optional promotion for the homepage Marketing Highlights carousel.
$promoBadge   = trim($_POST['promoBadge']   ?? '');
$promoTagline = trim($_POST['promoTagline'] ?? '');
$promoStyle   = in_array($_POST['promoStyle'] ?? '', ['primary', 'secondary', 'citrus', 'red'], true)
                ? $_POST['promoStyle'] : 'primary';

// Package page content, one item per line. Blank lines are dropped; an empty
// field hides its section on the public page rather than showing filler.
$listField = function (string $key): string {
    $lines = preg_split('/\R/u', (string)($_POST[$key] ?? ''));
    return implode("\n", array_values(array_filter(array_map('trim', $lines), 'strlen')));
};
$highlights = $listField('highlights');
$itinerary  = $listField('itinerary');
$inclusions = $listField('inclusions');
$exclusions = $listField('exclusions');

if (empty($title) || empty($location) || $price <= 0) {
    echo json_encode(['success' => false, 'error' => 'Title, destination, and price are required.']);
    exit;
}

if ($price > 99999999) {
    echo json_encode(['success' => false, 'error' => 'Price is too large.']);
    exit;
}

// Lengths the public cards are designed around (and that fit the columns).
// Rejecting here, rather than letting MySQL truncate silently, tells the admin
// exactly which field to shorten.
$limits = [
    'Title'         => [$title, 120],
    'Duration'      => [$duration, 40],
    'Badge text'    => [$promoBadge, 30],
    'Tagline'       => [$promoTagline, 90],
    'Destination'   => [$location, 255],
    'Category'      => [$category, 50],
    'Highlights'    => [$highlights, 4000],
    'Itinerary'     => [$itinerary, 20000],
    'Included'      => [$inclusions, 4000],
    'Not included'  => [$exclusions, 4000],
];
foreach ($limits as $label => [$value, $max]) {
    if (mb_strlen($value) > $max) {
        echo json_encode(['success' => false, 'error' => "$label is too long (maximum $max characters)."]);
        exit;
    }
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
        // The data-URL prefix is client-controlled; check the bytes really
        // are an image of the claimed type before writing them under assets/.
        $info = @getimagesizefromstring($decoded);
        $mimeToExt = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if ($info === false || !isset($mimeToExt[$info['mime']])) {
            echo json_encode(['success' => false, 'error' => 'The uploaded file is not a valid JPG, PNG or WEBP image.']);
            exit;
        }
        $ext = $mimeToExt[$info['mime']];
        $uploadDir = __DIR__ . '/../../assets/images/tour/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename  = uniqid('tour_', true) . '.' . $ext;
        if (file_put_contents($uploadDir . $filename, $decoded) === false) {
            error_log('save-tour: could not write ' . $uploadDir . $filename);
            echo json_encode(['success' => false, 'error' => 'The image could not be saved on the server. Check that assets/images/tour/uploads is writable.']);
            exit;
        }
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
                    promo_badge=?, promo_style=?, promo_tagline=?,
                    highlights=?, itinerary=?, inclusions=?, exclusions=?, travel_styles=?
             WHERE id=?"
        );
        $stmt->execute([$title, $location, $price, $duration, $category, $status, $description,
                        $rating, $imagePath, $showOnHome, $homeSection, $packagePages,
                        $promoBadge, $promoStyle, $promoTagline,
                        $highlights, $itinerary, $inclusions, $exclusions, $travelStyles, $id]);
        $newId = $id;
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO tours (title, location, price, duration, category, status, description,
                    rating, image, show_on_home, home_section, package_pages,
                    promo_badge, promo_style, promo_tagline,
                    highlights, itinerary, inclusions, exclusions, travel_styles)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $location, $price, $duration, $category, $status, $description,
                        $rating, $imagePath, $showOnHome, $homeSection, $packagePages,
                        $promoBadge, $promoStyle, $promoTagline,
                        $highlights, $itinerary, $inclusions, $exclusions, $travelStyles]);
        $newId = (int)$pdo->lastInsertId();
    }

    logActivity($pdo, $id ? 'updated the package' : 'created the package', $title);
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
