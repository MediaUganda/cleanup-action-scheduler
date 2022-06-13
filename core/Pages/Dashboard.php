<?php
/**
 * Add A Custom Dashboard Page.
 * 
 * Credit: Some of the code snippets are from https://gist.github.com/devinsays/4bb6f8804afe88a9435ac5635fa3dcfd.
 *
 * @package Cleanup_Action_Scheduler
 */

namespace Cleanup_Action_Scheduler\Pages;

use Cleanup_Action_Scheduler\Base\BaseController;

/**
 * Base Controller class
 */
class Dashboard extends BaseController {

	/**
	 * Fire class in Init.php.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_automation_menu' ) );
		add_action( 'admin_init', array( $this, 'clean_settings_init' ) );
		add_filter( 'action_scheduler_retention_period', array( $this, 'set_cleanup_time' ) );
		add_filter( 'action_scheduler_queue_runner_batch_size', array( $this, 'increase_queue_batch_size' ) );
		add_filter( 'action_scheduler_queue_runner_concurrent_batches', array( $this, 'increase_concurrent_batches' ), 10000 );
		add_filter( 'action_scheduler_timeout_period', array( $this, 'increase_timeout' ) );
		add_filter( 'action_scheduler_failure_period', array( $this, 'increase_timeout' ) );
		add_filter( 'action_scheduler_queue_runner_time_limit', array( $this, 'increase_time_limit' ) );
	}

	/**
	 * Add Admin Menu for the Plugin Page.
	 *
	 * @return void
	 */
	public function add_automation_menu() {

		add_submenu_page(
			'tools.php',
			__( 'Cleanup Action Scheduler Plugin', 'cleanup-action-scheduler' ),
			__( 'Cleanup Scheduler', 'cleanup-action-scheduler' ),
			'manage_options',
			'cleanup-as-plugin',
			array( $this, 'clean_plugin_function' )
		);

	}

	/**
	 * Settings Initialization for Dashboard.
	 *
	 * @return void
	 */
	public function clean_settings_init() {

		register_setting(
			'clean-setting',
			'clean_settings'
		);

		add_settings_section(
			'cleanup-as-plugin-section',
			__( 'Cleanup Action Scheduler', 'cleanup-action-scheduler' ),
			array( $this, 'clean_settings_section_callback' ),
			'clean-setting'
		);

		add_settings_field(
			'clean_duration',
			__( 'Duration', 'cleanup-action-scheduler' ),
			array( $this, 'clean_duration' ),
			'clean-setting',
			'cleanup-as-plugin-section'
		);

		add_settings_field(
			'queue_batch_size',
			__( 'Queue Batch Size', 'cleanup-action-scheduler' ),
			array( $this, 'set_queue_batch_size' ),
			'clean-setting',
			'cleanup-as-plugin-section'
		);

		add_settings_field(
			'concurrent_batches',
			__( 'Concurrent Batches', 'cleanup-action-scheduler' ),
			array( $this, 'set_concurrent_batches' ),
			'clean-setting',
			'cleanup-as-plugin-section'
		);

		add_settings_field(
			'timeout',
			__( 'Timeout', 'cleanup-action-scheduler' ),
			array( $this, 'set_timeout' ),
			'clean-setting',
			'cleanup-as-plugin-section'
		);

		add_settings_field(
			'time_limit',
			__( 'Time Limit', 'cleanup-action-scheduler' ),
			array( $this, 'set_time_limit' ),
			'clean-setting',
			'cleanup-as-plugin-section'
		);

	}

	/**
	 * Settings Section Callback.
	 *
	 * @return void
	 */
	public function clean_settings_section_callback() {

		printf(
			'<hr><h3>%s</h3>',
			esc_attr__( 'Schedule deletion of Completed Actions', 'cleanup-action-scheduler' )
		);

	}

	/**
	 * Set cleanup duration.
	 *
	 * @return void
	 */
	public function clean_duration() {

		$options = get_option( 'clean_settings' );
		?>
			<input type='number' min='1' name='clean_settings[duration]' value='<?php echo ! empty( $options['duration'] ) ? esc_attr( $options['duration'] ) : ''; ?>'/>
			<select name='clean_settings[period]'>
				<option value='SECONDS' <?php selected( $options['period'], 'SECONDS' ); ?>><?php esc_attr_e( 'Seconds', 'cleanup-action-scheduler' ); ?></option>
				<option value='HRS' <?php selected( $options['period'], 'HRS' ); ?>><?php esc_attr_e( 'Hours', 'cleanup-action-scheduler' ); ?></option>
				<option value='DAYS' <?php selected( $options['period'], 'DAYS' ); ?>><?php esc_attr_e( 'Days', 'cleanup-action-scheduler' ); ?></option>
			</select>
		<?php
			printf(
				'<p>%s</p>',
				esc_attr__( 'By default, Action Scheduler deletes completed actions every 30 days. Use this field to set a default actions delete duration.', 'cleanup-action-scheduler' )
			);
	}

	/**
	 * Increase the batch size.
	 *
	 * @return void
	 */
	public function set_queue_batch_size() {

		$options = get_option( 'clean_settings' );

		$queue_batch_size = ! empty( $options['queue_batch_size'] ) ? $options['queue_batch_size'] : '';
		?>
			<input type='number' min='25' name='clean_settings[queue_batch_size]' value='<?php echo esc_attr( $queue_batch_size ); ?>' placeholder="25"/>
		<?php
			printf(
				'<p>%s</p>',
				esc_attr__( 'By default, Action Scheduler claims a batch of 25 actions. This small batch size is because the default time limit is only 30 seconds. However, if your actions are processing very quickly use this field to increase the batch size.', 'cleanup-action-scheduler' )
			);
	}

	/**
	 * Incease number of concurrent batches if your server allows a large number of connections.
	 *
	 * @return void
	 */
	public function set_concurrent_batches() {

		$options = get_option( 'clean_settings' );
		?>
			<input type='number' min='1' name='clean_settings[concurrent_batches]' value='<?php echo ! empty( $options['concurrent_batches'] ) ? esc_attr( $options['concurrent_batches'] ) : ''; ?>' placeholder="1"/>
		<?php
			printf(
				'<p>%s</p><p>%s</p>',
				esc_attr__( 'By default, Action Scheduler will run only one concurrent batches of actions. This is to prevent consuming a lot of available connections or processes on your webserver.', 'cleanup-action-scheduler' ),
				esc_attr__( 'Incease number of concurrent batches if your server allows a large number of connections', 'cleanup-action-scheduler' ),
			);

	}

	/**
	 * Increase the amount of time given to queues before reseting claimed actions
	 *
	 * @return void
	 */
	public function set_timeout() {

		$options = get_option( 'clean_settings' );
		?>
			<input type='number' min='5' name='clean_settings[timeout]' value='<?php echo ! empty( $options['timeout'] ) ? esc_attr( $options['timeout'] ) : ''; ?>' placeholder="5"/>
		<?php
			printf(
				'<p>%s</p>',
				esc_attr__( 'By default Action scheduler reset actions claimed for more than 5 minutes (300 seconds). Because we are increasing the batch size, we also want to increase the amount of time given to queues before reseting claimed actions.', 'cleanup-action-scheduler' ),
			);

	}

	/**
	 * Increase this time limit allowing more actions to be processed in each request.
	 *
	 * @return void
	 */
	public function set_time_limit() {

		$options = get_option( 'clean_settings' );
		?>
			<input type='number' min='30' name='clean_settings[time_limit]' value='<?php echo ! empty( $options['time_limit'] ) ? esc_attr( $options['time_limit'] ) : ''; ?>' placeholder="30"/>
		<?php
			printf(
				'<p>%s</p><p>%s</p>',
				esc_attr__( 'By default, Action Scheduler will only process actions for a maximum of 30 seconds in each request. This time limit minimises the risk of a script timeout on unknown hosting environments, some of which enforce 30 second timeouts.', 'cleanup-action-scheduler' ),
				esc_attr__( 'If your host supports time limits longer than this for web requests, use this field to increase this time limit. This allows more actions to be processed in each request and reduces the lag between processing each queue, greatly speeding up the processing rate of scheduled actions.', 'cleanup-action-scheduler' ),
			);

	}
	/**
	 * Do admin page settings.
	 *
	 * @return void
	 */
	public function clean_plugin_function() {
		?>
		<form action='options.php' method='post'>
			<?php
				global $wpdb;
				settings_fields( 'clean-setting' );
				do_settings_sections( 'clean-setting' );
			?>
			<input type="submit" name="clean_row" class="button button-primary" value="<?php esc_attr_e( 'Save', 'cleanup-action-scheduler' ); ?>"/>
		</form>
		<?php
	}

	/**
	 * Time to change Filter cleanup by default to AS.
	 *
	 * @param string $time Time Period. Defaults to 30 days.
	 *
	 * @return string $time Time to change Filter.
	 */
	public function set_cleanup_time( $time ) {

		$options = get_option( 'clean_settings' );

		if ( ! empty( $options['duration'] ) ) {

			$duration = $options['duration'];
			$period   = $options['period'];

			switch ( $period ) {
				case 'HRS':
					$time = HOUR_IN_SECONDS * $duration;
					break;
				case 'DAYS':
					$time = DAY_IN_SECONDS * $duration;
					break;
				default:
					$time = $duration;
			}

			return $time;
		}

	}

	/**
	 * Increase number of actions to process in a queue
	 *
	 * @param int $batch_size Batch Size amount.
	 *
	 * @return int $batch_size Batch Size amount.
	 */
	public function increase_queue_batch_size( $batch_size ) {

		$options = get_option( 'clean_settings' );

		if ( ! empty( $options['batch_size'] ) ) {
			$batch_size = $batch_size * $options['batch_size'];
		}
		return $batch_size;
	}

	/**
	 * Action scheduler processes queues of actions in parallel to speed up the processing of large numbers.
	 * If each queue takes a long time, this will result in multiple PHP processes being used to process actions,
	 * which can prevent PHP processes being available to serve requests from visitors. This is why it defaults to
	 * only 1 (as of Action Scheduler 3.4.0). However, on high volume sites, this can be increased to speed up the
	 * processing time for actions.
	 *
	 * Use with caution as doing this can take down your site completely depending on your PHP configuration.
	 *
	 * For more details, see: https://actionscheduler.org/perf/#increasing-concurrent-batches
	 *
	 * @param int $concurrent_batches Actions in parallel to speed up the processing of large numbers.
	 *
	 * @return int $concurrent_batches Actions in parallel to speed up the processing of large numbers.
	 */
	public function increase_concurrent_batches( $concurrent_batches ) {

		$options = get_option( 'clean_settings' );

		if ( ! empty( $options['concurrent_batches'] ) ) {
			$concurrent_batches = $options['concurrent_batches'];
		}
		return $concurrent_batches;
	}

	/**
	 * Action scheduler reset actions claimed for more than 5 minutes (300 seconds).
	 * Because we're increasing the batch size, we also want to increase the amount of time
	 * given to queues before reseting claimed actions.
	 *
	 * @param int $timeout Amount of time given to queues before reseting claimed actions.
	 *
	 * @return int $timeout Amount of time given to queues before reseting claimed actions.
	 */
	public function increase_timeout( $timeout ) {

		$options = get_option( 'clean_settings' );

		if ( ! empty( $options['timeout'] ) ) {
			$timeout = $timeout * $options['timeout'];
		}
		return $timeout;
	}

	/**
	 * Action Scheduler provides a default maximum of 30 seconds in which to process actions.
	 * Increase this to 120 seconds for hosts like Pantheon which support such a long time limit.
	 * WP Engine only supports a maximum of 60 seconds.
	 * If you know your PHP and Apache, Nginx or other web server configs support a longer time limit.
	 *
	 * @param int $time_limit Maximum time in which to process actions.
	 *
	 * @return int $time_limit Maximum time in which to process actions.oid
	 */
	public function increase_time_limit( $time_limit ) {

		$options = get_option( 'clean_settings' );

		if ( ! empty( $options['time_limit'] ) ) {
			$time_limit = $options['time_limit'];
		}
		return $time_limit;
	}

}
