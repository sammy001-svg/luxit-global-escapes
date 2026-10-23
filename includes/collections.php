<?php
/**
 * Tour collection pages: the three package pages (packages chosen with the
 * admin's "Package Pages" checkboxes) and the seven travel styles (packages
 * chosen with the admin's "Travel Styles" checkboxes).
 *
 * Edit the wording here; layout is includes/collection-view.php. Each page
 * file (safari-packages.php, halal-travel.php, …) just names its key.
 *
 * destinations: keys from includes/destinations.php, linked as suggestions.
 */

const TOUR_COLLECTIONS = [
    // ── Package pages (source: tours.package_pages) ─────────────────────────
    'safari' => ['kind' => 'packages', 'source' => 'Safari', 'page' => 'safari-packages.php',
        'name' => 'Safari Packages', 'short' => 'Safari',
        'image' => 'assets/images/header-carousel/safari.png',
        'tagline' => 'Game drives, private conservancies and nights under canvas',
        'intro' => 'Our safaris are planned by a team based in Nairobi, close to the parks we know best. Choose a ready-made itinerary below, then let us adjust the lodges, pace and dates around you.',
        'destinations' => ['kenya', 'tanzania', 'uganda', 'rwanda', 'botswana', 'south-africa']],
    'local' => ['kind' => 'packages', 'source' => 'Local', 'page' => 'local-packages.php',
        'name' => 'Local Packages', 'short' => 'Local',
        'image' => 'assets/images/header-banners/destination-africa.png',
        'tagline' => 'Kenya and East Africa, planned by people who live here',
        'intro' => 'Weekend escapes, coast holidays and safaris close to home. These packages are ideal for residents and visitors already in the region who want local knowledge and easy logistics.',
        'destinations' => ['kenya', 'tanzania', 'uganda', 'rwanda', 'seychelles', 'madagascar']],
    'international' => ['kind' => 'packages', 'source' => 'International', 'page' => 'international-packages.php',
        'name' => 'International Packages', 'short' => 'International',
        'image' => 'assets/images/header-carousel/maldives.png',
        'tagline' => 'Islands, great cities and journeys across the world',
        'intro' => 'From overwater villas in the Maldives to the capitals of Europe, our international packages take care of flights, transfers and hotels so you can simply enjoy the trip.',
        'destinations' => ['maldives', 'dubai', 'indonesia', 'thailand', 'france', 'italy']],

    // ── Travel styles (source: tours.travel_styles) ─────────────────────────
    'romantic' => ['kind' => 'style', 'source' => 'romantic', 'page' => 'romantic-travel.php',
        'name' => 'Romantic Travel', 'short' => 'Romantic', 'icon' => 'fa-heart',
        'image' => 'assets/images/destinations/style1/pic2.jpg',
        'tagline' => 'Honeymoons, anniversaries and escapes for two',
        'intro' => 'Whether it is a honeymoon, an anniversary or simply time together, we plan romantic journeys with privacy, beautiful settings and the small touches that make a trip feel personal.',
        'how' => ['Private, intimate stays' => 'Villas, suites and lodges chosen for their setting and seclusion.',
                  'Moments to remember' => 'Sunset cruises, private dinners and celebrations arranged in advance.',
                  'An easy pace' => 'Fewer transfers and more time to relax, so the trip never feels rushed.',
                  'Honeymoon planning' => 'Tell us your dates and we will help combine safari, beach and city.'],
        'destinations' => ['maldives', 'seychelles', 'greece', 'italy', 'france', 'tanzania']],
    'wellness' => ['kind' => 'style', 'source' => 'wellness', 'page' => 'wellness-travel.php',
        'name' => 'Wellness Travel', 'short' => 'Wellness', 'icon' => 'fa-spa',
        'image' => 'assets/images/destinations/style1/pic5.jpg',
        'tagline' => 'Retreats, spas and slow travel that restores you',
        'intro' => 'Step away from the everyday with trips built around rest and renewal: yoga and spa retreats, nature, nourishing food and time to breathe.',
        'how' => ['Retreats that fit you' => 'From spa resorts to yoga and Ayurveda programmes.',
                  'Nourishing food' => 'Healthy menus and dietary requirements arranged ahead.',
                  'Nature and quiet' => 'Calm locations away from crowds, with time outdoors.',
                  'Unhurried itineraries' => 'Longer stays in fewer places, so you return rested.'],
        'destinations' => ['indonesia', 'india', 'thailand', 'maldives', 'seychelles', 'morocco']],
    'halal' => ['kind' => 'style', 'source' => 'halal', 'page' => 'halal-travel.php',
        'name' => 'Halal Travel', 'short' => 'Halal', 'icon' => 'fa-moon',
        'image' => 'assets/images/header-banners/destination-middle-east.png',
        'tagline' => 'Halal dining, prayer-friendly stays and family-friendly trips',
        'intro' => 'We plan Muslim-friendly journeys with halal food, prayer times and comfortable accommodation in mind, so you can travel with peace of mind and focus on the experience.',
        'how' => ['Halal dining' => 'Restaurants and hotels that serve halal food, confirmed before you travel.',
                  'Prayer-friendly planning' => 'Itineraries that leave time for prayer, with nearby mosques noted.',
                  'Comfortable stays' => 'Alcohol-free and family-friendly options, and private pools on request.',
                  'Ramadan and Eid travel' => 'Trips adapted to the season, including iftar arrangements.'],
        'destinations' => ['malaysia', 'turkey', 'dubai', 'morocco', 'egypt', 'indonesia']],
    'kosher' => ['kind' => 'style', 'source' => 'kosher', 'page' => 'kosher-travel.php',
        'name' => 'Kosher Travel', 'short' => 'Kosher', 'icon' => 'fa-star-of-david',
        'image' => 'assets/images/destinations/style1/pic8.jpg',
        'tagline' => 'Kosher meals, Shabbat planning and Jewish heritage',
        'intro' => 'We arrange journeys that respect kashrut and Shabbat, from sourcing kosher meals to planning travel days around the holidays, with Jewish heritage sites included where you wish.',
        'how' => ['Kosher meals arranged' => 'Kosher catering, restaurants or supervised kitchens, as available.',
                  'Shabbat-aware itineraries' => 'No travel on Shabbat, with hotels within walking distance where possible.',
                  'Jewish heritage' => 'Synagogues, historic quarters and local communities on request.',
                  'Holiday travel' => 'Pesach and other holiday trips planned well in advance.'],
        'destinations' => ['italy', 'france', 'spain', 'morocco', 'south-africa', 'greece']],
    'faith' => ['kind' => 'style', 'source' => 'faith', 'page' => 'faith-travel.php',
        'name' => 'Faith Travel', 'short' => 'Faith', 'icon' => 'fa-place-of-worship',
        'image' => 'assets/images/header-banners/destination-europe.png',
        'tagline' => 'Pilgrimages and journeys to sacred and historic sites',
        'intro' => 'We organise pilgrimages and faith-based journeys for individuals, families and church or community groups, with knowledgeable guides and time for reflection.',
        'how' => ['Meaningful itineraries' => 'Sacred and historic sites at a respectful, unhurried pace.',
                  'Expert guides' => 'Guides who understand the history and significance of each place.',
                  'Group travel' => 'Logistics for parishes, congregations and community groups.',
                  'Time for reflection' => 'Space for services, prayer and quiet moments.'],
        'destinations' => ['italy', 'jordan', 'egypt', 'turkey', 'greece', 'india']],
    'accessible' => ['kind' => 'style', 'source' => 'accessible', 'page' => 'accessible-travel.php',
        'name' => 'Accessible Travel', 'short' => 'Accessible', 'icon' => 'fa-wheelchair',
        'image' => 'assets/images/destinations/style1/pic3.jpg',
        'tagline' => 'Step-free stays, adapted transport and trips at your pace',
        'intro' => 'Travel should be open to everyone. Tell us about your needs and we will check every hotel, vehicle and activity in detail before you book, so there are no surprises on arrival.',
        'how' => ['Checked in detail' => 'Room layouts, bathrooms and access confirmed with each property.',
                  'Adapted transport' => 'Vehicles with ramps or lifts, and help at airports.',
                  'Activities that work for you' => 'Experiences chosen around your mobility and energy.',
                  'A pace that suits you' => 'Rest days and shorter transfers built into the plan.'],
        'destinations' => ['dubai', 'singapore', 'south-africa', 'kenya', 'spain', 'maldives']],
    'mice' => ['kind' => 'style', 'source' => 'mice', 'page' => 'mice-travel.php',
        'name' => 'MICE Travel', 'short' => 'MICE', 'icon' => 'fa-briefcase',
        'image' => 'assets/images/background/adv-bg-2.jpg',
        'tagline' => 'Meetings, incentives, conferences and events',
        'intro' => 'From board retreats to incentive trips and international conferences, we handle venues, travel and ground logistics so your team and guests can focus on the occasion.',
        'how' => ['Venues and accommodation' => 'Conference venues, retreat lodges and group rooms sourced to brief.',
                  'Group travel logistics' => 'Flights, transfers and on-the-ground coordination.',
                  'Incentive experiences' => 'Safaris, gala dinners and activities that reward your team.',
                  'One point of contact' => 'A dedicated planner from the first brief to the last transfer.'],
        'destinations' => ['kenya', 'dubai', 'rwanda', 'south-africa', 'singapore', 'turkey']],
];

/** Keys stored in tours.travel_styles (admin checkboxes). */
const TRAVEL_STYLE_KEYS = ['romantic', 'wellness', 'halal', 'kosher', 'faith', 'accessible', 'mice'];

if (!function_exists('collectionPackages')) {
    /** Active packages for a collection, newest first. */
    function collectionPackages(PDO $pdo, array $c): array {
        try {
            $column = $c['kind'] === 'packages' ? 'package_pages' : 'travel_styles';
            $st = $pdo->prepare(
                "SELECT id, title, location, price, duration, image, created_at FROM tours
                 WHERE status = 'Active' AND FIND_IN_SET(?, $column) > 0
                 ORDER BY created_at DESC"
            );
            $st->execute([$c['source']]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // travel_styles arrives with migrate.php; until then style pages
            // simply show the tailor-made panel.
            error_log('collectionPackages: ' . $e->getMessage());
            return [];
        }
    }
}
