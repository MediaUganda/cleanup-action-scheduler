<?php

/**
 * Files to enqueue on sitewide level.
 *
 * @package Cleanup_Action_Scheduler
 */
namespace Cleanup_Action_Scheduler\Base;

use  Cleanup_Action_Scheduler\Base\BaseController ;
class Enqueue extends BaseController
{
    /**
     * Run Enqueue Actions
     *
     * @return void
     */
    public function register()
    {
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
    }
    
    /**
     * Enqueue Backend Scripts and Styles.
     *
     * @return void
     */
    public function admin_scripts()
    {
        
        if ( 'tools_page_action-scheduler' === get_current_screen()->id ) {
            wp_enqueue_style(
                'admin-styles',
                $this->plugin_url . 'assets/build/css/admin.css',
                array(),
                '0.1.0',
                'all'
            );
            wp_enqueue_script(
                'admin-script',
                $this->plugin_url . 'assets/build/js/admin.js',
                array( 'jquery' ),
                '0.1.0',
                true
            );
            wp_localize_script( 'admin-script', 'cas_params', array(
                'ajax_url'         => admin_url( 'admin-ajax.php' ),
                'cas_delete_nonce' => wp_create_nonce( 'cas_delete_nonce' ),
            ) );
        }
    
    }

}