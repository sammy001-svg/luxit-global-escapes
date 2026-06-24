<?php
require_once 'includes/db.php';

// Pagination
$perPage = 9;
$page    = max(1, (int)($_GET['page'] ?? 1));
$cat     = trim($_GET['cat'] ?? '');
$offset  = ($page - 1) * $perPage;

function blogQuery(PDO $pdo, string $sql, array $params = []): array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { return []; }
}

function blogCount(PDO $pdo, string $sql, array $params = []): int {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) { return 0; }
}

if ($cat !== '') {
    $posts      = blogQuery($pdo, "SELECT * FROM blog_posts WHERE status='Published' AND category=? ORDER BY created_at DESC LIMIT ? OFFSET ?", [$cat, $perPage, $offset]);
    $totalPosts = blogCount($pdo, "SELECT COUNT(*) FROM blog_posts WHERE status='Published' AND category=?", [$cat]);
} else {
    $posts      = blogQuery($pdo, "SELECT * FROM blog_posts WHERE status='Published' ORDER BY created_at DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
    $totalPosts = blogCount($pdo, "SELECT COUNT(*) FROM blog_posts WHERE status='Published'");
}

$totalPages = max(1, (int)ceil($totalPosts / $perPage));

// Featured post — first post on page 1, no category filter
$featured = null;
if ($page === 1 && $cat === '' && !empty($posts)) {
    $featured = array_shift($posts);
}

$categories = blogQuery($pdo, "SELECT DISTINCT category FROM blog_posts WHERE status='Published' ORDER BY category ASC");

$page_title = "Travel Blog - Luxit Global Escapes";
include 'header.php';
?>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

            <!-- INNER BANNER -->
            <div class="relative bg-cover bg-center w-full overflow-hidden" style="background-image: url('assets/about/images/background/inr-banner.jpg');">
                <div class="bg-black/60 absolute left-0 top-0 size-full"></div>
                <div class="inr-banner-height flex w-full lg:h-160 md:h-135 h-100 pb-10 items-baseline mx-auto">
                    <div class="relative md:mt-60 mt-45 flex items-center justify-center w-full flex-col z-5">
                        <div>
                            <h2 class="lg:text-60 md:text-52 text-28 relative text-white">Our Blog</h2>
                        </div>
                        <div>
                            <ul class="inline-block">
                                <li class="text-base pr-7.5 relative inline-block font-semibold text-white after:content-['-'] after:absolute after:right-2 after:-top-1.5 after:text-white after:text-26 after:font-normal">
                                    <a href="index.php" class="text-white">Home</a>
                                </li>
                                <li class="relative inline-block text-base font-semibold text-white">Blog</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="inr-plane-wrap absolute w-1/2 right-0 top-0 bottom-0 z-1">
                    <div class="mt-60 animate-slide-right"><img src="assets/about/images/airplane.png" alt="" class="animate-slide-top-fast" width="378" height="146"></div>
                </div>
                <div class="inr-balloon-right absolute md:-right-15 -right-10 top-41.25 animate-slide-top"><img src="assets/about/images/hotballon-right.png" alt="" class="md:w-37.5 w-20" width="230" height="333"></div>
            </div>
            <!-- INNER BANNER END -->

            <!-- BLOG CONTENT -->
            <div class="bg-paleaqua xl:pt-22.5 pt-12 pb-20">
                <div class="container">

                    <!-- Category Filter Pills -->
                    <?php if (!empty($categories)) : ?>
                    <div class="flex flex-wrap gap-3 mb-12 justify-center">
                        <a href="blog.php" class="px-5 py-2.5 rounded-full text-sm font-bold transition <?php echo $cat === '' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-primary hover:bg-primary hover:text-white'; ?>">All Posts</a>
                        <?php foreach ($categories as $c) : ?>
                        <a href="blog.php?cat=<?php echo urlencode($c['category']); ?>" class="px-5 py-2.5 rounded-full text-sm font-bold transition <?php echo $cat === $c['category'] ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-primary hover:bg-primary hover:text-white'; ?>">
                            <?php echo htmlspecialchars($c['category']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (empty($featured) && empty($posts)) : ?>
                    <!-- Empty state -->
                    <div class="text-center py-24">
                        <i class="fas fa-newspaper text-primary text-6xl mb-6 block opacity-30"></i>
                        <h3 class="xl:text-30 text-2xl font-bold text-primary mb-3">No Posts Found</h3>
                        <p class="text-base text-gray-500 mb-8">Check back soon — we are working on new articles for you.</p>
                        <a href="blog.php" class="site-button butn-bg-shape">View All Posts</a>
                    </div>
                    <?php else : ?>

                    <!-- Featured Post (page 1, no filter only) -->
                    <?php if ($featured) :
                        $fDay  = date('j', strtotime($featured['created_at']));
                        $fMon  = date('F', strtotime($featured['created_at']));
                        $fYear = date('Y', strtotime($featured['created_at']));
                        $fImg  = !empty($featured['image']) ? htmlspecialchars($featured['image']) : 'assets/images/trv-blog/blog-lg/pic1.jpg';
                        $fLink = 'blog-detail.php?slug=' . urlencode($featured['slug']);
                    ?>
                    <div class="mb-16">
                        <div class="grid grid-cols-12 gap-8 bg-white rounded-3xl overflow-hidden shadow-[0px_20px_60px_rgba(0,106,114,0.12)] group">
                            <div class="lg:col-span-6 col-span-12">
                                <div class="relative overflow-hidden h-full min-h-72">
                                    <a href="<?php echo $fLink; ?>">
                                        <img src="<?php echo $fImg; ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>" class="w-full h-full object-cover min-h-72 group-hover:scale-105 duration-700">
                                    </a>
                                    <span class="absolute top-5 left-5 bg-primary text-white text-xs font-black uppercase px-3 py-1.5 rounded-full"><?php echo htmlspecialchars($featured['category']); ?></span>
                                </div>
                            </div>
                            <div class="lg:col-span-6 col-span-12 flex flex-col justify-center lg:py-12 lg:pr-12 p-8">
                                <div class="flex items-center gap-3 mb-5">
                                    <span class="text-xs font-bold text-primary uppercase tracking-widest">Featured</span>
                                    <span class="w-8 h-px bg-primary/30"></span>
                                    <span class="text-xs text-gray-400"><?php echo "$fDay $fMon $fYear"; ?></span>
                                </div>
                                <h2 class="xl:text-36 md:text-30 text-24 font-bold text-primary mb-5 leading-tight">
                                    <a href="<?php echo $fLink; ?>" class="hover:text-citrusyellow duration-500"><?php echo htmlspecialchars($featured['title']); ?></a>
                                </h2>
                                <?php if (!empty($featured['excerpt'])) : ?>
                                <p class="text-base text-gray-500 mb-8 leading-relaxed"><?php echo htmlspecialchars($featured['excerpt']); ?></p>
                                <?php endif; ?>
                                <div class="flex items-center justify-between mt-auto">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                            <?php echo strtoupper(substr($featured['author'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-primary"><?php echo htmlspecialchars($featured['author']); ?></p>
                                            <p class="text-xs text-gray-400">Author</p>
                                        </div>
                                    </div>
                                    <a href="<?php echo $fLink; ?>" class="flex items-center gap-2 text-sm font-bold text-primary hover:text-citrusyellow duration-500">
                                        Read Article <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Blog Grid -->
                    <?php if (!empty($posts)) : ?>
                    <div class="grid grid-cols-12 gap-7.5 mb-14">
                        <?php foreach ($posts as $post) :
                            $pDay  = date('j', strtotime($post['created_at']));
                            $pMon  = date('M', strtotime($post['created_at']));
                            $pYear = date('Y', strtotime($post['created_at']));
                            $pImg  = !empty($post['image']) ? htmlspecialchars($post['image']) : 'assets/images/trv-blog/blog-sm/pic1.jpg';
                            $pLink = 'blog-detail.php?slug=' . urlencode($post['slug']);
                        ?>
                        <div class="xl:col-span-4 md:col-span-6 col-span-12">
                            <div class="bg-white rounded-3xl overflow-hidden shadow-[0px_10px_40px_rgba(0,106,114,0.08)] hover:shadow-[0px_20px_60px_rgba(0,106,114,0.18)] duration-500 group h-full flex flex-col">
                                <div class="relative overflow-hidden h-56">
                                    <a href="<?php echo $pLink; ?>">
                                        <img src="<?php echo $pImg; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 duration-700">
                                    </a>
                                    <span class="absolute top-4 left-4 bg-primary text-white text-xs font-black uppercase px-3 py-1 rounded-full"><?php echo htmlspecialchars($post['category']); ?></span>
                                    <div class="absolute top-4 right-4 bg-white text-primary text-center rounded-xl px-3 py-2 shadow-lg min-w-12">
                                        <span class="block text-xl font-extrabold leading-none"><?php echo $pDay; ?></span>
                                        <span class="text-xs font-bold"><?php echo $pMon; ?></span>
                                    </div>
                                </div>
                                <div class="p-7 flex flex-col flex-1">
                                    <h3 class="text-lg font-bold text-primary mb-3 leading-snug hover:text-citrusyellow duration-500 line-clamp-2">
                                        <a href="<?php echo $pLink; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                                    </h3>
                                    <?php if (!empty($post['excerpt'])) : ?>
                                    <p class="text-sm text-gray-500 mb-5 leading-relaxed line-clamp-3 flex-1"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                    <?php endif; ?>
                                    <div class="flex items-center justify-between pt-5 border-t border-gray-100 mt-auto">
                                        <div class="flex items-center gap-2">
                                            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                                <?php echo strtoupper(substr($post['author'], 0, 1)); ?>
                                            </div>
                                            <span class="text-sm font-semibold text-primary"><?php echo htmlspecialchars($post['author']); ?></span>
                                        </div>
                                        <a href="<?php echo $pLink; ?>" class="text-sm font-bold text-citrusyellow hover:text-primary duration-300 flex items-center gap-1.5">
                                            Read More <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1) : ?>
                    <div class="flex items-center justify-center gap-3 mt-4">
                        <?php if ($page > 1) : ?>
                        <a href="blog.php?page=<?php echo $page - 1; ?><?php echo $cat ? '&cat=' . urlencode($cat) : ''; ?>" class="size-12 flex items-center justify-center rounded-xl bg-white text-primary hover:bg-primary hover:text-white shadow duration-300">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <a href="blog.php?page=<?php echo $i; ?><?php echo $cat ? '&cat=' . urlencode($cat) : ''; ?>" class="size-12 flex items-center justify-center rounded-xl font-bold text-sm shadow duration-300 <?php echo $i === $page ? 'bg-primary text-white shadow-primary/30 shadow-lg' : 'bg-white text-primary hover:bg-primary hover:text-white'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages) : ?>
                        <a href="blog.php?page=<?php echo $page + 1; ?><?php echo $cat ? '&cat=' . urlencode($cat) : ''; ?>" class="size-12 flex items-center justify-center rounded-xl bg-white text-primary hover:bg-primary hover:text-white shadow duration-300">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
            <!-- BLOG CONTENT END -->

            <?php include 'footer.php'; ?>
        </div>
    </div>
</div>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/vendor/gsap/gsap.min.js"></script>
<script src="assets/vendor/gsap/ScrollSmoother.js"></script>
<script src="assets/vendor/gsap/ScrollTrigger.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/xmenu/xmenu.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/custom.js"></script>
</body></html>
