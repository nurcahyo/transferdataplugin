<?php 
    /*
    Plugin Name: BizGym 2.0 Transfer Plugin
    Plugin URI: http://bizgym.com
    Description: Plugin for migrate all user data from Old 
    Author: BizGym @Devs
    Version: 1.0
    Author URI: http://www.bizgym.com
    */

    function migration_actions() {
    	add_options_page("BizGym 2.0 Transfer", "BizGym 2.0 Transfer", 1, "BizGym_Transfer", "migration_admin");
    }

    function migration_admin() {
    	include('bizgym_migration_admin.php');
    }

    // create table
    function migration_create_table(){
        global $wpdb;
        global $charset_collate;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql_create_table = "CREATE TABLE {$wpdb->prefix}transfers (
            id bigint(20) unsigned NOT NULL auto_increment,
            user_id bigint(20) unsigned NOT NULL default '0',
            email varchar(30) NOT NULL default '',
            transfer_date datetime NOT NULL default '0000-00-00 00:00:00',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql_create_table);
    }

    // hooks
    register_activation_hook( __FILE__, 'migration_create_table');
    add_action('admin_menu', 'migration_actions');
?>