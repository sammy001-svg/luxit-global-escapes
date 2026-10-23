<?php
$page_title       = 'Contact Us | Luxit Global Escapes';
$page_description = 'Talk to the Luxit Global Escapes travel team in Karen, Nairobi. Call, WhatsApp or send us a message and we will reply within 24 hours.';
$page_css         = ['assets/css/contact.css'];
$header_variant   = 'solid';
include 'header.php';

$phoneDisplay = '+254 737 800 900';
$phoneLink    = '+254737800900';
$waNumber     = '254737800900';
$email        = 'info@luxitglobalescapes.com';
$address      = 'Tana House, Karen, Nairobi, Kenya';
$mapQuery     = rawurlencode($address);
$topics       = ['Plan a trip', 'Existing booking', 'Group or corporate travel', 'Partnerships', 'Something else'];
// Pre-filled from links on destination pages, e.g. contact.php?topic=Plan+a+trip&destination=Kenya
$preTopic     = in_array($_GET['topic'] ?? '', $topics, true) ? $_GET['topic'] : $topics[0];
$preDest      = mb_substr(trim((string)($_GET['destination'] ?? '')), 0, 100);
// Destination suggestions come from the mega-menu list in header.php.
$destOptions  = [];
foreach (($mmRegions ?? []) as [$regionName, , , , $places]) {
    $destOptions[] = $regionName;
    foreach (array_keys($places) as $p) $destOptions[] = $p;
}
?>

<div id="smooth-wrapper">
    <div id="smooth-content">
        <div class="page-content">

        <div class="ct">
            <header class="ct-hero">
                <div class="ct-wrap">
                    <span class="ct-eyebrow">Contact us</span>
                    <h1 class="ct-title">Let's plan your next journey</h1>
                    <p class="ct-intro">Whether you have a destination in mind or just a feeling, our travel specialists will help you shape it. Send a message, call or WhatsApp us; we reply within 24 hours.</p>
                </div>
            </header>

            <div class="ct-wrap">
                <div class="ct-quick">
                    <a class="ct-qcard" href="tel:<?php echo $phoneLink; ?>">
                        <span class="ct-qicon"><i class="fa-solid fa-phone"></i></span>
                        <span><small>Call us</small><strong><?php echo $phoneDisplay; ?></strong><span class="ct-qnote">Speak to a travel specialist</span></span>
                    </a>
                    <a class="ct-qcard ct-qcard--wa" href="https://wa.me/<?php echo $waNumber; ?>?text=<?php echo rawurlencode('Hello Luxit Global Escapes, I would like help planning a trip.'); ?>" target="_blank" rel="noopener">
                        <span class="ct-qicon"><i class="fa-brands fa-whatsapp"></i></span>
                        <span><small>WhatsApp</small><strong>Chat with us</strong><span class="ct-qnote">Quick questions, quick answers</span></span>
                    </a>
                    <a class="ct-qcard" href="mailto:<?php echo $email; ?>">
                        <span class="ct-qicon"><i class="fa-solid fa-envelope"></i></span>
                        <span><small>Email</small><strong><?php echo $email; ?></strong><span class="ct-qnote">We reply within 24 hours</span></span>
                    </a>
                </div>

                <div class="ct-main">
                    <section class="ct-card ct-form-card" aria-labelledby="ct-form-title">
                        <div id="ct-form-wrap">
                            <h2 class="ct-h2" id="ct-form-title">Send us a message</h2>
                            <p class="ct-sub">Tell us a little about what you have in mind. The more we know, the better our first reply will be.</p>

                            <form id="ct-form" novalidate>
                                <input type="text" name="website" class="ct-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                                <div class="ct-grid">
                                    <div class="ct-error" id="ct-error" role="alert"></div>

                                    <fieldset class="ct-field" style="border:0;padding:0;margin:0">
                                        <legend class="ct-label" style="margin-bottom:8px">What can we help with?</legend>
                                        <div class="ct-topics">
                                            <?php foreach ($topics as $i => $t) : ?>
                                            <label class="ct-chip"><input type="radio" name="topic" value="<?php echo htmlspecialchars($t); ?>"<?php echo $t === $preTopic ? ' checked' : ''; ?>><span><?php echo htmlspecialchars($t); ?></span></label>
                                            <?php endforeach; ?>
                                        </div>
                                    </fieldset>

                                    <div class="ct-grid ct-grid--2">
                                        <div class="ct-field">
                                            <label class="ct-label" for="ct-name">Full name</label>
                                            <input class="ct-input" id="ct-name" name="full_name" autocomplete="name" maxlength="100" required>
                                        </div>
                                        <div class="ct-field">
                                            <label class="ct-label" for="ct-email">Email</label>
                                            <input class="ct-input" id="ct-email" name="email" type="email" autocomplete="email" maxlength="100" required>
                                        </div>
                                    </div>

                                    <div class="ct-grid ct-grid--2">
                                        <div class="ct-field">
                                            <label class="ct-label" for="ct-phone">Phone / WhatsApp <em id="ct-phone-opt">(optional)</em></label>
                                            <input class="ct-input" id="ct-phone" name="phone" type="tel" autocomplete="tel" placeholder="+254 700 000 000">
                                        </div>
                                        <div class="ct-field">
                                            <label class="ct-label" for="ct-method">Best way to reach you</label>
                                            <select class="ct-input" id="ct-method" name="contact_method">
                                                <option>Email</option>
                                                <option>WhatsApp</option>
                                                <option>Phone call</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="ct-trip" id="ct-trip">
                                        <p class="ct-trip-title">Your trip (if you know)</p>
                                        <div class="ct-grid ct-grid--3">
                                            <div class="ct-field">
                                                <label class="ct-label" for="ct-dest">Destination</label>
                                                <input class="ct-input" id="ct-dest" name="destination" list="ct-dest-list" maxlength="100" placeholder="e.g. Kenya, Zanzibar" value="<?php echo htmlspecialchars($preDest); ?>">
                                                <datalist id="ct-dest-list"><?php foreach ($destOptions as $d) echo '<option value="' . htmlspecialchars($d) . '">'; ?></datalist>
                                            </div>
                                            <div class="ct-field">
                                                <label class="ct-label" for="ct-month">When</label>
                                                <input class="ct-input" id="ct-month" name="travel_month" type="month" min="<?php echo date('Y-m'); ?>">
                                            </div>
                                            <div class="ct-field">
                                                <label class="ct-label" for="ct-trav">Travellers</label>
                                                <input class="ct-input" id="ct-trav" name="travelers" type="number" min="1" max="500" inputmode="numeric" placeholder="2">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ct-field">
                                        <label class="ct-label" for="ct-msg">Your message</label>
                                        <textarea class="ct-input" id="ct-msg" name="message" maxlength="3000" required placeholder="Dates, budget, who is travelling, the experiences you are dreaming of…"></textarea>
                                    </div>

                                    <div class="ct-actions">
                                        <span class="ct-privacy"><i class="fa-solid fa-lock"></i> We only use your details to reply to you.</span>
                                        <button type="submit" class="ct-submit" id="ct-submit"><span>Send message</span> <i class="fa-solid fa-paper-plane"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="ct-success" id="ct-success" role="status" tabindex="-1">
                            <div class="ct-success-icon"><i class="fa-solid fa-check"></i></div>
                            <h2 class="ct-h2">Message sent</h2>
                            <span class="ct-ref" id="ct-ref"></span>
                            <p id="ct-success-msg"></p>
                            <div class="ct-success-actions">
                                <a class="ct-btn ct-btn--primary" href="international-packages.php">Browse packages</a>
                                <button type="button" class="ct-btn" id="ct-again">Send another message</button>
                            </div>
                        </div>
                    </section>

                    <aside class="ct-aside" aria-label="Contact details">
                        <div class="ct-card ct-info">
                            <h3>Talk to us directly</h3>
                            <div class="ct-info-row"><i class="fa-solid fa-phone"></i><div><small>Phone</small><a href="tel:<?php echo $phoneLink; ?>"><?php echo $phoneDisplay; ?></a></div></div>
                            <div class="ct-info-row"><i class="fa-brands fa-whatsapp"></i><div><small>WhatsApp</small><a href="https://wa.me/<?php echo $waNumber; ?>" target="_blank" rel="noopener"><?php echo $phoneDisplay; ?></a></div></div>
                            <div class="ct-info-row"><i class="fa-solid fa-envelope"></i><div><small>Email</small><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></div></div>
                            <div class="ct-info-row"><i class="fa-solid fa-location-dot"></i><div><small>Office</small><span><?php echo $address; ?></span></div></div>
                            <div class="ct-social" aria-label="Follow us">
                                <a href="https://www.facebook.com/luxitafrica" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="https://x.com/Luxitafrica" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                                <a href="https://www.instagram.com/p/DIoeX4NuF4S/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="https://www.tiktok.com/@luxitglobalescapes" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                            </div>
                        </div>

                        <div class="ct-card ct-map">
                            <iframe title="Map: <?php echo $address; ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                    src="https://maps.google.com/maps?q=<?php echo $mapQuery; ?>&amp;z=15&amp;output=embed"></iframe>
                            <div class="ct-map-foot">
                                <span>Karen, Nairobi</span>
                                <a href="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo $mapQuery; ?>" target="_blank" rel="noopener">Get directions <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <section class="ct-faq" aria-labelledby="ct-faq-title">
                <div class="ct-wrap ct-faq-grid">
                    <div>
                        <span class="ct-eyebrow">Good to know</span>
                        <h2 id="ct-faq-title">Before you get in touch</h2>
                        <p class="ct-sub">A few answers to the questions we are asked most often.</p>
                    </div>
                    <div>
                        <details class="ct-qa" open>
                            <summary>How quickly will you reply?</summary>
                            <div>Within 24 hours, by your preferred method: email, phone or WhatsApp. For anything urgent, calling or WhatsApp is fastest.</div>
                        </details>
                        <details class="ct-qa">
                            <summary>Does it cost anything to ask for a quote?</summary>
                            <div>No. Asking questions and requesting a quote is free. We only arrange payment once you are happy with a confirmed itinerary.</div>
                        </details>
                        <details class="ct-qa">
                            <summary>Can you tailor a package to me?</summary>
                            <div>Yes. Every package on our site can be adjusted to your dates, group size and budget, and we also plan trips from scratch.</div>
                        </details>
                        <details class="ct-qa">
                            <summary>I already have a booking. How do I change it?</summary>
                            <div>Choose "Existing booking" above and include your booking reference (it starts with BK-). Our team will pick it up from there.</div>
                        </details>
                    </div>
                </div>
            </section>
        </div>

        </div><!-- /page-content -->

<?php ob_start(); ?>
<script>
(function () {
    var form = document.getElementById('ct-form');
    var wrap = document.getElementById('ct-form-wrap');
    var success = document.getElementById('ct-success');
    var errorBox = document.getElementById('ct-error');
    var submit = document.getElementById('ct-submit');
    var method = document.getElementById('ct-method');
    var phone = document.getElementById('ct-phone');
    var trip = document.getElementById('ct-trip');

    // Phone becomes required when they ask to be called or messaged.
    var syncMethod = function () {
        var needsPhone = method.value !== 'Email';
        phone.required = needsPhone;
        document.getElementById('ct-phone-opt').hidden = needsPhone;
    };
    method.addEventListener('change', syncMethod);
    syncMethod();

    // Trip details only matter when planning a trip or a group.
    var syncTopic = function () {
        var t = form.querySelector('[name="topic"]:checked').value;
        trip.hidden = !(t === 'Plan a trip' || t === 'Group or corporate travel');
    };
    form.querySelectorAll('[name="topic"]').forEach(function (r) { r.addEventListener('change', syncTopic); });
    syncTopic();

    var showError = function (msg, field) {
        errorBox.textContent = msg;
        errorBox.classList.add('is-on');
        form.querySelectorAll('[aria-invalid]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
        if (field) { field.setAttribute('aria-invalid', 'true'); field.focus(); }
    };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorBox.classList.remove('is-on');
        var checks = [
            [!form.full_name.value.trim(), 'Please enter your name.', form.full_name],
            [!/^\S+@\S+\.\S+$/.test(form.email.value.trim()), 'Please enter a valid email address.', form.email],
            [phone.required && !phone.value.trim(), 'Please add your phone number so we can ' + (method.value === 'WhatsApp' ? 'message you on WhatsApp.' : 'call you.'), phone],
            [!form.message.value.trim(), 'Please tell us a little about what you need.', form.message],
        ];
        for (var i = 0; i < checks.length; i++) {
            if (checks[i][0]) { showError(checks[i][1], checks[i][2]); return; }
        }
        submit.disabled = true;
        submit.querySelector('span').textContent = 'Sending…';

        var data = new FormData(form);
        if (trip.hidden) { data.delete('destination'); data.delete('travel_month'); data.delete('travelers'); }

        fetch('contact-handler.php', { method: 'POST', body: data })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res.success) { showError(res.error || 'Something went wrong. Please try again.'); return; }
                document.getElementById('ct-ref').textContent = 'Ref ' + res.booking_id;
                document.getElementById('ct-success-msg').textContent = res.message;
                wrap.style.display = 'none';
                success.classList.add('is-on');
                success.focus();
            })
            .catch(function () { showError('We could not reach our server. Please check your connection, or call +254 737 800 900.'); })
            .then(function () { submit.disabled = false; submit.querySelector('span').textContent = 'Send message'; });
    });

    document.getElementById('ct-again').addEventListener('click', function () {
        form.reset(); syncMethod(); syncTopic();
        success.classList.remove('is-on');
        wrap.style.display = '';
    });
})();
</script>
<?php $page_scripts = ob_get_clean(); ?>

<?php include 'footer.php'; ?>
