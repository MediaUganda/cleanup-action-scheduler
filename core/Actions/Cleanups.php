<?php
/**
 * Handle Custom End Points.
 *
 * @package Cleanup_Action_Scheduler
 */

namespace Cleanup_Action_Scheduler\Actions;

use Cleanup_Action_Scheduler\Base\BaseController;

/**
 * Cleanup Action Schedules.
 */
class Cleanups extends BaseController {

	/**
	 * AJAX to Cleanup all Action Schedules.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_ajax_cas_delete_all', [ $this, 'cas_delete_all_Action' ] );
		add_action( 'wp_ajax_nopriv_cas_delete_all', [ $this, 'cas_delete_all_Action' ] );
	}

	/**
	 * Cleanup all Action Schedules.
	 * Returns success for AJAX Response.
	 *
	 * @return void
	 */
	public function cas_delete_all_Action() {

		global $wpdb;
		$action_status = 'all';
		$nonce         = '';

		if ( isset( $_POST['cas_delete_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['cas_delete_nonce'] ) );
		}

		if ( ! wp_verify_nonce( $nonce, 'cas_delete_nonce' ) ) {
			// This nonce is not valid.
			die( esc_attr__( 'Security check', 'cleanup-action-scheduler' ) );
		}

		if ( isset( $_POST['action_status'] ) ) {
			$action_status = sanitize_text_field( wp_unslash( $_POST['action_status'] ) );
		}

		if ( 'all' !== $action_status ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}actionscheduler_actions WHERE status = %s", $action_status ) );
		} else {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}actionscheduler_actions" );
		}

		echo wp_json_encode( 'Success. Deleted the actions!' );
		wp_die();
	}

}
