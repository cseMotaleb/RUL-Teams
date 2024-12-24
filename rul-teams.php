<?php
/**
 * Plugin Name:       RUL Teams
 * Plugin URI:        https://rul-teams.com
 * Description:       A plugin to manage team members with CRUD functionality.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * @package           Rul_Teams
 * Author:            Motaleb Hossain
 * Author URI:        https://csemotaleb.github.io/cv//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rul-teams
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit;

define('RUL_TEAMS_VERSION', '1.0');
define('RUL_TEAMS_PATH', plugin_dir_path(__FILE__));
define('RUL_TEAMS_URL', plugin_dir_url(__FILE__));

// Include core files
require_once RUL_TEAMS_PATH . 'includes/class-activator.php';
require_once RUL_TEAMS_PATH . 'includes/class-database.php';
require_once RUL_TEAMS_PATH . 'includes/admin/class-admin-menu.php';
require_once RUL_TEAMS_PATH . 'includes/admin/ajax-handlers.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, ['RUL_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['RUL_Activator', 'deactivate']);

// Initialize the admin menu
if (is_admin()) {
    new RUL_Admin_Menu();
}
