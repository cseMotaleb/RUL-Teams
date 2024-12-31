<?php
    if (!defined('ABSPATH')) exit;

    require_once RUL_TEAMS_PATH . 'includes/admin/class-team-list-table.php';

    $list_table = new RUL_Team_List_Table();
    $list_table->prepare_items();
?>

<div class="wrap">
    <h1><?php _e('Team Members', 'rul-teams'); ?></h1>
    <a href="admin.php?page=rul-teams-add" class="button button-primary"><?php _e('Add New', 'rul-teams'); ?></a>
    <!-- Display Success Message -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'member_added') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Team Member added successfully.', 'rul-teams'); ?></p>
        </div>
    <?php endif; ?>
    <form method="get">
        <input type="hidden" name="page" value="rul-teams">
        <?php $list_table->search_box(__('Search Members', 'rul-teams'), 'team-member'); ?>
        <?php $list_table->display(); ?>
    </form>
</div>
