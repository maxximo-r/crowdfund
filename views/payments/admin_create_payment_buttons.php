<?php

require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_smart_checkout_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_braintree_buy_now_button.php';

do_action( 'crwdfnd_create_new_button_process_submission' );
?>

<div class="crwdfnd-grey-box">
<?php echo CrwdfndUtils::_( 'You can create a new payment button for your memberships using this interface.' ); ?>
</div>

<?php
if ( ! isset( $_REQUEST['crwdfnd_button_type_selected'] ) ) {
	?>
	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_( 'Select Payment Button Type' ); ?></label></h3>
		<div class="inside">
			<form action="" method="post">
			<table class="form-table" role="presentation">
			<tr>
			<td>
			<fieldset>
				<label><input type="radio" name="button_type" value="pp_buy_now" checked /> <?php CrwdfndUtils::e( 'PayPal Buy Now' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="pp_subscription" /> <?php CrwdfndUtils::e( 'PayPal Subscription' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="pp_smart_checkout" /> <?php CrwdfndUtils::e( 'PayPal Smart Checkout' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="braintree_buy_now" /> <?php CrwdfndUtils::e( 'Braintree Buy Now' ); ?></label>
				<br />
			</fieldset>
			</td>
			</tr>
			</table>
	<?php
	apply_filters( 'crwdfnd_new_button_select_button_type', '' );
	wp_nonce_field( 'crwdfnd_admin_create_btns', 'crwdfnd_admin_create_btns' );
	?>

				<br />
				<input type="submit" name="crwdfnd_button_type_selected" class="button-primary" value="<?php echo CrwdfndUtils::_( 'Next' ); ?>" />
			</form>

		</div>
	</div>
	<?php
} else {
	check_admin_referer( 'crwdfnd_admin_create_btns', 'crwdfnd_admin_create_btns' );
	$button_type = sanitize_text_field( $_REQUEST['button_type'] );
	do_action( 'crwdfnd_create_new_button_for_' . $button_type );
}
