<?php
require_once 'includes/db.php';
require_once 'includes/packages.php';

$tour_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tour = null;
if ($tour_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ? AND status = 'Active'");
    $stmt->execute([$tour_id]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Unknown or unpublished package: send people to the listings rather than
// silently showing a different package under this URL.
if (!$tour) {
    header('Location: international-packages.php', true, 302);
    exit;
}

// Related: same category first, then the newest of the rest.
$stmt = $pdo->prepare(
    "SELECT id, title, location, price, duration, image FROM tours
     WHERE status = 'Active' AND id != ?
     ORDER BY (category = ?) DESC, created_at DESC LIMIT 3"
);
$stmt->execute([$tour['id'], $tour['category']]);
$related = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Admin-entered content. Each section renders only if it has content.
$highlights = packageLines($tour['highlights'] ?? '');
$itinerary  = packageItinerary($tour['itinerary'] ?? '');
$inclusions = packageLines($tour['inclusions'] ?? '');
$exclusions = packageLines($tour['exclusions'] ?? '');
$about      = trim((string)($tour['description'] ?? ''));

$price    = (float)$tour['price'];
$rating   = (float)($tour['rating'] ?? 0);
$heroImg  = packageImagePath($tour, 'assets/images/tour/style1/pic1.jpg');
$duration = trim((string)($tour['duration'] ?? ''));
$waNumber = '254737800900';

$page_title       = $tour['title'] . ' | Luxit Global Escapes'; // header.php escapes it
$page_description = $about !== ''
    ? mb_substr(preg_replace('/\s+/u', ' ', $about), 0, 300)
    : $tour['title'] . ' — ' . $tour['location'] . '. Request to book with Luxit Global Escapes; our team confirms availability within 24 hours.';
$page_image       = $heroImg;
$page_css         = ['assets/css/tour.css'];
$header_variant   = 'overlay';
include 'header.php';
?>

<!-- Booking bar (fixed, so outside #smooth-content). -->
<div class="pk-bar" id="pk-bar" aria-hidden="true">
    <div class="pk-bar-price">
        <strong>$<?php echo number_format($price); ?></strong>
        <span>per person<?php echo $duration !== '' ? ' · ' . htmlspecialchars($duration) : ''; ?></span>
    </div>
    <button type="button" id="pk-bar-btn">Book now</button>
</div>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

        <section class="pk-hero">
            <img src="<?php echo htmlspecialchars($heroImg); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" class="pk-hero-img">
            <div class="pk-hero-inner">
                <div class="pk-wrap">
                    <nav class="pk-crumbs" aria-label="Breadcrumb">
                        <a href="index.php">Home</a><span aria-hidden="true">/</span>
                        <a href="international-packages.php">Packages</a><span aria-hidden="true">/</span>
                        <span aria-current="page"><?php echo htmlspecialchars($tour['title']); ?></span>
                    </nav>
                    <span class="pk-loc"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($tour['location']); ?></span>
                    <h1 class="pk-title"><?php echo htmlspecialchars($tour['title']); ?></h1>
                    <div class="pk-facts">
                        <?php if ($duration !== '') : ?><span class="pk-fact"><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($duration); ?></span><?php endif; ?>
                        <?php if (!empty($tour['category'])) : ?><span class="pk-fact"><i class="fa-solid fa-compass"></i> <?php echo htmlspecialchars($tour['category']); ?></span><?php endif; ?>
                        <?php if ($rating > 0) : ?><span class="pk-fact"><i class="fa-solid fa-star"></i> <?php echo number_format($rating, 1); ?> rating</span><?php endif; ?>
                        <span class="pk-fact"><i class="fa-solid fa-tag"></i> From $<?php echo number_format($price); ?> pp</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="pk">
            <div class="pk-wrap pk-layout">

                <div class="pk-main">
                    <?php if ($about !== '') : ?>
                    <section class="pk-section" aria-labelledby="pk-about">
                        <h2 class="pk-h2" id="pk-about">About this trip</h2>
                        <p class="pk-lead"><?php echo htmlspecialchars($about); ?></p>
                    </section>
                    <?php endif; ?>

                    <?php if ($highlights) : ?>
                    <section class="pk-section" aria-labelledby="pk-hi">
                        <h2 class="pk-h2" id="pk-hi">Highlights</h2>
                        <ul class="pk-list pk-list--2 pk-list--hi">
                            <?php foreach ($highlights as $h) : ?>
                            <li><i class="fa-solid fa-star" aria-hidden="true"></i><span><?php echo htmlspecialchars($h); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                    <?php endif; ?>

                    <?php if ($itinerary) : ?>
                    <section class="pk-section" aria-labelledby="pk-it">
                        <h2 class="pk-h2" id="pk-it">Day-by-day itinerary</h2>
                        <ol class="pk-days">
                            <?php foreach ($itinerary as $i => $day) : ?>
                            <li class="pk-day">
                                <span class="pk-day-num" aria-hidden="true"><?php echo $i + 1; ?></span>
                                <div>
                                    <span class="pk-day-label">Day <?php echo $i + 1; ?></span>
                                    <h3 class="pk-day-title"><?php echo htmlspecialchars($day['title']); ?></h3>
                                    <?php if ($day['details'] !== '') : ?><p><?php echo htmlspecialchars($day['details']); ?></p><?php endif; ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </section>
                    <?php endif; ?>

                    <?php if ($inclusions || $exclusions) : ?>
                    <section class="pk-section" aria-labelledby="pk-inc">
                        <h2 class="pk-h2" id="pk-inc">What's included</h2>
                        <div class="pk-incl">
                            <?php if ($inclusions) : ?>
                            <div>
                                <h3 class="pk-h3">Included</h3>
                                <ul class="pk-list pk-list--yes">
                                    <?php foreach ($inclusions as $x) : ?>
                                    <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><?php echo htmlspecialchars($x); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            <?php if ($exclusions) : ?>
                            <div>
                                <h3 class="pk-h3">Not included</h3>
                                <ul class="pk-list pk-list--no">
                                    <?php foreach ($exclusions as $x) : ?>
                                    <li><i class="fa-solid fa-xmark" aria-hidden="true"></i><span><?php echo htmlspecialchars($x); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <section class="pk-section" aria-labelledby="pk-how">
                        <h2 class="pk-h2" id="pk-how">How booking works</h2>
                        <ol class="pk-steps">
                            <li><span class="pk-step-n">1</span><b>Send your request</b><span>Choose your date and group size. It takes a minute and costs nothing.</span></li>
                            <li><span class="pk-step-n">2</span><b>We confirm availability</b><span>A travel specialist checks space and contacts you within 24 hours.</span></li>
                            <li><span class="pk-step-n">3</span><b>Secure your trip</b><span>Once you're happy with the confirmed details, we arrange payment with you.</span></li>
                        </ol>
                    </section>
                </div>

                <aside class="pk-book" id="pk-book" aria-label="Book this trip">
                    <div class="pk-card">
                        <div class="pk-card-head">
                            <span class="pk-from">From</span>
                            <div class="pk-price"><strong>$<?php echo number_format($price); ?></strong><span>per person</span></div>
                            <?php if ($rating > 0) : ?>
                            <div class="pk-rating"><i class="fa-solid fa-star"></i> <?php echo number_format($rating, 1); ?> guest rating<?php echo $duration !== '' ? ' · ' . htmlspecialchars($duration) : ''; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="pk-card-body" id="pk-form-wrap">
                            <div class="pk-mode" role="group" aria-label="Request type">
                                <button type="button" data-mode="book" aria-pressed="true">Request to book</button>
                                <button type="button" data-mode="enquire" aria-pressed="false">Ask a question</button>
                            </div>

                            <form id="pk-form" novalidate>
                                <input type="hidden" name="tour_id" value="<?php echo (int)$tour['id']; ?>">
                                <input type="hidden" name="type" id="pk-type" value="book">
                                <input type="text" name="website" class="pk-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

                                <div class="pk-error" id="pk-error" role="alert"></div>

                                <div class="pk-row">
                                    <div class="pk-field">
                                        <label class="pk-label" for="pk-date">Travel date <em data-optional hidden>(optional)</em></label>
                                        <input class="pk-input" type="date" id="pk-date" name="travel_date" required>
                                    </div>
                                    <div class="pk-field">
                                        <label class="pk-label" for="pk-travelers">Travellers</label>
                                        <div class="pk-stepper">
                                            <button type="button" data-step="-1" aria-label="Fewer travellers">&minus;</button>
                                            <input type="number" id="pk-travelers" name="travelers" value="2" min="1" max="50" inputmode="numeric">
                                            <button type="button" data-step="1" aria-label="More travellers">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="pk-total" aria-live="polite">
                                    <div>
                                        <span id="pk-total-label">$<?php echo number_format($price); ?> × 2 travellers</span>
                                        <small>Estimated. Final price confirmed by our team.</small>
                                    </div>
                                    <strong id="pk-total">$<?php echo number_format($price * 2); ?></strong>
                                </div>

                                <div class="pk-field">
                                    <label class="pk-label" for="pk-name">Full name</label>
                                    <input class="pk-input" type="text" id="pk-name" name="full_name" autocomplete="name" maxlength="100" required>
                                </div>
                                <div class="pk-field">
                                    <label class="pk-label" for="pk-email">Email</label>
                                    <input class="pk-input" type="email" id="pk-email" name="email" autocomplete="email" maxlength="100" required>
                                </div>
                                <div class="pk-field">
                                    <label class="pk-label" for="pk-phone">Phone / WhatsApp <em data-optional hidden>(optional)</em></label>
                                    <input class="pk-input" type="tel" id="pk-phone" name="phone" autocomplete="tel" placeholder="+254 700 000 000" required>
                                </div>
                                <div class="pk-field">
                                    <label class="pk-label" for="pk-msg" id="pk-msg-label">Anything we should know? <em>(optional)</em></label>
                                    <textarea class="pk-input" id="pk-msg" name="message" maxlength="2000" placeholder="Special occasions, dietary needs, room preferences…"></textarea>
                                </div>

                                <button type="submit" class="pk-submit" id="pk-submit"><i class="fa-solid fa-calendar-check"></i> <span>Request to book</span></button>

                                <div class="pk-assure">
                                    <span><i class="fa-solid fa-circle-check"></i> No payment needed to request</span>
                                    <span><i class="fa-solid fa-circle-check"></i> Availability confirmed within 24 hours</span>
                                </div>
                            </form>
                        </div>

                        <div class="pk-success" id="pk-success" role="status" tabindex="-1">
                            <div class="pk-success-icon"><i class="fa-solid fa-check"></i></div>
                            <h3 id="pk-success-title">Request received</h3>
                            <span class="pk-ref" id="pk-ref"></span>
                            <div class="pk-summary" id="pk-summary"></div>
                            <p id="pk-success-msg"></p>
                            <div class="pk-success-actions">
                                <a class="pk-btn-wa" id="pk-wa" href="https://wa.me/<?php echo $waNumber; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> Message us on WhatsApp</a>
                                <button type="button" class="pk-btn-ghost" id="pk-again">Send another request</button>
                            </div>
                        </div>
                    </div>

                    <div class="pk-help">
                        <b>Prefer to talk it through?</b>
                        <a href="tel:+254737800900"><i class="fa-solid fa-phone"></i> +254 737 800 900</a>
                        <a href="https://wa.me/<?php echo $waNumber; ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp our team</a>
                        <a href="mailto:info@luxitglobalescapes.com"><i class="fa-solid fa-envelope"></i> info@luxitglobalescapes.com</a>
                    </div>
                </aside>
            </div>
        </div>

        <?php if ($related) : ?>
        <section class="pk-more" aria-labelledby="pk-more-title">
            <div class="pk-wrap">
                <div class="pk-more-head">
                    <h2 id="pk-more-title">You may also like</h2>
                    <a href="international-packages.php">All packages <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="pk-grid">
                    <?php foreach ($related as $rt) packageTile($rt); ?>
                </div>
            </div>
        </section>
        <?php else : ?>
        <div class="pk-more" style="padding-top:0"></div>
        <?php endif; ?>

        </div><!-- /page-content -->

<?php ob_start(); ?>
<script>
(function () {
    var PRICE = <?php echo json_encode($price); ?>;
    var TOUR = <?php echo json_encode($tour['title']); ?>;
    var WA = <?php echo json_encode($waNumber); ?>;

    var form = document.getElementById('pk-form');
    var wrap = document.getElementById('pk-form-wrap');
    var success = document.getElementById('pk-success');
    var errorBox = document.getElementById('pk-error');
    var submit = document.getElementById('pk-submit');
    var typeInput = document.getElementById('pk-type');
    var date = document.getElementById('pk-date');
    var phone = document.getElementById('pk-phone');
    var trav = document.getElementById('pk-travelers');
    var money = function (n) { return '$' + Math.round(n).toLocaleString('en-US'); };

    // Earliest date: tomorrow (booking-handler.php enforces the same rule).
    var tomorrow = new Date(Date.now() + 864e5);
    date.min = tomorrow.toISOString().slice(0, 10);

    // Travellers stepper + live estimate.
    var clamp = function () {
        var n = parseInt(trav.value, 10);
        if (isNaN(n) || n < 1) n = 1;
        if (n > 50) n = 50;
        trav.value = n;
        return n;
    };
    var refresh = function () {
        var n = clamp();
        document.getElementById('pk-total-label').textContent = money(PRICE) + ' × ' + n + (n === 1 ? ' traveller' : ' travellers');
        document.getElementById('pk-total').textContent = money(PRICE * n);
        document.querySelector('[data-step="-1"]').disabled = n <= 1;
        document.querySelector('[data-step="1"]').disabled = n >= 50;
    };
    document.querySelectorAll('[data-step]').forEach(function (b) {
        b.addEventListener('click', function () { trav.value = clamp() + parseInt(b.dataset.step, 10); refresh(); });
    });
    trav.addEventListener('input', function () { if (trav.value !== '') refresh(); });
    trav.addEventListener('blur', refresh);
    refresh();

    // Booking vs. enquiry: an enquiry needs neither a date nor a phone number.
    var setMode = function (mode) {
        typeInput.value = mode;
        document.querySelectorAll('[data-mode]').forEach(function (b) { b.setAttribute('aria-pressed', String(b.dataset.mode === mode)); });
        var enquiry = mode === 'enquire';
        date.required = !enquiry;
        phone.required = !enquiry;
        document.querySelectorAll('[data-optional]').forEach(function (el) { el.hidden = !enquiry; });
        document.getElementById('pk-msg-label').innerHTML = enquiry ? 'Your question' : 'Anything we should know? <em>(optional)</em>';
        submit.querySelector('span').textContent = enquiry ? 'Send question' : 'Request to book';
        submit.querySelector('i').className = enquiry ? 'fa-solid fa-paper-plane' : 'fa-solid fa-calendar-check';
    };
    document.querySelectorAll('[data-mode]').forEach(function (b) { b.addEventListener('click', function () { setMode(b.dataset.mode); }); });

    var showError = function (msg, field) {
        errorBox.textContent = msg;
        errorBox.classList.add('is-on');
        form.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
        if (field) { field.setAttribute('aria-invalid', 'true'); field.focus(); }
    };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorBox.classList.remove('is-on');
        var enquiry = typeInput.value === 'enquire';
        var checks = [
            [!enquiry && !date.value, 'Please choose your travel date.', date],
            [date.value && date.value < date.min, 'Please choose a travel date from tomorrow onwards.', date],
            [!form.full_name.value.trim(), 'Please enter your full name.', form.full_name],
            [!/^\S+@\S+\.\S+$/.test(form.email.value.trim()), 'Please enter a valid email address.', form.email],
            [!enquiry && !phone.value.trim(), 'Please add a phone number so we can confirm your booking with you.', phone],
            [enquiry && !form.message.value.trim(), 'Please type your question.', form.message],
        ];
        for (var i = 0; i < checks.length; i++) {
            if (checks[i][0]) { showError(checks[i][1], checks[i][2]); return; }
        }

        submit.disabled = true;
        var label = submit.querySelector('span').textContent;
        submit.querySelector('span').textContent = 'Sending…';

        fetch('booking-handler.php', { method: 'POST', body: new FormData(form) })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { showError(res.error || 'Something went wrong. Please try again.'); return; }
                var rows = [['Trip', res.tour], ['Travellers', res.travelers]];
                if (res.date) rows.push(['Travel date', res.date]);
                if (!enquiry) rows.push(['Estimated total', money(res.amount)]);
                var summary = document.getElementById('pk-summary');
                summary.innerHTML = '';
                rows.forEach(function (r) {
                    var d = document.createElement('div');
                    var s = document.createElement('span'); s.textContent = r[0];
                    var b = document.createElement('b'); b.textContent = r[1];
                    d.appendChild(s); d.appendChild(b); summary.appendChild(d);
                });
                document.getElementById('pk-success-title').textContent = enquiry ? 'Question sent' : 'Request received';
                document.getElementById('pk-ref').textContent = 'Ref ' + res.booking_id;
                document.getElementById('pk-success-msg').textContent = res.message;
                document.getElementById('pk-wa').href = 'https://wa.me/' + WA + '?text=' +
                    encodeURIComponent('Hello Luxit Global Escapes, about my ' + (enquiry ? 'question' : 'booking request') + ' ' + res.booking_id + ' for ' + TOUR + '.');
                wrap.style.display = 'none';
                success.classList.add('is-on');
                success.focus();
            })
            .catch(function () { showError('We could not reach our server. Please check your connection, or call +254 737 800 900.'); })
            .then(function () { submit.disabled = false; submit.querySelector('span').textContent = label; });
    });

    document.getElementById('pk-again').addEventListener('click', function () {
        form.reset(); refresh(); setMode('book');
        success.classList.remove('is-on');
        wrap.style.display = '';
    });

    // Booking bar: visible while the booking card is off screen.
    var card = document.getElementById('pk-book');
    var bar = document.getElementById('pk-bar');
    var goToCard = function () {
        var smoother = window.ScrollSmoother && ScrollSmoother.get && ScrollSmoother.get();
        if (smoother) smoother.scrollTo(Math.max(0, smoother.offset(card, 'top top') - 110), true);
        else card.scrollIntoView({ behavior: 'smooth' });
        setTimeout(function () { (date.value ? form.full_name : date).focus({ preventScroll: true }); }, 900);
    };
    document.getElementById('pk-bar-btn').addEventListener('click', goToCard);
    var updateBar = function () {
        var r = card.getBoundingClientRect();
        var onScreen = r.top < window.innerHeight && r.bottom > 80;
        bar.classList.toggle('is-on', !onScreen);
        bar.setAttribute('aria-hidden', String(onScreen));
    };
    var settle;
    window.addEventListener('scroll', function () {
        updateBar(); clearInterval(settle);
        var n = 0; settle = setInterval(function () { updateBar(); if (++n > 40) clearInterval(settle); }, 50);
    }, { passive: true });
    window.addEventListener('resize', updateBar);
    updateBar();
})();
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include 'footer.php'; ?>
