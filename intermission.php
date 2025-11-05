<?php
/**
 * Plugin Name: Intermission
 * Plugin URI: https://github.com/abrayall/intermission
 * Description: Sophisticated maintenance mode with beautiful templates
 * Version: 0.3.1
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
        add_action('template_redirect', array($this, 'show_maintenance_page'), 1);
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_bar_menu', array($this, 'add_admin_bar_toggle'), 35);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_toolbar_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_toolbar_assets'));
        add_action('wp_ajax_intermission_toggle', array($this, 'ajax_toggle_maintenance'));
    }

    public function show_maintenance_page() {
        if (!get_option('intermission_enabled', false)) {
            return;
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
            'Intermission',
            'manage_options',
            'intermission',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('intermission_settings', 'intermission_enabled');
        register_setting('intermission_settings', 'intermission_headline');
        register_setting('intermission_settings', 'intermission_message');
        register_setting('intermission_settings', 'intermission_countdown_date');
        register_setting('intermission_settings', 'intermission_countdown_time');
        register_setting('intermission_settings', 'intermission_theme');
        register_setting('intermission_settings', 'intermission_secret_key');
        register_setting('intermission_settings', 'intermission_whitelist_ips');
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

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_intermission') {
            return;
        }
        wp_enqueue_style('intermission-admin', INTERMISSION_PLUGIN_URL . 'assets/css/admin.css', array(), INTERMISSION_VERSION);
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

        $current = get_option('intermission_enabled', false);
        $new_value = !$current;
        update_option('intermission_enabled', $new_value);

        wp_send_json_success(array(
            'enabled' => $new_value,
            'status_text' => $new_value ? 'Maintenance' : 'Live'
        ));
    }
}

Intermission::get_instance();
