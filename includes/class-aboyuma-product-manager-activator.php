<?php

/**
 * Fired during plugin activation
 *
 * @link       https://aboyuma.com
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
		/**
		 * Create database
		 */
		require_once plugin_dir_path(__FILE__) . 'database/class-aboyuma-product-manager-database.php';
		Aboyuma_Product_Manager_Database::install();

		/**
		 * Create product listing page
		 */
		$product_page = get_page_by_path('amway-products');

		if (! $product_page) {

			$product_page_id = wp_insert_post(array(
				'post_title'   => 'Amway Products',
				'post_name'    => 'amway-products',
				'post_content' => 'Product list page',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1, // User ID 1 (typically the first administrator)
			));
			/**
			 * Store the page ID in wp_options so the plugin
			 * can reliably reference this page later.
			 *
			 * We store the ID instead of the slug because:
			 * - ID is immutable
			 * - Slug can be changed by admin
			 * - Avoid extra DB lookups with get_page_by_path()
			 */
			if ($product_page_id && ! is_wp_error($product_page_id)) {
				update_option('aboyuma_product_page_id', $product_page_id);
			}
		} else {
			update_option('aboyuma_product_page_id', $product_page->ID);
		}

		flush_rewrite_rules();
	}
}
