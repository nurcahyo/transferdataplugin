<?php
	
	global $wpdb;	
	$name = $email = $message = '';
	$plan = 'starter';

	$user = wp_get_current_user();
	
	if ($user) {
		$name = $user->display_name;
		$email = $user->user_email;	
	}

	$transfers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}transfers", OBJECT );

	if ($_REQUEST['bm_hidden'] == 'Y') {

		$email = $_REQUEST['bm_email'];
		$plan = $_REQUEST['bm_plan'];

		$user = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}users WHERE user_email = '{$email}'", OBJECT );
		
		$pass = $user->user_pass;

		$url = "http://bizgym.dev/transfer-old-user?current_plan={$plan}&email={$email}&encrypted_password={$pass}";		

		$result = json_decode(file_get_contents($url), true);

		$message = "Error when processing transfer";

		if ($result['success']) {
			$link = $result['data']['link'];
			$title = 'BizGym 2.0 Migration';
			
			ob_start();
			require 'email_template.php';
			$body = ob_get_clean();

			$sent = wp_mail( $email, $title, $body );

			if ($sent) { //if sent
				$rows_affected = $wpdb->insert( $wpdb->prefix . 'transfers', array( 'user_id' => $user->ID, 'email' => $user->user_email, 'transfer_date' => date('Y-m-d H:i:s') ) );

				if ($rows_affected > 0) {
					$message = 'Transfer Success. Please check your email for the next process';
				}	
			}			
		}
	}
?>


<div class="wrap">
	<?php echo "<h2>BizGym 2.0 Transfer</h2>"; ?>

	<?php if ($message != '') { ?>
	<?php echo "<h3>{$message}</h3>"; ?>
	<?php  } ?>

	<form name="transfer" method="GET" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="page" value="BizGym_Transfer">
		<input type="hidden" name="bm_hidden" value="Y">
		<?php echo "<h4>Please check & ensure these data below before you do the transfer</h4>"; ?>
		<p><?php _e("Name: " ); ?><input readonly=readonly type="text" name="bm_name" value="<?php echo $name; ?>" size="20"></p>
		<p><?php _e("Email: " ); ?><input type="text" name="bm_email" value="<?php echo $email; ?>" size="20"></p>
		<p><?php _e("Choose the plan target for migrate: " ); ?>
			<select name="bm_plan">
				<option value='starter'>Starter</option>
				<option value='pro'>Pro</option>
				<option value='enterprise'>Free</option>
			</select>
		<p class="submit">
			<input type="submit" name="Submit" value="Start Transfer!" />
		</p>
	</form>

	<h2>User Transfer List</h2>
	<table class="wp-list-table widefat fixed pages">
		<thead>
			<tr>
				<td>Email</td>
				<td>Transfer Date</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($transfers as $transfer) { ?>
			<tr>
				<td><?php echo $transfer->email ?></td>
				<td><?php echo date('F d, Y - H:i:s', strtotime($transfer->transfer_date)) ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>