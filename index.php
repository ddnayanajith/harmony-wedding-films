<?php
$siteName = 'Harmony Wedding Films';
$contactEmail = 'bookings@harmonyweddingfilms.com';
$contactPhone = '078 762 0353';
$whatsAppNumber = '078 762 0353';
$instagramHandle = '@harmony_wedding_films';
$instagramUrl = 'https://www.instagram.com/harmony_wedding_films/';
$facebookUrl = 'https://web.facebook.com/Harmonyproductionlk';
$mailFrom = 'noreply@harmonyweddingfilms.com';
$contactFormValues = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'wedding_date' => '',
    'venue' => '',
    'event_type' => '',
    'message' => '',
];
$contactFormErrors = [];
$contactFormStatus = '';
$contactFormStatusType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    foreach ($contactFormValues as $field => $value) {
        $contactFormValues[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    $contactFormValues['name'] = str_replace(["\r", "\n"], ' ', $contactFormValues['name']);
    $contactFormValues['email'] = str_replace(["\r", "\n"], '', $contactFormValues['email']);

    if ($contactFormValues['name'] === '') {
        $contactFormErrors['name'] = 'Please enter your name.';
    }

    if (!filter_var($contactFormValues['email'], FILTER_VALIDATE_EMAIL)) {
        $contactFormErrors['email'] = 'Please enter a valid email address.';
    }

    if ($contactFormValues['message'] === '' || reviewWordCount($contactFormValues['message']) < 4) {
        $contactFormErrors['message'] = 'Please add a short message about your wedding.';
    }

    if ($contactFormValues['event_type'] === '') {
        $contactFormErrors['event_type'] = 'Please choose the event type.';
    }

    if (empty($contactFormErrors)) {
        $subject = 'New Wedding Inquiry - ' . $contactFormValues['name'];
        $bodyLines = [
            'New inquiry from the website',
            '',
            'Name: ' . $contactFormValues['name'],
            'Email: ' . $contactFormValues['email'],
            'Phone: ' . ($contactFormValues['phone'] !== '' ? $contactFormValues['phone'] : '-'),
            'Event Date: ' . ($contactFormValues['wedding_date'] !== '' ? $contactFormValues['wedding_date'] : '-'),
            'Venue: ' . ($contactFormValues['venue'] !== '' ? $contactFormValues['venue'] : '-'),
            'Event Type: ' . $contactFormValues['event_type'],
            '',
            'Message:',
            $contactFormValues['message'],
        ];

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $siteName . ' <' . $mailFrom . '>',
            'Reply-To: ' . $contactFormValues['name'] . ' <' . $contactFormValues['email'] . '>',
        ];

        $sent = @mail(
            $contactEmail,
            $subject,
            implode("\r\n", $bodyLines),
            implode("\r\n", $headers)
        );

        if ($sent) {
            $contactFormStatus = 'Your inquiry was sent. We will get back to you soon.';
            $contactFormStatusType = 'success';
            foreach ($contactFormValues as $field => $value) {
                $contactFormValues[$field] = '';
            }
        } else {
            $contactFormStatus = 'The form could not send right now. Please use phone, WhatsApp, or email.';
            $contactFormStatusType = 'error';
        }
    } else {
        $contactFormStatus = 'Please check the highlighted fields and try again.';
        $contactFormStatusType = 'error';
    }
}
$reviews = file_exists(__DIR__ . '/assets/data/reviews.php')
    ? require __DIR__ . '/assets/data/reviews.php'
    : [];
$portfolioVideos = [
    ['youtube_id' => 'EWHCNrKLvI4', 'label' => 'Film 01'],
    ['youtube_id' => 'moxtvOAd6to', 'label' => 'Film 02'],
    ['youtube_id' => 'xcizBqXVbwg', 'label' => 'Film 03'],
    ['youtube_id' => 'maaOIcIkszc', 'label' => 'Film 04'],
    ['youtube_id' => '61HbU_Np4nA', 'label' => 'Film 05'],
    ['youtube_id' => '5G4AGYM9Z9w', 'label' => 'Film 06'],
];

function formatReviewDate($date)
{
    $timestamp = strtotime((string) $date);

    if ($timestamp === false) {
        return '';
    }

    return date('F Y', $timestamp);
}

function reviewInitials($name)
{
    $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'R';
}

function reviewTextLength($text)
{
    $text = trim((string) $text);

    if (function_exists('mb_strlen')) {
        return mb_strlen($text, 'UTF-8');
    }

    return strlen($text);
}

function reviewPreviewText($text, $limit = 180)
{
    $text = trim((string) $text);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit, 'UTF-8'));
    }

    if (strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(substr($text, 0, $limit));
}

function reviewWordCount($text)
{
    $words = preg_split('/\s+/', trim((string) $text)) ?: [];
    $words = array_values(array_filter($words, static function ($word) {
        return $word !== '';
    }));

    return count($words);
}

function reviewPreviewWords($text, $limit = 30)
{
    $words = preg_split('/\s+/', trim((string) $text)) ?: [];
    $words = array_values(array_filter($words, static function ($word) {
        return $word !== '';
    }));

    if (count($words) <= $limit) {
        return trim((string) $text);
    }

    return implode(' ', array_slice($words, 0, $limit));
}

function youtubeThumbUrl($videoId)
{
    return 'https://i.ytimg.com/vi/' . rawurlencode((string) $videoId) . '/hqdefault.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta
        name="description"
        content="Wedding films and photography with a calm, elegant, minimal approach."
    >
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&family=Tangerine:wght@400&display=swap"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.3.19/dist/lenis.css">
    <script src="https://unpkg.com/lenis@1.3.19/dist/lenis.min.js"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
</head>
<body>
    <div class="site-loader" data-site-loader>
        <div class="site-loader-inner">
            <img
                class="site-loader-logo"
                src="assets/images/logo-black.png"
                alt="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>
    </div>

    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="/">
                <img
                    class="brand-logo"
                    src="assets/images/logo.png"
                    alt="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <a href="#hero">Home</a>
                <a href="#about">About</a>
                <a href="#portfolio">Portfolio</a>
                <a href="#reviews">Reviews</a>
                <a href="#contact">Contact</a>
            </nav>

            <a class="button button-primary header-cta" href="#contact">
                <svg class="header-cta-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path>
                </svg>
                <span>Appointment Now</span>
            </a>
        </div>
    </header>

    <main>
        <section class="hero-section" id="hero">
            <div class="hero-panel">
                <video class="hero-panel-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
                    <source src="assets/videos/hero.mp4" type="video/mp4">
                </video>
                <div class="container hero-shell">
                    <div class="hero-content">
                        <p class="eyebrow">Wedding Films & Photography</p>
                        <h1>Capturing Love in Its Purest Form</h1>
                        <p class="hero-text">
                            Cinematic wedding films and timeless photography crafted to preserve your
                            story forever, capturing every emotion and beautiful moment so you can
                            relive your love for generations to come.
                        </p>

                        <div class="hero-actions">
                            <a class="button button-primary" href="#portfolio">View Portfolio</a>
                            <a class="button button-secondary" href="#contact">Book a Consultation</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-story-section" id="about">
            <div class="container about-story-grid">
                <div class="about-story-visual">
                    <figure class="about-story-card about-story-card-main">
                        <img src="assets/images/img1.jpg" alt="Wedding couple portrait">
                    </figure>

                    <figure class="about-story-card about-story-card-accent">
                        <img src="assets/images/img2.jpg" alt="Wedding moment detail">
                    </figure>
                </div>

                <div class="about-story-copy">
                    <p class="eyebrow">About The Studio</p>
                    <h2>Harmony Wedding Films</h2>
                    <p>
                        Every wedding is a once in a lifetime celebration, shaped by emotion,
                        elegance, and meaningful detail. Harmony is devoted to capturing these
                        moments with refinement and intention.
                    </p>
                    <p>
                        Our work is guided by cinematic vision and timeless aesthetics. We focus on
                        authenticity, subtle gestures, and the atmosphere that makes your day
                        uniquely yours. With a calm presence and thoughtful approach, we create
                        films and photographs that feel sophisticated, natural, and enduring.
                    </p>
                    <p>
                        This is more than documentation. It is the art of preserving legacy, so
                        your story can be experienced beautifully for generations.
                    </p>
                </div>
            </div>
        </section>

        <section class="portfolio-section" id="portfolio">
            <div class="container">
                <div class="portfolio-intro">
                    <p class="eyebrow">Featured Portfolio</p>
                    <h2>Beautiful Moments Through Our Lens</h2>
                    <p class="portfolio-text">
                        Every wedding is filled with genuine emotions, beautiful details, and
                        unforgettable moments. Through our lens, we capture these memories so you
                        can relive the story of your special day for years to come.
                    </p>
                </div>

                <div class="portfolio-gallery" data-portfolio-gallery data-gallery-layout="strip">
                    <div class="portfolio-film" data-video-player>
                        <div class="portfolio-film-frame">
                            <div
                                class="portfolio-film-video"
                                data-youtube-player
                                data-video-id="<?php echo htmlspecialchars($portfolioVideos[0]['youtube_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Featured wedding film"
                            ></div>

                            <button class="portfolio-play-overlay" type="button" aria-label="Play video">
                                <span aria-hidden="true">&#9654;</span>
                            </button>

                            <div class="portfolio-video-controls" aria-label="Video controls">
                                <button class="portfolio-video-button" type="button" data-video-action="toggle-play" aria-label="Pause video">
                                    <span class="portfolio-video-icon portfolio-video-icon-play" data-play-icon aria-hidden="true">&#10074;&#10074;</span>
                                </button>

                                <div class="portfolio-video-progress">
                                    <input
                                        class="portfolio-video-range"
                                        type="range"
                                        min="0"
                                        max="100"
                                        value="0"
                                        step="0.1"
                                        data-video-progress
                                        aria-label="Video progress"
                                    >
                                </div>

                                <span class="portfolio-video-time" data-video-time>0:00 / 0:00</span>

                                <div class="portfolio-video-volume">
                                    <span class="portfolio-video-icon" aria-hidden="true">Vol</span>
                                    <input
                                        class="portfolio-video-range portfolio-video-volume-range"
                                        type="range"
                                        min="0"
                                        max="100"
                                        value="0"
                                        step="1"
                                        data-video-volume
                                        aria-label="Video volume"
                                    >
                                </div>

                                <button class="portfolio-video-button" type="button" data-video-action="fullscreen" aria-label="Fullscreen">
                                    <span class="portfolio-video-icon portfolio-video-icon-fullscreen" aria-hidden="true">&#9974;</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="portfolio-thumb-slider">
                        <button class="portfolio-thumb-nav portfolio-thumb-nav-prev" type="button" aria-label="Previous videos">
                            <span aria-hidden="true">&#8249;</span>
                        </button>

                        <div class="portfolio-film-thumbs" role="list" aria-label="Wedding film gallery">
                            <?php foreach ($portfolioVideos as $index => $video): ?>
                                <button
                                    class="portfolio-thumb<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                    type="button"
                                    data-video-id="<?php echo htmlspecialchars($video['youtube_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="<?php echo htmlspecialchars($video['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                >
                                    <span class="portfolio-thumb-media">
                                        <img
                                            class="portfolio-thumb-image"
                                            src="<?php echo htmlspecialchars(youtubeThumbUrl($video['youtube_id']), ENT_QUOTES, 'UTF-8'); ?>"
                                            alt="<?php echo htmlspecialchars($video['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                            loading="lazy"
                                        >
                                    </span>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <button class="portfolio-thumb-nav portfolio-thumb-nav-next" type="button" aria-label="Next videos">
                            <span aria-hidden="true">&#8250;</span>
                        </button>
                    </div>
                </div>

                <div class="portfolio-gallery portfolio-gallery-coverflow" data-portfolio-gallery data-gallery-layout="coverflow">
                    <div class="portfolio-coverflow-stage">
                        <div class="portfolio-coverflow">
                            <div class="portfolio-coverflow-track" role="list" aria-label="Wedding film selector coverflow">
                                <?php foreach ($portfolioVideos as $index => $video): ?>
                                    <button
                                        class="portfolio-thumb portfolio-coverflow-thumb<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                        type="button"
                                        data-video-id="<?php echo htmlspecialchars($video['youtube_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-coverflow-index="<?php echo $index; ?>"
                                        aria-label="<?php echo htmlspecialchars($video['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                        aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    >
                                        <span class="portfolio-thumb-media">
                                            <img
                                                class="portfolio-thumb-image"
                                                src="<?php echo htmlspecialchars(youtubeThumbUrl($video['youtube_id']), ENT_QUOTES, 'UTF-8'); ?>"
                                                alt="<?php echo htmlspecialchars($video['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                                loading="lazy"
                                            >
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="portfolio-film portfolio-film-coverflow" data-video-player>
                            <button class="portfolio-coverflow-nav portfolio-coverflow-nav-prev" type="button" aria-label="Previous video">
                                <span aria-hidden="true">&#8249;</span>
                            </button>

                            <div class="portfolio-film-frame">
                                <div
                                    class="portfolio-film-video"
                                    data-youtube-player
                                    data-video-id="<?php echo htmlspecialchars($portfolioVideos[0]['youtube_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-label="Featured wedding film alternate layout"
                                ></div>

                                <button class="portfolio-play-overlay" type="button" aria-label="Play video">
                                    <span aria-hidden="true">&#9654;</span>
                                </button>

                                <div class="portfolio-video-controls" aria-label="Video controls">
                                    <button class="portfolio-video-button" type="button" data-video-action="toggle-play" aria-label="Pause video">
                                        <span class="portfolio-video-icon portfolio-video-icon-play" data-play-icon aria-hidden="true">&#10074;&#10074;</span>
                                    </button>

                                    <div class="portfolio-video-progress">
                                        <input
                                            class="portfolio-video-range"
                                            type="range"
                                            min="0"
                                            max="100"
                                            value="0"
                                            step="0.1"
                                            data-video-progress
                                            aria-label="Video progress"
                                        >
                                    </div>

                                    <span class="portfolio-video-time" data-video-time>0:00 / 0:00</span>

                                    <div class="portfolio-video-volume">
                                        <span class="portfolio-video-icon" aria-hidden="true">Vol</span>
                                        <input
                                            class="portfolio-video-range portfolio-video-volume-range"
                                            type="range"
                                            min="0"
                                            max="100"
                                            value="0"
                                            step="1"
                                            data-video-volume
                                            aria-label="Video volume"
                                        >
                                    </div>

                                    <button class="portfolio-video-button" type="button" data-video-action="fullscreen" aria-label="Fullscreen">
                                        <span class="portfolio-video-icon portfolio-video-icon-fullscreen" aria-hidden="true">&#9974;</span>
                                    </button>
                                </div>
                            </div>

                            <button class="portfolio-coverflow-nav portfolio-coverflow-nav-next" type="button" aria-label="Next video">
                                <span aria-hidden="true">&#8250;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="why-choose-alt-section">
            <div class="container">
                <div class="why-choose-alt-shell">
                    <div class="why-choose-alt-intro">
                        <p class="eyebrow">Why Choose Us</p>
                        <h2>Built for couples who care about quality, presence, and lasting work.</h2>
                        <p class="why-choose-text">
                            Harmony combines refined storytelling with reliable production standards,
                            giving couples films and photographs that feel elevated, natural, and
                            professionally crafted from start to finish.
                        </p>
                    </div>

                    <div class="why-choose-alt-layout">
                        <div class="why-choose-alt-stack why-choose-alt-stack-left">
                            <article class="why-choose-alt-card">
                                <div class="why-choose-alt-card-head">
                                    <span class="why-choose-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m12.296 3.464 3.02 3.956"></path>
                                            <path d="M20.2 6 3 11l-.9-2.4c-.3-1.1.3-2.2 1.3-2.5l13.5-4c1.1-.3 2.2.3 2.5 1.3z"></path>
                                            <path d="M3 11h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <path d="m6.18 5.276 3.1 3.899"></path>
                                        </svg>
                                    </span>
                                    <div class="why-choose-alt-card-copy">
                                        <h3>True 4K Delivery</h3>
                                        <p>Wedding films are captured with clarity, smooth motion, and a polished cinematic finish that stays timeless.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="why-choose-alt-card">
                                <div class="why-choose-alt-card-head">
                                    <span class="why-choose-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 7h4l2-2h4l2 2h4"></path>
                                            <rect x="3" y="7" width="18" height="12" rx="2"></rect>
                                            <circle cx="12" cy="13" r="3.5"></circle>
                                            <path d="M17 10.5h.01"></path>
                                        </svg>
                                    </span>
                                    <div class="why-choose-alt-card-copy">
                                        <h3>Professional Gear Setup</h3>
                                        <p>Premium cameras, lenses, stabilization, and dependable audio tools support a consistently high-end result.</p>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <figure class="why-choose-alt-portrait">
                            <img src="assets/images/img3.jpg" alt="Wedding photographer and filmmaker">
                        </figure>

                        <div class="why-choose-alt-stack why-choose-alt-stack-right">
                            <article class="why-choose-alt-card">
                                <div class="why-choose-alt-card-head">
                                    <span class="why-choose-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                            <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                            <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                            <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                        </svg>
                                    </span>
                                    <div class="why-choose-alt-card-copy">
                                        <h3>Calm, Organized Presence</h3>
                                        <p>Coverage is guided with professionalism and quiet confidence, so the day feels smooth instead of interrupted.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="why-choose-alt-card">
                                <div class="why-choose-alt-card-head">
                                    <span class="why-choose-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15.707 21.293a1 1 0 0 1-1.414 0l-1.586-1.586a1 1 0 0 1 0-1.414l5.586-5.586a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 1 0 1.414z"></path>
                                            <path d="m18 13-1.375-6.874a1 1 0 0 0-.746-.776L3.235 2.028a1 1 0 0 0-1.207 1.207L5.35 15.879a1 1 0 0 0 .776.746L13 18"></path>
                                            <path d="m2.3 2.3 7.286 7.286"></path>
                                            <circle cx="11" cy="11" r="2"></circle>
                                        </svg>
                                    </span>
                                    <div class="why-choose-alt-card-copy">
                                        <h3>Editing That Lasts</h3>
                                        <p>Color, pacing, and image selection are handled with restraint, giving your gallery and film a lasting editorial feel.</p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonials-section" id="reviews">
            <div class="container">
                <div class="section-heading testimonials-heading">
                    <div class="section-copy">
                        <p class="eyebrow">Kind Words</p>
                        <h2>Chosen for calm energy and emotional storytelling.</h2>
                    </div>
                </div>

                <div class="testimonials-carousel" data-review-carousel>
                    <button class="testimonial-nav testimonial-nav-prev" type="button" aria-label="Previous reviews">
                        <span aria-hidden="true">&#8249;</span>
                    </button>

                    <div class="testimonials-track" data-review-track>
                    <?php foreach ($reviews as $index => $review): ?>
                        <article class="testimonial-card">
                            <div class="testimonial-head">
                                <span class="testimonial-avatar"><?php echo htmlspecialchars(reviewInitials($review['name']), ENT_QUOTES, 'UTF-8'); ?></span>
                                <div class="testimonial-person">
                                    <strong><?php echo htmlspecialchars($review['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <span><?php echo htmlspecialchars(formatReviewDate($review['date']), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>

                            <p class="testimonial-quote">"<?php echo htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8'); ?>"</p>

                            <div class="testimonial-meta">
                                <span class="testimonial-stars" aria-label="<?php echo (int) $review['rating']; ?> star review">★★★★★</span>
                                <a href="<?php echo htmlspecialchars($review['source_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="testimonial-source-icon" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    <span><?php echo htmlspecialchars($review['source_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    </div>

                    <button class="testimonial-nav testimonial-nav-next" type="button" aria-label="Next reviews">
                        <span aria-hidden="true">&#8250;</span>
                    </button>
                </div>
            </div>
        </section>

        <div class="review-modal" data-review-modal hidden>
            <div class="review-modal-backdrop" data-review-close></div>
            <div class="review-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="review-modal-title">
                <button class="review-modal-close" type="button" aria-label="Close review" data-review-close>&#10005;</button>
                <article class="testimonial-card testimonial-card-modal">
                    <div class="testimonial-head">
                        <span class="testimonial-avatar" data-review-modal-initials>RW</span>
                        <div class="testimonial-person">
                            <strong id="review-modal-title" data-review-modal-name>Reviewer Name</strong>
                            <span data-review-modal-date>March 2026</span>
                        </div>
                    </div>

                    <p class="testimonial-quote" data-review-modal-text></p>

                    <div class="testimonial-meta">
                        <span class="testimonial-stars" data-review-modal-stars aria-label="5 star review">★★★★★</span>
                        <a href="#" target="_blank" rel="noreferrer" data-review-modal-source>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="testimonial-source-icon" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            <span data-review-modal-source-label>Facebook Review</span>
                        </a>
                    </div>
                </article>
            </div>
        </div>

        <?php if (false): ?>
        <section class="contact-section" id="contact">
            <div class="container">
                <div class="contact-shell">
                    <div class="contact-heading">
                    <p class="eyebrow">Contact</p>
                    <h2>Let’s talk about your wedding.</h2>
                    <p class="contact-text">For bookings and availability, reach out by phone, WhatsApp, email, or Instagram.</p>

                    <div class="contact-actions">
                        <a class="button button-primary" href="https://wa.me/94<?php echo htmlspecialchars(substr(preg_replace('/\D+/', '', $whatsAppNumber), -9), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                            WhatsApp
                        </a>
                        <a class="button button-secondary" href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>">
                            Email
                        </a>
                    </div>
                </div>

                <div class="contact-side">
                    <p class="contact-side-note">Send your date, venue, and whether you need videography, photography, or both.</p>

                    <ul class="contact-details">
                        <li>
                            <span>Mobile</span>
                            <a href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $contactPhone), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($contactPhone, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                        <li>
                            <span>WhatsApp</span>
                            <a href="https://wa.me/94<?php echo htmlspecialchars(substr(preg_replace('/\D+/', '', $whatsAppNumber), -9), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                                <?php echo htmlspecialchars($whatsAppNumber, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                        <li>
                            <span>Email</span>
                            <a href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                        <li>
                            <span>Instagram</span>
                            <a href="<?php echo htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                                <?php echo htmlspecialchars($instagramHandle, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <section class="contact-section" id="contact">
            <div class="container">
                <div class="contact-shell contact-shell-minimal">
                    <div class="contact-body">
                        <div class="contact-copy">
                            <div class="contact-heading">
                                <p class="eyebrow">Contact Us</p>
                                <h2>Let's talk about your celebration.</h2>
                                <p class="contact-text">For bookings and availability, reach out by phone or send a quick message on any platform below.</p>
                            </div>

                            <div class="contact-connect">
                                <div class="contact-link-list">
                                    <a class="contact-link-row" href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $contactPhone), ENT_QUOTES, 'UTF-8'); ?>">
                                        <span>078 762 0353</span>
                                        <svg class="contact-link-arrow" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M18 8L22 12L18 16"/>
                                            <path d="M2 12H22"/>
                                        </svg>
                                    </a>
                                    <a class="contact-link-row" href="https://wa.me/94<?php echo htmlspecialchars(substr(preg_replace('/\D+/', '', $whatsAppNumber), -9), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">
                                        <span>WhatsApp</span>
                                        <svg class="contact-link-arrow" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M18 8L22 12L18 16"/>
                                            <path d="M2 12H22"/>
                                        </svg>
                                    </a>
                                    <a class="contact-link-row" href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span>Send Email</span>
                                        <svg class="contact-link-arrow" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M18 8L22 12L18 16"/>
                                            <path d="M2 12H22"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="contact-form-shell">
                            <?php if ($contactFormStatus !== ''): ?>
                                <p class="contact-form-status contact-form-status-<?php echo htmlspecialchars($contactFormStatusType, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($contactFormStatus, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php endif; ?>

                            <form class="contact-form" method="post" action="#contact" novalidate>
                                <input type="hidden" name="contact_form" value="1">

                                <div class="contact-form-grid">
                                    <label class="contact-field">
                                        <span>Name</span>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($contactFormValues['name'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name" placeholder="Your full name">
                                        <?php if (isset($contactFormErrors['name'])): ?>
                                            <small><?php echo htmlspecialchars($contactFormErrors['name'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </label>

                                    <label class="contact-field">
                                        <span>Email</span>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($contactFormValues['email'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" placeholder="you@example.com">
                                        <?php if (isset($contactFormErrors['email'])): ?>
                                            <small><?php echo htmlspecialchars($contactFormErrors['email'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </label>

                                    <label class="contact-field">
                                        <span>WhatsApp Number</span>
                                        <input type="text" name="phone" value="<?php echo htmlspecialchars($contactFormValues['phone'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="tel" placeholder="077 123 4567">
                                    </label>

                                    <label class="contact-field contact-field-date">
                                        <span>Event Date</span>
                                        <input type="date" name="wedding_date" value="<?php echo htmlspecialchars($contactFormValues['wedding_date'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </label>

                                    <label class="contact-field">
                                        <span>Venue</span>
                                        <input type="text" name="venue" value="<?php echo htmlspecialchars($contactFormValues['venue'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Venue or city">
                                    </label>

                                    <label class="contact-field">
                                        <span>Event Type</span>
                                        <select name="event_type">
                                            <option value="">Select one</option>
                                            <option value="Pre-wedding" <?php echo $contactFormValues['event_type'] === 'Pre-wedding' ? 'selected' : ''; ?>>Pre-wedding</option>
                                            <option value="Engagement" <?php echo $contactFormValues['event_type'] === 'Engagement' ? 'selected' : ''; ?>>Engagement</option>
                                            <option value="Homecoming" <?php echo $contactFormValues['event_type'] === 'Homecoming' ? 'selected' : ''; ?>>Homecoming</option>
                                            <option value="Other" <?php echo $contactFormValues['event_type'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                        <?php if (isset($contactFormErrors['event_type'])): ?>
                                            <small><?php echo htmlspecialchars($contactFormErrors['event_type'], ENT_QUOTES, 'UTF-8'); ?></small>
                                        <?php endif; ?>
                                    </label>

                                </div>

                                <label class="contact-field contact-field-full">
                                    <span>Message</span>
                                    <textarea name="message" rows="6" placeholder="Tell us about your date, plans, and the kind of coverage you need."><?php echo htmlspecialchars($contactFormValues['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    <?php if (isset($contactFormErrors['message'])): ?>
                                        <small><?php echo htmlspecialchars($contactFormErrors['message'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    <?php endif; ?>
                                </label>

                                <button class="button button-secondary contact-submit" type="submit">Send Inquiry</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="footer-logo-link" href="#" aria-label="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
                <img class="footer-logo" src="assets/images/logo-black.png" alt="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
            </a>

            <div class="footer-socials" aria-label="Social links">
                <a class="footer-social-link" href="https://wa.me/94<?php echo htmlspecialchars(substr(preg_replace('/\D+/', '', $whatsAppNumber), -9), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" aria-label="WhatsApp">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.5 11.4c0 4.7-3.8 8.5-8.5 8.5-1.5 0-2.9-.4-4.2-1.1L3.5 20l1.3-4.1A8.4 8.4 0 0 1 3.5 11.4C3.5 6.7 7.3 3 12 3s8.5 3.7 8.5 8.4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.1 8.6c-.2-.4-.5-.4-.8-.4h-.6c-.2 0-.6.1-.8.4-.3.3-1 1-.9 2.3.1 1.3 1 2.6 1.1 2.7.1.2 1.9 3 4.8 4 .7.3 1.2.4 1.6.2.5-.1 1.5-.7 1.7-1.4.2-.7.2-1.3.1-1.4-.1-.1-.3-.2-.7-.4-.4-.2-1.1-.5-1.3-.6-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8 1-.2.2-.3.2-.6.1-.3-.2-1.2-.4-2.2-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.4.1-.6l.4-.4c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.6l-.8-1.6Z" fill="currentColor" stroke="none"/>
                    </svg>
                </a>
                <a class="footer-social-link" href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Email">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6.5h16A1.5 1.5 0 0 1 21.5 8v8A1.5 1.5 0 0 1 20 17.5H4A1.5 1.5 0 0 1 2.5 16V8A1.5 1.5 0 0 1 4 6.5Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m4 8 8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a class="footer-social-link" href="<?php echo htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="4.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="12" cy="12" r="3.8" fill="none" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="17.3" cy="6.7" r="1.1" fill="currentColor"/>
                    </svg>
                </a>
                <a class="footer-social-link" href="<?php echo htmlspecialchars($facebookUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.4 20.5v-7h2.3l.4-2.7h-2.7V9.1c0-.8.2-1.3 1.3-1.3h1.5V5.4c-.3 0-1.1-.1-2-.1-2 0-3.4 1.2-3.4 3.5v2h-2.3v2.7h2.3v7h2.3Z" fill="currentColor"/>
                    </svg>
                </a>
            </div>

            <p class="footer-copy">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var header = document.querySelector(".site-header");
            var body = document.body;
            var siteLoader = document.querySelector("[data-site-loader]");
            var heroVideo = document.querySelector(".hero-panel-video");
            var reviewModal = document.querySelector("[data-review-modal]");
            var reviewModalName = reviewModal ? reviewModal.querySelector("[data-review-modal-name]") : null;
            var reviewModalDate = reviewModal ? reviewModal.querySelector("[data-review-modal-date]") : null;
            var reviewModalText = reviewModal ? reviewModal.querySelector("[data-review-modal-text]") : null;
            var reviewModalSource = reviewModal ? reviewModal.querySelector("[data-review-modal-source]") : null;
            var reviewModalStars = reviewModal ? reviewModal.querySelector("[data-review-modal-stars]") : null;
            var reviewModalInitials = reviewModal ? reviewModal.querySelector("[data-review-modal-initials]") : null;
            var reviewCarousels = document.querySelectorAll("[data-review-carousel]");
            var portfolioGalleries = document.querySelectorAll("[data-portfolio-gallery]");
            var lenis = typeof window.Lenis === "function"
                ? new window.Lenis({
                    autoRaf: true,
                    anchors: true,
                    smoothWheel: true,
                    lerp: 0.08
                })
                : null;

            window.__siteLenis = lenis;

            body.classList.add("is-loading");

            if (lenis) {
                lenis.stop();
            }

            function hideSiteLoader() {
                if (!siteLoader || siteLoader.classList.contains("is-hidden")) {
                    body.classList.remove("is-loading");
                    if (lenis) {
                        lenis.start();
                    }
                    return;
                }

                siteLoader.classList.add("is-hidden");
                body.classList.remove("is-loading");
                if (lenis) {
                    lenis.start();
                }
            }

            if (heroVideo) {
                heroVideo.addEventListener("loadeddata", hideSiteLoader, { once: true });
                heroVideo.addEventListener("canplay", hideSiteLoader, { once: true });
            }

            window.setTimeout(hideSiteLoader, 2600);

            function formatVideoTime(seconds) {
                var totalSeconds = Number.isFinite(seconds) ? Math.max(0, Math.floor(seconds)) : 0;
                var minutes = Math.floor(totalSeconds / 60);
                var remainingSeconds = totalSeconds % 60;

                return minutes + ":" + String(remainingSeconds).padStart(2, "0");
            }

            var youtubeReadyQueue = window.__youtubeReadyQueue || [];

            window.__youtubeReadyQueue = youtubeReadyQueue;

            if (!window.__youtubeReadyHandlerInstalled) {
                var previousYouTubeReady = window.onYouTubeIframeAPIReady;

                window.onYouTubeIframeAPIReady = function () {
                    if (typeof previousYouTubeReady === "function") {
                        previousYouTubeReady();
                    }

                    window.__youtubeApiReady = true;

                    youtubeReadyQueue.splice(0).forEach(function (callback) {
                        callback();
                    });
                };

                window.__youtubeReadyHandlerInstalled = true;
            }

            function whenYouTubeReady(callback) {
                if (window.YT && typeof window.YT.Player === "function") {
                    callback();
                    return;
                }

                youtubeReadyQueue.push(callback);
            }

            function initVideoPlayer(player) {
                var playerElement;
                var overlayButton;
                var playButton;
                var fullscreenButton;
                var progressInput;
                var volumeInput;
                var timeLabel;
                var playIcon;
                var fullscreenTarget;
                var youtubeState;

                if (!player || player.getAttribute("data-player-ready") === "true") {
                    return;
                }

                playerElement = player.querySelector("[data-youtube-player]");
                fullscreenTarget = player;
                overlayButton = player.querySelector(".portfolio-play-overlay");
                playButton = player.querySelector('[data-video-action="toggle-play"]');
                fullscreenButton = player.querySelector('[data-video-action="fullscreen"]');
                progressInput = player.querySelector("[data-video-progress]");
                volumeInput = player.querySelector("[data-video-volume]");
                timeLabel = player.querySelector("[data-video-time]");
                playIcon = player.querySelector("[data-play-icon]");

                if (!playerElement || !overlayButton || !playButton || !fullscreenButton || !progressInput || !volumeInput || !timeLabel) {
                    return;
                }

                player.setAttribute("data-player-ready", "true");
                youtubeState = {
                    apiPlayer: null,
                    intervalId: 0,
                    currentVideoId: playerElement.getAttribute("data-video-id") || "",
                    isSwitching: false
                };
                player._youtubeState = youtubeState;

                function stopProgressTicker() {
                    if (youtubeState.intervalId) {
                        window.clearInterval(youtubeState.intervalId);
                        youtubeState.intervalId = 0;
                    }
                }

                function updatePlayState() {
                    var playerState = youtubeState.apiPlayer ? youtubeState.apiPlayer.getPlayerState() : -1;
                    var isPlaying = playerState === window.YT.PlayerState.PLAYING;

                    player.classList.toggle("is-playing", isPlaying);
                    playButton.setAttribute("aria-label", isPlaying ? "Pause video" : "Play video");
                    overlayButton.setAttribute("aria-label", isPlaying ? "Pause video" : "Play video");

                    if (playIcon) {
                        playIcon.innerHTML = isPlaying ? "&#10074;&#10074;" : "&#9654;";
                    }
                }

                function updateVolumeState() {
                    var volumeValue;

                    if (!youtubeState.apiPlayer) {
                        return;
                    }

                    volumeValue = youtubeState.apiPlayer.isMuted() ? 0 : Math.round(youtubeState.apiPlayer.getVolume());
                    volumeInput.value = String(volumeValue);
                    volumeInput.style.setProperty("--progress", volumeValue + "%");
                    volumeInput.setAttribute("aria-valuetext", volumeValue + "%");
                }

                function updateProgress() {
                    var duration;
                    var currentTime;
                    var value;

                    if (!youtubeState.apiPlayer) {
                        return;
                    }

                    duration = Number(youtubeState.apiPlayer.getDuration()) || 0;
                    currentTime = Number(youtubeState.apiPlayer.getCurrentTime()) || 0;
                    value = duration > 0 ? (currentTime / duration) * 100 : 0;

                    progressInput.value = String(value);
                    progressInput.style.setProperty("--progress", value + "%");
                    timeLabel.textContent = formatVideoTime(currentTime) + " / " + formatVideoTime(duration);
                }

                function startProgressTicker() {
                    stopProgressTicker();
                    youtubeState.intervalId = window.setInterval(updateProgress, 250);
                }

                function togglePlay() {
                    var playerState;

                    if (!youtubeState.apiPlayer) {
                        return;
                    }

                    playerState = youtubeState.apiPlayer.getPlayerState();

                    if (playerState === window.YT.PlayerState.PLAYING) {
                        youtubeState.apiPlayer.pauseVideo();
                        return;
                    }

                    youtubeState.apiPlayer.playVideo();
                }

                function toggleFullscreen() {
                    if (document.fullscreenElement === fullscreenTarget) {
                        document.exitFullscreen();
                        return;
                    }

                    if (fullscreenTarget.requestFullscreen) {
                        fullscreenTarget.requestFullscreen();
                        return;
                    }

                    if (fullscreenTarget.webkitRequestFullscreen) {
                        fullscreenTarget.webkitRequestFullscreen();
                    }
                }

                overlayButton.addEventListener("click", togglePlay);
                playButton.addEventListener("click", togglePlay);
                fullscreenButton.addEventListener("click", toggleFullscreen);

                progressInput.addEventListener("input", function () {
                    var duration;

                    if (!youtubeState.apiPlayer) {
                        return;
                    }

                    duration = Number(youtubeState.apiPlayer.getDuration()) || 0;

                    if (duration <= 0) {
                        return;
                    }

                    youtubeState.apiPlayer.seekTo((Number(progressInput.value) / 100) * duration, true);
                    updateProgress();
                });

                volumeInput.addEventListener("input", function () {
                    var nextVolume = Number(volumeInput.value);

                    if (!youtubeState.apiPlayer) {
                        return;
                    }

                    youtubeState.apiPlayer.setVolume(nextVolume);

                    if (nextVolume === 0) {
                        youtubeState.apiPlayer.mute();
                    } else {
                        youtubeState.apiPlayer.unMute();
                    }

                    updateVolumeState();
                });

                whenYouTubeReady(function () {
                    youtubeState.apiPlayer = new window.YT.Player(playerElement, {
                        videoId: youtubeState.currentVideoId,
                        playerVars: {
                            autoplay: 1,
                            controls: 0,
                            rel: 0,
                            playsinline: 1,
                            fs: 0,
                            iv_load_policy: 3,
                            disablekb: 1
                        },
                        events: {
                            onReady: function (event) {
                                event.target.mute();
                                event.target.setVolume(0);
                                event.target.playVideo();
                                updatePlayState();
                                updateVolumeState();
                                updateProgress();
                            },
                            onStateChange: function (event) {
                                updatePlayState();
                                updateVolumeState();
                                updateProgress();

                                if (event.data === window.YT.PlayerState.PLAYING) {
                                    startProgressTicker();

                                    if (youtubeState.isSwitching) {
                                        player.closest("[data-portfolio-gallery]").classList.remove("is-switching");
                                        youtubeState.isSwitching = false;
                                    }
                                } else {
                                    stopProgressTicker();
                                }

                                if (event.data === window.YT.PlayerState.ENDED) {
                                    event.target.seekTo(0);
                                    event.target.playVideo();
                                }

                                if (youtubeState.isSwitching && event.data === window.YT.PlayerState.CUED) {
                                    player.closest("[data-portfolio-gallery]").classList.remove("is-switching");
                                    youtubeState.isSwitching = false;
                                }
                            }
                        }
                    });
                });
            }

            function updateHeaderState() {
                if (!header) {
                    return;
                }

                header.classList.toggle("is-scrolled", window.scrollY > 24);
            }

            updateHeaderState();
            window.addEventListener("scroll", updateHeaderState, { passive: true });

            function openReviewModal(card) {
                var fullText;
                var name;
                var date;
                var source;
                var rating;
                var initials;

                if (!reviewModal || !card) {
                    return;
                }

                fullText = card.getAttribute("data-full-review") || "";
                name = card.querySelector(".testimonial-person strong");
                date = card.querySelector(".testimonial-person span");
                source = card.querySelector(".testimonial-meta a");
                rating = card.querySelector(".testimonial-stars");
                initials = card.querySelector(".testimonial-avatar");

                if (reviewModalName && name) {
                    reviewModalName.textContent = name.textContent;
                }

                if (reviewModalDate && date) {
                    reviewModalDate.textContent = date.textContent;
                }

                if (reviewModalText) {
                    reviewModalText.textContent = '"' + fullText + '"';
                }

                if (reviewModalSource && source) {
                    var reviewModalSourceLabel = reviewModalSource.querySelector("[data-review-modal-source-label]");

                    if (reviewModalSourceLabel) {
                        reviewModalSourceLabel.textContent = source.textContent.trim();
                    }

                    reviewModalSource.setAttribute("href", source.getAttribute("href") || "#");
                }

                if (reviewModalStars && rating) {
                    reviewModalStars.setAttribute("aria-label", rating.getAttribute("aria-label") || "5 star review");
                }

                if (reviewModalInitials && initials) {
                    reviewModalInitials.textContent = initials.textContent;
                }

                reviewModal.style.opacity = "0";
                reviewModal.hidden = false;
                reviewModal.offsetHeight;
                reviewModal.hidden = false;
                body.classList.add("has-review-modal");
                reviewModal.style.opacity = "1";
                if (lenis) {
                    lenis.stop();
                }
            }

            function closeReviewModal() {
                if (!reviewModal) {
                    return;
                }

                reviewModal.hidden = true;
                body.classList.remove("has-review-modal");
                if (lenis && !body.classList.contains("is-loading")) {
                    lenis.start();
                }
            }

            document.querySelectorAll(".testimonial-card").forEach(function (card) {
                var quote = card.querySelector(".testimonial-quote");
                var moreButton;
                var fullText;

                if (!quote) {
                    return;
                }

                fullText = quote.textContent.trim().replace(/^"|"$/g, "");
                card.setAttribute("data-full-review", fullText);

                if ((fullText.match(/\S+/g) || []).length <= 30) {
                    return;
                }

                quote.textContent = '"' + (fullText.match(/\S+/g) || []).slice(0, 30).join(" ");

                moreButton = document.createElement("button");
                moreButton.className = "testimonial-more";
                moreButton.type = "button";
                moreButton.textContent = "...See more";
                moreButton.addEventListener("click", function () {
                    openReviewModal(card);
                });

                quote.appendChild(moreButton);
            });

            if (reviewModal) {
                reviewModal.querySelectorAll("[data-review-close]").forEach(function (button) {
                    button.addEventListener("click", closeReviewModal);
                });

                document.addEventListener("keydown", function (event) {
                    if (event.key === "Escape" && !reviewModal.hidden) {
                        closeReviewModal();
                    }
                });
            }

            reviewCarousels.forEach(function (carousel) {
                var track = carousel.querySelector("[data-review-track]");
                var prevButton = carousel.querySelector(".testimonial-nav-prev");
                var nextButton = carousel.querySelector(".testimonial-nav-next");
                var cards = track ? track.querySelectorAll(".testimonial-card") : [];

                function getReviewStep() {
                    var firstCard = cards[0];
                    var styles;
                    var gap;

                    if (!track || !firstCard) {
                        return 0;
                    }

                    styles = window.getComputedStyle(track);
                    gap = parseFloat(styles.columnGap || styles.gap) || 0;

                    return firstCard.getBoundingClientRect().width + gap;
                }

                if (!track || !cards.length) {
                    return;
                }

                if (prevButton) {
                    prevButton.addEventListener("click", function () {
                        track.scrollBy({ left: -getReviewStep(), behavior: "smooth" });
                    });
                }

                if (nextButton) {
                    nextButton.addEventListener("click", function () {
                        track.scrollBy({ left: getReviewStep(), behavior: "smooth" });
                    });
                }
            });

            portfolioGalleries.forEach(function (gallery) {
                var player = gallery.querySelector("[data-video-player]");
                var layout = gallery.getAttribute("data-gallery-layout") || "strip";
                var thumbStrip = gallery.querySelector(".portfolio-film-thumbs");
                var prevButton = gallery.querySelector(".portfolio-thumb-nav-prev");
                var nextButton = gallery.querySelector(".portfolio-thumb-nav-next");
                var thumbs = gallery.querySelectorAll(".portfolio-thumb");
                var coverflowThumbs = gallery.querySelectorAll(".portfolio-coverflow-thumb");
                var coverflowPrevButton = gallery.querySelector(".portfolio-coverflow-nav-prev");
                var coverflowNextButton = gallery.querySelector(".portfolio-coverflow-nav-next");
                var isDragging = false;
                var dragMoved = false;
                var dragStartX = 0;
                var dragStartScrollLeft = 0;
                var suppressClickUntil = 0;
                var activeIndex = 0;
                var switchTimeoutId = 0;
                var youtubeState;

                if (!player || !thumbs.length) {
                    return;
                }

                initVideoPlayer(player);
                youtubeState = player._youtubeState;

                function normalizeRelativeIndex(index, current, total) {
                    var delta = index - current;

                    if (delta > total / 2) {
                        delta -= total;
                    } else if (delta < -(total / 2)) {
                        delta += total;
                    }

                    return delta;
                }

                function applyCoverflowState() {
                    if (layout !== "coverflow" || !coverflowThumbs.length) {
                        return;
                    }

                    coverflowThumbs.forEach(function (thumb, index) {
                        var delta = normalizeRelativeIndex(index, activeIndex, coverflowThumbs.length);
                        var absDelta = Math.abs(delta);
                        var x = delta < 0 ? "-13%" : "13%";
                        var scale = 0.86;

                        thumb.classList.toggle("is-prev", delta === -1);
                        thumb.classList.toggle("is-next", delta === 1);
                        thumb.classList.toggle("is-active", delta === 0);
                        thumb.classList.toggle("is-visible", absDelta === 1);
                        thumb.style.setProperty("--coverflow-x", absDelta === 1 ? x : "0%");
                        thumb.style.setProperty("--coverflow-scale", String(scale));
                        thumb.style.setProperty("--coverflow-z", String(absDelta === 1 ? 1 : 0));
                    });
                }

                function selectThumb(thumb) {
                    var nextVideoId;
                    var nextIndex;

                    if (!thumb || Date.now() < suppressClickUntil) {
                        return;
                    }

                    nextVideoId = thumb.getAttribute("data-video-id");
                    nextIndex = Array.prototype.indexOf.call(thumbs, thumb);

                    if (!nextVideoId || !youtubeState || !youtubeState.apiPlayer) {
                        return;
                    }

                    thumbs.forEach(function (item) {
                        item.classList.remove("is-active");
                        item.setAttribute("aria-pressed", "false");
                    });

                    thumb.classList.add("is-active");
                    thumb.setAttribute("aria-pressed", "true");
                    activeIndex = nextIndex;
                    applyCoverflowState();

                    if (thumbStrip) {
                        thumb.scrollIntoView({ behavior: "smooth", inline: "nearest", block: "nearest" });
                    }

                    if (youtubeState.currentVideoId !== nextVideoId) {
                        if (switchTimeoutId) {
                            window.clearTimeout(switchTimeoutId);
                        }

                        youtubeState.currentVideoId = nextVideoId;
                        youtubeState.isSwitching = true;
                        gallery.classList.add("is-switching");

                        switchTimeoutId = window.setTimeout(function () {
                            youtubeState.apiPlayer.loadVideoById(nextVideoId);
                        }, 100);

                        return;
                    }

                    if (youtubeState.isSwitching) {
                        return;
                    }

                    youtubeState.apiPlayer.playVideo();
                }

                function getThumbStep() {
                    var firstThumb = thumbs[0];
                    var styles;
                    var gap;

                    if (!firstThumb || !thumbStrip) {
                        return 0;
                    }

                    styles = window.getComputedStyle(thumbStrip);
                    gap = parseFloat(styles.columnGap || styles.gap) || 0;

                    return firstThumb.getBoundingClientRect().width + gap;
                }

                if (prevButton && thumbStrip) {
                    prevButton.addEventListener("click", function () {
                        thumbStrip.scrollBy({ left: -getThumbStep(), behavior: "smooth" });
                    });
                }

                if (nextButton && thumbStrip) {
                    nextButton.addEventListener("click", function () {
                        thumbStrip.scrollBy({ left: getThumbStep(), behavior: "smooth" });
                    });
                }

                if (thumbStrip) {
                    thumbStrip.addEventListener("pointerdown", function (event) {
                        if (event.pointerType === "mouse" && event.button !== 0) {
                            return;
                        }

                        if (event.target.closest(".portfolio-thumb-nav")) {
                            return;
                        }

                        isDragging = true;
                        dragMoved = false;
                        dragStartX = event.clientX;
                        dragStartScrollLeft = thumbStrip.scrollLeft;
                    });

                    thumbStrip.addEventListener("pointermove", function (event) {
                        if (!isDragging) {
                            return;
                        }

                        var deltaX = event.clientX - dragStartX;

                        if (Math.abs(deltaX) > 12) {
                            if (!dragMoved) {
                                thumbStrip.classList.add("is-dragging");

                                if (thumbStrip.setPointerCapture) {
                                    thumbStrip.setPointerCapture(event.pointerId);
                                }
                            }

                            dragMoved = true;
                        }

                        if (dragMoved) {
                            event.preventDefault();
                            thumbStrip.scrollLeft = dragStartScrollLeft - deltaX;
                        }
                    });

                    function stopThumbDrag(pointerId) {
                        if (dragMoved) {
                            suppressClickUntil = Date.now() + 250;
                        }

                        isDragging = false;
                        dragMoved = false;
                        thumbStrip.classList.remove("is-dragging");

                        if (pointerId !== undefined && thumbStrip.hasPointerCapture && thumbStrip.hasPointerCapture(pointerId)) {
                            thumbStrip.releasePointerCapture(pointerId);
                        }
                    }

                    thumbStrip.addEventListener("pointerup", function (event) {
                        stopThumbDrag(event.pointerId);
                    });

                    thumbStrip.addEventListener("pointercancel", function (event) {
                        stopThumbDrag(event.pointerId);
                    });

                    thumbStrip.addEventListener("lostpointercapture", function () {
                        stopThumbDrag();
                    });
                }

                thumbs.forEach(function (thumb) {
                    thumb.addEventListener("click", function () {
                        selectThumb(thumb);
                    });
                });

                if (layout === "coverflow" && coverflowPrevButton && coverflowThumbs.length) {
                    coverflowPrevButton.addEventListener("click", function () {
                        selectThumb(thumbs[(activeIndex - 1 + thumbs.length) % thumbs.length]);
                    });
                }

                if (layout === "coverflow" && coverflowNextButton && coverflowThumbs.length) {
                    coverflowNextButton.addEventListener("click", function () {
                        selectThumb(thumbs[(activeIndex + 1) % thumbs.length]);
                    });
                }

                applyCoverflowState();
            });
        });
    </script>
</body>
</html>
