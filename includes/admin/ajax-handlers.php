<?php

    if (!defined('ABSPATH')) exit;

    // Handle Add Team Member
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

    // Handle Update Team Member
    add_action('wp_ajax_update_team_member', 'handle_update_team_member');

    function handle_update_team_member()
    {
        // Verify nonce
        if (!isset($_POST['edit_team_member_nonce']) || !wp_verify_nonce($_POST['edit_team_member_nonce'], 'edit_team_member_action')) {
            wp_send_json_error(['message' => esc_html__('Nonce verification failed.', 'rul-teams')]);
        }

        // Validate required fields
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $designation = isset($_POST['designation']) ? sanitize_text_field($_POST['designation']) : '';
        $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        if ($id === 0 || empty($name) || empty($designation) || $member_id === 0 || empty($email)) {
            wp_send_json_error(['message' => esc_html__('All fields are required.', 'rul-teams')]);
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => esc_html__('Invalid email address.', 'rul-teams')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        $updated = $wpdb->update(
            $table_name,
            [
                'name'        => $name,
                'designation' => $designation,
                'member_id'   => $member_id,
                'email'       => $email,
            ],
            ['id' => $id],
            ['%s', '%s', '%d', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => esc_html__('Team Member updated successfully.', 'rul-teams')]);
        } else {
            wp_send_json_error(['message' => esc_html__('Failed to update Team Member.', 'rul-teams')]);
        }
    }

    // Single Delete Handler
    add_action('wp_ajax_delete_team_member', 'rul_delete_team_member');
    function rul_delete_team_member()
    {
        check_ajax_referer('rul-teams-nonce', 'nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id === 0) {
            wp_send_json_error(['message' => __('Invalid Team Member ID.', 'rul-teams')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        $deleted = $wpdb->delete($table_name, ['id' => $id]);

        if ($deleted) {
            wp_send_json_success(['message' => __('Team Member deleted successfully.', 'rul-teams')]);
        } else {
            wp_send_json_error(['message' => __('Failed to delete Team Member.', 'rul-teams')]);
        }
    }

    // Bulk Delete Handler
    add_action('wp_ajax_bulk_delete_team_members', 'rul_bulk_delete_team_members');

    function rul_bulk_delete_team_members()
    {
        check_ajax_referer('rul-teams-nonce', 'nonce'); // Verify nonce for security

        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];

        if (empty($ids)) {
            wp_send_json_error(['message' => __('No valid IDs provided.', 'rul-teams')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        // Delete all selected IDs in a single query
        $ids_placeholder = implode(',', array_fill(0, count($ids), '%d'));
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $ids));

        wp_send_json_success(['message' => __('Selected Team Members deleted successfully.', 'rul-teams')]);
    }

