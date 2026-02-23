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
	 * Admin module instances.
	 *
	 * These objects handle admin functionality such as
	 * listing, creating, editing, and deleting records.
	 *
	 * @since    1.0.0
	 */
	private $category_admin;
	private $brand_admin;
	private $property_admin;

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

		// Require admin module classes
		require_once plugin_dir_path(__FILE__) . 'modules/category/class-aboyuma-product-category-admin.php';
		require_once plugin_dir_path(__FILE__) . 'modules/brand/class-aboyuma-product-brand-admin.php';
		require_once plugin_dir_path(__FILE__) . 'modules/product-property/class-aboyuma-product-property-admin.php';

		// Instantiate admin modules
		$this->category_admin = new Aboyuma_Product_Category_Admin();
		$this->brand_admin = new Aboyuma_Product_Brand_Admin();
		$this->property_admin = new Aboyuma_Product_Property_Admin();
	}

	/**
	 * Retrieve shared admin module instances.
	 *
	 * These getters expose the already-instantiated
	 * admin modules so they can be reused (e.g., for
	 * hook registration) without creating duplicates.
	 */

	/**
	 * Get Category Admin instance.
	 *
	 * @return Aboyuma_Product_Category_Admin
	 */
	public function get_category_admin() {
		return $this->category_admin;
	}

	/**
	 * Get Brand Admin instance.
	 *
	 * @return Aboyuma_Product_Brand_Admin
	 */
	public function get_brand_admin() {
		return $this->brand_admin;
	}

	/**
	 * Get Product Property Admin instance.
	 *
	 * @return Aboyuma_Product_Property_Admin
	 */
	public function get_property_admin() {
		return $this->property_admin;
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

		wp_localize_script(
			$this->plugin_name,
			'productCategoryDelete',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('aboyuma_product_category_delete'),
			]
		);

		wp_localize_script(
			$this->plugin_name,
			'productBrandDelete',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('aboyuma_product_brand_delete'),
			]
		);

		wp_localize_script(
			$this->plugin_name,
			'productPropertyDelete',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('aboyuma_product_property_delete'),
			]
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
		$capability = 'manage_options';
		$parent_slug = 'aboyuma-product-list';
		$icon = 'dashicons-products';
		$position = 25;

		/**
		 * ==============================
		 * MAIN MENU
		 * ==============================
		 * Important:
		 * Callback MUST match first submenu
		 */
		add_menu_page(
			'ABO YUMA Product Manager',
			'ABO YUMA Product Manager',
			$capability,
			$parent_slug,
			'aboyuma_product_list', // default page
			$icon,
			$position
		);

		/**
		 * ==============================
		 * 1 : PRODUCTS
		 * ==============================
		 */
		add_submenu_page(
			$parent_slug,
			'Products',
			'Products',
			$capability,
			$parent_slug,
			'aboyuma_product_list'
		);

		add_submenu_page(
			null,
			'Add Product',
			null,
			$capability,
			'aboyuma-product-add',
			'aboyuma_product_add'
		);

		add_submenu_page(
			null,
			'Edit Product',
			null,
			$capability,
			'aboyuma-product-edit',
			'aboyuma_product_edit'
		);

		/**
		 * ==============================
		 * 2 : PRODUCT PROPERTIES
		 * ==============================
		 */
		add_submenu_page(
			$parent_slug,
			'Product Properties',
			'Product Properties',
			$capability,
			'aboyuma-product-property-list',
			[$this->property_admin, 'property_list']
		);

		add_submenu_page(
			null,
			'Add Property',
			null,
			$capability,
			'aboyuma-product-property-add',
			[$this->property_admin, 'property_add']
		);

		add_submenu_page(
			null,
			'Edit Property',
			null,
			$capability,
			'aboyuma-product-property-edit',
			[$this->property_admin, 'property_edit']
		);

		/**
		 * ==============================
		 * 3 : CATEGORIES
		 * ==============================
		 */
		add_submenu_page(
			$parent_slug,
			'Categories',
			'Categories',
			$capability,
			'aboyuma-product-category-list',
			[$this->category_admin, 'category_list'] // Call Category Admin category_list method
		);

		add_submenu_page(
			null,
			'Add Category',
			null,
			$capability,
			'aboyuma-product-category-add',
			[$this->category_admin, 'category_add']
		);

		add_submenu_page(
			null,
			'Edit Category',
			null,
			$capability,
			'aboyuma-product-category-edit',
			[$this->category_admin, 'category_edit']
		);

		/**
		 * ==============================
		 * 4 : BRANDS
		 * ==============================
		 */
		add_submenu_page(
			$parent_slug,
			'Brands',
			'Brands',
			$capability,
			'aboyuma-product-brand-list',
			[$this->brand_admin, 'brand_list']
		);

		add_submenu_page(
			null,
			'Add Brand',
			null,
			$capability,
			'aboyuma-product-brand-add',
			[$this->brand_admin, 'brand_add']
		);

		add_submenu_page(
			null,
			'Edit Brand',
			null,
			$capability,
			'aboyuma-product-brand-edit',
			[$this->brand_admin, 'brand_edit']
		);
	}
}
