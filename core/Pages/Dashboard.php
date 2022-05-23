<?php
/**
 * Add A Custom Dashboard Page.
 *
 * @package Cleanup_Action_Scheduler
 */
namespace Cleanup_Action_Scheduler\Pages;

use Cleanup_Action_Scheduler\Base\BaseController;

class Dashboard extends BaseController {

	/**
	 * Fire class in Init.php.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_automation_menu' ] );
		add_action( 'admin_init', [ $this, 'clean_settings_init' ] );
		add_filter( 'action_scheduler_retention_period', [ $this, 'set_cleanup_time' ] );
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
			[ $this, 'clean_plugin_function' ]
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
			[ $this, 'clean_settings_section_callback' ],
			'clean-setting'
		);

		add_settings_field(
			'clean_duration',
			__( 'Duration', 'cleanup-action-scheduler' ),
			[ $this, 'clean_duration' ],
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
	 * Undocumented function
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

	}

	/**
	 * Undocumented function
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

}
