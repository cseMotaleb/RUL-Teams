<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php _e('Add Team Member', 'rul-teams'); ?></h1>

    <!-- Display Success Message -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'member_added') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Team Member added successfully.', 'rul-teams'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="add_team_member">
        <?php wp_nonce_field('add_team_member_action', 'add_team_member_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="name"><?php _e('Name', 'rul-teams'); ?></label></th>
                <td><input name="name" type="text" id="name" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="designation"><?php _e('Designation', 'rul-teams'); ?></label></th>
                <td><input name="designation" type="text" id="designation" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="member_id"><?php _e('ID', 'rul-teams'); ?></label></th>
                <td>
                    <input name="member_id" type="number" id="member_id" class="regular-text" 
                           min="0" step="1" title="<?php _e('Member ID must be a number.', 'rul-teams'); ?>" 
                           required>
                </td>
            </tr>
            <tr>
                <th><label for="email"><?php _e('Email', 'rul-teams'); ?></label></th>
                <td><input name="email" type="email" id="email" class="regular-text" required></td>
            </tr>
        </table>

        <?php submit_button(__('Add Team Member', 'rul-teams')); ?>
    </form>
</div>
