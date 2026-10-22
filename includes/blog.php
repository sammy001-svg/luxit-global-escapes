<?php
/**
 * Helpers for rendering blog articles on the public site.
 *
 * Article bodies are HTML typed into a plain textarea in the admin panel, so
 * they arrive in any state: unclosed tags, pasted plain text, stray scripts.
 * Everything goes through blogRenderContent() before it reaches the page, so
 * a malformed post can never break the surrounding layout.
 */

if (!function_exists('blogImagePath')) {
    /** Image path for a post, falling back when it is empty or missing on disk. */
    function blogImagePath(?string $img, string $fallback = 'assets/images/trv-blog/blog-lg/pic1.jpg'): string {
        $img = trim((string)$img);
        if ($img === '' || !is_file(__DIR__ . '/../' . $img)) return $fallback;
        return $img;
    }
}

if (!function_exists('blogPlainText')) {
    /**
     * Article HTML as readable plain text. Block boundaries become spaces
     * first: a bare strip_tags() runs "…seasons.</p><h2>The Migration" into
     * "seasons.The Migration".
     */
    function blogPlainText(?string $html): string {
        $html = preg_replace('#<br\s*/?>|</(p|div|h[1-6]|li|blockquote|figcaption|td|th)>#i', '$0 ', (string)$html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}

if (!function_exists('blogReadingMinutes')) {
    /** Estimated reading time at ~200 words per minute, never less than 1. */
    function blogReadingMinutes(string $html): int {
        $words = count(preg_split('/\s+/u', blogPlainText($html), -1, PREG_SPLIT_NO_EMPTY));
        return max(1, (int)round($words / 200));
    }
}

if (!function_exists('blogExcerpt')) {
    /** Card summary: the author's excerpt, else the opening of the article. */
    function blogExcerpt(array $post, int $max = 160): string {
        $text = trim(strip_tags((string)($post['excerpt'] ?? '')));
        if ($text === '') {
            $text = blogPlainText($post['content'] ?? '');
        }
        if (mb_strlen($text) <= $max) return $text;
        $cut = mb_substr($text, 0, $max);
        $space = mb_strrpos($cut, ' ');
        return rtrim($space !== false ? mb_substr($cut, 0, $space) : $cut, ' ,.;:') . '…';
    }
}

if (!function_exists('blogCard')) {
    /**
     * Article card used on blog.php, the homepage and "Keep reading", so a
     * post looks the same everywhere it appears. Styles: assets/css/blog.css.
     */
    function blogCard(array $post): void {
        $link = 'blog-detail.php?slug=' . urlencode($post['slug']);
        $text = blogExcerpt($post);
        $mins = blogReadingMinutes((string)(($post['content'] ?? '') ?: ($post['excerpt'] ?? '')));
        ?>
                        <a class="ba-card" href="<?php echo $link; ?>">
                            <div class="ba-card-img"><img src="<?php echo htmlspecialchars(blogImagePath($post['image'] ?? '')); ?>" alt="" loading="lazy"></div>
                            <div class="ba-card-body">
                                <span class="ba-card-cat"><?php echo htmlspecialchars($post['category'] ?? ''); ?></span>
                                <h3 class="ba-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <?php if ($text !== '') : ?><p class="ba-card-text"><?php echo htmlspecialchars($text); ?></p><?php endif; ?>
                                <span class="ba-card-meta"><?php echo date('j M Y', strtotime($post['created_at'])); ?> &middot; <?php echo $mins; ?> min read</span>
                            </div>
                        </a>
        <?php
    }
}

if (!function_exists('blogRenderContent')) {
    /**
     * Normalise an article body for display.
     *
     * Returns ['html' => safe markup, 'toc' => [['id','text','level'], …]].
     *
     * - Plain text (no block tags) becomes paragraphs split on blank lines.
     * - The fragment is parsed and re-serialised with DOMDocument, which
     *   closes any unclosed tags inside the article instead of letting them
     *   swallow the sidebar and footer.
     * - <script>, <style>, <iframe>, <object>, <embed>, <form> and inline
     *   event handlers / javascript: URLs are removed.
     * - h2/h3 headings get ids for the table of contents; external links open
     *   in a new tab.
     */
    function blogRenderContent(?string $raw): array {
        $raw = trim((string)$raw);
        if ($raw === '') return ['html' => '', 'toc' => []];

        if (!preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|table|figure|img)\b/i', $raw)) {
            $paras = preg_split('/\R\s*\R/u', $raw);
            $raw = implode('', array_map(
                fn($p) => '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>',
                array_filter($paras, fn($p) => trim($p) !== '')
            ));
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="blog-root">' . $raw . '</div>',
                       LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $root = $doc->getElementById('blog-root');
        if (!$root) return ['html' => '<p>' . htmlspecialchars(strip_tags($raw)) . '</p>', 'toc' => []];

        $xp = new DOMXPath($doc);
        foreach ($xp->query('.//script|.//style|.//iframe|.//object|.//embed|.//form|.//link|.//meta', $root) as $bad) {
            $bad->parentNode->removeChild($bad);
        }
        foreach ($xp->query('.//@*', $root) as $attr) {
            $name = strtolower($attr->nodeName);
            $val  = strtolower(trim($attr->nodeValue));
            if (str_starts_with($name, 'on')
                || (in_array($name, ['href', 'src'], true) && str_starts_with(preg_replace('/\s+/', '', $val), 'javascript:'))) {
                $attr->ownerElement->removeAttributeNode($attr);
            }
        }

        $toc = [];
        $used = [];
        foreach ($xp->query('.//h2|.//h3', $root) as $h) {
            $text = trim(preg_replace('/\s+/u', ' ', $h->textContent));
            if ($text === '') continue;
            $id = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-') ?: 'section';
            $base = $id; $n = 2;
            while (isset($used[$id])) $id = $base . '-' . $n++;
            $used[$id] = true;
            $h->setAttribute('id', $id);
            $toc[] = ['id' => $id, 'text' => $text, 'level' => (int)substr($h->nodeName, 1)];
        }

        foreach ($xp->query('.//a[@href]', $root) as $a) {
            if (preg_match('#^https?://#i', $a->getAttribute('href'))) {
                $a->setAttribute('target', '_blank');
                $a->setAttribute('rel', 'noopener');
            }
        }
        foreach ($xp->query('.//img', $root) as $img) {
            $img->setAttribute('loading', 'lazy');
            if (!$img->hasAttribute('alt')) $img->setAttribute('alt', '');
        }

        $html = '';
        foreach ($root->childNodes as $child) $html .= $doc->saveHTML($child);
        return ['html' => $html, 'toc' => $toc];
    }
}
