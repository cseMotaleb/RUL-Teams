<?php

if (!defined('ABSPATH')) exit;

class RUL_Activator
{
    public static function activate()
    {
        $db = new RUL_Database();
        $db->create_table();
    }

    public static function deactivate()
    {
        // Optional: Add deactivation logic if needed
    }
}
