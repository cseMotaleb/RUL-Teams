<?php

if (!defined('ABSPATH')) exit;

class RUL_Admin_Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

      public function register_menus()
    {
        // Add main menu
        add_menu_page(
            __('Team Members', 'rul-teams'),
            __('RUL Teams', 'rul-teams'),
            'manage_options',
            'rul-teams',
            [$this, 'team_list_page'], // Callback function for listing team members
            'dashicons-groups'
        );

        // Add submenu for "Add Team Member"
        add_submenu_page(
            'rul-teams', // Parent slug
            __('Add Team Member', 'rul-teams'),
            __('Add New', 'rul-teams'),
            'manage_options',
            'rul-teams-add', // Menu slug
            [$this, 'add_team_member_page'] // Callback function for "Add Team Member" page
        );

        // Add hidden submenu for "Edit Team Member"
        add_submenu_page(
            'rul-teams', // Parent slug (use 'rul-teams' instead of null)
            __('Edit Team Member', 'rul-teams'),
            '', // Empty title to hide it in the UI
            'manage_options',
            'rul-teams-edit', // Menu slug
            [$this, 'edit_team_member_page'] // Callback function for "Edit Team Member" page
        );
    }

    // Enqueue Scripts
    public function enqueue_assets($hook)
    {
        // Ensure $hook is a valid string before using strpos()
        if (!is_string($hook) || strpos($hook, 'rul-teams') === false) {
            return;
        }
        wp_enqueue_script(
            'rul-teams-js',
            RUL_TEAMS_URL . 'assets/js/ajax.js',
            ['jquery'],
            RUL_TEAMS_VERSION,
            true
        );

        // Localize script with nonce and Ajax URL
        wp_localize_script('rul-teams-js', 'rulTeams', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('rul-teams-nonce'),
        ]);
    }

    public function team_list_page()
    {
        $file_path = RUL_TEAMS_PATH . 'views/team-list.php';
        if (file_exists($file_path)) {
            include_once $file_path;
        } else {
            wp_die(esc_html__('The requested page could not be found.', 'rul-teams'));
        }
    }

    public function add_team_member_page()
    {
        $file_path = RUL_TEAMS_PATH . 'views/add-team-member.php';
        if (file_exists($file_path)) {
            include_once $file_path;
        } else {
            wp_die(esc_html__('The requested page could not be found.', 'rul-teams'));
        }
    }

    public function edit_team_member_page()
    {
        $file_path = RUL_TEAMS_PATH . 'views/edit-team-member.php';
        if (file_exists($file_path)) {
            include_once $file_path;
        } else {
            wp_die(esc_html__('The requested page could not be found.', 'rul-teams'));
        }
    }
}
