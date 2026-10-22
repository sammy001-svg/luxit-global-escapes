<?php
require_once 'includes/db.php';
require_once 'includes/blog.php';

$perPage = 9;
$page    = max(1, (int)($_GET['page'] ?? 1));
$cat     = trim($_GET['cat'] ?? '');

function blogQuery(PDO $pdo, string $sql, array $params = []): array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { return []; }
}

$cols = "id, title, slug, excerpt, content, image, author, category, created_at";

// Lead story: the post the admin featured, else the newest. Shown on the
// unfiltered list only, and kept out of the grid on every page so it never
// appears twice.
$lead = null;
if ($cat === '') {
    $lead = blogQuery($pdo, "SELECT $cols, featured FROM blog_posts WHERE status = 'Published' ORDER BY featured DESC, created_at DESC LIMIT 1")[0]
         ?? blogQuery($pdo, "SELECT $cols FROM blog_posts WHERE status = 'Published' ORDER BY created_at DESC LIMIT 1")[0] // before migrate.php adds `featured`
         ?? null;
}

$where  = "status = 'Published'";
$params = [];
if ($cat !== '') { $where .= " AND category = ?"; $params[] = $cat; }
if ($lead)       { $where .= " AND id != ?";      $params[] = $lead['id']; }

$total      = (int)(blogQuery($pdo, "SELECT COUNT(*) AS n FROM blog_posts WHERE $where", $params)[0]['n'] ?? 0);
$totalPages = max(1, (int)ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$posts      = blogQuery($pdo, "SELECT $cols FROM blog_posts WHERE $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset", $params);

$categories = blogQuery($pdo,
    "SELECT category, COUNT(*) AS cnt FROM blog_posts WHERE status = 'Published' AND category <> '' GROUP BY category ORDER BY cnt DESC, category"
);
$allCount = array_sum(array_column($categories, 'cnt'));

$pageUrl = function (int $p) use ($cat): string {
    $q = [];
    if ($cat !== '') $q['cat'] = $cat;
    if ($p > 1) $q['page'] = $p;
    return 'blog.php' . ($q ? '?' . http_build_query($q) : '');
};

$page_title       = ($cat !== '' ? $cat . ' | ' : '') . 'Travel Journal | Luxit Global Escapes';
$page_description = 'Travel advice, destination guides and stories from the Luxit Global Escapes team, from planning a first safari to the best time to visit.';
$page_css         = ['assets/css/blog.css'];
$header_variant   = 'solid';
include 'header.php';
?>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

            <div class="bl">
                <header class="bl-hero">
                    <div class="ba-wrap">
                        <span class="bl-eyebrow">The Luxit Journal</span>
                        <h1 class="bl-title"><?php echo $cat !== '' ? htmlspecialchars($cat) : 'Travel stories &amp; advice'; ?></h1>
                        <p class="bl-intro">Destination guides, planning advice and stories from our team, to help you travel further and better.</p>
                        <?php if (count($categories) > 1) : ?>
                        <nav class="bl-filters" aria-label="Topics">
                            <a class="bl-filter" href="blog.php"<?php echo $cat === '' ? ' aria-current="page"' : ''; ?>>All <span><?php echo $allCount; ?></span></a>
                            <?php foreach ($categories as $c) : ?>
                            <a class="bl-filter" href="blog.php?cat=<?php echo urlencode($c['category']); ?>"<?php echo $c['category'] === $cat ? ' aria-current="page"' : ''; ?>>
                                <?php echo htmlspecialchars($c['category']); ?> <span><?php echo (int)$c['cnt']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </nav>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="bl-body">
                    <div class="ba-wrap">
                        <?php if ($lead && $page === 1) :
                            $leadLink = 'blog-detail.php?slug=' . urlencode($lead['slug']);
                            $leadAuthor = trim((string)$lead['author']) ?: 'Luxit Global Escapes';
                        ?>
                        <a class="bl-lead" href="<?php echo $leadLink; ?>">
                            <div class="bl-lead-img"><img src="<?php echo htmlspecialchars(blogImagePath($lead['image'])); ?>" alt=""></div>
                            <div class="bl-lead-body">
                                <span class="bl-lead-tag"><b><?php echo !empty($lead['featured']) ? 'Featured' : 'Latest'; ?></b> <?php echo htmlspecialchars($lead['category']); ?></span>
                                <h2 class="bl-lead-title"><?php echo htmlspecialchars($lead['title']); ?></h2>
                                <p class="bl-lead-text"><?php echo htmlspecialchars(blogExcerpt($lead, 260)); ?></p>
                                <div class="bl-lead-meta">
                                    <span class="ba-avatar" aria-hidden="true"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($leadAuthor, 0, 1))); ?></span>
                                    <div><strong><?php echo htmlspecialchars($leadAuthor); ?></strong>
                                        <?php echo date('j F Y', strtotime($lead['created_at'])); ?> &middot; <?php echo blogReadingMinutes((string)($lead['content'] ?: $lead['excerpt'])); ?> min read</div>
                                </div>
                                <span class="bl-read">Read the article <i class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if ($posts) : ?>
                            <?php if ($lead && $page === 1) : ?><h2 class="bl-section-title">Latest articles</h2><?php endif; ?>
                            <div class="ba-grid">
                                <?php foreach ($posts as $p) blogCard($p); ?>
                            </div>
                        <?php elseif (!$lead) : ?>
                            <div class="bl-empty">
                                <i class="fa-regular fa-newspaper"></i>
                                <h2><?php echo $cat !== '' ? 'No articles in this topic yet' : 'Our first articles are on the way'; ?></h2>
                                <p>In the meantime, our travel specialists are happy to answer any question.</p>
                                <a href="<?php echo $cat !== '' ? 'blog.php' : 'contact.php'; ?>"><?php echo $cat !== '' ? 'See all articles' : 'Talk to us'; ?></a>
                            </div>
                        <?php endif; ?>

                        <?php if ($totalPages > 1) : ?>
                        <nav class="bl-pages" aria-label="Pages">
                            <?php if ($page > 1) : ?><a href="<?php echo htmlspecialchars($pageUrl($page - 1)); ?>" rel="prev"><i class="fa-solid fa-arrow-left"></i> Newer</a><?php endif; ?>
                            <?php
                            // First, last and the pages around the current one.
                            $last = 0;
                            for ($i = 1; $i <= $totalPages; $i++) {
                                if ($i !== 1 && $i !== $totalPages && abs($i - $page) > 1) continue;
                                if ($last && $i - $last > 1) echo '<span class="is-gap">…</span>';
                                echo $i === $page
                                    ? '<span aria-current="page">' . $i . '</span>'
                                    : '<a href="' . htmlspecialchars($pageUrl($i)) . '">' . $i . '</a>';
                                $last = $i;
                            }
                            ?>
                            <?php if ($page < $totalPages) : ?><a href="<?php echo htmlspecialchars($pageUrl($page + 1)); ?>" rel="next">Older <i class="fa-solid fa-arrow-right"></i></a><?php endif; ?>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- /page-content -->

<?php include 'footer.php'; ?>
