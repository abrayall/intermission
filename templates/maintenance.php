<?php
if (!defined('ABSPATH')) {
    exit;
}

$selected_theme = get_option('intermission_theme', 'default');

if (isset($_GET['theme'])) {
    $preview_theme = sanitize_text_field($_GET['theme']);
    $intermission = Intermission::get_instance();
    $available_themes = $intermission->get_available_themes();

    if (isset($available_themes[$preview_theme])) {
        $selected_theme = $preview_theme;
    }
}

wp_enqueue_style('intermission-base', plugin_dir_url(dirname(__FILE__)) . 'assets/css/intermission.css', array(), INTERMISSION_VERSION);
wp_enqueue_style('intermission-theme', plugin_dir_url(dirname(__FILE__)) . 'themes/' . $selected_theme . '.css', array('intermission-base'), INTERMISSION_VERSION);

$countdown_gmt = get_option('intermission_countdown_gmt', 0);
if ($countdown_gmt > 0) {
    wp_enqueue_script('intermission-countdown', plugin_dir_url(dirname(__FILE__)) . 'assets/js/intermission.js', array(), INTERMISSION_VERSION, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_option('intermission_headline', 'Under Maintenance')); ?></title>
    <?php wp_head(); ?>
</head>
<body>
    <div class="intermission-container">
        <div class="intermission-logo">
            <?php
            $intermission = Intermission::get_instance();
            echo $intermission->get_icon_html();
            ?>
        </div>

        <h1 class="intermission-headline"><?php echo esc_html(get_option('intermission_headline', 'Under Maintenance')); ?></h1>

        <p class="intermission-message">
            <?php echo wp_kses_post(nl2br(get_option('intermission_message', 'We are currently performing scheduled maintenance. We will be back shortly!'))); ?>
        </p>

        <?php
        $countdown_gmt = get_option('intermission_countdown_gmt', 0);
        if ($countdown_gmt > 0) {
            ?>
            <div class="intermission-countdown" id="countdown" data-target="<?php echo esc_attr($countdown_gmt); ?>" data-autodisable="<?php echo esc_attr(get_option('intermission_auto_disable', false) ? 'true' : 'false'); ?>" data-homeurl="<?php echo esc_url(home_url('/')); ?>">
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

        <?php
        $social = get_option('intermission_social', array());
        $has_social = false;
        foreach ($social as $platform => $url) {
            if (!empty($url)) {
                $has_social = true;
                break;
            }
        }

        if ($has_social):
        ?>
        <div class="intermission-social">
            <?php if (!empty($social['facebook'])): ?>
            <a href="<?php echo esc_url($social['facebook']); ?>" class="intermission-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['instagram'])): ?>
            <a href="<?php echo esc_url($social['instagram']); ?>" class="intermission-social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                    <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
                    <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['x'])): ?>
            <a href="<?php echo esc_url($social['x']); ?>" class="intermission-social-link" aria-label="X" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['tiktok'])): ?>
            <a href="<?php echo esc_url($social['tiktok']); ?>" class="intermission-social-link" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['youtube'])): ?>
            <a href="<?php echo esc_url($social['youtube']); ?>" class="intermission-social-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['linkedin'])): ?>
            <a href="<?php echo esc_url($social['linkedin']); ?>" class="intermission-social-link" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['snapchat'])): ?>
            <a href="<?php echo esc_url($social['snapchat']); ?>" class="intermission-social-link" aria-label="Snapchat" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12 1.033-.301.165-.088.344-.104.464-.104.182 0 .359.029.509.09.45.149.734.479.734.838.015.449-.39.839-1.213 1.168-.089.029-.209.075-.344.119-.45.135-1.139.36-1.333.81-.09.224-.061.524.12.868l.015.015c.06.136 1.526 3.475 4.791 4.014.255.044.435.27.42.509 0 .075-.015.149-.045.225-.24.569-1.273.988-3.146 1.271-.059.091-.12.375-.164.57-.029.179-.074.36-.134.553-.076.271-.27.405-.555.405h-.03c-.135 0-.313-.031-.538-.074-.36-.075-.765-.135-1.273-.135-.3 0-.599.015-.913.074-.6.104-1.123.464-1.723.884-.853.599-1.826 1.288-3.294 1.288-.06 0-.119-.015-.18-.015h-.149c-1.468 0-2.427-.675-3.279-1.288-.599-.42-1.107-.779-1.707-.884-.314-.045-.629-.074-.928-.074-.54 0-.958.089-1.272.149-.211.043-.391.074-.54.074-.374 0-.523-.224-.583-.42-.061-.192-.09-.389-.135-.567-.046-.181-.105-.494-.166-.57-1.918-.222-2.95-.642-3.189-1.226-.031-.063-.052-.15-.055-.225-.015-.243.165-.465.42-.509 3.264-.54 4.73-3.879 4.791-4.02l.016-.029c.18-.345.224-.645.119-.869-.195-.434-.884-.658-1.332-.809-.121-.029-.24-.074-.346-.119-1.107-.435-1.257-.93-1.197-1.273.09-.479.674-.793 1.168-.793.146 0 .27.029.383.074.42.194.789.3 1.104.3.234 0 .384-.06.465-.105l-.046-.569c-.098-1.626-.225-3.651.307-4.837C7.392 1.077 10.739.807 11.727.807l.419-.015h.06z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['pinterest'])): ?>
            <a href="<?php echo esc_url($social['pinterest']); ?>" class="intermission-social-link" aria-label="Pinterest" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['reddit'])): ?>
            <a href="<?php echo esc_url($social['reddit']); ?>" class="intermission-social-link" aria-label="Reddit" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>

            <?php if (!empty($social['github'])): ?>
            <a href="<?php echo esc_url($social['github']); ?>" class="intermission-social-link" aria-label="GitHub" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" fill="currentColor"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
