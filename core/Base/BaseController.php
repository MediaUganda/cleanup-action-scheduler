<?php
/**
 * Base Controlller file that can be extended.
 *
 * @package Cleanup_Action_Scheduler
 */
namespace Cleanup_Action_Scheduler\Base;

class BaseController {
	
	/**
	 * Plugin directory path
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Plugin url
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	public $plugin;

	/**
	 * Run plugin constructor actions
	 */
	public function __construct() {
		$this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url  = plugin_dir_url( dirname( __FILE__, 2 ) );
		$this->plugin      = plugin_basename( dirname( __FILE__, 3 ) ) . '/cleanup-action-scheduler.php';
	}

}
