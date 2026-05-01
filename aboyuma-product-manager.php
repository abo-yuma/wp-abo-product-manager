<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://aboyuma.com
 * @since             1.0.0
 * @package           Aboyuma_Product_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       ABO YUMA Product Manager
 * Plugin URI:        https://aboyuma.com
 * Description:       Product manager for ABO YUMA personal blog.
 * Version:           1.0.0
 * Author:            Yuma
 * Author URI:        https://aboyuma.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aboyuma-product-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ABOYUMA_PRODUCT_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aboyuma-product-manager-activator.php
 */
function activate_aboyuma_product_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aboyuma-product-manager-activator.php';
	Aboyuma_Product_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aboyuma-product-manager-deactivator.php
 */
function deactivate_aboyuma_product_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aboyuma-product-manager-deactivator.php';
	Aboyuma_Product_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aboyuma_product_manager' );
register_deactivation_hook( __FILE__, 'deactivate_aboyuma_product_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aboyuma-product-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aboyuma_product_manager() {

	$plugin = new Aboyuma_Product_Manager();
	$plugin->run();

}
run_aboyuma_product_manager();
