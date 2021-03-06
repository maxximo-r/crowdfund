<div id="poststuff">
	<div id="post-body">

		<?php
		global $wpdb;

		if ( isset( $_POST['crwdfnd_generate_adv_code'] ) ) {
			$paypal_ipn_url             = CROWDFUND_ME_SITE_HOME_URL . '/?crwdfnd_process_ipn=1';
			$mem_level                  = trim( sanitize_text_field( $_POST['crwdfnd_paypal_adv_member_level'] ) );
			$mem_level                  = absint( $mem_level );
			$query                      = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'crwdfnd_membership_tbl WHERE id !=1 AND id =%d', $mem_level );
			$membership_level_resultset = $wpdb->get_row( $query );
			if ( $membership_level_resultset ) {
				$pp_av_code = 'notify_url=' . $paypal_ipn_url . '<br /> ' . 'custom=subsc_ref=' . $mem_level;
				echo '<div id="message" class="updated fade"><p>';
				echo '<strong>Paste the code below in the "Add advanced variables" field of your PayPal button for membership level ' . $mem_level . '</strong>';
				echo '<br /><br /><code>' . $pp_av_code . '</code>';
				echo '</p></div>';
			} else {
				echo '<div id="message" class="updated fade"><p><strong>';
				CrwdfndUtils::e( 'Error! The membership level ID (' . $mem_level . ') you specified is incorrect. Please check this value again.' );
				echo '</strong></p></div>';
			}
		}

		echo '<div class="crwdfnd-grey-box">';
		echo '<p>';
		CrwdfndUtils::e( 'You can create membership payment buttons from the payments menu of this plugin (useful if you want to offer paid membership on the site).' );
		echo '</p>';
		echo '</div>';
		?>
		<div class="postbox">
			<h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_( 'PayPal Integration Settings' ); ?></label></h3>
			<div class="inside">

				<p><strong><?php echo CrwdfndUtils::_( 'Generate the "Advanced Variables" Code for your PayPal button' ); ?></strong></p>

				<form action="" method="post">
					<?php echo CrwdfndUtils::_( 'Enter the Membership Level ID' ); ?>
					<input type="text" value="" size="4" name="crwdfnd_paypal_adv_member_level">
					<input type="submit" value="<?php echo CrwdfndUtils::_( 'Generate Code' ); ?>" class="button-primary" name="crwdfnd_generate_adv_code">
				</form>

			</div>
		</div>

	</div>
</div>
<form action="options.php" method="POST">
	<input type="hidden" name="tab" value="2" />
	<?php settings_fields( 'crwdfnd-settings-tab-' . $current_tab ); ?>
	<?php do_settings_sections( 'crowdfund_me_settings' ); ?>
	<?php submit_button(); ?>
</form>
