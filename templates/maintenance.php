<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_option('intermission_headline', 'Under Maintenance')); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url(plugin_dir_url(dirname(__FILE__)) . 'assets/css/intermission.css'); ?>">
    <?php
    $selected_theme = get_option('intermission_theme', 'default');

    if (isset($_GET['theme'])) {
        $preview_theme = sanitize_text_field($_GET['theme']);
        $intermission = Intermission::get_instance();
        $available_themes = $intermission->get_available_themes();

        if (isset($available_themes[$preview_theme])) {
            $selected_theme = $preview_theme;
        }
    }

    $theme_url = plugin_dir_url(dirname(__FILE__)) . 'themes/' . $selected_theme . '.css';
    ?>
    <link rel="stylesheet" href="<?php echo esc_url($theme_url); ?>">
</head>
<body>
    <div class="intermission-container">
        <div class="intermission-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>

        <h1 class="intermission-headline"><?php echo esc_html(get_option('intermission_headline', 'Under Maintenance')); ?></h1>

        <p class="intermission-message">
            <?php echo wp_kses_post(nl2br(get_option('intermission_message', 'We are currently performing scheduled maintenance. We will be back shortly!'))); ?>
        </p>

        <?php
        $countdown_date = get_option('intermission_countdown_date', '');
        $countdown_time = get_option('intermission_countdown_time', '');
        if (!empty($countdown_date) && !empty($countdown_time)) {
            $countdown_datetime = $countdown_date . ' ' . $countdown_time;
            ?>
            <div class="intermission-countdown" id="countdown" data-target="<?php echo esc_attr($countdown_datetime); ?>">
                <div class="intermission-countdown-item">
                    <span class="intermission-countdown-value" id="days">00</span>
                    <span class="intermission-countdown-label">Days</span>
                </div>
                <div class="intermission-countdown-item">
                    <span class="intermission-countdown-value" id="hours">00</span>
                    <span class="intermission-countdown-label">Hours</span>
                </div>
                <div class="intermission-countdown-item">
                    <span class="intermission-countdown-value" id="minutes">00</span>
                    <span class="intermission-countdown-label">Minutes</span>
                </div>
                <div class="intermission-countdown-item">
                    <span class="intermission-countdown-value" id="seconds">00</span>
                    <span class="intermission-countdown-label">Seconds</span>
                </div>
            </div>
        <?php } ?>

        <div class="intermission-social">
            <a href="https://twitter.com" class="intermission-social-link" aria-label="Twitter">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" fill="currentColor"/>
                </svg>
            </a>
            <a href="https://facebook.com" class="intermission-social-link" aria-label="Facebook">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" fill="currentColor"/>
                </svg>
            </a>
            <a href="https://instagram.com" class="intermission-social-link" aria-label="Instagram">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
                    <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/>
                </svg>
            </a>
        </div>
    </div>

    <script>
        function updateCountdown() {
            const countdown = document.getElementById('countdown');
            if (!countdown) return;

            const targetDate = new Date(countdown.dataset.target).getTime();

            function update() {
                const now = new Date().getTime();
                const distance = targetDate - now;

                if (distance < 0) {
                    countdown.innerHTML = '<p class="intermission-message">We are launching now!</p>';
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = String(days).padStart(2, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }

            update();
            setInterval(update, 1000);
        }

        updateCountdown();
    </script>
</body>
</html>
