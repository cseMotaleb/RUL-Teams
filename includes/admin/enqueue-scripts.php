<?php
    // Enqueue Scripts 
    add_action('admin_enqueue_scripts', 'rul_enqueue_scripts');
    function rul_enqueue_scripts($hook)
    {
        // Ensure the script is only loaded on the relevant admin pages
        if (strpos($hook, 'rul-teams') === false) return;
        
        // Ajax Scripts for update and delete
        wp_enqueue_script(
            'rul-teams-ajax',
            RUL_TEAMS_URL . 'assets/js/ajax-script.js',
            ['jquery'],
            RUL_TEAMS_VERSION,
            true
        );

        // Ajax Loalize Script
        wp_localize_script('rul-teams-ajax', 'rulTeams', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('rul-teams-nonce'),
        ]);
    }
