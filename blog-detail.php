<?php
require_once 'includes/db.php';

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

// 404 redirect if not found
if (!$post) {
    header("HTTP/1.0 404 Not Found");
    header("Location: blog.php");
    exit;
}

// Related posts — same category, excluding current
$related = sideQuery($pdo,
    "SELECT id, title, slug, excerpt, image, author, created_at FROM blog_posts WHERE status='Published' AND category=? AND id != ? ORDER BY created_at DESC LIMIT 3",
    [$post['category'], $post['id']]
);

// Recent posts for sidebar
$recent = sideQuery($pdo,
    "SELECT id, title, slug, image, created_at FROM blog_posts WHERE status='Published' AND id != ? ORDER BY created_at DESC LIMIT 5",
    [$post['id']]
);

// Categories with counts
$categories = sideQuery($pdo,
    "SELECT category, COUNT(*) as cnt FROM blog_posts WHERE status='Published' GROUP BY category ORDER BY cnt DESC"
);

$postDate = date('d F Y', strtotime($post['created_at']));
$postImg  = !empty($post['image']) ? htmlspecialchars($post['image']) : 'assets/images/trv-blog/blog-lg/pic1.jpg';

$page_title = htmlspecialchars($post['title']) . " - Luxit Global Escapes Blog";
include 'header.php';
?>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

            <!-- INNER BANNER -->
            <div class="relative bg-cover bg-center w-full overflow-hidden" style="background-image: url('<?php echo $postImg; ?>');">
                <div class="bg-black/70 absolute left-0 top-0 size-full"></div>
                <div class="inr-banner-height flex w-full lg:h-160 md:h-135 h-100 pb-10 items-baseline mx-auto">
                    <div class="relative md:mt-60 mt-45 flex items-center justify-center w-full flex-col z-5 px-5">
                        <span class="bg-primary text-white text-xs font-black uppercase px-4 py-1.5 rounded-full mb-5"><?php echo htmlspecialchars($post['category']); ?></span>
                        <h1 class="lg:text-50 md:text-40 text-2xl relative text-white text-center max-w-4xl leading-tight mb-5">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h1>
                        <div class="flex items-center gap-6 text-white/80 text-sm">
                            <span class="flex items-center gap-2"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                            <span class="flex items-center gap-2"><i class="fas fa-calendar-alt"></i> <?php echo $postDate; ?></span>
                            <?php if (!empty($post['tags'])) : ?>
                            <span class="flex items-center gap-2 max-sm:hidden"><i class="fas fa-tags"></i> <?php echo htmlspecialchars($post['tags']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="inr-plane-wrap absolute w-1/2 right-0 top-0 bottom-0 z-1">
                    <div class="mt-60 animate-slide-right"><img src="assets/about/images/airplane.png" alt="" class="animate-slide-top-fast" width="378" height="146"></div>
                </div>
                <div class="inr-balloon-right absolute md:-right-15 -right-10 top-41.25 animate-slide-top"><img src="assets/about/images/hotballon-right.png" alt="" class="md:w-37.5 w-20" width="230" height="333"></div>
            </div>
            <!-- INNER BANNER END -->

            <!-- BLOG DETAIL CONTENT -->
            <div class="bg-paleaqua xl:pt-22.5 pt-12 pb-20">
                <div class="container">
                    <div class="grid grid-cols-12 gap-10">

                        <!-- Main Article -->
                        <div class="lg:col-span-8 col-span-12">
                            <!-- Breadcrumb -->
                            <div class="flex items-center gap-2 text-sm text-gray-400 mb-8">
                                <a href="index.php" class="hover:text-primary duration-300">Home</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <a href="blog.php" class="hover:text-primary duration-300">Blog</a>
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span class="text-primary font-semibold line-clamp-1"><?php echo htmlspecialchars($post['title']); ?></span>
                            </div>

                            <!-- Article Card -->
                            <div class="bg-white rounded-3xl overflow-hidden shadow-[0px_10px_50px_rgba(0,106,114,0.10)] mb-10">
                                <!-- Featured Image -->
                                <div class="relative overflow-hidden h-80 sm:h-96">
                                    <img src="<?php echo $postImg; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover">
                                </div>

                                <!-- Article Meta & Content -->
                                <div class="p-8 lg:p-12">
                                    <!-- Meta bar -->
                                    <div class="flex flex-wrap items-center gap-5 mb-8 pb-8 border-b border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-lg">
                                                <?php echo strtoupper(substr($post['author'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-primary text-sm"><?php echo htmlspecialchars($post['author']); ?></p>
                                                <p class="text-xs text-gray-400">Author</p>
                                            </div>
                                        </div>
                                        <div class="h-8 w-px bg-gray-100 max-sm:hidden"></div>
                                        <div class="flex items-center gap-2 text-sm text-gray-400">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                            <span><?php echo $postDate; ?></span>
                                        </div>
                                        <div class="h-8 w-px bg-gray-100 max-sm:hidden"></div>
                                        <div class="flex items-center gap-2 text-sm text-gray-400">
                                            <i class="fas fa-folder-open text-primary"></i>
                                            <a href="blog.php?cat=<?php echo urlencode($post['category']); ?>" class="hover:text-primary duration-300"><?php echo htmlspecialchars($post['category']); ?></a>
                                        </div>
                                    </div>

                                    <!-- Article Body -->
                                    <div class="prose prose-lg max-w-none blog-content">
                                        <?php if (!empty($post['content'])) : ?>
                                            <?php echo $post['content']; ?>
                                        <?php elseif (!empty($post['excerpt'])) : ?>
                                            <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                        <?php else : ?>
                                            <p>No content available for this post.</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Tags -->
                                    <?php if (!empty($post['tags'])) :
                                        $tagList = array_map('trim', explode(',', $post['tags']));
                                    ?>
                                    <div class="mt-10 pt-8 border-t border-gray-100">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tags:</span>
                                            <?php foreach ($tagList as $tag) : if ($tag === '') continue; ?>
                                            <a href="blog.php" class="px-4 py-1.5 bg-paleaqua text-primary text-xs font-bold rounded-full hover:bg-primary hover:text-white duration-300">
                                                <?php echo htmlspecialchars($tag); ?>
                                            </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Share -->
                                    <div class="mt-8 pt-8 border-t border-gray-100 flex flex-wrap items-center gap-4">
                                        <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Share:</span>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://luxitglobalescapes.com/blog-detail.php?slug=' . $post['slug']); ?>" target="_blank" rel="noopener" class="size-10 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:scale-110 duration-300">
                                            <i class="fab fa-facebook-f text-sm"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://luxitglobalescapes.com/blog-detail.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" rel="noopener" class="size-10 rounded-full bg-[#1DA1F2] text-white flex items-center justify-center hover:scale-110 duration-300">
                                            <i class="fab fa-twitter text-sm"></i>
                                        </a>
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://luxitglobalescapes.com/blog-detail.php?slug=' . $post['slug']); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" rel="noopener" class="size-10 rounded-full bg-[#0A66C2] text-white flex items-center justify-center hover:scale-110 duration-300">
                                            <i class="fab fa-linkedin-in text-sm"></i>
                                        </a>
                                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - https://luxitglobalescapes.com/blog-detail.php?slug=' . $post['slug']); ?>" target="_blank" rel="noopener" class="size-10 rounded-full bg-[#25D366] text-white flex items-center justify-center hover:scale-110 duration-300">
                                            <i class="fab fa-whatsapp text-sm"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Back to Blog -->
                            <div class="mb-12">
                                <a href="blog.php" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-citrusyellow duration-300">
                                    <i class="fas fa-arrow-left text-xs"></i> Back to All Posts
                                </a>
                            </div>

                            <!-- Related Posts -->
                            <?php if (!empty($related)) : ?>
                            <div>
                                <h3 class="xl:text-30 text-24 font-bold text-primary mb-8">Related <span class="text-citrusyellow">Articles</span></h3>
                                <div class="grid grid-cols-12 gap-6">
                                    <?php foreach ($related as $rel) :
                                        $rImg  = !empty($rel['image']) ? htmlspecialchars($rel['image']) : 'assets/images/trv-blog/blog-sm/pic1.jpg';
                                        $rLink = 'blog-detail.php?slug=' . urlencode($rel['slug']);
                                        $rDay  = date('j M Y', strtotime($rel['created_at']));
                                    ?>
                                    <div class="md:col-span-4 col-span-12">
                                        <div class="bg-white rounded-2xl overflow-hidden shadow-[0px_5px_30px_rgba(0,106,114,0.08)] hover:shadow-[0px_15px_50px_rgba(0,106,114,0.15)] duration-500 group">
                                            <div class="relative overflow-hidden h-44">
                                                <a href="<?php echo $rLink; ?>">
                                                    <img src="<?php echo $rImg; ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 duration-700">
                                                </a>
                                            </div>
                                            <div class="p-5">
                                                <p class="text-xs text-gray-400 mb-2"><?php echo $rDay; ?></p>
                                                <h4 class="text-sm font-bold text-primary hover:text-citrusyellow duration-300 line-clamp-2">
                                                    <a href="<?php echo $rLink; ?>"><?php echo htmlspecialchars($rel['title']); ?></a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Sidebar -->
                        <div class="lg:col-span-4 col-span-12">
                            <!-- Recent Posts -->
                            <?php if (!empty($recent)) : ?>
                            <div class="bg-white rounded-3xl p-7 shadow-[0px_10px_40px_rgba(0,106,114,0.08)] mb-8">
                                <h4 class="text-lg font-bold text-primary mb-6 pb-4 border-b border-gray-100">Recent Posts</h4>
                                <div class="space-y-5">
                                    <?php foreach ($recent as $r) :
                                        $rImg  = !empty($r['image']) ? htmlspecialchars($r['image']) : 'assets/images/trv-blog/blog-sm/pic1.jpg';
                                        $rLink = 'blog-detail.php?slug=' . urlencode($r['slug']);
                                        $rDate = date('d M Y', strtotime($r['created_at']));
                                    ?>
                                    <div class="flex gap-4 group">
                                        <div class="min-w-16 w-16 h-16 rounded-xl overflow-hidden flex-shrink-0">
                                            <a href="<?php echo $rLink; ?>">
                                                <img src="<?php echo $rImg; ?>" alt="<?php echo htmlspecialchars($r['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 duration-500">
                                            </a>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 mb-1"><?php echo $rDate; ?></p>
                                            <h5 class="text-sm font-bold text-primary hover:text-citrusyellow duration-300 line-clamp-2">
                                                <a href="<?php echo $rLink; ?>"><?php echo htmlspecialchars($r['title']); ?></a>
                                            </h5>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Categories -->
                            <?php if (!empty($categories)) : ?>
                            <div class="bg-white rounded-3xl p-7 shadow-[0px_10px_40px_rgba(0,106,114,0.08)] mb-8">
                                <h4 class="text-lg font-bold text-primary mb-6 pb-4 border-b border-gray-100">Categories</h4>
                                <div class="space-y-3">
                                    <?php foreach ($categories as $c) : ?>
                                    <a href="blog.php?cat=<?php echo urlencode($c['category']); ?>" class="flex items-center justify-between py-2.5 px-4 rounded-xl hover:bg-paleaqua group duration-300">
                                        <span class="text-sm font-semibold text-primary group-hover:text-citrusyellow duration-300"><?php echo htmlspecialchars($c['category']); ?></span>
                                        <span class="text-xs font-bold text-white bg-primary rounded-full size-6 flex items-center justify-center"><?php echo (int)$c['cnt']; ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- CTA Box -->
                            <div class="bg-primary rounded-3xl p-8 text-center text-white">
                                <i class="fas fa-paper-plane text-4xl mb-5 block text-secondary"></i>
                                <h4 class="text-xl font-bold mb-3">Plan Your Dream Trip</h4>
                                <p class="text-white/80 text-sm mb-6 leading-relaxed">Let our experts craft a bespoke itinerary tailored exactly to your travel dreams.</p>
                                <a href="contact.php" class="inline-block bg-secondary text-primary font-black text-sm px-6 py-3 rounded-full hover:bg-white duration-300">Get a Free Quote</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- BLOG DETAIL CONTENT END -->

            <?php include 'footer.php'; ?>
        </div>
    </div>
</div>

<style>
/* Blog content typography */
.blog-content p { margin-bottom: 1.25rem; line-height: 1.8; color: #4B5563; font-size: 1rem; }
.blog-content h2 { font-size: 1.5rem; font-weight: 700; color: #006A72; margin-top: 2rem; margin-bottom: 1rem; }
.blog-content h3 { font-size: 1.25rem; font-weight: 700; color: #006A72; margin-top: 1.5rem; margin-bottom: 0.75rem; }
.blog-content ul, .blog-content ol { margin-left: 1.5rem; margin-bottom: 1.25rem; color: #4B5563; }
.blog-content li { margin-bottom: 0.5rem; line-height: 1.7; }
.blog-content strong { color: #1F2937; font-weight: 700; }
.blog-content a { color: #006A72; text-decoration: underline; }
.blog-content a:hover { color: #FFD214; }
.blog-content blockquote { border-left: 4px solid #006A72; padding-left: 1.25rem; margin: 1.5rem 0; font-style: italic; color: #6B7280; }
.blog-content img { max-width: 100%; border-radius: 12px; margin: 1.5rem 0; }
</style>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/vendor/gsap/gsap.min.js"></script>
<script src="assets/vendor/gsap/ScrollSmoother.js"></script>
<script src="assets/vendor/gsap/ScrollTrigger.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/xmenu/xmenu.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/custom.js"></script>
</body></html>
