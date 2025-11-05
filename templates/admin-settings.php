<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['intermission_settings_nonce']) && wp_verify_nonce($_POST['intermission_settings_nonce'], 'intermission_settings')) {
    update_option('intermission_enabled', isset($_POST['intermission_enabled']));
    update_option('intermission_headline', sanitize_text_field($_POST['intermission_headline']));
    update_option('intermission_message', wp_kses_post($_POST['intermission_message']));
    update_option('intermission_auto_enable_date', sanitize_text_field($_POST['intermission_auto_enable_date']));
    update_option('intermission_auto_enable_time', sanitize_text_field($_POST['intermission_auto_enable_time']));

    if (!empty($_POST['intermission_auto_enable_gmt'])) {
        update_option('intermission_auto_enable_gmt', intval($_POST['intermission_auto_enable_gmt']));
    } else {
        delete_option('intermission_auto_enable_gmt');
    }

    update_option('intermission_countdown_date', sanitize_text_field($_POST['intermission_countdown_date']));
    update_option('intermission_countdown_time', sanitize_text_field($_POST['intermission_countdown_time']));

    if (!empty($_POST['intermission_countdown_gmt'])) {
        update_option('intermission_countdown_gmt', intval($_POST['intermission_countdown_gmt']));
    } else {
        delete_option('intermission_countdown_gmt');
    }

    update_option('intermission_auto_disable', isset($_POST['intermission_auto_disable']));
    update_option('intermission_theme', sanitize_text_field($_POST['intermission_theme']));
    update_option('intermission_secret_key', sanitize_text_field($_POST['intermission_secret_key']));
    update_option('intermission_whitelist_ips', sanitize_textarea_field($_POST['intermission_whitelist_ips']));
    update_option('intermission_icon_type', sanitize_text_field($_POST['intermission_icon_type']));
    update_option('intermission_custom_icon_url', esc_url_raw($_POST['intermission_custom_icon_url']));

    $social = array(
        'facebook' => isset($_POST['intermission_social_facebook']) ? esc_url_raw($_POST['intermission_social_facebook']) : '',
        'instagram' => isset($_POST['intermission_social_instagram']) ? esc_url_raw($_POST['intermission_social_instagram']) : '',
        'x' => isset($_POST['intermission_social_x']) ? esc_url_raw($_POST['intermission_social_x']) : '',
        'tiktok' => isset($_POST['intermission_social_tiktok']) ? esc_url_raw($_POST['intermission_social_tiktok']) : '',
        'youtube' => isset($_POST['intermission_social_youtube']) ? esc_url_raw($_POST['intermission_social_youtube']) : '',
        'linkedin' => isset($_POST['intermission_social_linkedin']) ? esc_url_raw($_POST['intermission_social_linkedin']) : '',
        'snapchat' => isset($_POST['intermission_social_snapchat']) ? esc_url_raw($_POST['intermission_social_snapchat']) : '',
        'pinterest' => isset($_POST['intermission_social_pinterest']) ? esc_url_raw($_POST['intermission_social_pinterest']) : '',
        'reddit' => isset($_POST['intermission_social_reddit']) ? esc_url_raw($_POST['intermission_social_reddit']) : '',
        'github' => isset($_POST['intermission_social_github']) ? esc_url_raw($_POST['intermission_social_github']) : '',
    );
    update_option('intermission_social', $social);

    echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
}

$enabled = get_option('intermission_enabled', false);
$headline = get_option('intermission_headline', 'Under Maintenance');
$message = get_option('intermission_message', 'We are currently performing scheduled maintenance. We will be back shortly!');
$auto_enable_date = get_option('intermission_auto_enable_date', '');
$auto_enable_time = get_option('intermission_auto_enable_time', '');
$countdown_date = get_option('intermission_countdown_date', '');
$countdown_time = get_option('intermission_countdown_time', '');
$auto_disable = get_option('intermission_auto_disable', false);

$countdown_gmt = get_option('intermission_countdown_gmt', 0);
if ($countdown_gmt > 0 && time() >= $countdown_gmt) {
    $countdown_date = '';
    $countdown_time = '';
}

$auto_enable_gmt = get_option('intermission_auto_enable_gmt', 0);
if ($auto_enable_gmt > 0 && time() >= $auto_enable_gmt) {
    $auto_enable_date = '';
    $auto_enable_time = '';
}
$selected_theme = get_option('intermission_theme', 'default');
$secret_key = get_option('intermission_secret_key', '');
$whitelist_ips = get_option('intermission_whitelist_ips', '');
$icon_type = get_option('intermission_icon_type', 'wrench');
$custom_icon_url = get_option('intermission_custom_icon_url', '');
$social = get_option('intermission_social', array(
    'facebook' => '',
    'instagram' => '',
    'x' => '',
    'tiktok' => '',
    'youtube' => '',
    'linkedin' => '',
    'snapchat' => '',
    'pinterest' => '',
    'reddit' => '',
    'github' => ''
));

$intermission = Intermission::get_instance();
$available_themes = $intermission->get_available_themes();
?>

<div class="wrap intermission-settings">
    <h1>Intermission Settings</h1>

    <?php if ($enabled): ?>
        <div class="notice notice-warning">
            <p><strong>Maintenance Mode is Active!</strong> Your site is currently showing the maintenance page to visitors.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field('intermission_settings', 'intermission_settings_nonce'); ?>

        <div class="intermission-mode-toggle" style="margin-bottom: 20px;">
            <input type="checkbox" name="intermission_enabled" id="intermission_enabled" value="1" <?php checked($enabled); ?>>
            <label for="intermission_enabled" class="intermission-toggle-switch <?php echo $enabled ? 'maintenance' : 'live'; ?>">
                <span class="intermission-toggle-slider"></span>
            </label>
            <span class="intermission-mode-label"><?php echo $enabled ? 'Maintenance' : 'Live'; ?></span>
        </div>

        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active" data-tab="general">General</a>
            <a href="#times" class="nav-tab" data-tab="times">Times</a>
            <a href="#social" class="nav-tab" data-tab="social">Social</a>
            <a href="#advanced" class="nav-tab" data-tab="advanced">Advanced</a>
        </h2>

        <div id="general" class="intermission-tab-content">
            <table class="form-table">
                <tr>
                    <th scope="row">Theme:</th>
                <td>
                    <select name="intermission_theme" id="intermission_theme" class="regular-text">
                        <?php foreach ($available_themes as $theme_slug => $theme_data): ?>
                            <option value="<?php echo esc_attr($theme_slug); ?>" data-description="<?php echo esc_attr($theme_data['description']); ?>" <?php selected($selected_theme, $theme_slug); ?>>
                                <?php echo esc_html($theme_data['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?php echo esc_url(home_url('/intermission?theme=' . $selected_theme)); ?>" target="_blank" id="intermission-theme-preview" style="display: none; margin-left: 8px;">Preview</a>
                    <?php if (isset($available_themes[$selected_theme])): ?>
                        <p class="description" id="intermission-theme-description">
                            <?php echo esc_html($available_themes[$selected_theme]['description']); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th scope="row">Icon:</th>
                <td>
                    <select name="intermission_icon_type" id="intermission_icon_type" class="regular-text">
                        <option value="wrench" <?php selected($icon_type, 'wrench'); ?>>Wrench</option>
                        <option value="gear" <?php selected($icon_type, 'gear'); ?>>Gear</option>
                        <option value="tools" <?php selected($icon_type, 'tools'); ?>>Tools</option>
                        <option value="clock" <?php selected($icon_type, 'clock'); ?>>Clock</option>
                        <option value="rocket" <?php selected($icon_type, 'rocket'); ?>>Rocket</option>
                        <option value="code" <?php selected($icon_type, 'code'); ?>>Code</option>
                        <option value="shield" <?php selected($icon_type, 'shield'); ?>>Shield</option>
                        <option value="custom" <?php selected($icon_type, 'custom'); ?>>Custom Image</option>
                    </select>
                    <div id="intermission-custom-icon-upload" style="margin-top: 10px; <?php echo $icon_type !== 'custom' ? 'display: none;' : ''; ?>">
                        <input type="hidden" name="intermission_custom_icon_url" id="intermission_custom_icon_url" value="<?php echo esc_attr($custom_icon_url); ?>">
                        <button type="button" class="button" id="intermission_upload_icon_button">Select Image</button>
                        <button type="button" class="button" id="intermission_remove_icon_button" style="<?php echo empty($custom_icon_url) ? 'display: none;' : ''; ?>">Remove</button>
                        <div id="intermission_custom_icon_preview" style="margin-top: 10px; <?php echo empty($custom_icon_url) ? 'display: none;' : ''; ?>">
                            <img src="<?php echo esc_url($custom_icon_url); ?>" style="max-width: 100px; max-height: 100px; border-radius: 8px;">
                        </div>
                    </div>
                    <p class="description">Choose an icon to display on the maintenance page.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Headline:</th>
                <td>
                    <input type="text" name="intermission_headline" value="<?php echo esc_attr($headline); ?>" class="regular-text">
                    <p class="description">Main heading displayed on the maintenance page.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Message:</th>
                <td>
                    <textarea name="intermission_message" rows="5" class="large-text"><?php echo esc_textarea($message); ?></textarea>
                    <p class="description">Message displayed below the headline.</p>
                </td>
            </tr>
        </table>
        </div>

        <div id="times" class="intermission-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Start:</th>
                <td>
                    <input type="date" name="intermission_auto_enable_date" id="intermission_auto_enable_date" value="<?php echo esc_attr($auto_enable_date); ?>">
                    <input type="time" name="intermission_auto_enable_time" id="intermission_auto_enable_time" value="<?php echo esc_attr($auto_enable_time); ?>">
                    <input type="hidden" name="intermission_auto_enable_gmt" id="intermission_auto_enable_gmt" value="">
                    <p class="description">Automatically schedules maintenance mode for this time (in your local timezone). Leave empty to disable.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">End:</th>
                <td>
                    <input type="date" name="intermission_countdown_date" id="intermission_countdown_date" value="<?php echo esc_attr($countdown_date); ?>">
                    <input type="time" name="intermission_countdown_time" id="intermission_countdown_time" value="<?php echo esc_attr($countdown_time); ?>">
                    <input type="hidden" name="intermission_countdown_gmt" id="intermission_countdown_gmt" value="">
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="intermission_auto_disable" value="1" <?php checked($auto_disable, true); ?>>
                        Automatically put site in live mode
                    </label>
                    <p class="description">Optional countdown timer (times are in your local timezone). Leave empty to hide.</p>
                </td>
            </tr>
        </table>
        </div>

        <div id="social" class="intermission-tab-content" style="display: none;">
            <p class="description" style="margin-top: 0; margin-bottom: 10px;">Add links to your social media profiles. Only filled links will be displayed.</p>
            <table class="form-table">
                        <tr>
                            <td style="padding-left: 0;">
                                <label for="intermission_social_facebook" style="font-weight: 600;">Facebook:</label><br>
                                <input type="url" name="intermission_social_facebook" id="intermission_social_facebook" value="<?php echo esc_attr($social['facebook']); ?>" placeholder="https://facebook.com/yourpage" class="regular-text">
                            </td>
                            <td>
                                <label for="intermission_social_instagram" style="font-weight: 600;">Instagram:</label><br>
                                <input type="url" name="intermission_social_instagram" id="intermission_social_instagram" value="<?php echo esc_attr($social['instagram']); ?>" placeholder="https://instagram.com/yourusername" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 0;">
                                <label for="intermission_social_x" style="font-weight: 600;">X (Twitter):</label><br>
                                <input type="url" name="intermission_social_x" id="intermission_social_x" value="<?php echo esc_attr($social['x']); ?>" placeholder="https://x.com/yourusername" class="regular-text">
                            </td>
                            <td>
                                <label for="intermission_social_tiktok" style="font-weight: 600;">TikTok:</label><br>
                                <input type="url" name="intermission_social_tiktok" id="intermission_social_tiktok" value="<?php echo esc_attr($social['tiktok']); ?>" placeholder="https://tiktok.com/@yourusername" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 0;">
                                <label for="intermission_social_youtube" style="font-weight: 600;">YouTube:</label><br>
                                <input type="url" name="intermission_social_youtube" id="intermission_social_youtube" value="<?php echo esc_attr($social['youtube']); ?>" placeholder="https://youtube.com/@yourusername" class="regular-text">
                            </td>
                            <td>
                                <label for="intermission_social_linkedin" style="font-weight: 600;">LinkedIn:</label><br>
                                <input type="url" name="intermission_social_linkedin" id="intermission_social_linkedin" value="<?php echo esc_attr($social['linkedin']); ?>" placeholder="https://linkedin.com/in/yourusername" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 0;">
                                <label for="intermission_social_snapchat" style="font-weight: 600;">Snapchat:</label><br>
                                <input type="url" name="intermission_social_snapchat" id="intermission_social_snapchat" value="<?php echo esc_attr($social['snapchat']); ?>" placeholder="https://snapchat.com/add/yourusername" class="regular-text">
                            </td>
                            <td>
                                <label for="intermission_social_pinterest" style="font-weight: 600;">Pinterest:</label><br>
                                <input type="url" name="intermission_social_pinterest" id="intermission_social_pinterest" value="<?php echo esc_attr($social['pinterest']); ?>" placeholder="https://pinterest.com/yourusername" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 0;">
                                <label for="intermission_social_reddit" style="font-weight: 600;">Reddit:</label><br>
                                <input type="url" name="intermission_social_reddit" id="intermission_social_reddit" value="<?php echo esc_attr($social['reddit']); ?>" placeholder="https://reddit.com/user/yourusername" class="regular-text">
                            </td>
                            <td>
                                <label for="intermission_social_github" style="font-weight: 600;">GitHub:</label><br>
                                <input type="url" name="intermission_social_github" id="intermission_social_github" value="<?php echo esc_attr($social['github']); ?>" placeholder="https://github.com/yourusername" class="regular-text">
                            </td>
                        </tr>
            </table>
        </div>

        <div id="advanced" class="intermission-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Preview Key:</th>
                <td>
                    <input type="text" name="intermission_secret_key" value="<?php echo esc_attr($secret_key); ?>" class="regular-text">
                    <p class="description">
                        Use <code>?preview=<?php echo esc_html($secret_key ? $secret_key : 'your-key'); ?></code> to bypass maintenance mode.
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">Whitelist IPs:</th>
                <td>
                    <textarea name="intermission_whitelist_ips" rows="5" class="large-text" placeholder="127.0.0.1&#10;192.168.1.1"><?php echo esc_textarea($whitelist_ips); ?></textarea>
                    <p class="description">One IP address per line. These IPs can access the site during maintenance.</p>
                </td>
            </tr>
        </table>
        </div>

        <p class="submit">
            <?php submit_button('Save', 'primary', 'submit', false); ?>
            <a href="<?php echo esc_url(home_url('/intermission')); ?>" target="_blank" class="button" id="intermission-preview-button">Preview</a>
        </p>
    </form>
</div>
