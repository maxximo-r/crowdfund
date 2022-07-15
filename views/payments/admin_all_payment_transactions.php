<?php
?>

<div class="crwdfnd-grey-box">
    <?php echo CrwdfndUtils::_('All the payments/transactions of your members are recorded here.'); ?>
</div>

<div class="postbox">
    <h3 class="hndle"><label for="title">Search Transaction</label></h3>
    <div class="inside">
        <?php echo CrwdfndUtils::_('Search for a transaction by using email or name'); ?>
        <br /><br />
        <form method="post" action="">
            <input name="crwdfnd_txn_search" type="text" size="40" value="<?php echo isset($_POST['crwdfnd_txn_search']) ? esc_attr($_POST['crwdfnd_txn_search']) : ''; ?>"/>
            <input type="submit" name="crwdfnd_txn_search_btn" class="button" value="<?php echo CrwdfndUtils::_('Search'); ?>" />
        </form>
    </div></div>

<?php
include_once(CROWDFUND_ME_PATH . 'classes/admin-includes/class.crwdfnd-payments-list-table.php');
$payments_list_table = new CRWDFNDPaymentsListTable();

if (isset($_REQUEST['action'])) {
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_txn') {
        $record_id = sanitize_text_field($_REQUEST['id']);
        $record_id = absint($record_id);
        check_admin_referer('crwdfnd_delete_txn_'.$record_id);
        $payments_list_table->delete_record($record_id);
        $success_msg = '<div id="message" class="updated"><p><strong>';
        $success_msg .= CrwdfndUtils::_('The selected entry was deleted!');
        $success_msg .= '</strong></p></div>';
        echo $success_msg;
    }
}

$payments_list_table->prepare_items();
?>
<form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
    <?php $payments_list_table->display(); ?>
</form>

<p class="submit">
    <a href="admin.php?page=crowdfund_me_payments&tab=add_new_txn" class="button"><?php echo CrwdfndUtils::_('Add a Transaction Manually'); ?></a>
</p>
