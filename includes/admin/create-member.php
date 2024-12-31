<?php
    if (!defined('ABSPATH')) exit;

    // Handle Add Team Member create function not ajax call 
    add_action('admin_post_add_team_member', 'handle_add_team_member');
    function handle_add_team_member()
    {
        // Verify nonce
        if (!isset($_POST['add_team_member_nonce']) || !wp_verify_nonce($_POST['add_team_member_nonce'], 'add_team_member_action')) {
            wp_die(esc_html__('Nonce verification failed.', 'rul-teams'));
        }

        // Validate inputs
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $designation = isset($_POST['designation']) ? sanitize_text_field($_POST['designation']) : '';
        $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        if (empty($name) || empty($designation) || $member_id === 0 || empty($email)) {
            wp_die(esc_html__('All fields are required.', 'rul-teams'));
        }

        if (!is_email($email)) {
            wp_die(esc_html__('Invalid email address.', 'rul-teams'));
        }

        // Insert into database
        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        $inserted = $wpdb->insert(
            $table_name,
            [
                'name'        => $name,
                'designation' => $designation,
                'member_id'   => $member_id,
                'email'       => $email,
            ],
            ['%s', '%s', '%d', '%s']
        );

        if ($inserted) {
            wp_redirect(admin_url('admin.php?page=rul-teams&message=member_added'));
            exit;
        } else {
            wp_die(esc_html__('Failed to add the team member.', 'rul-teams'));
        }
    }