<?php

function register_client_scripts() {
	global $wpdb;
	global $current_user;
	get_currentuserinfo();

	$source =  plugins_url().DIRECTORY_SEPARATOR.'transfer/transfer.js';

	$transfer = get_transfer_by_field('email', $current_user->user_email);
	if (is_user_logged_in() && empty($transfer)) wp_enqueue_script('transfer_scripts', $source, array(), '1.0', true);
}

add_action( 'wp_enqueue_scripts', 'register_client_scripts' );
