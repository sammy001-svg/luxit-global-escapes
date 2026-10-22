<?php
require_once 'includes/db.php';
require_once 'includes/blog.php';

function detailQuery(PDO $pdo, string $sql, array $params = []): ?array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (PDOException $e) { return null; }
}

function sideQuery(PDO $pdo, string $sql, array $params = []): array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { return []; }
}

// Accept either ?slug=... or ?id=...
$slug = trim($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

$post = null;
if ($slug !== '') {
    $post = detailQuery($pdo, "SELECT * FROM blog_posts WHERE slug = ? AND status = 'Published'", [$slug]);
} elseif ($id > 0) {
    $post = detailQuery($pdo, "SELECT * FROM blog_posts WHERE id = ? AND status = 'Published'", [$id]);
}

// Unknown or unpublished post: back to the article list.
if (!$post) {
    header("Location: blog.php", true, 302);
    exit;
}

$article = blogRenderContent(!empty($post['content']) ? $post['content'] : $post['excerpt']);
$minutes = blogReadingMinutes($article['html']);

// "Keep reading": same category first, topped up with the latest posts.
$more = sideQuery($pdo,
    "SELECT id, title, slug, excerpt, content, image, category, created_at FROM blog_posts
     WHERE status = 'Published' AND id != ?
     ORDER BY (category = ?) DESC, created_at DESC LIMIT 3",
    [$post['id'], $post['category']]
);

$categories = sideQuery($pdo,
    "SELECT category, COUNT(*) AS cnt FROM blog_posts WHERE status = 'Published' GROUP BY category ORDER BY cnt DESC, category"
);

$postImg   = blogImagePath($post['image']);
$author    = trim((string)$post['author']) ?: 'Luxit Global Escapes';
$initial   = mb_strtoupper(mb_substr($author, 0, 1));
$category  = trim((string)$post['category']);
$catLink   = 'blog.php?cat=' . urlencode($category);
$dek       = trim(strip_tags((string)$post['excerpt']));
$tags      = array_values(array_filter(array_map('trim', explode(',', (string)$post['tags']))));
$published = strtotime($post['created_at']);

// Absolute URL of this article for share links, built from the request so it
// is right on any host (local, staging, live) and in a subdirectory install.
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$shareUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'luxitglobalescapes.com') . $basePath
          . '/blog-detail.php?slug=' . urlencode($post['slug']);
$enc      = fn($s) => rawurlencode($s);

// header.php escapes these.
$page_title       = $post['title'] . ' | Luxit Global Escapes Journal';
$page_description = $dek !== '' ? mb_substr($dek, 0, 300) : mb_substr(trim(strip_tags($article['html'])), 0, 300);
$page_image       = $postImg;
$page_css         = ['assets/css/blog.css'];
$header_variant   = 'solid';
include 'header.php';
?>

<!-- Reading progress (fixed, so outside #smooth-content) -->
<div class="ba-progress" aria-hidden="true"><span id="ba-progress-bar"></span></div>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

            <article class="ba" id="ba-article">
                <header class="ba-hero">
                    <div class="ba-wrap ba-wrap--narrow">
                        <nav class="ba-crumbs" aria-label="Breadcrumb">
                            <a href="index.php">Home</a><span aria-hidden="true">/</span>
                            <a href="blog.php">Journal</a><span aria-hidden="true">/</span>
                            <span aria-current="page"><?php echo htmlspecialchars($category ?: 'Article'); ?></span>
                        </nav>
                        <?php if ($category !== '') : ?>
                        <a class="ba-chip" href="<?php echo $catLink; ?>"><?php echo htmlspecialchars($category); ?></a>
                        <?php endif; ?>
                        <h1 class="ba-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                        <?php if ($dek !== '') : ?>
                        <p class="ba-dek"><?php echo htmlspecialchars($dek); ?></p>
                        <?php endif; ?>
                        <div class="ba-meta">
                            <span class="ba-avatar" aria-hidden="true"><?php echo htmlspecialchars($initial); ?></span>
                            <div>
                                <span class="ba-author">By <?php echo htmlspecialchars($author); ?></span>
                                <span class="ba-sub">
                                    <time datetime="<?php echo date('Y-m-d', $published); ?>"><?php echo date('j F Y', $published); ?></time>
                                    &middot; <?php echo $minutes; ?> min read
                                </span>
                            </div>
                        </div>
                    </div>
                </header>

                <figure class="ba-cover ba-wrap">
                    <img src="<?php echo htmlspecialchars($postImg); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" width="1120" height="480">
                </figure>

                <div class="ba-wrap ba-layout">
                    <div class="ba-main">
                        <div class="ba-body">
                            <?php echo $article['html'] !== '' ? $article['html'] : '<p>This article is being written. Please check back soon.</p>'; ?>
                        </div>

                        <?php if ($tags) : ?>
                        <div class="ba-tags" aria-label="Tags">
                            <?php foreach ($tags as $tag) : ?>
                            <span class="ba-tag">#<?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="ba-share">
                            <span class="ba-share-label">Share</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $enc($shareUrl); ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $enc($shareUrl); ?>&amp;text=<?php echo $enc($post['title']); ?>" target="_blank" rel="noopener" aria-label="Share on X"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $enc($shareUrl); ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="https://wa.me/?text=<?php echo $enc($post['title'] . ' ' . $shareUrl); ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                            <a href="mailto:?subject=<?php echo $enc($post['title']); ?>&amp;body=<?php echo $enc($shareUrl); ?>" aria-label="Share by email"><i class="fa-solid fa-envelope"></i></a>
                            <button type="button" id="ba-copy" data-url="<?php echo htmlspecialchars($shareUrl); ?>" aria-label="Copy link"><i class="fa-solid fa-link"></i></button>
                            <span class="ba-share-note" id="ba-copy-note" aria-live="polite"></span>
                        </div>

                        <div class="ba-authorbox">
                            <span class="ba-avatar" aria-hidden="true"><?php echo htmlspecialchars($initial); ?></span>
                            <div>
                                <div class="ba-authorbox-label">Written by</div>
                                <div class="ba-authorbox-name"><?php echo htmlspecialchars($author); ?></div>
                                <p>Part of the Luxit Global Escapes travel team, sharing first-hand advice to help you plan journeys that run smoothly from the first enquiry to the flight home.</p>
                            </div>
                        </div>
                    </div>

                    <aside class="ba-aside" aria-label="Article sidebar">
                        <?php if (count($article['toc']) >= 2) : ?>
                        <nav class="ba-panel ba-toc-panel" aria-label="Contents">
                            <p class="ba-panel-title">In this article</p>
                            <ol class="ba-toc">
                                <?php foreach ($article['toc'] as $t) : ?>
                                <li class="<?php echo $t['level'] === 3 ? 'is-sub' : ''; ?>"><a href="#<?php echo htmlspecialchars($t['id']); ?>" data-toc><?php echo htmlspecialchars($t['text']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                        <?php endif; ?>

                        <div class="ba-cta">
                            <span class="ba-cta-eyebrow">Plan with an expert</span>
                            <h3>Ready to turn ideas into an itinerary?</h3>
                            <p>Tell us where you'd like to go. Our team will design a tailor-made trip around your dates and budget.</p>
                            <a href="contact.php">Start planning <i class="fa-solid fa-arrow-right"></i></a>
                        </div>

                        <?php if ($categories) : ?>
                        <div class="ba-panel">
                            <p class="ba-panel-title">Explore topics</p>
                            <ul class="ba-cats">
                                <?php foreach ($categories as $c) : ?>
                                <li><a href="blog.php?cat=<?php echo urlencode($c['category']); ?>"<?php echo $c['category'] === $category ? ' aria-current="true"' : ''; ?>>
                                    <?php echo htmlspecialchars($c['category']); ?> <span><?php echo (int)$c['cnt']; ?></span>
                                </a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </aside>
                </div>
            </article>

            <?php if ($more) : ?>
            <section class="ba-more" aria-labelledby="ba-more-title">
                <div class="ba-wrap">
                    <div class="ba-more-head">
                        <h2 id="ba-more-title">Keep reading</h2>
                        <a href="blog.php">View all articles <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <div class="ba-grid">
                        <?php foreach ($more as $m) :
                            $mText = trim(strip_tags((string)$m['excerpt']));
                        ?>
                        <a class="ba-card" href="blog-detail.php?slug=<?php echo urlencode($m['slug']); ?>">
                            <div class="ba-card-img"><img src="<?php echo htmlspecialchars(blogImagePath($m['image'])); ?>" alt="" loading="lazy"></div>
                            <div class="ba-card-body">
                                <span class="ba-card-cat"><?php echo htmlspecialchars($m['category']); ?></span>
                                <h3 class="ba-card-title"><?php echo htmlspecialchars($m['title']); ?></h3>
                                <?php if ($mText !== '') : ?><p class="ba-card-text"><?php echo htmlspecialchars($mText); ?></p><?php endif; ?>
                                <span class="ba-card-meta"><?php echo date('j M Y', strtotime($m['created_at'])); ?> &middot; <?php echo blogReadingMinutes((string)($m['content'] ?: $m['excerpt'])); ?> min read</span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php else : ?>
            <div class="ba-more" style="padding-top:0"></div>
            <?php endif; ?>

        </div><!-- /page-content -->

<?php ob_start(); ?>
<script>
(function () {
    // Reading progress through the article.
    var bar = document.getElementById('ba-progress-bar');
    var article = document.getElementById('ba-article');
    if (bar && article) {
        var ticking = false;
        var update = function () {
            var r = article.getBoundingClientRect();
            var total = r.height - window.innerHeight;
            var done = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 1;
            bar.style.width = (done * 100) + '%';
            ticking = false;
        };
        var onScroll = function () { if (!ticking) { ticking = true; requestAnimationFrame(update); } };
        // ScrollSmoother eases the content after each native scroll event, so
        // keep sampling briefly after the last one until it settles.
        var settle;
        window.addEventListener('scroll', function () {
            onScroll();
            clearInterval(settle);
            var n = 0;
            settle = setInterval(function () { onScroll(); if (++n > 40) clearInterval(settle); }, 50);
        }, { passive: true });
        window.addEventListener('resize', onScroll);
        onScroll();
    }

    // Contents links: ScrollSmoother intercepts native anchor jumps.
    document.querySelectorAll('[data-toc]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(a.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            // animation.js handles every #link at document level and scrolls the
            // target flush to the top, under the fixed header; keep it out.
            e.stopPropagation();
            // Land the heading below the fixed header, not underneath it.
            var gap = 120;
            var smoother = window.ScrollSmoother && ScrollSmoother.get && ScrollSmoother.get();
            if (smoother) {
                smoother.scrollTo(Math.max(0, smoother.offset(target, 'top top') - gap), true);
            } else {
                window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - gap, behavior: 'smooth' });
            }
        });
    });

    // Copy link.
    var copy = document.getElementById('ba-copy');
    var note = document.getElementById('ba-copy-note');
    if (copy) {
        copy.addEventListener('click', function () {
            var url = copy.getAttribute('data-url');
            var done = function () {
                copy.classList.add('is-copied');
                if (note) note.textContent = 'Link copied';
                setTimeout(function () { copy.classList.remove('is-copied'); if (note) note.textContent = ''; }, 2200);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(done, function () { window.prompt('Copy this link:', url); });
            } else {
                window.prompt('Copy this link:', url);
            }
        });
    }
})();
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include 'footer.php'; ?>
