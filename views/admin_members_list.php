<?php

if (isset($_REQUEST['member_action']) && $_REQUEST['member_action'] == 'delete') {

    $this->delete();
    $success_msg = '<div id="message" class="updated"><p>';
    $success_msg .= CrwdfndUtils::_('The selected entry was deleted!');
    $success_msg .= '</p></div>';
    echo $success_msg;
}

$this->prepare_items();
$count = $this->get_user_count_by_account_state();

global $wpdb;
$query = "SELECT * FROM " . $wpdb->prefix . "crwdfnd_membership_tbl WHERE  id !=1 ";
$levels = $wpdb->get_results($query, ARRAY_A);

$account_state = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$membership_level = filter_input(INPUT_GET, 'membership_level', FILTER_SANITIZE_NUMBER_INT);
?>
<style>
    select.crwdfnd-admin-search-dropdown {
        vertical-align: inherit;
    }
    input.button.crwdfnd-admin-search-btn {
        vertical-align: top;
    }
</style>
<ul class="subsubsub">
    <li class="all"><a href="admin.php?page=crowdfund_me" <?php echo $status == "" ? "class='current'" : ""; ?> ><?php echo CrwdfndUtils::_('All') ?> <span class="count">(<?php echo $count['all']; ?>)</span></a> |</li>
    <li class="active"><a href="admin.php?page=crowdfund_me&status=active" <?php echo $status == "active" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Active') ?> <span class="count">(<?php echo isset($count['active']) ? $count['active'] : 0 ?>)</span></a> |</li>
    <li class="active"><a href="admin.php?page=crowdfund_me&status=inactive" <?php echo $status == "inactive" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Inactive') ?> <span class="count">(<?php echo isset($count['inactive']) ? $count['inactive'] : 0 ?>)</span></a> |</li>
    <li class="pending"><a href="admin.php?page=crowdfund_me&status=activation_required" <?php echo $status == "activation_required" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Activation Required') ?> <span class="count">(<?php echo isset($count['activation_required']) ? $count['activation_required'] : 0 ?>)</span></a> |</li>
    <li class="pending"><a href="admin.php?page=crowdfund_me&status=pending" <?php echo $status == "pending" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Pending') ?> <span class="count">(<?php echo isset($count['pending']) ? $count['pending'] : 0 ?>)</span></a> |</li>
    <li class="incomplete"><a href="admin.php?page=crowdfund_me&status=incomplete" <?php echo $status == "incomplete" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Incomplete') ?> <span class="count">(<?php echo isset($count['incomplete']) ? $count['incomplete'] : 0 ?>)</span></a> |</li>
    <li class="expired"><a href="admin.php?page=crowdfund_me&status=expired" <?php echo $status == "expired" ? "class='current'" : ""; ?>><?php echo CrwdfndUtils::_('Expired') ?> <span class="count">(<?php echo isset($count['expired']) ? $count['expired'] : 0 ?>)</span></a></li>
</ul>

<br />
<form method="get">
    <p class="search-box">
        <select name="status" class="crwdfnd-admin-search-dropdown" id="account_state">
            <option value=""<?php echo empty($account_state) ? ' selected' : ''; ?>> <?php echo CrwdfndUtils::_('Account State'); ?></option>
            <?php echo CrwdfndUtils::account_state_dropdown($account_state); ?>
            <option value="incomplete"<?php echo $account_state === "incomplete" ? ' selected' : ''; ?>> <?php echo CrwdfndUtils::_('Incomplete'); ?></option>
        </select>
        <select name="membership_level" class="crwdfnd-admin-search-dropdown" id="membership_level">
            <option value=""<?php echo empty($membership_level) ? ' selected' : ''; ?>> <?php echo CrwdfndUtils::_('Membership Level'); ?></option>
            <?php foreach ($levels as $level): ?>
                <option <?php echo ($level['id'] == $membership_level) ? "selected='selected'" : ""; ?> value="<?php echo $level['id']; ?>"> <?php echo $level['alias'] ?></option>
            <?php endforeach; ?>
        </select>
        <input id="search_id-search-input" type="text" name="s" value="<?php echo isset($_REQUEST['s']) ? esc_attr($_REQUEST['s']) : ''; ?>" />
        <input id="search-submit" class="button crwdfnd-admin-search-btn" type="submit" name="" value="<?php echo CrwdfndUtils::_('Search') ?>" />
        <input type="hidden" name="page" value="crowdfund_me" />
    </p>
</form>

<form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">

    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />

    <?php $this->display(); ?>
    <?php wp_nonce_field( 'crwdfnd_bulk_action', 'crwdfnd_bulk_action_nonce' ); ?>
</form>

<p>
    <a href="admin.php?page=crowdfund_me&member_action=add" class="button-primary"><?php echo CrwdfndUtils::_('Add New') ?></a>
</p>
