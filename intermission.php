<?php
/**
 * Plugin Name: Intermission
 * Plugin URI: https://github.com/abrayall/intermission
 * Description: Sophisticated maintenance mode with beautiful templates
 * Version: 0.4.0
 * Author: Brayall, LLC
 * Author URI: https://brayall.com
 * License: GPL v2 or later
 * Text Domain: intermission
 */

if (!defined('ABSPATH')) {
    exit;
}

function intermission_get_version() {
    $version_file = plugin_dir_path(__FILE__) . 'version.properties';

    if (!file_exists($version_file)) {
        return '0.1.0';
    }

    $properties = parse_ini_file($version_file);

    if ($properties === false || !isset($properties['major']) || !isset($properties['minor']) || !isset($properties['maintenance'])) {
        return '0.1.0';
    }

    return $properties['major'] . '.' . $properties['minor'] . '.' . $properties['maintenance'];
}

define('INTERMISSION_VERSION', intermission_get_version());
define('INTERMISSION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('INTERMISSION_PLUGIN_URL', plugin_dir_url(__FILE__));

class Intermission {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'register_preview_endpoint'));
        add_action('template_redirect', array($this, 'handle_preview_endpoint'), 0);
        add_action('template_redirect', array($this, 'show_maintenance_page'), 1);
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_bar_menu', array($this, 'add_admin_bar_toggle'), 35);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_toolbar_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_toolbar_assets'));
        add_action('wp_ajax_intermission_toggle', array($this, 'ajax_toggle_maintenance'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_plugin_action_links'));
    }

    public function register_preview_endpoint() {
        add_rewrite_rule('^intermission/?$', 'index.php?intermission_preview=1', 'top');
        add_rewrite_tag('%intermission_preview%', '([^&]+)');
    }

    public function handle_preview_endpoint() {
        if (get_query_var('intermission_preview')) {
            header('X-Robots-Tag: noindex, nofollow', true);
            nocache_headers();
            include INTERMISSION_PLUGIN_DIR . 'templates/maintenance.php';
            exit;
        }
    }

    public function show_maintenance_page() {
        $enable_gmt = get_option('intermission_auto_enable_gmt', 0);
        if ($enable_gmt > 0) {
            $current_gmt = time();
            if ($current_gmt >= $enable_gmt) {
                update_option('intermission_enabled', true);
                delete_option('intermission_auto_enable_gmt');
                delete_option('intermission_auto_enable_date');
                delete_option('intermission_auto_enable_time');
            }
        }

        if (!get_option('intermission_enabled', false)) {
            return;
        }

        $auto_disable = get_option('intermission_auto_disable', false);
        if ($auto_disable) {
            $countdown_gmt = get_option('intermission_countdown_gmt', 0);
            if ($countdown_gmt > 0) {
                $current_gmt = time();
                if ($current_gmt >= $countdown_gmt) {
                    update_option('intermission_enabled', false);
                    delete_option('intermission_countdown_gmt');
                    delete_option('intermission_countdown_date');
                    delete_option('intermission_countdown_time');
                    return;
                }
            }
        }

        if (is_user_logged_in() && current_user_can('administrator')) {
            return;
        }

        $secret_key = get_option('intermission_secret_key', '');
        if (!empty($secret_key) && isset($_GET['preview']) && $_GET['preview'] === $secret_key) {
            return;
        }

        $whitelist_ips = get_option('intermission_whitelist_ips', '');
        if (!empty($whitelist_ips)) {
            $client_ip = $this->get_client_ip();
            $allowed_ips = array_map('trim', explode("\n", $whitelist_ips));
            if (in_array($client_ip, $allowed_ips)) {
                return;
            }
        }

        status_header(503);
        header('Retry-After: 3600');
        nocache_headers();

        include INTERMISSION_PLUGIN_DIR . 'templates/maintenance.php';
        exit;
    }

    private function get_client_ip() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function add_admin_menu() {
        add_options_page(
            'Intermission Settings',
            'Maintenance Mode',
            'manage_options',
            'intermission',
            array($this, 'render_settings_page')
        );
    }

    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=intermission') . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings() {
        register_setting('intermission_settings', 'intermission_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_boolean')
        ));
        register_setting('intermission_settings', 'intermission_headline', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_message', array(
            'type' => 'string',
            'sanitize_callback' => 'wp_kses_post'
        ));
        register_setting('intermission_settings', 'intermission_countdown_date', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_countdown_time', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_countdown_gmt', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint'
        ));
        register_setting('intermission_settings', 'intermission_auto_enable_date', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_auto_enable_time', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_auto_enable_gmt', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint'
        ));
        register_setting('intermission_settings', 'intermission_auto_disable', array(
            'type' => 'boolean',
            'sanitize_callback' => array($this, 'sanitize_boolean')
        ));
        register_setting('intermission_settings', 'intermission_theme', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_secret_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_whitelist_ips', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field'
        ));
        register_setting('intermission_settings', 'intermission_icon_type', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('intermission_settings', 'intermission_custom_icon_url', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_facebook', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_instagram', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_x', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_tiktok', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_youtube', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_linkedin', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_snapchat', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_pinterest', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_reddit', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('intermission_settings', 'intermission_social_github', array(
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw'
        ));
    }

    public function sanitize_boolean($value) {
        return !empty($value) ? true : false;
    }

    public function get_available_themes() {
        $themes = array();
        $themes_dir = INTERMISSION_PLUGIN_DIR . 'themes/';

        if (!is_dir($themes_dir)) {
            return $themes;
        }

        $theme_files = glob($themes_dir . '*.css');

        foreach ($theme_files as $theme_file) {
            $theme_data = $this->get_theme_data($theme_file);
            if ($theme_data) {
                $themes[basename($theme_file, '.css')] = $theme_data;
            }
        }

        uasort($themes, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        if (isset($themes['default'])) {
            $default_theme = $themes['default'];
            unset($themes['default']);
            $themes = array('default' => $default_theme) + $themes;
        }

        return $themes;
    }

    private function get_theme_data($theme_file) {
        $theme_data = array();
        $file_contents = file_get_contents($theme_file);

        if (preg_match('/Theme Name:\s*(.+)/i', $file_contents, $match)) {
            $theme_data['name'] = trim($match[1]);
        }
        if (preg_match('/Description:\s*(.+)/i', $file_contents, $match)) {
            $theme_data['description'] = trim($match[1]);
        }
        if (preg_match('/Version:\s*(.+)/i', $file_contents, $match)) {
            $theme_data['version'] = trim($match[1]);
        }
        if (preg_match('/Author:\s*(.+)/i', $file_contents, $match)) {
            $theme_data['author'] = trim($match[1]);
        }

        return !empty($theme_data['name']) ? $theme_data : null;
    }

    public function get_icon_html() {
        $icon_type = get_option('intermission_icon_type', 'wrench');
        $custom_icon_url = get_option('intermission_custom_icon_url', '');

        if ($icon_type === 'custom' && !empty($custom_icon_url)) {
            return '<img src="' . esc_url($custom_icon_url) . '" alt="Icon" style="width: 100%; height: 100%; object-fit: contain;">';
        }

        $icons = array(
            'wrench' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            'gear' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m11-11h-6m-6 0H1m15.4-3.6l-4.2 4.2m-4.4 0L3.6 3.6m0 16.8l4.2-4.2m4.4 0l4.2 4.2"/></svg>',
            'tools' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/><path d="M8 17l5 5M17 8L8 17"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'rocket' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09zM12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
            'code' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
            'shield' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>'
        );

        return isset($icons[$icon_type]) ? $icons[$icon_type] : $icons['wrench'];
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_intermission') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('intermission-admin', INTERMISSION_PLUGIN_URL . 'assets/css/admin.css', array(), INTERMISSION_VERSION);
        wp_enqueue_script('intermission-admin', INTERMISSION_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), INTERMISSION_VERSION, true);
        wp_localize_script('intermission-admin', 'context', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('intermission_toggle_nonce')
        ));
    }

    public function render_settings_page() {
        include INTERMISSION_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    public function add_admin_bar_toggle($wp_admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }

        $enabled = get_option('intermission_enabled', false);
        $status_class = $enabled ? 'maintenance' : 'live';
        $status_text = $enabled ? 'Maintenance' : 'Live';

        $wp_admin_bar->add_node(array(
            'id' => 'intermission-toggle',
            'title' => '<span class="intermission-status-dot ' . esc_attr($status_class) . '"></span><span class="intermission-label">' . esc_html($status_text) . '</span>',
            'href' => '#',
            'meta' => array(
                'class' => 'intermission-toolbar-item',
                'onclick' => 'return false;'
            )
        ));
    }

    public function enqueue_toolbar_assets() {
        if (!is_admin_bar_showing() || !current_user_can('manage_options')) {
            return;
        }

        wp_enqueue_style('intermission-toolbar', INTERMISSION_PLUGIN_URL . 'assets/css/toolbar.css', array(), INTERMISSION_VERSION, 'all');
        wp_enqueue_script('intermission-toolbar', INTERMISSION_PLUGIN_URL . 'assets/js/toolbar.js', array('jquery'), INTERMISSION_VERSION, true);
        wp_localize_script('intermission-toolbar', 'intermissionToolbar', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('intermission_toggle_nonce')
        ));

        $inline_css = "
            #wp-admin-bar-intermission-toggle .ab-item { display: flex !important; align-items: center !important; gap: 8px !important; padding: 0 12px !important; cursor: pointer !important; }
            #wp-admin-bar-intermission-toggle .intermission-status-dot { display: inline-block !important; width: 10px !important; height: 10px !important; border-radius: 50% !important; transition: all 0.3s ease !important; flex-shrink: 0 !important; }
            #wp-admin-bar-intermission-toggle .intermission-status-dot.live { background-color: #46b450 !important; box-shadow: 0 0 8px rgba(70, 180, 80, 0.6) !important; }
            #wp-admin-bar-intermission-toggle .intermission-status-dot.maintenance { background-color: #ffb900 !important; box-shadow: 0 0 8px rgba(255, 185, 0, 0.6) !important; }
            #wp-admin-bar-intermission-toggle .intermission-label { font-size: 13px !important; color: #eee !important; white-space: nowrap !important; }
            #wp-admin-bar-intermission-toggle:hover .ab-item { background-color: rgba(255, 255, 255, 0.05) !important; }
        ";
        wp_add_inline_style('intermission-toolbar', $inline_css);
    }

    public function ajax_toggle_maintenance() {
        check_ajax_referer('intermission_toggle_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $new_value = isset($_POST['enabled']) ? ($_POST['enabled'] == '1') : !get_option('intermission_enabled', false);
        update_option('intermission_enabled', $new_value);

        wp_send_json_success(array(
            'enabled' => $new_value,
            'status_text' => $new_value ? 'Maintenance' : 'Live'
        ));
    }
}

function intermission_activation() {
    Intermission::get_instance()->register_preview_endpoint();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'intermission_activation');

Intermission::get_instance();
