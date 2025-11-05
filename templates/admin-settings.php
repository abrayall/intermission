<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['intermission_settings_nonce']) && wp_verify_nonce($_POST['intermission_settings_nonce'], 'intermission_settings')) {
    update_option('intermission_enabled', isset($_POST['intermission_enabled']));
    update_option('intermission_headline', sanitize_text_field($_POST['intermission_headline']));
    update_option('intermission_message', wp_kses_post($_POST['intermission_message']));
    update_option('intermission_countdown_date', sanitize_text_field($_POST['intermission_countdown_date']));
    update_option('intermission_countdown_time', sanitize_text_field($_POST['intermission_countdown_time']));
    update_option('intermission_theme', sanitize_text_field($_POST['intermission_theme']));
    update_option('intermission_secret_key', sanitize_text_field($_POST['intermission_secret_key']));
    update_option('intermission_whitelist_ips', sanitize_textarea_field($_POST['intermission_whitelist_ips']));

    echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
}

$enabled = get_option('intermission_enabled', false);
$headline = get_option('intermission_headline', 'Under Maintenance');
$message = get_option('intermission_message', 'We are currently performing scheduled maintenance. We will be back shortly!');
$countdown_date = get_option('intermission_countdown_date', '');
$countdown_time = get_option('intermission_countdown_time', '');
$selected_theme = get_option('intermission_theme', 'default');
$secret_key = get_option('intermission_secret_key', '');
$whitelist_ips = get_option('intermission_whitelist_ips', '');

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

        <table class="form-table">
            <tr>
                <th scope="row">Mode:</th>
                <td>
                    <div class="intermission-mode-toggle">
                        <input type="checkbox" name="intermission_enabled" id="intermission_enabled" value="1" <?php checked($enabled); ?>>
                        <label for="intermission_enabled" class="intermission-toggle-switch <?php echo $enabled ? 'maintenance' : 'live'; ?>">
                            <span class="intermission-toggle-slider"></span>
                        </label>
                        <span class="intermission-mode-label"><?php echo $enabled ? 'Maintenance' : 'Live'; ?></span>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row">Theme</th>
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
                <th scope="row">Headline</th>
                <td>
                    <input type="text" name="intermission_headline" value="<?php echo esc_attr($headline); ?>" class="regular-text">
                    <p class="description">Main heading displayed on the maintenance page.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Message</th>
                <td>
                    <textarea name="intermission_message" rows="5" class="large-text"><?php echo esc_textarea($message); ?></textarea>
                    <p class="description">Message displayed below the headline.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Countdown Timer</th>
                <td>
                    <input type="date" name="intermission_countdown_date" value="<?php echo esc_attr($countdown_date); ?>">
                    <input type="time" name="intermission_countdown_time" value="<?php echo esc_attr($countdown_time); ?>">
                    <p class="description">Optional countdown timer. Leave empty to hide.</p>
                </td>
            </tr>

            <tr>
                <th scope="row">Preview Secret Key</th>
                <td>
                    <input type="text" name="intermission_secret_key" value="<?php echo esc_attr($secret_key); ?>" class="regular-text">
                    <p class="description">
                        Use <code>?preview=<?php echo esc_html($secret_key ? $secret_key : 'your-key'); ?></code> to bypass maintenance mode.
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">Whitelist IP Addresses</th>
                <td>
                    <textarea name="intermission_whitelist_ips" rows="5" class="large-text" placeholder="127.0.0.1&#10;192.168.1.1"><?php echo esc_textarea($whitelist_ips); ?></textarea>
                    <p class="description">One IP address per line. These IPs can access the site during maintenance.</p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php submit_button('Save', 'primary', 'submit', false); ?>
            <a href="<?php echo esc_url(home_url('/intermission')); ?>" target="_blank" class="button" id="intermission-preview-button">Preview</a>
        </p>
    </form>
</div>
