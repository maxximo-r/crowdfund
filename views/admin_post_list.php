
<style>
    #crwdfnd-list-type-nav .nav-tab {
        padding: 1px 15px;
        font-size: 12px;
    }
</style>
<div id="crwdfnd-list-type-nav" class="nav-tab-wrapper">
    <a class="nav-tab<?php echo $post_list->type == 'post' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_levels&level_action=post_list&list_type=post"><?php CrwdfndUtils::e('Posts'); ?></a>
    <a class="nav-tab<?php echo $post_list->type == 'page' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_levels&level_action=post_list&list_type=page"><?php CrwdfndUtils::e('Pages'); ?></a>
    <a class="nav-tab<?php echo $post_list->type == 'custom_post' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_levels&level_action=post_list&list_type=custom_post"><?php CrwdfndUtils::e('Custom Posts'); ?></a>
</div>

<br />
<div class="crwdfnd_post_protection_list_form">
<form id="post_list_form" method="post">
    <input type="hidden" name="crwdfnd_post_prot_update_nonce" value="<?php echo wp_create_nonce('crwdfnd_post_prot_update_nonce_action'); ?>" />

    <p class="crwdfnd-select-box-left">
        <label for="membership_level_id"><?php CrwdfndUtils::e('Membership Level:'); ?></label>
        <select id="membership_level_id" name="membership_level_id">
            <option <?php echo $post_list->selected_level_id == 1 ? "selected" : "" ?> value="1"><?php echo CrwdfndUtils::_('General Protection'); ?></option>
            <?php echo CrwdfndUtils::membership_level_dropdown($post_list->selected_level_id); ?>
        </select>
    </p>
    <p class="crwdfnd-select-box-left">
        <input type="submit" class="button-primary" name="update_post_list" value="<?php CrwdfndUtils::e('Update'); ?>">
    </p>
        <?php $post_list->prepare_items(); ?>
        <?php $post_list->display(); ?>
    <input type="hidden" name="list_type" value="<?php echo $post_list->type; ?>">
</form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#membership_level_id').change(function () {
            $('#post_list_form').submit();
        });
    });
</script>
