<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class CrwdfndMembers extends WP_List_Table {

	function __construct() {
		parent::__construct(
			array(
				'singular' => CrwdfndUtils::_( 'Member' ),
				'plural'   => CrwdfndUtils::_( 'Members' ),
				'ajax'     => false,
			)
		);
	}

	function get_columns() {
		$columns = array(
			'cb'                  => '<input type="checkbox" />',
			'member_id'           => CrwdfndUtils::_( 'ID' ),
			'user_name'           => CrwdfndUtils::_( 'Username' ),
			'first_name'          => CrwdfndUtils::_( 'First Name' ),
			'last_name'           => CrwdfndUtils::_( 'Last Name' ),
			'email'               => CrwdfndUtils::_( 'Email' ),
			'alias'               => CrwdfndUtils::_( 'Membership Level' ),
			'subscription_starts' => CrwdfndUtils::_( 'Access Starts' ),
			'account_state'       => CrwdfndUtils::_( 'Account State' ),
			'last_accessed'       => CrwdfndUtils::_( 'Last Login Date' ),
		);
		return apply_filters( 'crwdfnd_admin_members_table_columns', $columns );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'member_id'           => array( 'member_id', true ), //True means already sorted
			'user_name'           => array( 'user_name', false ),
			'first_name'          => array( 'first_name', false ),
			'last_name'           => array( 'last_name', false ),
			'email'               => array( 'email', false ),
			'alias'               => array( 'alias', false ),
			'subscription_starts' => array( 'subscription_starts', false ),
			'account_state'       => array( 'account_state', false ),
			'last_accessed'       => array( 'last_accessed', false ),
		);
		return apply_filters( 'crwdfnd_admin_members_table_sortable_columns', $sortable_columns );
	}

	function get_bulk_actions() {
		$actions = array(
			'bulk_delete'        => CrwdfndUtils::_( 'Delete' ),
			'bulk_active'        => CrwdfndUtils::_( 'Set Status to Active' ),
			'bulk_active_notify' => CrwdfndUtils::_( 'Set Status to Active and Notify' ),
			'bulk_inactive'      => CrwdfndUtils::_( 'Set Status to Inactive' ),
			'bulk_pending'       => CrwdfndUtils::_( 'Set Status to Pending' ),
			'bulk_expired'       => CrwdfndUtils::_( 'Set Status to Expired' ),
		);
		return $actions;
	}

	function column_default( $item, $column_name ) {
		$column_data = apply_filters( 'crwdfnd_admin_members_table_column_' . $column_name, $item[ $column_name ], $item );
		return $column_data;
	}

	function column_account_state( $item ) {
		$acc_state_str = ucfirst( $item['account_state'] );
		return CrwdfndUtils::_( $acc_state_str );
	}

	function column_member_id( $item ) {
		$delete_crwdfnduser_nonce = wp_create_nonce( 'delete_crwdfnduser_admin_end' );
		$actions               = array(
			'edit'   => sprintf( '<a href="admin.php?page=crowdfund_me&member_action=edit&member_id=%s">Edit/View</a>', $item['member_id'] ),
			'delete' => sprintf( '<a href="admin.php?page=crowdfund_me&member_action=delete&member_id=%s&delete_crwdfnduser_nonce=%s" onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>', $item['member_id'], $delete_crwdfnduser_nonce ),
		);
		return $item['member_id'] . $this->row_actions( $actions );
	}

	function column_user_name( $item ) {
		$user_name = $item['user_name'];
		if ( empty( $user_name ) ) {
			$user_name = '[' . CrwdfndUtils::_( 'incomplete' ) . ']';
		}
		return $user_name;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="members[]" value="%s" />',
			$item['member_id']
		);
	}

	function prepare_items() {
		global $wpdb;

		$this->process_bulk_action();

		$records_query_head = 'SELECT member_id,user_name,first_name,last_name,email,alias,subscription_starts,account_state,last_accessed';
		$count_query_head   = 'SELECT COUNT(member_id)';

		$query  = ' ';
		$query .= ' FROM ' . $wpdb->prefix . 'crwdfnd_members_tbl';
		$query .= ' LEFT JOIN ' . $wpdb->prefix . 'crwdfnd_membership_tbl';
		$query .= ' ON ( membership_level = id ) ';

		//Get the search string (if any)
		$s = filter_input( INPUT_GET, 's' );
		if ( empty( $s ) ) {
			$s = filter_input( INPUT_POST, 's' );
		}

		$status = filter_input( INPUT_GET, 'status' );
                $status = esc_attr( $status );//Escape value

		$filters = array();

		//Add the search parameter to the query
		if ( ! empty( $s ) ) {
			$s = sanitize_text_field( $s );
			$s = trim( $s ); //Trim the input
                        $s = esc_attr( $s );
			$filters[] = "( user_name LIKE '%" . strip_tags( $s ) . "%' "
					. " OR first_name LIKE '%" . strip_tags( $s ) . "%' "
					. " OR last_name LIKE '%" . strip_tags( $s ) . "%' "
					. " OR email LIKE '%" . strip_tags( $s ) . "%' "
					. " OR address_city LIKE '%" . strip_tags( $s ) . "%' "
					. " OR address_state LIKE '%" . strip_tags( $s ) . "%' "
					. " OR country LIKE '%" . strip_tags( $s ) . "%' "
					. " OR company_name LIKE '%" . strip_tags( $s ) . "%' )";
		}

		//Add account status filtering to the query
		if ( ! empty( $status ) ) {
			if ( $status == 'incomplete' ) {
				$filters[] = "user_name = ''";
			} else {
				$filters[] = "account_state = '" . $status . "'";
			}
		}

		//Add membership level filtering
		$membership_level = filter_input( INPUT_GET, 'membership_level', FILTER_SANITIZE_NUMBER_INT );

		if ( ! empty( $membership_level ) ) {
			$filters[] = sprintf( "membership_level = '%d'", $membership_level );
		}

		//Build the WHERE clause of the query string
		if ( ! empty( $filters ) ) {
			$filter_str = '';
			foreach ( $filters as $ind => $filter ) {
				$filter_str .= $ind === 0 ? $filter : ' AND ' . $filter;
			}
			$query .= 'WHERE ' . $filter_str;
		}

		//Build the orderby and order query parameters
		$orderby          = filter_input( INPUT_GET, 'orderby' );
		$orderby          = apply_filters( 'crwdfnd_admin_members_table_orderby', $orderby );
		$orderby          = empty( $orderby ) ? 'member_id' : $orderby;
		$order            = filter_input( INPUT_GET, 'order' );
		$order            = empty( $order ) ? 'DESC' : $order;
		$sortable_columns = $this->get_sortable_columns();
		$orderby          = CrwdfndUtils::sanitize_value_by_array( $orderby, $sortable_columns );
		$order            = CrwdfndUtils::sanitize_value_by_array(
			$order,
			array(
				'DESC' => '1',
				'ASC'  => '1',
			)
		);
		$query           .= ' ORDER BY ' . $orderby . ' ' . $order;

		//Execute the query
		$totalitems = $wpdb->get_var( $count_query_head . $query );
		//Pagination setup
		$perpage = apply_filters( 'crwdfnd_members_menu_items_per_page', 50 );
		$paged   = filter_input( INPUT_GET, 'paged' );
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		$totalpages = ceil( $totalitems / $perpage );
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $wpdb->get_results( $records_query_head . $query, ARRAY_A );
		$this->items           = apply_filters( 'crwdfnd_admin_members_table_items', $this->items );
	}

	function get_user_count_by_account_state() {
		global $wpdb;
		$query  = 'SELECT count(member_id) AS count, account_state FROM ' . $wpdb->prefix . 'crwdfnd_members_tbl GROUP BY account_state';
		$result = $wpdb->get_results( $query, ARRAY_A );
		$count  = array();

		$all = 0;
		foreach ( $result as $row ) {
			$count[ $row['account_state'] ] = $row['count'];
			$all                           += intval( $row['count'] );
		}
		$count ['all'] = $all;

		$count_incomplete_query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . "crwdfnd_members_tbl WHERE user_name = ''";
		$count['incomplete']    = $wpdb->get_var( $count_incomplete_query );

		return $count;
	}

	function no_items() {
		_e( 'No member found.', 'crowd-fund' );
	}

	function process_form_request() {
		if ( isset( $_REQUEST['member_id'] ) ) {
			//This is a member profile edit action
			$record_id = sanitize_text_field( $_REQUEST['member_id'] );
			if ( ! is_numeric( $record_id ) ) {
				wp_die( 'Error! ID must be numeric.' );
			}
			return $this->edit( absint( $record_id ) );
		}

		//This is an profile add action.
		return $this->add();
	}

	function add() {
		$form = apply_filters( 'crwdfnd_admin_registration_form_override', '' );
		if ( ! empty( $form ) ) {
			echo $form;
			return;
		}
		global $wpdb;
		$member                        = CrwdfndTransfer::$default_fields;
		$member['member_since']        = CrwdfndUtils::get_current_date_in_wp_zone();//date( 'Y-m-d' );
		$member['subscription_starts'] = CrwdfndUtils::get_current_date_in_wp_zone();//date( 'Y-m-d' );
		if ( isset( $_POST['createcrwdfnduser'] ) ) {
			$member = array_map( 'sanitize_text_field', $_POST );
		}
		extract( $member, EXTR_SKIP );
		$query  = 'SELECT * FROM ' . $wpdb->prefix . 'crwdfnd_membership_tbl WHERE  id !=1 ';
		$levels = $wpdb->get_results( $query, ARRAY_A );

                $add_user_template_path = apply_filters('crwdfnd_admin_registration_add_user_template_path', CROWDFUND_ME_PATH . 'views/admin_add.php');
		include_once $add_user_template_path;

		return false;
	}

	function edit( $id ) {
		global $wpdb;
		$id     = absint( $id );
		$query  = "SELECT * FROM {$wpdb->prefix}crwdfnd_members_tbl WHERE member_id = $id";
		$member = $wpdb->get_row( $query, ARRAY_A );
		if ( isset( $_POST['editcrwdfnduser'] ) ) {
			$_POST['user_name'] = sanitize_text_field( $member['user_name'] );
			$_POST['email']     = sanitize_email( $member['email'] );
			foreach ( $_POST as $key => $value ) {
				$key = sanitize_text_field( $key );
				if ( $key == 'email' ) {
					$member[ $key ] = sanitize_email( $value );
				} else {
					$member[ $key ] = sanitize_text_field( $value );
				}
			}
		}
		extract( $member, EXTR_SKIP );
		$query  = 'SELECT * FROM ' . $wpdb->prefix . 'crwdfnd_membership_tbl WHERE  id !=1 ';
		$levels = $wpdb->get_results( $query, ARRAY_A );

                $edit_user_template_path = apply_filters('crwdfnd_admin_registration_edit_user_template_path', CROWDFUND_ME_PATH . 'views/admin_edit.php');
		include_once $edit_user_template_path;

		return false;
	}

	function process_bulk_action() {
		//Detect when a bulk action is being triggered... then perform the action.
		$members = isset( $_REQUEST['members'] ) ? $_REQUEST['members'] : array();
		$members = array_map( 'sanitize_text_field', $members );

		$current_action = $this->current_action();
		if ( ! empty( $current_action ) ) {
			//Bulk operation action. Lets make sure multiple records were selected before going ahead.
			if ( empty( $members ) ) {
				echo '<div id="message" class="error"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
				return;
			}
		} else {
			//No bulk operation.
			return;
		}

		check_admin_referer( 'crwdfnd_bulk_action', 'crwdfnd_bulk_action_nonce' );

		//perform the bulk operation according to the selection
		if ( 'bulk_delete' === $current_action ) {
			foreach ( $members as $record_id ) {
				if ( ! is_numeric( $record_id ) ) {
					wp_die( 'Error! ID must be numeric.' );
				}
				self::delete_user_by_id( $record_id );
			}
			echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
			return;
		} elseif ( 'bulk_active' === $current_action ) {
			$this->bulk_set_status( $members, 'active' );
		} elseif ( 'bulk_active_notify' == $current_action ) {
			$this->bulk_set_status( $members, 'active', true );
		} elseif ( 'bulk_inactive' == $current_action ) {
			$this->bulk_set_status( $members, 'inactive' );
		} elseif ( 'bulk_pending' == $current_action ) {
			$this->bulk_set_status( $members, 'pending' );
		} elseif ( 'bulk_expired' == $current_action ) {
			$this->bulk_set_status( $members, 'expired' );
		}

		echo '<div id="message" class="updated fade"><p>Bulk operation completed successfully!</p></div>';
	}

	function bulk_set_status( $members, $status, $notify = false ) {
		$ids = implode( ',', array_map( 'absint', $members ) );
		if ( empty( $ids ) ) {
			return;
		}
		global $wpdb;
		$query = 'UPDATE ' . $wpdb->prefix . 'crwdfnd_members_tbl ' .
				" SET account_state = '" . $status . "' WHERE member_id in (" . $ids . ')';
		$wpdb->query( $query );

		if ( $notify ) {
			$settings = CrwdfndSettings::get_instance();

			$emails = $wpdb->get_col( 'SELECT email FROM ' . $wpdb->prefix . 'crwdfnd_members_tbl ' . " WHERE member_id IN ( $ids  ) " );

			$subject = $settings->get_value( 'bulk-activate-notify-mail-subject' );
			if ( empty( $subject ) ) {
                            $subject = 'Account Activated!';
			}
			$body = $settings->get_value( 'bulk-activate-notify-mail-body' );
			if ( empty( $body ) ) {
                            $body = 'Hi, Your account has been activated successfully!';
			}

			$from_address = $settings->get_value( 'email-from' );
			$headers = 'From: ' . $from_address . "\r\n";

                        foreach ($emails as $to_email) {
                            //Send the activation email one by one to all the selected members.
                            $subject = apply_filters( 'crwdfnd_email_bulk_set_status_subject', $subject );
                            $body = apply_filters( 'crwdfnd_email_bulk_set_status_body', $body );
                            $to_email = trim($to_email);
                            CrwdfndMiscUtils::mail( $to_email, $subject, $body, $headers );
                            CrwdfndLog::log_simple_debug( 'Bulk activation email notification sent. Activation email sent to the following email: ' . $to_email, true );
                        }
		}
	}

	function delete() {
		if ( isset( $_REQUEST['member_id'] ) ) {
			//Check we are on the admin end and user has management permission
			CrwdfndMiscUtils::check_user_permission_and_is_admin( 'member deletion by admin' );

			//Check nonce
			if ( ! isset( $_REQUEST['delete_crwdfnduser_nonce'] ) || ! wp_verify_nonce( $_REQUEST['delete_crwdfnduser_nonce'], 'delete_crwdfnduser_admin_end' ) ) {
				//Nonce check failed.
				wp_die( CrwdfndUtils::_( 'Error! Nonce verification failed for user delete from admin end.' ) );
			}

			$id = sanitize_text_field( $_REQUEST['member_id'] );
			$id = absint( $id );
			self::delete_user_by_id( $id );
		}
	}

	public static function delete_user_by_id( $id ) {
		if ( ! is_numeric( $id ) ) {
			wp_die( 'Error! Member ID must be numeric.' );
		}

                //Trigger action hook
                do_action( 'crwdfnd_admin_end_user_delete_action', $id );

		$crwdfnd_user = CrwdfndMemberUtils::get_user_by_id( $id );
		$user_name = $crwdfnd_user->user_name;
		self::delete_wp_user( $user_name ); //Deletes the WP User record
		self::delete_crwdfnd_user_by_id( $id ); //Deletes the CRWDFND record
	}

	public static function delete_crwdfnd_user_by_id( $id ) {
		self::delete_user_subs( $id );
		global $wpdb;
		$query = 'DELETE FROM ' . $wpdb->prefix . "crwdfnd_members_tbl WHERE member_id = $id";
		$wpdb->query( $query );
	}

	public static function delete_wp_user( $user_name ) {
		$wp_user_id = username_exists( $user_name );
		if ( empty( $wp_user_id ) || ! is_numeric( $wp_user_id ) ) {
			return;
		}

		if ( ! self::is_wp_super_user( $wp_user_id ) ) {
			//Not an admin user so it is safe to delete this user.
			include_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( $wp_user_id, 1 ); //assigns all related to this user to admin.
		} else {
			//This is an admin user. So not going to delete the WP User record.
			CrwdfndTransfer::get_instance()->set( 'status', 'For safety, we do not allow deletion of any associated WordPress account with administrator role.' );
			return;
		}
	}

	private static function delete_user_subs( $id ) {
		$member = CrwdfndMemberUtils::get_user_by_id( $id );
		if ( ! $member ) {
			return false;
		}

		$res = $wpdb->get_results( $q, ARRAY_A );

		if ( ! $res ) {
			return false;
		}

		foreach ( $res as $sub ) {

			if ( substr( $sub['subscr_id'], 0, 4 ) !== 'sub_' ) {
				continue;
			}

			//let's find the payment button
			$q        = $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='subscr_id' AND meta_value=%s", $sub['subscr_id'] );
			$res_post = $wpdb->get_row( $q );

			if ( ! $res_post ) {
				//no button found
				continue;
			}

			$button_id = get_post_meta( $res_post->post_id, 'payment_button_id', true );

			$button = get_post( $button_id );

			if ( ! $button ) {
				//no button found
				continue;
			}
		}
	}

	public static function is_wp_super_user( $wp_user_id ) {
		$user_data = get_userdata( $wp_user_id );
		if ( empty( $user_data ) ) {
			//Not an admin user if we can't find his data for the given ID.
			return false;
		}
		if ( isset( $user_data->wp_capabilities['administrator'] ) ) {//Check capability
			//admin user
			return true;
		}
		if ( $user_data->wp_user_level == 10 ) {//Check for old style wp user level
			//admin user
			return true;
		}
		//This is not an admin user
		return false;
	}

	function bulk_operation_menu() {
		echo '<div id="poststuff"><div id="post-body">';

		if ( isset( $_REQUEST['crwdfnd_bulk_change_level_process'] ) ) {
			//Check nonce
			$crwdfnd_bulk_change_level_nonce = filter_input( INPUT_POST, 'crwdfnd_bulk_change_level_nonce' );
			if ( ! wp_verify_nonce( $crwdfnd_bulk_change_level_nonce, 'crwdfnd_bulk_change_level_nonce_action' ) ) {
				//Nonce check failed.
				wp_die( CrwdfndUtils::_( 'Error! Nonce security verification failed for Bulk Change Membership Level action. Clear cache and try again.' ) );
			}

			$errorMsg      = '';
			$from_level_id = sanitize_text_field( $_REQUEST['crwdfnd_bulk_change_level_from'] );
			$to_level_id   = sanitize_text_field( $_REQUEST['crwdfnd_bulk_change_level_to'] );

			if ( $from_level_id == 'please_select' || $to_level_id == 'please_select' ) {
				$errorMsg = CrwdfndUtils::_( 'Error! Please select a membership level first.' );
			}

			if ( empty( $errorMsg ) ) {//No validation errors so go ahead
				$member_records = CrwdfndMemberUtils::get_all_members_of_a_level( $from_level_id );
				if ( $member_records ) {
					foreach ( $member_records as $row ) {
						$member_id = $row->member_id;
						CrwdfndMemberUtils::update_membership_level( $member_id, $to_level_id );
					}
				}
			}

			$message = '';
			if ( ! empty( $errorMsg ) ) {
				$message = $errorMsg;
			} else {
				$message = CrwdfndUtils::_( 'Membership level change operation completed successfully.' );
			}
			echo '<div id="message" class="updated fade"><p><strong>';
			echo $message;
			echo '</strong></p></div>';
		}

		if ( isset( $_REQUEST['crwdfnd_bulk_user_start_date_change_process'] ) ) {
			//Check nonce
			$crwdfnd_bulk_start_date_nonce = filter_input( INPUT_POST, 'crwdfnd_bulk_start_date_nonce' );
			if ( ! wp_verify_nonce( $crwdfnd_bulk_start_date_nonce, 'crwdfnd_bulk_start_date_nonce_action' ) ) {
				//Nonce check failed.
				wp_die( CrwdfndUtils::_( 'Error! Nonce security verification failed for Bulk Change Access Starts Date action. Clear cache and try again.' ) );
			}

			$errorMsg = '';
			$level_id = sanitize_text_field( $_REQUEST['crwdfnd_bulk_user_start_date_change_level'] );
			$new_date = sanitize_text_field( $_REQUEST['crwdfnd_bulk_user_start_date_change_date'] );

			if ( $level_id == 'please_select' ) {
				$errorMsg = CrwdfndUtils::_( 'Error! Please select a membership level first.' );
			}

			if ( empty( $errorMsg ) ) {//No validation errors so go ahead
				$member_records = CrwdfndMemberUtils::get_all_members_of_a_level( $level_id );
				if ( $member_records ) {
					foreach ( $member_records as $row ) {
						$member_id = $row->member_id;
						CrwdfndMemberUtils::update_access_starts_date( $member_id, $new_date );
					}
				}
			}

			$message = '';
			if ( ! empty( $errorMsg ) ) {
				$message = $errorMsg;
			} else {
				$message = CrwdfndUtils::_( 'Access starts date change operation successfully completed.' );
			}
			echo '<div id="message" class="updated fade"><p><strong>';
			echo $message;
			echo '</strong></p></div>';
		}
		?>

		<div class="postbox">
			<h3 class="hndle"><label for="title"><?php CrwdfndUtils::e( 'Bulk Update Membership Level of Members' ); ?></label></h3>
			<div class="inside">
				<p>
					<?php CrwdfndUtils::e( 'You can manually change the membership level of any member by editing the record from the members menu. ' ); ?>
					<?php CrwdfndUtils::e( 'You can use the following option to bulk update the membership level of users who belong to the level you select below.' ); ?>
				</p>
				<form method="post" action="">
					<input type="hidden" name="crwdfnd_bulk_change_level_nonce" value="<?php echo wp_create_nonce( 'crwdfnd_bulk_change_level_nonce_action' ); ?>" />

					<table width="100%" border="0" cellspacing="0" cellpadding="6">
						<tr valign="top">
							<td width="25%" align="left">
								<strong><?php CrwdfndUtils::e( 'Membership Level: ' ); ?></strong>
							</td>
							<td align="left">
								<select name="crwdfnd_bulk_change_level_from">
									<option value="please_select"><?php CrwdfndUtils::e( 'Select Current Level' ); ?></option>
									<?php echo CrwdfndUtils::membership_level_dropdown(); ?>
								</select>
								<p class="description"><?php CrwdfndUtils::e( 'Select the current membership level (the membership level of all members who are in this level will be updated).' ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<td width="25%" align="left">
								<strong><?php CrwdfndUtils::e( 'Level to Change to: ' ); ?></strong>
							</td>
							<td align="left">
								<select name="crwdfnd_bulk_change_level_to">
									<option value="please_select"><?php CrwdfndUtils::e( 'Select Target Level' ); ?></option>
									<?php echo CrwdfndUtils::membership_level_dropdown(); ?>
								</select>
								<p class="description"><?php CrwdfndUtils::e( 'Select the new membership level.' ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<td width="25%" align="left">
								<input type="submit" class="button" name="crwdfnd_bulk_change_level_process" value="<?php CrwdfndUtils::e( 'Bulk Change Membership Level' ); ?>" />
							</td>
							<td align="left"></td>
						</tr>

					</table>
				</form>
			</div></div>

		<div class="postbox">
			<h3 class="hndle"><label for="title"><?php CrwdfndUtils::e( 'Bulk Update Access Starts Date of Members' ); ?></label></h3>
			<div class="inside">

				<p>
					<?php CrwdfndUtils::e( 'The access starts date of a member is set to the day the user registers. This date value is used to calculate how long the member can access your content that are protected with a duration type protection in the membership level. ' ); ?>
					<?php CrwdfndUtils::e( 'You can manually set a specific access starts date value of all members who belong to a particular level using the following option.' ); ?>
				</p>
				<form method="post" action="">
					<input type="hidden" name="crwdfnd_bulk_start_date_nonce" value="<?php echo wp_create_nonce( 'crwdfnd_bulk_start_date_nonce_action' ); ?>" />

					<table width="100%" border="0" cellspacing="0" cellpadding="6">
						<tr valign="top">
							<td width="25%" align="left">
								<strong><?php CrwdfndUtils::e( 'Membership Level: ' ); ?></strong>
							</td><td align="left">
								<select name="crwdfnd_bulk_user_start_date_change_level">
									<option value="please_select"><?php CrwdfndUtils::e( 'Select Level' ); ?></option>
									<?php echo CrwdfndUtils::membership_level_dropdown(); ?>
								</select>
								<p class="description"><?php CrwdfndUtils::e( 'Select the Membership level (the access start date of all members who are in this level will be updated).' ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<td width="25%" align="left">
								<strong>Access Starts Date: </strong>
							</td><td align="left">
								<input name="crwdfnd_bulk_user_start_date_change_date" id="crwdfnd_bulk_user_start_date_change_date" class="crwdfnd-select-date" type="text" size="20" value="<?php echo ( date( 'Y-m-d' ) ); ?>" />
								<p class="description"><?php CrwdfndUtils::e( 'Specify the Access Starts date value.' ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<td width="25%" align="left">
								<input type="submit" class="button" name="crwdfnd_bulk_user_start_date_change_process" value="<?php CrwdfndUtils::e( 'Bulk Change Access Starts Date' ); ?>" />
							</td>
							<td align="left"></td>
						</tr>

					</table>
				</form>
			</div></div>

		<script>
			jQuery(document).ready(function ($) {
				$('#crwdfnd_bulk_user_start_date_change_date').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "-100:+100"});
			});
		</script>
		<?php
		echo '</div></div>'; //<!-- end of #poststuff #post-body -->
	}

	function show_all_members() {
		ob_start();
		$status = filter_input( INPUT_GET, 'status' );
		include_once CROWDFUND_ME_PATH . 'views/admin_members_list.php';
		$output = ob_get_clean();
		return $output;
	}

	function handle_main_members_admin_menu() {
		do_action( 'crwdfnd_members_menu_start' );

		//Check current_user_can() or die.
		CrwdfndMiscUtils::check_user_permission_and_is_admin( 'Main Members Admin Menu' );

		$action   = filter_input( INPUT_GET, 'member_action' );
		$action   = empty( $action ) ? filter_input( INPUT_POST, 'action' ) : $action;
		$selected = $action;
		?>
		<div class="wrap crwdfnd-admin-menu-wrap"><!-- start wrap -->

			<h1><?php echo CrwdfndUtils::_( 'CrowdFund Me::Members' ); ?><!-- page title -->
				<a href="admin.php?page=crowdfund_me&member_action=add" class="add-new-h2"><?php echo CrwdfndUtils::_( 'Add New' ); ?></a>
			</h1>

			<h2 class="nav-tab-wrapper crwdfnd-members-nav-tab-wrapper"><!-- start nav menu tabs -->
				<a class="nav-tab <?php echo ( $selected == '' ) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me"><?php echo CrwdfndUtils::_( 'Members' ); ?></a>
				<a class="nav-tab <?php echo ( $selected == 'add' ) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me&member_action=add"><?php echo CrwdfndUtils::_( 'Add Member' ); ?></a>
				<a class="nav-tab <?php echo ( $selected == 'bulk' ) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me&member_action=bulk"><?php echo CrwdfndUtils::_( 'Bulk Operation' ); ?></a>
				<?php
				if ( $selected == 'edit' ) {//Only show the "edit member" tab when a member profile is being edited from the admin side.
					echo '<a class="nav-tab nav-tab-active" href="#">Edit Member</a>';
				}

				//Trigger hooks that allows an extension to add extra nav tabs in the members menu.
				do_action( 'crwdfnd_members_menu_nav_tabs', $selected );

				$menu_tabs = apply_filters( 'crwdfnd_members_additional_menu_tabs_array', array() );
				foreach ( $menu_tabs as $member_action => $title ) {
					?>
					<a class="nav-tab <?php echo ( $selected == $member_action ) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=crowdfund_me&member_action=<?php echo $member_action; ?>" ><?php CrwdfndUtils::e( $title ); ?></a>
					<?php
				}
				?>
			</h2><!-- end nav menu tabs -->
			<?php
			do_action( 'crwdfnd_members_menu_after_nav_tabs' );

			//Trigger hook so anyone listening for this particular action can handle the output.
			do_action( 'crwdfnd_members_menu_body_' . $action );

			//Allows an addon to completely override the body section of the members admin menu for a given action.
			$output = apply_filters( 'crwdfnd_members_menu_body_override', '', $action );
			if ( ! empty( $output ) ) {
				//An addon has overriden the body of this page for the given action. So no need to do anything in core.
				echo $output;
				echo '</div>'; //<!-- end of wrap -->
				return;
			}

			//Switch case for the various different actions handled by the core plugin.
			switch ( $action ) {
				case 'members_list':
					//Show the members listing
					echo $this->show_all_members();
					break;
				case 'add':
					//Process member profile add
					$this->process_form_request();
					break;
				case 'edit':
					//Process member profile edit
					$this->process_form_request();
					break;
				case 'bulk':
					//Handle the bulk operation menu
					$this->bulk_operation_menu();
					break;
				default:
					//Show the members listing page by default.
					echo $this->show_all_members();
					break;
			}

			echo '</div>'; //<!-- end of wrap -->
	}

}
