<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://aboyuma.com
 * @since      1.0.0
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin
 * @author     Yuma <yuma.dev03@gmail.com>
 */
class Aboyuma_Product_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if (! $this->is_allowed_admin_page()) {
			return;
		}

		// Bootstrap5 css
		wp_enqueue_style(
			'bootstrap-css',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
			array(),
			'5.3.2',
			'all' // Media type
		);

		// jQuery DataTables CSS
		wp_enqueue_style(
			'datatables-css',
			'https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css',
			array(),
			'1.13.8',
			'all'
		);

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if (! $this->is_allowed_admin_page()) {
			return;
		}

		// Bootstrap 5 Bundle (Includes Popper)
		wp_enqueue_script(
			'bootstrap-js',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
			array('jquery'),
			'5.3.2',
			true
		);

		// jQuery DataTables
		wp_enqueue_script(
			'datatables-js',
			'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js',
			array('jquery'),
			'1.13.8',
			true
		);

		// Main admin script
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-admin.js',
			array('jquery', 'bootstrap-js', 'datatables-js'),
			$this->version,
			false
		);
	}

	/**
	 * Check whether current admin page belongs to Aboyuma plugin.
	 *
	 * @return bool
	 */
	private function is_allowed_admin_page() {

		if (! isset($_GET['page'])) {
			return false;
		}

		$current_page = sanitize_key($_GET['page']);

		$allowed_pages = array(
			'aboyuma-product-list',
			'aboyuma-product-add',
			'aboyuma-product-detail',
			'aboyuma-product-edit',
			'aboyuma-product-property-list',
			'aboyuma-product-property-add',
			'aboyuma-product-property-edit',
			'aboyuma-product-brand-list',
			'aboyuma-product-brand-add',
			'aboyuma-product-brand-edit',
			'aboyuma-product-category-list',
			'aboyuma-product-category-add',
			'aboyuma-product-category-edit',
		);

		return in_array($current_page, $allowed_pages, true);
	}

	/**
	 * ------------------------------------------------------------
	 * Register Admin Menu Structure
	 * ------------------------------------------------------------
	 *
	 * This method registers the full admin menu hierarchy
	 * for the Aboyuma Product Manager plugin.
	 *
	 * Notes:
	 * - Hidden pages are registered using `add_submenu_page( null, ... )`
	 *   so they do not appear in the sidebar but remain accessible
	 *   via direct links.
	 *
	 * - WordPress automatically generates hook names based on:
	 *   {parent_slug}_page_{menu_slug}
	 *
	 * Example:
	 *   aboyuma-product-list_page_aboyuma-product-category-list
	 *
	 * - These hook names are used for conditional asset loading
	 *   inside admin_enqueue_scripts.
	 *
	 * @since 1.0.0
	 */
	public function aboyuma_product_menu() {
		$productPageTitle = 'Amway Products';
		$productPageMenuTitle = 'Amway Products';
		$productPageCapability = 'manage_options';
		$productPageMenuSlug = 'aboyuma-product-list';
		$productPageFunction = 'aboyuma_product_list';
		$productPageIconUrl = 'dashicons-products';
		$productPagePosition = 25;

		add_menu_page(
			$productPageTitle,
			$productPageMenuTitle,
			$productPageCapability,
			$productPageMenuSlug,
			$productPageFunction,
			$productPageIconUrl,
			$productPagePosition
		);

		add_submenu_page(
			$productPageMenuSlug,
			'Products', // Page title
			'Products', // Menu title
			$productPageCapability,
			$productPageMenuSlug, // Use the same slug as the main menu
			$productPageFunction
		);
		add_submenu_page(
			null,
			'Add New Product',
			null,
			$productPageCapability,
			'aboyuma-product-add',
			'aboyuma_product_add'
		);
		// Add submenu page without adding menu 
		// Ref:https://stackoverflow.com/a/12758400/8527193
		add_submenu_page(
			null,
			'Product Detail',
			null,
			$productPageCapability,
			'aboyuma-product-detail',
			'aboyuma_product_detail'
		);
		add_submenu_page(
			null,
			'Edit Product',
			null,
			$productPageCapability,
			'aboyuma-product-edit',
			'aboyuma_product_edit'
		);
		// ==============================
		// PRODUCT PROPERTIES
		// ==============================
		add_submenu_page(
			$productPageMenuSlug,
			'Product Properties',
			'Product Properties',
			$productPageCapability,
			'aboyuma-product-property-list',
			'aboyuma_product_property_list'
		);

		add_submenu_page(
			null,
			'Add New Property',
			null,
			$productPageCapability,
			'aboyuma-product-property-add',
			'aboyuma_product_property_add'
		);

		add_submenu_page(
			null,
			'Edit Property',
			null,
			$productPageCapability,
			'aboyuma-product-property-edit',
			'aboyuma_product_property_edit'
		);
		// ==============================
		// PRODUCT BRANDS
		// ==============================
		add_submenu_page(
			$productPageMenuSlug,
			'Brands',
			'Brands',
			$productPageCapability,
			'aboyuma-product-brand-list',
			'aboyuma_product_brand_list'
		);
		add_submenu_page(
			null,
			'Add New Brand',
			null,
			$productPageCapability,
			'aboyuma-product-brand-add',
			'aboyuma_product_brand_add'
		);
		add_submenu_page(
			null,
			'Edit Brand',
			null,
			$productPageCapability,
			'aboyuma-product-brand-edit',
			'aboyuma_product_brand_edit'
		);
		// ==============================
		// PRODUCT CATEGORIES
		// ==============================
		add_submenu_page(
			$productPageMenuSlug,
			'Categories',
			'Categories',
			$productPageCapability,
			'aboyuma-product-category-list',
			array($this, 'aboyuma_product_category_list')
		);
		add_submenu_page(
			null,
			'Add New Category',
			null,
			$productPageCapability,
			'aboyuma-product-category-add',
			'aboyuma_product_category_add'
		);
		add_submenu_page(
			null,
			'Edit Category',
			null,
			$productPageCapability,
			'aboyuma-product-category-edit',
			'aboyuma_product_category_edit'
		);
	}

	/**
	 * Register menu callback
	 */
	public function aboyuma_product_category_list() {
		require_once plugin_dir_path(__FILE__) . 'partials/template-product-category-list.php';
	}
}
