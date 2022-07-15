<?php

require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_paypal_smart_checkout_button.php';
require_once CROWDFUND_ME_PATH . 'views/payments/payment-gateway/admin_braintree_buy_now_button.php';

do_action( 'crwdfnd_edit_payment_button_process_submission' );
?>

<div class="crwdfnd-grey-box">
	<?php echo CrwdfndUtils::_( 'You can edit a payment button using this interface.' ); ?>
</div>

<?php
$button_type = sanitize_text_field( $_REQUEST['button_type'] );
$button_id   = sanitize_text_field( $_REQUEST['button_id'] );
$button_id   = absint( $button_id );
do_action( 'crwdfnd_edit_payment_button_for_' . $button_type, $button_id );
