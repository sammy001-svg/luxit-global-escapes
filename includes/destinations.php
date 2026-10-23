<?php
/**
 * Destination content for the region and country pages.
 *
 * Edit the text here; every destination page, the destinations overview and
 * the region pages read from it. Each country page is a two-line file that
 * names its key (see kenya.php).
 *
 * Photos: a country uses its own photo when one exists at
 * assets/images/destinations/countries/{key}.jpg (or .png / .webp), and its
 * region's photo otherwise. Landscape, 1600px wide or more works best.
 *
 * keywords: words matched against a package's title and location to list it
 * on this destination (the country name is always included).
 */

const DEST_REGIONS = [
    'africa' => [
        'name'  => 'Africa',
        'page'  => 'africa.php',
        'image' => 'assets/images/header-banners/destination-africa.png',
        'tagline' => 'Safaris, gorilla treks and Indian Ocean islands',
        'intro' => 'From the Great Migration on the Maasai Mara to gorilla families in the misty forests of Rwanda and Uganda, Africa is where our team was born. Pair a safari with the beaches of Zanzibar or the Seychelles, or explore the ancient wonders of Egypt and Morocco.',
    ],
    'asia' => [
        'name'  => 'Asia',
        'page'  => 'asia.php',
        'image' => 'assets/images/header-banners/destination-asia.png',
        'tagline' => 'Island hideaways, temples and vibrant cities',
        'intro' => 'Overwater villas in the Maldives, rice terraces in Bali, street food in Penang and the palaces of Rajasthan. Asia rewards every kind of traveller, and our specialists know which island, season and hotel suits you best.',
    ],
    'middle-east' => [
        'name'  => 'Middle East',
        'page'  => 'middle-east.php',
        'image' => 'assets/images/header-banners/destination-middle-east.png',
        'tagline' => 'Desert luxury, ancient wonders and city breaks',
        'intro' => 'Skyline glamour in Dubai, dramatic wadis and sands in Oman, and the rose-red city of Petra in Jordan. The Middle East makes an easy, rewarding escape, whether for a long weekend or a once-in-a-lifetime journey.',
    ],
    'europe' => [
        'name'  => 'Europe',
        'page'  => 'europe.php',
        'image' => 'assets/images/header-banners/destination-europe.png',
        'tagline' => 'Historic capitals, coastlines and fine food',
        'intro' => 'Art and cafés in Paris, the hill towns of Tuscany, flamenco in Andalusia and sunsets over Santorini. We plan European journeys at an unhurried pace, with the right mix of icons and quieter corners.',
    ],
];

const DEST_COUNTRIES = [
    // ── Africa ──────────────────────────────────────────────────────────────
    'kenya' => ['name' => 'Kenya', 'region' => 'africa',
        'tagline' => 'Big-game safaris, Rift Valley lakes and Indian Ocean beaches',
        'intro' => 'Kenya is the home of the classic safari. Watch the Great Migration thunder across the Maasai Mara, see elephants beneath Kilimanjaro in Amboseli, then unwind on the white sands of Diani or in the old Swahili town of Lamu.',
        'highlights' => ['Maasai Mara and the Great Migration' => 'River crossings and big cats on Kenya\'s most famous reserve.',
                         'Amboseli' => 'Large elephant herds with Kilimanjaro on the horizon.',
                         'Rift Valley lakes' => 'Boat safaris on Naivasha and rhinos at Lake Nakuru.',
                         'The Swahili coast' => 'Diani beach, Watamu reefs and historic Lamu.'],
        'best' => 'Late June to October, and January to February', 'ideal' => ['Safari', 'Families', 'Honeymoon'],
        'keywords' => ['Maasai Mara', 'Masai Mara', 'Nairobi', 'Amboseli', 'Mombasa', 'Diani', 'Naivasha', 'Samburu', 'Tsavo']],
    'tanzania' => ['name' => 'Tanzania', 'region' => 'africa',
        'tagline' => 'The Serengeti, Ngorongoro and the spice island of Zanzibar',
        'intro' => 'Tanzania combines some of Africa\'s greatest wildlife spectacles with its highest mountain and an island steeped in history. Follow the herds across the Serengeti, descend into the Ngorongoro Crater and finish on Zanzibar\'s beaches.',
        'highlights' => ['Serengeti National Park' => 'Endless plains and the Great Migration year-round.',
                         'Ngorongoro Crater' => 'A volcanic caldera teeming with wildlife.',
                         'Mount Kilimanjaro' => 'Africa\'s highest peak, for trekkers or views alone.',
                         'Zanzibar' => 'Stone Town, spice farms and turquoise beaches.'],
        'best' => 'June to October; January to February for calving season', 'ideal' => ['Safari', 'Adventure', 'Beach'],
        'keywords' => ['Serengeti', 'Zanzibar', 'Kilimanjaro', 'Ngorongoro', 'Arusha']],
    'uganda' => ['name' => 'Uganda', 'region' => 'africa',
        'tagline' => 'Mountain gorillas, chimpanzees and the source of the Nile',
        'intro' => 'Uganda, the "Pearl of Africa", is one of the few places on earth to sit quietly with mountain gorillas. Add chimpanzee tracking, boat safaris on the Kazinga Channel and the thundering Murchison Falls.',
        'highlights' => ['Bwindi Impenetrable Forest' => 'Gorilla trekking with expert rangers.',
                         'Murchison Falls' => 'The Nile forced through a narrow gorge.',
                         'Queen Elizabeth National Park' => 'Boat safaris and the tree-climbing lions of Ishasha.',
                         'Jinja' => 'The source of the Nile and white-water rafting.'],
        'best' => 'June to August, and December to February', 'ideal' => ['Wildlife', 'Adventure', 'Photography'],
        'keywords' => ['Bwindi', 'Kampala', 'Murchison', 'Entebbe']],
    'rwanda' => ['name' => 'Rwanda', 'region' => 'africa',
        'tagline' => 'Gorilla treks in the land of a thousand hills',
        'intro' => 'Compact, green and welcoming, Rwanda pairs world-class gorilla trekking in Volcanoes National Park with chimpanzees in Nyungwe Forest, the peaceful shores of Lake Kivu and the clean, lively capital of Kigali.',
        'highlights' => ['Volcanoes National Park' => 'Mountain gorillas and golden monkeys.',
                         'Nyungwe Forest' => 'Chimpanzees and a canopy walkway.',
                         'Lake Kivu' => 'Lakeside relaxation between treks.',
                         'Kigali' => 'Markets, art and the moving Genocide Memorial.'],
        'best' => 'June to September, and December to February', 'ideal' => ['Wildlife', 'Luxury', 'Culture'],
        'keywords' => ['Kigali', 'Volcanoes', 'Nyungwe']],
    'south-africa' => ['name' => 'South Africa', 'region' => 'africa',
        'tagline' => 'Cape Town, the Winelands and the Kruger',
        'intro' => 'South Africa packs a remarkable variety into one trip: Table Mountain and the beaches of Cape Town, world-class wine estates, the scenic Garden Route and Big Five safaris in and around the Kruger.',
        'highlights' => ['Cape Town' => 'Table Mountain, the Cape Peninsula and the V&A Waterfront.',
                         'Cape Winelands' => 'Stellenbosch and Franschhoek wine estates.',
                         'Kruger National Park' => 'Big Five safaris and private game reserves.',
                         'The Garden Route' => 'A coastal road trip of forests and lagoons.'],
        'best' => 'Year-round; the Cape is best November to March, safaris May to September', 'ideal' => ['Safari', 'Food & wine', 'Road trips'],
        'keywords' => ['Cape Town', 'Kruger', 'Johannesburg', 'Garden Route']],
    'botswana' => ['name' => 'Botswana', 'region' => 'africa',
        'tagline' => 'The Okavango Delta and Chobe\'s great elephant herds',
        'intro' => 'Botswana is a quiet, exclusive safari destination. Glide through the Okavango Delta by mokoro, watch elephants gather along the Chobe River and spend a night under the stars on the Makgadikgadi salt pans.',
        'highlights' => ['Okavango Delta' => 'Mokoro trips through a vast inland delta.',
                         'Chobe National Park' => 'River safaris among huge elephant herds.',
                         'Moremi Game Reserve' => 'Predators in a mosaic of lagoons and woodland.',
                         'Makgadikgadi Pans' => 'Salt flats, meerkats and starlit nights.'],
        'best' => 'June to October', 'ideal' => ['Safari', 'Luxury', 'Honeymoon'],
        'keywords' => ['Okavango', 'Chobe', 'Maun']],
    'namibia' => ['name' => 'Namibia', 'region' => 'africa',
        'tagline' => 'Towering red dunes, desert wildlife and wild coastlines',
        'intro' => 'Namibia is a land of huge skies and dramatic landscapes, ideal for a self-drive or a guided journey. Climb the dunes of Sossusvlei, spot wildlife at Etosha\'s waterholes and explore the haunting Skeleton Coast.',
        'highlights' => ['Sossusvlei' => 'Some of the tallest sand dunes on earth.',
                         'Etosha National Park' => 'Wildlife gathering at desert waterholes.',
                         'Skeleton Coast' => 'Shipwrecks, seals and fog-swept shores.',
                         'Swakopmund' => 'A coastal base for desert adventures.'],
        'best' => 'May to October', 'ideal' => ['Adventure', 'Photography', 'Road trips'],
        'keywords' => ['Sossusvlei', 'Etosha', 'Windhoek', 'Swakopmund']],
    'zambia' => ['name' => 'Zambia', 'region' => 'africa',
        'tagline' => 'Victoria Falls, walking safaris and the Zambezi',
        'intro' => 'Zambia is the birthplace of the walking safari and one side of the mighty Victoria Falls. Track wildlife on foot in South Luangwa, canoe past elephants on the Lower Zambezi and feel the spray of the falls.',
        'highlights' => ['Victoria Falls' => 'The "smoke that thunders", seen from Livingstone.',
                         'South Luangwa' => 'Pioneering walking safaris and leopards.',
                         'Lower Zambezi' => 'Canoe safaris along the river.',
                         'Kafue National Park' => 'Vast, remote wilderness.'],
        'best' => 'June to October; the falls are fullest February to May', 'ideal' => ['Safari', 'Adventure'],
        'keywords' => ['Livingstone', 'Luangwa', 'Victoria Falls', 'Zambezi']],
    'zimbabwe' => ['name' => 'Zimbabwe', 'region' => 'africa',
        'tagline' => 'Victoria Falls, Hwange and ancient stone ruins',
        'intro' => 'Zimbabwe offers some of the best views of Victoria Falls, excellent guiding in Hwange and Mana Pools, and the remarkable ruins of Great Zimbabwe, the medieval stone city that gave the country its name.',
        'highlights' => ['Victoria Falls' => 'Rainforest trails along the falls.',
                         'Hwange National Park' => 'Large elephant herds and predators.',
                         'Mana Pools' => 'Riverside safaris on the Zambezi.',
                         'Great Zimbabwe' => 'Ancient stone ruins and history.'],
        'best' => 'May to October', 'ideal' => ['Safari', 'History', 'Adventure'],
        'keywords' => ['Victoria Falls', 'Hwange', 'Harare', 'Mana Pools']],
    'seychelles' => ['name' => 'Seychelles', 'region' => 'africa',
        'tagline' => 'Granite-strewn beaches and island hopping in the Indian Ocean',
        'intro' => 'The Seychelles is made for unhurried island life. Swim from some of the world\'s most beautiful beaches, walk among giant tortoises and the rare coco de mer palms, and hop between Mahé, Praslin and La Digue.',
        'highlights' => ['La Digue' => 'Anse Source d\'Argent and bicycle-paced island life.',
                         'Praslin' => 'The Vallée de Mai and Anse Lazio.',
                         'Mahé' => 'Mountain trails, markets and Victoria.',
                         'Snorkelling and diving' => 'Clear water and coral reefs.'],
        'best' => 'April to May, and October to November', 'ideal' => ['Honeymoon', 'Beach', 'Luxury'],
        'keywords' => ['Mahé', 'Mahe', 'Praslin', 'La Digue']],
    'madagascar' => ['name' => 'Madagascar', 'region' => 'africa',
        'tagline' => 'Lemurs, baobabs and wildlife found nowhere else',
        'intro' => 'Madagascar is a world of its own, home to lemurs, chameleons and landscapes found nowhere else. See the Avenue of the Baobabs at sunset, explore the limestone Tsingy and relax on the beaches of Nosy Be.',
        'highlights' => ['Andasibe-Mantadia' => 'The haunting call of the indri lemur.',
                         'Avenue of the Baobabs' => 'Ancient trees near Morondava.',
                         'Tsingy de Bemaraha' => 'A forest of limestone pinnacles.',
                         'Nosy Be' => 'Island beaches and marine life.'],
        'best' => 'April to November', 'ideal' => ['Wildlife', 'Adventure', 'Photography'],
        'keywords' => ['Nosy Be', 'Antananarivo', 'Morondava']],
    'morocco' => ['name' => 'Morocco', 'region' => 'africa',
        'tagline' => 'Imperial cities, the Atlas Mountains and Sahara nights',
        'intro' => 'Morocco is a feast for the senses. Lose yourself in the souks of Marrakech and Fes, cross the High Atlas, spend a night in a Sahara desert camp and wander the blue-washed streets of Chefchaouen.',
        'highlights' => ['Marrakech' => 'Souks, riads and the Jemaa el-Fnaa square.',
                         'Fes' => 'The world\'s largest car-free medina.',
                         'The Sahara' => 'Camel treks and desert camps near Merzouga.',
                         'Chefchaouen' => 'The blue city of the Rif Mountains.'],
        'best' => 'March to May, and September to November', 'ideal' => ['Culture', 'Food', 'Adventure'],
        'keywords' => ['Marrakech', 'Fes', 'Casablanca', 'Sahara']],
    'egypt' => ['name' => 'Egypt', 'region' => 'africa',
        'tagline' => 'Pyramids, Nile cruises and the Red Sea',
        'intro' => 'Egypt brings ancient history vividly to life. Stand before the Pyramids of Giza, cruise the Nile between Luxor and Aswan, explore the Valley of the Kings and dive the coral reefs of the Red Sea.',
        'highlights' => ['Giza and Cairo' => 'The Pyramids, the Sphinx and great museums.',
                         'Nile cruise' => 'Temples between Luxor and Aswan.',
                         'Valley of the Kings' => 'Royal tombs on Luxor\'s west bank.',
                         'The Red Sea' => 'World-class diving and beach resorts.'],
        'best' => 'October to April', 'ideal' => ['History', 'Culture', 'Family'],
        'keywords' => ['Cairo', 'Giza', 'Luxor', 'Aswan', 'Nile', 'Hurghada', 'Sharm']],

    // ── Asia ────────────────────────────────────────────────────────────────
    'maldives' => ['name' => 'Maldives', 'region' => 'asia',
        'tagline' => 'Overwater villas, house reefs and barefoot luxury',
        'intro' => 'The Maldives is the ultimate island escape: one resort, one island, crystal lagoons on every side. Snorkel house reefs, swim with manta rays and dine on a private sandbank. We will help you choose the right island for you.',
        'highlights' => ['Overwater villas' => 'Step from your deck straight into the lagoon.',
                         'House reefs' => 'Snorkelling with turtles and reef sharks.',
                         'Manta rays and whale sharks' => 'Seasonal encounters in the atolls.',
                         'Sandbank dinners' => 'Private dining under the stars.'],
        'best' => 'November to April', 'ideal' => ['Honeymoon', 'Luxury', 'Beach'],
        'keywords' => ['Male', 'Malé', 'Atoll']],
    'thailand' => ['name' => 'Thailand', 'region' => 'asia',
        'tagline' => 'Golden temples, island beaches and world-famous food',
        'intro' => 'Thailand blends vibrant cities, gentle hill country and idyllic islands. Explore Bangkok\'s temples and markets, meet elephants responsibly near Chiang Mai and finish on the beaches of Phuket, Krabi or Koh Samui.',
        'highlights' => ['Bangkok' => 'The Grand Palace, canals and street food.',
                         'Chiang Mai' => 'Old-city temples and mountain scenery.',
                         'The islands' => 'Phuket, Krabi, Koh Samui and beyond.',
                         'Thai cuisine' => 'Cooking classes and night markets.'],
        'best' => 'November to February for most regions', 'ideal' => ['Beach', 'Culture', 'Food'],
        'keywords' => ['Bangkok', 'Phuket', 'Krabi', 'Chiang Mai', 'Samui']],
    'indonesia' => ['name' => 'Indonesia', 'region' => 'asia',
        'tagline' => 'Bali\'s rice terraces, temples and island hopping',
        'intro' => 'With thousands of islands, Indonesia is endlessly varied. Most visitors start in Bali for its temples, rice terraces and beaches, then add the Gili Islands, Komodo\'s dragons or Borobudur in Java.',
        'highlights' => ['Bali' => 'Ubud\'s rice terraces, temples and beach clubs.',
                         'Komodo National Park' => 'Komodo dragons and superb diving.',
                         'Java' => 'Borobudur and Prambanan temples.',
                         'Lombok and the Gilis' => 'Quiet beaches and turquoise water.'],
        'best' => 'April to October', 'ideal' => ['Honeymoon', 'Culture', 'Beach'],
        'keywords' => ['Bali', 'Lombok', 'Komodo', 'Java', 'Gili', 'Jakarta']],
    'malaysia' => ['name' => 'Malaysia', 'region' => 'asia',
        'tagline' => 'Modern cities, island beaches and Borneo rainforest',
        'intro' => 'Malaysia offers excellent value and great variety: the skyline of Kuala Lumpur, the food and heritage of Penang, the beaches of Langkawi and wild orangutans in the rainforests of Borneo.',
        'highlights' => ['Kuala Lumpur' => 'The Petronas Towers and lively food streets.',
                         'Penang' => 'George Town\'s heritage and hawker food.',
                         'Langkawi' => 'Beaches, cable car views and mangroves.',
                         'Borneo' => 'Orangutans and rainforest in Sabah.'],
        'best' => 'Year-round: west coast December to April, east coast March to October', 'ideal' => ['Family', 'Food', 'Nature'],
        'keywords' => ['Kuala Lumpur', 'Penang', 'Langkawi', 'Borneo', 'Sabah']],
    'singapore' => ['name' => 'Singapore', 'region' => 'asia',
        'tagline' => 'Garden city, hawker food and an easy stopover',
        'intro' => 'Singapore is clean, safe and full of surprises: futuristic gardens, world-class dining from hawker stalls to fine restaurants, and a perfect stopover on the way to Bali, Thailand or the Maldives.',
        'highlights' => ['Gardens by the Bay' => 'Supertrees and cloud-forest domes.',
                         'Marina Bay' => 'Skyline views and waterfront evenings.',
                         'Hawker centres' => 'Some of the world\'s best street food.',
                         'Sentosa' => 'Beaches and attractions for families.'],
        'best' => 'Year-round; February to April is driest', 'ideal' => ['City break', 'Family', 'Food'],
        'keywords' => ['Sentosa', 'Marina Bay']],
    'philippines' => ['name' => 'Philippines', 'region' => 'asia',
        'tagline' => 'Limestone lagoons, white beaches and warm hospitality',
        'intro' => 'Over seven thousand islands make the Philippines a paradise for beach lovers and divers. Island-hop through Palawan\'s lagoons, relax on Boracay\'s famous sand and explore the Chocolate Hills of Bohol.',
        'highlights' => ['Palawan' => 'El Nido and Coron\'s limestone lagoons.',
                         'Boracay' => 'Powder-white White Beach.',
                         'Bohol' => 'The Chocolate Hills and tarsiers.',
                         'Diving' => 'Rich reefs and wrecks across the islands.'],
        'best' => 'December to May', 'ideal' => ['Beach', 'Diving', 'Adventure'],
        'keywords' => ['Palawan', 'Boracay', 'Manila', 'Cebu', 'El Nido']],
    'india' => ['name' => 'India', 'region' => 'asia',
        'tagline' => 'Palaces, tigers and the backwaters of Kerala',
        'intro' => 'India is a journey of a lifetime. See the Taj Mahal at sunrise, stay in the palaces of Rajasthan, look for tigers in Ranthambore and drift through Kerala\'s backwaters on a traditional houseboat.',
        'highlights' => ['The Golden Triangle' => 'Delhi, Agra and Jaipur.',
                         'Rajasthan' => 'Palaces, forts and desert cities.',
                         'Tiger safaris' => 'Ranthambore and central India\'s reserves.',
                         'Kerala' => 'Backwater houseboats and tea hills.'],
        'best' => 'October to March', 'ideal' => ['Culture', 'Wildlife', 'Wellness'],
        'keywords' => ['Delhi', 'Agra', 'Jaipur', 'Rajasthan', 'Kerala', 'Goa', 'Mumbai']],
    'china' => ['name' => 'China', 'region' => 'asia',
        'tagline' => 'The Great Wall, ancient capitals and dramatic landscapes',
        'intro' => 'China rewards curious travellers with thousands of years of history and striking scenery. Walk the Great Wall, explore Beijing\'s Forbidden City, meet the Terracotta Warriors in Xi\'an and cruise the Li River near Guilin.',
        'highlights' => ['Beijing' => 'The Great Wall and the Forbidden City.',
                         'Xi\'an' => 'The Terracotta Warriors.',
                         'Guilin and Yangshuo' => 'Karst peaks along the Li River.',
                         'Shanghai' => 'The Bund and a modern skyline.'],
        'best' => 'April to May, and September to October', 'ideal' => ['History', 'Culture', 'City break'],
        'keywords' => ['Beijing', 'Shanghai', 'Xian', 'Guilin', 'Hong Kong']],

    // ── Middle East ─────────────────────────────────────────────────────────
    'dubai' => ['name' => 'Dubai', 'region' => 'middle-east',
        'tagline' => 'Skyline glamour, desert adventures and winter sun',
        'intro' => 'Dubai is the perfect short-haul escape for sunshine, shopping and spectacle. Ride to the top of the Burj Khalifa, dine on the Marina, explore the old souks and spend an evening in the desert dunes.',
        'highlights' => ['Burj Khalifa' => 'Views from the world\'s tallest building.',
                         'Desert safari' => 'Dune drives and a desert dinner.',
                         'Old Dubai' => 'The creek, abras and gold and spice souks.',
                         'Abu Dhabi' => 'The Sheikh Zayed Grand Mosque nearby.'],
        'best' => 'November to March', 'ideal' => ['City break', 'Family', 'Luxury'],
        'keywords' => ['UAE', 'Abu Dhabi', 'Emirates']],
    'oman' => ['name' => 'Oman', 'region' => 'middle-east',
        'tagline' => 'Wadis, desert sands and authentic Arabian hospitality',
        'intro' => 'Oman offers a gentler, more traditional side of Arabia. Swim in the emerald pools of Wadi Shab, camp in the Wahiba Sands, explore the forts of Nizwa and cruise the fjord-like coast of Musandam.',
        'highlights' => ['Muscat' => 'The Grand Mosque and Mutrah souk.',
                         'Wahiba Sands' => 'Desert camps among golden dunes.',
                         'Wadi Shab' => 'Hikes to turquoise pools.',
                         'Nizwa' => 'Historic fort and mountain villages.'],
        'best' => 'October to April', 'ideal' => ['Adventure', 'Culture', 'Road trips'],
        'keywords' => ['Muscat', 'Salalah', 'Wahiba']],
    'jordan' => ['name' => 'Jordan', 'region' => 'middle-east',
        'tagline' => 'Petra, Wadi Rum and the Dead Sea',
        'intro' => 'Jordan holds some of the world\'s most extraordinary sights within a short drive of one another. Walk through the Siq to Petra\'s Treasury, sleep under the stars in Wadi Rum and float in the Dead Sea.',
        'highlights' => ['Petra' => 'The rose-red city carved from rock.',
                         'Wadi Rum' => 'Desert camps and jeep tours.',
                         'The Dead Sea' => 'Float in mineral-rich waters.',
                         'Jerash' => 'Remarkably preserved Roman ruins.'],
        'best' => 'March to May, and September to November', 'ideal' => ['History', 'Adventure', 'Faith'],
        'keywords' => ['Petra', 'Amman', 'Wadi Rum', 'Dead Sea']],

    // ── Europe ──────────────────────────────────────────────────────────────
    'france' => ['name' => 'France', 'region' => 'europe',
        'tagline' => 'Paris, Provence and the French Riviera',
        'intro' => 'France is endlessly romantic. Spend days in the galleries and cafés of Paris, drive through Provence\'s lavender fields, unwind on the Riviera and taste your way through Bordeaux and the Loire Valley.',
        'highlights' => ['Paris' => 'The Louvre, the Eiffel Tower and Left Bank cafés.',
                         'Provence' => 'Lavender fields and hilltop villages.',
                         'The French Riviera' => 'Nice, Cannes and Monaco.',
                         'Wine country' => 'Bordeaux, Champagne and the Loire.'],
        'best' => 'April to June, and September to October', 'ideal' => ['Romance', 'Food & wine', 'Culture'],
        'keywords' => ['Paris', 'Nice', 'Provence', 'Riviera', 'Bordeaux']],
    'italy' => ['name' => 'Italy', 'region' => 'europe',
        'tagline' => 'Rome, Tuscany, Venice and the Amalfi Coast',
        'intro' => 'Italy is art, history and la dolce vita. Explore ancient Rome, the Renaissance treasures of Florence, the canals of Venice and the cliffside villages of the Amalfi Coast, with long lunches all the way.',
        'highlights' => ['Rome' => 'The Colosseum, the Vatican and piazzas.',
                         'Florence and Tuscany' => 'Renaissance art and vineyards.',
                         'Venice' => 'Canals, gondolas and lagoon islands.',
                         'The Amalfi Coast' => 'Positano, Ravello and Capri.'],
        'best' => 'April to June, and September to October', 'ideal' => ['Romance', 'Food & wine', 'History'],
        'keywords' => ['Rome', 'Venice', 'Florence', 'Tuscany', 'Amalfi', 'Milan']],
    'spain' => ['name' => 'Spain', 'region' => 'europe',
        'tagline' => 'Barcelona, Andalusia and island sunshine',
        'intro' => 'Spain combines vibrant cities, rich history and long sunny days. Admire Gaudí\'s Barcelona, the Alhambra in Granada and the plazas of Seville, then relax in the Balearic or Canary Islands.',
        'highlights' => ['Barcelona' => 'Gaudí, tapas and beaches.',
                         'Madrid' => 'The Prado and lively neighbourhoods.',
                         'Andalusia' => 'Seville, Granada and Córdoba.',
                         'The islands' => 'Mallorca, Ibiza and the Canaries.'],
        'best' => 'April to June, and September to October', 'ideal' => ['Culture', 'Food', 'Beach'],
        'keywords' => ['Barcelona', 'Madrid', 'Seville', 'Mallorca', 'Ibiza']],
    'greece' => ['name' => 'Greece', 'region' => 'europe',
        'tagline' => 'Ancient Athens and sun-drenched islands',
        'intro' => 'Greece is history and island life in perfect balance. Climb to the Acropolis in Athens, watch the sunset over Santorini\'s caldera, explore Crete and visit the clifftop monasteries of Meteora.',
        'highlights' => ['Athens' => 'The Acropolis and lively Plaka.',
                         'Santorini' => 'Whitewashed villages and caldera sunsets.',
                         'Crete' => 'Beaches, gorges and Minoan history.',
                         'Meteora' => 'Monasteries perched on rock pillars.'],
        'best' => 'May to June, and September to October', 'ideal' => ['Honeymoon', 'History', 'Beach'],
        'keywords' => ['Athens', 'Santorini', 'Mykonos', 'Crete']],
    'turkey' => ['name' => 'Turkey', 'region' => 'europe',
        'tagline' => 'Istanbul, Cappadocia and the Turquoise Coast',
        'intro' => 'Turkey bridges Europe and Asia with spectacular results. Explore Istanbul\'s mosques and bazaars, float over Cappadocia in a hot-air balloon, walk ancient Ephesus and sail the Turquoise Coast.',
        'highlights' => ['Istanbul' => 'Hagia Sophia, the Blue Mosque and the Grand Bazaar.',
                         'Cappadocia' => 'Balloon flights over fairy chimneys.',
                         'Ephesus' => 'One of the great cities of the ancient world.',
                         'The Turquoise Coast' => 'Gulet cruises and beach towns.'],
        'best' => 'April to May, and September to October', 'ideal' => ['Culture', 'History', 'Food'],
        'keywords' => ['Istanbul', 'Cappadocia', 'Antalya', 'Ephesus', 'Bodrum']],
];

if (!function_exists('destinationImage')) {
    /** Country photo if one has been added, else its region's photo. */
    function destinationImage(string $key): string {
        foreach (['jpg', 'png', 'webp'] as $ext) {
            $path = "assets/images/destinations/countries/$key.$ext";
            if (is_file(__DIR__ . '/../' . $path)) return $path;
        }
        $region = DEST_COUNTRIES[$key]['region'] ?? $key;
        return DEST_REGIONS[$region]['image'] ?? 'assets/images/header-banners/destination-africa.png';
    }
}

if (!function_exists('destinationCountries')) {
    /** Countries in a region, keyed by country key. */
    function destinationCountries(string $region): array {
        return array_filter(DEST_COUNTRIES, fn($c) => $c['region'] === $region);
    }
}

if (!function_exists('destinationPackages')) {
    /**
     * Active packages whose title or location mention any of the given
     * countries (name or keywords). One query, newest first.
     */
    function destinationPackages(PDO $pdo, array $countryKeys, int $limit = 9): array {
        $terms = [];
        foreach ($countryKeys as $k) {
            if (!isset(DEST_COUNTRIES[$k])) continue;
            $terms[] = DEST_COUNTRIES[$k]['name'];
            foreach (DEST_COUNTRIES[$k]['keywords'] ?? [] as $w) $terms[] = $w;
        }
        $terms = array_values(array_unique($terms));
        if (!$terms) return [];
        $where = []; $params = [];
        foreach ($terms as $t) {
            $where[] = '(location LIKE ? OR title LIKE ?)';
            $params[] = "%$t%"; $params[] = "%$t%";
        }
        try {
            $st = $pdo->prepare(
                "SELECT id, title, location, price, duration, image FROM tours
                 WHERE status = 'Active' AND (" . implode(' OR ', $where) . ")
                 ORDER BY created_at DESC LIMIT " . (int)$limit
            );
            $st->execute($params);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('destinationPackages: ' . $e->getMessage());
            return [];
        }
    }
}
