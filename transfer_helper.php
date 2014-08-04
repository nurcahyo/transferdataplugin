<?php

function transfer_user($transfer, $email, $plan, $title, $user, $pass, $template) {
		global $wpdb;
		if($transfer) { // resend
			$link = $transfer->reference_link;
			send_email($email, $title, $link, $user->display_name, $template);
			$message = 'Resend Success';
		} else {
			$params = http_build_query(array(
				'current_plan' => $plan,
				'email' => $email,
				'encrypted_password' => $pass
			));

			$url = TRANSFER_ENDPOINT . '?' . $params;
			$result = json_decode(file_get_contents($url), true);
			$message = "Error when processing transfer";

			if ($result['success']) {
				$link = $result['data']['link'];

				$rows_affected = $wpdb->insert( $wpdb->prefix . 'transfers', array( 
						'user_id' => $user->ID, 
						'email' => $user->user_email, 
						'reference_link' => $link,
						'status' => 'failed',
						'transfer_date' => date('Y-m-d H:i:s') ) );

				$sent = send_email($email, $title, $link, $user->display_name, $template);
				if ($sent) { //if sent
					// update failed to success
					$rows_affected = update_status_transfers($wpdb->insert_id, 'sent');
					if ($rows_affected > 0) {
						$message = 'Transfer Success. Please check your email for the next process';
					}	
				}
			}
		}

		return $message;
	}

	function send_email($email, $title, $link, $name, $template) {
		$link = $link;
		$name = $name;

		ob_start();
		require $template;
		$body = ob_get_clean();
		$sent = wp_mail( $email, $title, $body, 'Content-Type: text/html; charset=UTF-8' );

		return $sent;
	}

	function update_status_transfers($id, $status) {
		global $wpdb;
		$rows_affected = $wpdb->update( $wpdb->prefix . 'transfers', 
			array(
				'status' => $status,
				'transfer_date' => date('Y-m-d H:i:s')
				), 
			array('id' => $id )
		);
		return $rows_affected;
	}

	function get_transfer_by_field($field = 'id', $value = '') {
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}transfers WHERE {$field} = '{$value}'", OBJECT);
		return $row;
	}

	function get_user_by_field($field = 'id', $value = '') {
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}users WHERE {$field} = '{$value}'", OBJECT);
		return $row;	
	}