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
        add_menu_page(
            __('Team Members', 'rul-teams'),
            __('RUL Teams', 'rul-teams'),
            'manage_options',
            'rul-teams',
            [$this, 'team_list_page'],
            'dashicons-groups'
        );

        add_submenu_page(
            'rul-teams',
            __('Add Team Member', 'rul-teams'),
            __('Add New', 'rul-teams'),
            'manage_options',
            'rul-teams-add',
            [$this, 'add_team_member_page']
        );

        add_submenu_page(
            null, // Hidden from menu
            __('Edit Team Member', 'rul-teams'),
            __('Edit Member', 'rul-teams'),
            'manage_options',
            'rul-teams-edit',
            [$this, 'edit_team_member_page']
        );
    }

    // Enqueue scripts
    public function enqueue_assets($hook)
    {
        if (strpos($hook, 'rul-teams') !== false) {
            wp_enqueue_style('rul-teams-css', RUL_TEAMS_URL . 'assets/css/styles.css', [], RUL_TEAMS_VERSION);
            wp_enqueue_script('rul-teams-js', RUL_TEAMS_URL . 'assets/js/ajax.js', ['jquery'], RUL_TEAMS_VERSION, true);
            wp_localize_script('rul-teams-js', 'rulTeams', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('rul-teams-nonce'),
            ]);
        }
    }

    // CRUD admin page 
    public function team_list_page()
    {
        include_once RUL_TEAMS_PATH . 'views/team-list.php';
    }

    public function add_team_member_page()
    {
        include_once RUL_TEAMS_PATH . 'views/add-team-member.php';
    }

    public function edit_team_member_page()
    {
        include_once RUL_TEAMS_PATH . 'views/edit-team-member.php';
    }
}
