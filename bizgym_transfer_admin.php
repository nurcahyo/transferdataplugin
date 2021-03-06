<?php

	require_once 'transfer_helper.php';

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
		$title = 'BizGym 2.0 Transfer';

		$user = get_user_by_field('user_email', $email);
		$pass = $user->user_pass;

		// choose template
		switch ($_REQUEST['bm_template']) {
			case 'click':
				$template = 'click_email_template.php';
				break;
			case 'cron':
				$template = 'cron_email_template.php';
				break;
			}

		$transfer = get_transfer_by_field('email', $email);
		$message = transfer_user($transfer, $email, $plan, $title, $user, $pass, $template);
		
	} else if ($_REQUEST['resend']) {
		$template = 'click_email_template.php';
		$transfer = get_transfer_by_field('id', $_REQUEST['resend_id']);
		$user = get_user_by_field('ID', $transfer->user_id);
		$email = $transfer->email;
		$title = 'BizGym 2.0 Transfer';
		$link = $transfer->reference_link;
		$sent = send_email($email, $title, $link, $user->display_name, $template);

		if ($sent) {
			update_status_transfers($transfer->id, 'sent');
			$message = 'Resend Success';
		}
	}
?>

<div class="wrap">
	<?php echo "<h2>BizGym 2.0 Transfer</h2>"; ?>
	<?php if ($message != '') { ?>
	<?php echo "<h3>{$message}</h3>"; ?>
	<?php  } ?>

	<form name="transfer" method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="page" value="BizGym_Transfer">
		<input type="hidden" name="bm_hidden" value="Y">
		<?php echo "<h4>Please check & ensure these data below before you do the transfer</h4>"; ?>
		<p><?php _e("Name: " ); ?><input readonly=readonly type="text" name="bm_name" value="<?php echo $name; ?>" size="20"></p>
		<p><?php _e("Email: " ); ?><input type="text" name="bm_email" value="<?php echo $email; ?>" size="20"></p>
		<p><?php _e("Choose the plan target for transfer: " ); ?>
			<select name="bm_plan">
				<option value='starter'>Starter</option>
				<option value='pro'>Pro</option>
				<option value='enterprise'>Free</option>
			</select>
		</p>
		<p><?php _e("Choose email template: " ); ?>
			<select name="bm_template">
				<option value='click'>Click Scenario</option>
				<option value='cron'>Cron Scenario</option>
			</select>
		</p>
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
				<td>Action</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($transfers as $transfer) { ?>
			<tr>
				<td><?php echo $transfer->email ?></td>
				<td><?php echo date('F d, Y - H:i:s', strtotime($transfer->transfer_date)) ?></td>
				<td><a href="?&page=BizGym_Transfer&resend=true&resend_id=<?php echo $transfer->id ?>">Resend</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>