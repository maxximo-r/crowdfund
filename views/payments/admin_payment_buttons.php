<?php
?>

<div class="crwdfnd-grey-box">
    <?php echo CrwdfndUtils::_('All the membership buttons that you created in the plugin are displayed here.'); ?>
</div>

<?php
include_once(CROWDFUND_ME_PATH . 'classes/admin-includes/class.crwdfnd-payment-buttons-list-table.php');
$payments_buttons_table = new CrwdfndPaymentButtonsListTable();

$payments_buttons_table->prepare_items();

?>

<form id="crwdfnd-payment-buttons-filter" method="post" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">

    <input type="hidden" name="page" value="" />
    <?php $payments_buttons_table->display(); ?>
</form>

<p>
    <a href="admin.php?page=crowdfund_me_payments&tab=create_new_button" class="button"><?php CrwdfndUtils::e('Create New Button'); ?></a>
</p>
