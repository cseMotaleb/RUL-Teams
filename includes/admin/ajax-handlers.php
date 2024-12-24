<?php

if (!defined('ABSPATH')) exit;

// Handle Add Team Member
add_action('admin_post_add_team_member', 'handle_add_team_member');

function handle_add_team_member()
{
    // Verify nonce
    if (!isset($_POST['add_team_member_nonce']) || !wp_verify_nonce($_POST['add_team_member_nonce'], 'add_team_member_action')) {
        wp_die(__('Nonce verification failed.', 'rul-teams'));
    }

    // Validate inputs
    if (empty($_POST['name']) || empty($_POST['designation']) || empty($_POST['member_id']) || empty($_POST['email'])) {
        wp_die(__('All fields are required.', 'rul-teams'));
    }

    // Sanitize inputs
    $name = sanitize_text_field($_POST['name']);
    $designation = sanitize_text_field($_POST['designation']);
    $member_id = intval($_POST['member_id']); // Ensure member_id is an integer
    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        wp_die(__('Invalid email address.', 'rul-teams'));
    }

    // Insert data into the database
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
        wp_die(__('Failed to add the team member.', 'rul-teams'));
    }
}

// Handle Update Team Member
add_action('wp_ajax_update_team_member', 'handle_update_team_member');

function handle_update_team_member()
{
    // Verify nonce
    if (!isset($_POST['edit_team_member_nonce']) || !wp_verify_nonce($_POST['edit_team_member_nonce'], 'edit_team_member_action')) {
        wp_send_json_error(['message' => __('Nonce verification failed.', 'rul-teams')]);
    }

    // Validate required fields
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['designation']) || empty($_POST['member_id']) || empty($_POST['email'])) {
        wp_send_json_error(['message' => __('All fields are required.', 'rul-teams')]);
    }

    // Sanitize inputs
    $id = intval($_POST['id']);
    $name = sanitize_text_field($_POST['name']);
    $designation = sanitize_text_field($_POST['designation']);
    $member_id = intval($_POST['member_id']); // Ensure member_id is a number
    $email = sanitize_email($_POST['email']);

    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(['message' => __('Invalid email address.', 'rul-teams')]);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'rul_teams';

    // Update the team member in the database
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
        wp_send_json_success(['message' => __('Team Member updated successfully.', 'rul-teams')]);
    } else {
        wp_send_json_error(['message' => __('Failed to update Team Member.', 'rul-teams')]);
    }
}


// Handle Delete Team Member 
add_action('wp_ajax_delete_team_member', 'handle_delete_team_member');
add_action('wp_ajax_bulk_delete_team_members', 'handle_bulk_delete_team_members');

function handle_delete_team_member()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rul-teams-nonce')) {
        wp_send_json_error(['message' => __('Nonce verification failed.', 'rul-teams')]);
    }

    if (!isset($_POST['id'])) {
        wp_send_json_error(['message' => __('No ID provided.', 'rul-teams')]);
    }

    $id = intval($_POST['id']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'rul_teams';

    $deleted = $wpdb->delete($table_name, ['id' => $id]);

    if ($deleted) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => __('Failed to delete team member.', 'rul-teams')]);
    }
}

function handle_bulk_delete_team_members()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rul-teams-nonce')) {
        wp_send_json_error(['message' => __('Nonce verification failed.', 'rul-teams')]);
    }

    if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
        wp_send_json_error(['message' => __('No IDs provided.', 'rul-teams')]);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'rul_teams';

    foreach ($_POST['ids'] as $id) {
        $wpdb->delete($table_name, ['id' => intval($id)]);
    }

    wp_send_json_success();
}
