<?php
    if (!defined('ABSPATH')) exit;

    global $wpdb;
    $table_name = $wpdb->prefix . 'rul_teams';

    // Validate and sanitize the `id` parameter
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        wp_die(esc_html__('Invalid Team Member ID.', 'rul-teams'));
    }

    $id = intval($_GET['id']);

    // Fetch member data safely
    $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);

    // Ensure `$member` fields are not `null`
    $member = array_map(function ($value) {
        return $value ?? ''; // Default to an empty string if null
    }, (array) $member);

    if (empty($member)) {
        wp_die(esc_html__('Team Member not found.', 'rul-teams'));
    }
?>

<div class="wrap">
    <h1><?php esc_html_e('Edit Team Member', 'rul-teams'); ?></h1>
   <!-- Display Success Message -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'member_updated') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Team Member updated successfully.', 'rul-teams'); ?></p>
        </div>
    <?php endif; ?>
    <form id="edit-team-member-form">
        <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>">
        <?php wp_nonce_field('edit_team_member_action', 'edit_team_member_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="name"><?php esc_html_e('Name', 'rul-teams'); ?></label></th>
                <td><input name="name" type="text" id="name" value="<?php echo esc_attr($member['name']); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="designation"><?php esc_html_e('Designation', 'rul-teams'); ?></label></th>
                <td><input name="designation" type="text" id="designation" value="<?php echo esc_attr($member['designation']); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="member_id"><?php esc_html_e('Member ID', 'rul-teams'); ?></label></th>
                <td><input name="member_id" type="number" id="member_id" value="<?php echo esc_attr($member['member_id']); ?>" class="regular-text" min="0" step="1" required></td>
            </tr>
            <tr>
                <th><label for="email"><?php esc_html_e('Email', 'rul-teams'); ?></label></th>
                <td><input name="email" type="email" id="email" value="<?php echo esc_attr($member['email']); ?>" class="regular-text" required></td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="update-team-member" class="button button-primary"><?php esc_html_e('Update Member', 'rul-teams'); ?></button>
        </p>
    </form>
</div>



