<?php

class CRWDFND_Member_Subscriptions {

	private $active_statuses   = array( 'trialing', 'active' );
	private $active_subs_count = 0;
	private $subs_count        = 0;
	private $subs              = array();
	private $member_id;

	public function __construct( $member_id ) {

		$this->member_id = $member_id;

		$subscr_id = CrwdfndMemberUtils::get_member_field_by_id( $member_id, 'subscr_id' );

		$query_args = array(
			'post_type'  => 'crwdfnd_transactions',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'member_id',
						'value'   => $member_id,
						'compare' => '=',
					),
					array(
						'key'     => 'subscr_id',
						'value'   => $subscr_id,
						'compare' => '=',
					),
				),
			),
		);

		$found_subs = new WP_Query( $query_args );

		$this->subs_count = $found_subs->post_count;

		foreach ( $found_subs->posts as $found_sub ) {
			$sub            = array();
			$post_id        = $found_sub->ID;
			$sub['post_id'] = $post_id;
			$sub_id         = get_post_meta( $post_id, 'subscr_id', true );

			$sub['sub_id'] = $sub_id;

			$status = get_post_meta( $post_id, 'subscr_status', true );

			$sub['status'] = $status;

			if ( $this->is_active( $status ) ) {
				$this->active_subs_count++;
			}

			$cancel_token = get_post_meta( $post_id, 'subscr_cancel_token', true );

			if ( empty( $cancel_token ) ) {
				$cancel_token = md5( $post_id . $sub_id . uniqid() );
				update_post_meta( $post_id, 'subscr_cancel_token', $cancel_token );
			}

			$sub['cancel_token'] = $cancel_token;

			$is_live        = get_post_meta( $post_id, 'is_live', true );
			$is_live        = empty( $is_live ) ? false : true;
			$sub['is_live'] = $is_live;

			$sub['payment_button_id'] = get_post_meta( $post_id, 'payment_button_id', true );

			$this->subs[ $sub_id ] = $sub;
		}

		$this->recheck_status_if_needed();

	}

	public function get_active_subs_count() {
		return $this->active_subs_count;
	}

	public function is_active( $status ) {
		return  in_array( $status, $this->active_statuses, true );
	}

	private function recheck_status_if_needed() {
		foreach ( $this->subs as $sub_id => $sub ) {
			if ( ! empty( $sub['status'] ) ) {
				continue;
			}
			try {

			} catch ( \Exception $e ) {
				return false;
			}
		}
	}

	public function find_by_token( $token ) {
		foreach ( $this->subs as $sub_id => $sub ) {
			if ( $sub['cancel_token'] === $token ) {
				return $sub;
			}
		}
	}

	public function cancel( $sub_id ) {
		$sub = $this->subs[ $sub_id ];

		try {
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
		return true;
	}

}
