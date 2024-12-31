<?php

    if (!defined('ABSPATH')) exit;

    if (!class_exists('WP_List_Table')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    }
    
    // Extends Wp list table 
    class RUL_Team_List_Table extends WP_List_Table
    {
        public function __construct()
        {
            parent::__construct([
                'singular' => esc_html__('Team Member', 'rul-teams'),
                'plural'   => esc_html__('Team Members', 'rul-teams'),
                'ajax'     => true, // Enable Ajax support
            ]);
        }

        // Override the single_row method
        public function single_row($item) {
            // Add a dynamic ID attribute to the <tr> tag
            echo '<tr id="team-member-' . esc_attr($item['id']) . '">';
            $this->single_row_columns($item);
            echo '</tr>';
        }

        // Define table columns
        public function get_columns()
        {
            return [
                'cb'          => '<input type="checkbox" />',
                'name'        => esc_html__('Name', 'rul-teams'),
                'designation' => esc_html__('Designation', 'rul-teams'),
                'member_id'   => esc_html__('ID', 'rul-teams'),
                'email'       => esc_html__('Email', 'rul-teams'),
            ];
        }

        /**=== Define sortable columns ===*/
        public function get_sortable_columns()
        {
            return [
                'name'        => ['name', true],
                'designation' => ['designation', false],
                'member_id'   => ['member_id', false],
            ];
        }

        // Prepare table items
        public function prepare_items()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rul_teams';
            $search = isset($_REQUEST['s']) ? esc_sql($_REQUEST['s']) : '';
            $orderby = isset($_REQUEST['orderby']) ? esc_sql($_REQUEST['orderby']) : 'name'; // Default to "name"
            $order = isset($_REQUEST['order']) ? esc_sql($_REQUEST['order']) : 'desc'; // Default to "DESC"
            // Handle pagination
            $per_page = 10;
            $current_page = $this->get_pagenum();
            $offset = ($current_page - 1) * $per_page;
            $total_items = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE name LIKE %s",
                    '%' . $search . '%'
                )
            );
            // Fetch data
            $this->items = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE name LIKE %s ORDER BY $orderby $order LIMIT %d OFFSET %d",
                    '%' . $search . '%',
                    $per_page,
                    $offset
                ),
                ARRAY_A
            );

            // Set pagination arguments
            $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items / $per_page),
            ]);

            // Set column headers
            $this->_column_headers = [
                $this->get_columns(),
                [],
                $this->get_sortable_columns(),
            ];
        }

        /*=== Render columns ===*/
        public function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'designation':
                case 'member_id':
                case 'email':
                    return esc_html($item[$column_name] ?? '');
                default:
                    return '';
            }
        }

        // Render "Name" column with actions
        public function column_name($item)
        {
            $edit_url = admin_url("admin.php?page=rul-teams-edit&id=" . $item['id']);
            $delete_action = sprintf(
                '<a href="#" class="delete-team-member" data-id="%d">%s</a>',
                intval($item['id']),
                esc_html__('Delete', 'rul-teams')
            );

            // Edit action
            $edit_action = sprintf(
                '<a href="%s">%s</a>',
                esc_url($edit_url),
                esc_html__('Edit', 'rul-teams')
            );

            return sprintf(
                '<strong>%s</strong><br>%s | %s',
                esc_html($item['name']),
                $edit_action,
                $delete_action
            );
        }

        // Render checkbox column
        public function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="id[]" value="%d" />', intval($item['id']));
        }

        // Bulk actions
        public function get_bulk_actions()
        {
            return [
                'delete' => esc_html__('Delete', 'rul-teams'),
            ];
        }

        // Handle bulk actions
        public function process_bulk_action()
        {
            if ('delete' === $this->current_action() && !empty($_POST['id'])) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rul_teams';
                // Sanitize and delete each selected ID
                $ids = array_map('intval', $_POST['id']);
                if (!empty($ids)) {
                    $ids_placeholder = implode(',', array_fill(0, count($ids), '%d'));
                    $query = $wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $ids);
                    $wpdb->query($query);
                    wp_redirect(add_query_arg(['message' => 'bulk_deleted'], admin_url('admin.php?page=rul-teams')));
                    exit;
                }
            }
        }

        //Add a search box
        public function search_box($text, $input_id)
        {
            if (empty($_REQUEST['s']) && !$this->has_items()) {
                return;
            }

            $input_id = $input_id . '-search-input';
            ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($text); ?>:</label>
                <input type="search" id="<?php echo esc_attr($input_id); ?>" name="s" value="<?php echo esc_attr($_REQUEST['s'] ?? ''); ?>" />
                <?php submit_button($text, 'button', false, false, ['id' => 'search-submit']); ?>
            </p>
            <?php
        }
    }
