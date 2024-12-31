<?php

    if (!defined('ABSPATH')) exit;

    class RUL_Admin_Menu
    {
        public function __construct()
        {
            add_action('admin_menu', [$this, 'register_menus']);
        }

        public function register_menus()
        {
            // Add main menu
            add_menu_page(
                __('Team Members', 'rul-teams'),
                __('RUL Teams', 'rul-teams'),
                'manage_options',
                'rul-teams',
                [$this, 'team_list_page'],
                'dashicons-groups'
            );

            // Add submenu for "Add Team Member"
            add_submenu_page(
                'rul-teams', 
                __('Add Team Member', 'rul-teams'),
                __('Add New', 'rul-teams'),
                'manage_options',
                'rul-teams-add', 
                [$this, 'add_team_member_page']
            );

            // Add hidden submenu for "Edit Team Member"
            add_submenu_page(
                'rul-teams', 
                __('Edit Team Member', 'rul-teams'),
                '', // Empty title to hide it in the UI
                'manage_options',
                'rul-teams-edit', 
                [$this, 'edit_team_member_page'] 
            );
        }

        // Teams list file path
        public function team_list_page()
        {
            $file_path = RUL_TEAMS_PATH . 'views/team-list.php';
            if (file_exists($file_path)) {
                include_once $file_path;
            } else {
                wp_die(esc_html__('The requested page could not be found.', 'rul-teams'));
            }
        }

        // Add teams member file path
        public function add_team_member_page()
        {
            $file_path = RUL_TEAMS_PATH . 'views/add-team-member.php';
            if (file_exists($file_path)) {
                include_once $file_path;
            } else {
                wp_die(esc_html__('The requested page could not be found.', 'rul-teams'));
            }
        }

        // Update teams member file path
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
