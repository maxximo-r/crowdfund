<?php

class CrwdfndPaymentsAdminMenu {

    function __construct() {

    }

    function handle_main_payments_admin_menu() {
        do_action('crwdfnd_payments_menu_start');

        //Check current_user_can() or die.
        CrwdfndMiscUtils::check_user_permission_and_is_admin('Main Payments Admin Menu');

        $output = '';
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
        $selected = $tab;
        ?>


        <div class="wrap crwdfnd-admin-menu-wrap"><!-- start wrap -->

            <h1><?php echo CrwdfndUtils::_('CrowdFund Me::Payments') ?></h1><!-- page title -->

            <!-- start nav menu tabs -->
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo ($tab == '') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_payments"><?php CrwdfndUtils::e('Transactions'); ?></a>
                <a class="nav-tab <?php echo ($tab == 'payment_buttons') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_payments&tab=payment_buttons"><?php CrwdfndUtils::e('Manage Payment Buttons'); ?></a>
                <a class="nav-tab <?php echo ($tab == 'create_new_button') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_payments&tab=create_new_button"><?php CrwdfndUtils::e('Create New Button'); ?></a>
                <?php
                if ($tab == 'edit_button') {//Only show the "edit button" tab when a button is being edited.
                    echo '<a class="nav-tab nav-tab-active" href="#">Edit Button</a>';
                }

                //Trigger hooks that allows an extension to add extra nav tabs in the payments menu.
                do_action ('crwdfnd_payments_menu_nav_tabs', $selected);

                $menu_tabs = apply_filters('crwdfnd_payments_menu_additional_menu_tabs_array', array());
                foreach ($menu_tabs as $menu_action => $title){
                    ?>
                    <a class="nav-tab <?php echo ($selected == $menu_action) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me_payments&tab=<?php echo $menu_action; ?>" ><?php CrwdfndUtils::e($title); ?></a>
                    <?php
                }

                ?>
            </h2>
            <!-- end nav menu tabs -->

            <?php

            do_action('crwdfnd_payments_menu_after_nav_tabs');

            //Allows an addon to completely override the body section of the payments admin menu for a given action.
            $output = apply_filters('crwdfnd_payments_menu_body_override', '', $tab);
            if (!empty($output)) {
                //An addon has overriden the body of this page for the given tab/action. So no need to do anything in core.
                echo $output;
                echo '</div>';//<!-- end of wrap -->
                return;
            }

            echo '<div id="poststuff"><div id="post-body">';


            //Switch case for the various different tabs handled by the core plugin.
            switch ($tab) {
                case 'payment_buttons':
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_payment_buttons.php');
                    break;
                case 'create_new_button':
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_create_payment_buttons.php');
                    break;
                case 'edit_button':
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_edit_payment_buttons.php');
                    break;
                case 'all_txns':
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_all_payment_transactions.php');
                    break;
                case 'add_new_txn':
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_add_edit_transaction_manually.php');
                    crwdfnd_handle_add_new_txn_manually();
                    break;
                default:
                    include_once(CROWDFUND_ME_PATH . '/views/payments/admin_all_payment_transactions.php');
                    break;
            }

            echo '</div></div>'; //<!-- end of post-body -->

        echo '</div>'; //<!-- end of .wrap -->
    }

}
