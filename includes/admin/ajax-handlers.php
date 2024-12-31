<?php
    if (!defined('ABSPATH')) exit;

    // Handle Update Team Member
    add_action('wp_ajax_update_team_member', 'rul_update_team_member');
    function rul_update_team_member()
    {
        // Verify Nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rul-teams-nonce')) {
            wp_send_json_error(['message' => __('Nonce verification failed.', 'rul-teams')]);
        }

        // Validate Inputs
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $designation = isset($_POST['designation']) ? sanitize_text_field($_POST['designation']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $member_id = isset($_POST['member_id']) ? sanitize_text_field($_POST['member_id']) : '';

        if ($id === 0 || empty($name) || empty($designation) || empty($email) || empty($member_id)) {
            wp_send_json_error(['message' => __('All fields are required.', 'rul-teams')]);
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Invalid email address.', 'rul-teams')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        // Update Database Record
        $updated = $wpdb->update(
            $table_name,
            [
                'name'        => $name,
                'designation' => $designation,
                'email'       => $email,
                'member_id'   => $member_id,
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => __('Team Member updated successfully.', 'rul-teams')]);
        } else {
            wp_send_json_error(['message' => __('Failed to update Team Member.', 'rul-teams')]);
        }
    }

    // Single Delete Handler 
    add_action('wp_ajax_delete_team_member', 'rul_delete_team_member');
    function rul_delete_team_member()
    {
        // Verify Nonce
        check_ajax_referer('rul-teams-nonce', 'nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id === 0) {
            wp_send_json_error(['message' => __('Invalid Team Member ID.', 'rul-teams')]);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams';

        // Delete the record from the database
        $deleted = $wpdb->delete($table_name, ['id' => $id], ['%d']);

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

