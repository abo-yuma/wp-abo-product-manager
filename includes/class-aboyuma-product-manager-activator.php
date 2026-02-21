<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://aboyuma.com
 * @since      1.0.0
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/includes
 * @author     Yuma <yuma.dev03@gmail.com>
 */
class Aboyuma_Product_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Create database
		require_once plugin_dir_path(__FILE__) . 'database/class-aboyuma-product-manager-database.php';
		Aboyuma_Product_Manager_Database::install();
	}
}
