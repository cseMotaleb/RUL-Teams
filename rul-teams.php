<?php
/**
 * Plugin Name:       RUL Teams
 * Plugin URI:        https://github.com/cseMotaleb/RUL-Teams
 * Description:       A plugin to manage team members with CRUD functionality.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Motaleb Hossain
 * Author URI:        https://csemotaleb.github.io/cv/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rul-teams
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define constants
define('RUL_TEAMS_VERSION', '1.0');
define('RUL_TEAMS_PATH', plugin_dir_path(__FILE__));
define('RUL_TEAMS_URL', plugin_dir_url(__FILE__));

// Required files for the plugin
$required_files = [
    'includes/class-activator.php',
    'includes/class-database.php',
    'includes/admin/class-admin-menu.php',
    'includes/admin/ajax-handlers.php'
];

// Include required files with error handling
foreach ($required_files as $file) {
    $file_path = RUL_TEAMS_PATH . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        // Log error for missing files
        error_log('RUL Teams Plugin Error: Missing required file - ' . $file_path);
    }
}

// Register activation and deactivation hooks
if (class_exists('RUL_Activator')) {
    register_activation_hook(__FILE__, ['RUL_Activator', 'activate']);
    register_deactivation_hook(__FILE__, ['RUL_Activator', 'deactivate']);
} else {
    error_log('RUL Teams Plugin Error: RUL_Activator class is missing. Activation and deactivation hooks will not work.');
}

// Initialize the admin menu
if (is_admin()) {
    if (class_exists('RUL_Admin_Menu')) {
        new RUL_Admin_Menu();
    } else {
        error_log('RUL Teams Plugin Error: RUL_Admin_Menu class is missing. Admin functionality will not load.');
    }
}

// Load localization files
add_action('plugins_loaded', function () {
    load_plugin_textdomain('rul-teams', false, basename(dirname(__FILE__)) . '/languages');
});
