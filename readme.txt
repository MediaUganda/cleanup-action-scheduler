=== Cleanup Action Scheduler ===
Contributors: mediauganda, laurencebahiirwa, faithimokol
Donate link: https://paypal.me/laurencebahiirwa
Tags: woocommerce, action, scheduler, cleanup, cron, job, woocommerce
Requires at least: 4.9
Requires PHP: 7.0
Tested up to: 5.9.3
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Delete Action Scheduler Events to avoid having large database tables.

== Features: ==
* Delete button to remove all actions [Completed, Failed]. See screenshots for example.

== Frequently Asked Questions ==

= The Plugin does not work on my website =

* First, check the version of WordPress that you are using. The Plugin works with WordPress version 4.9 or later.

* Check if you have either the [Action Scheduler](https://wordpress.org/plugins/action-scheduler/) Plugin or 
[WooCommerrce](https://wordpress.org/plugins/woocommerce/) plugin installed and activated.
Deactivate the Plugin and activate it again.

== Screenshots ==
1. Pre Clean up database
2. Pre Clean up admin
3. Post Clean up database
4. Post Clean up admin

== Installation ==
1. Extract the downloaded zip file and upload the `cleanup-action-scheduler` folder to the `/wp-content/plugins/` directory. Alternatively, install this plugin by searching for it from the plugins area of your WordPress website.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

=== 1.2.1 ===

* Update: FS SDK to add new garbage collector and fix PHP 8+ errors.

=== 1.2.0 ===

* Important bug fixes.

=== 1.1.0 ===

* Added new enhancement settings to allow better usage of the Action Scheduler plugin performance.

=== 1.0.0 ===

* Initial Plugin.

### Features

#### Free Plugin
* Add delete icons for each state in admin area including pending actions..
* Add option to change default cleanup for the Action Scheduler.
* Advanced settings to allow better usage of the Action Scheduler plugin performance.

#### Premium Plugin
* All Free plugin features plus:
* Add WPCLI functionality to delete states.
* Deletion of pending actions.
* Add automated cleanup for selected action states.