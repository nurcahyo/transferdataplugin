<?php

function register_client_scripts() {
	$source =  plugins_url().DIRECTORY_SEPARATOR.'transfer/transfer.js';

	if (is_user_logged_in()) wp_enqueue_script('transfer_scripts', $source, array(), '1.0', true);
}

add_action( 'wp_enqueue_scripts', 'register_client_scripts' );
