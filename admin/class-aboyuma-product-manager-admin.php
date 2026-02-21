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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aboyuma_Product_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aboyuma_Product_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aboyuma_Product_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aboyuma_Product_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-admin.js', array('jquery'), $this->version, false);

		if (! isset($_GET['page'])) {
			return;
		}

		$current_page = sanitize_key($_GET['page']);

		/**
		 * ==========================================================
		 * GLOBAL ASSETS (Shared Across All ABOYUMA Admin Pages)
		 * ==========================================================
		 */

		// WordPress Media Library (image uploader)
		wp_enqueue_media();

		// Drag & Drop Sorting
		wp_enqueue_script('jquery-ui-sortable');

		// Shared Media Handler (Add/Edit)
		wp_enqueue_script(
			'aboyuma-product-media',
			plugin_dir_url(__FILE__) . 'js/aboyuma-product-media.js',
			array('jquery'),
			$this->version,
			false
		);

		wp_localize_script(
			'aboyuma-product-media',
			'aboyumaProductMedia',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('aboyuma_product_media_nonce'),
			)
		);

		/**
		 * ==========================================================
		 * PRODUCT SECTION
		 * ==========================================================
		 * Pages:
		 * - Add Product
		 * - Edit Product
		 */

		if (
			$current_page === 'aboyuma-product-add' ||
			$current_page === 'aboyuma-product-edit'
		) {

			wp_enqueue_style(
				'aboyuma-product-admin',
				plugin_dir_url(__FILE__) . 'css/aboyuma-product-admin.css',
				array(),
				$this->version,
				'all'
			);

			wp_enqueue_script(
				'aboyuma-product-admin',
				plugin_dir_url(__FILE__) . 'js/aboyuma-product-admin.js',
				array('jquery', 'aboyuma-product-media'),
				$this->version,
				false
			);
		}

		/**
		 * ==========================================================
		 * PRODUCT CATEGORY SECTION
		 * ==========================================================
		 * Pages:
		 * - Category List
		 */

		if ($current_page === 'aboyuma-product-category-list') {

			wp_enqueue_script(
				'aboyuma-product-category-delete',
				plugin_dir_url(__FILE__) . 'js/aboyuma-product-category-delete.js',
				array('jquery'),
				$this->version,
				false
			);

			wp_localize_script(
				'aboyuma-product-category-delete',
				'aboyumaProductCategoryDelete',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce'   => wp_create_nonce('aboyuma_product_category_delete_nonce')
				)
			);
		}

		/**
		 * ==========================================================
		 * PRODUCT PROPERTY SECTION
		 * ==========================================================
		 * Pages:
		 * - Property List
		 */

		if ($current_page === 'aboyuma-product-property-list') {

			wp_enqueue_script(
				'aboyuma-product-property-delete',
				plugin_dir_url(__FILE__) . 'js/aboyuma-product-property-delete.js',
				array('jquery'),
				$this->version,
				false
			);

			wp_localize_script(
				'aboyuma-product-property-delete',
				'aboyumaProductPropertyDelete',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce'   => wp_create_nonce('aboyuma_product_property_delete_nonce'),
				)
			);
		}

		/**
		 * ==========================================================
		 * PRODUCT BRAND SECTION
		 * ==========================================================
		 * Pages:
		 * - Brand List
		 * - Add Brand
		 * - Edit Brand
		 *
		 * (Ready for future implementation)
		 */

		if ($current_page === 'aboyuma-product-brand-list') {

			wp_enqueue_script(
				'aboyuma-product-brand-delete',
				plugin_dir_url(__FILE__) . 'js/aboyuma-product-brand-delete.js',
				array('jquery'),
				$this->version,
				false
			);

			wp_localize_script(
				'aboyuma-product-brand-delete',
				'aboyumaProductBrandDelete',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce'   => wp_create_nonce('aboyuma_product_brand_delete_nonce'),
				)
			);
		}
	}

	/**
	 * ------------------------------------------------------------
	 * Register Admin Menu Structure
	 * ------------------------------------------------------------
	 *
	 * This method registers the full admin menu hierarchy
	 * for the Aboyuma Product Manager plugin.
	 *
	 * MENU STRUCTURE OVERVIEW:
	 *
	 * Amway Products (Top-Level Menu)
	 * ├── Products (List Page - same slug as parent)
	 * ├── Product Properties (List Page)
	 * ├── Brands (List Page)
	 * ├── Categories (List Page)
	 *
	 * Hidden Pages (Not visible in sidebar menu):
	 * ├── Add Product
	 * ├── Product Detail
	 * ├── Edit Product
	 * ├── Add Property
	 * ├── Edit Property
	 * ├── Add Brand
	 * ├── Edit Brand
	 * ├── Add Category
	 * ├── Edit Category
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
			'aboyuma_product_category_list'
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
	 * ==============================================
	 * AJAX: Load Subcategories
	 * ==============================================
	 */
	public function get_subcategories() {

		global $wpdb;

		$parent_id = isset($_GET['parent_id'])
			? intval($_GET['parent_id'])
			: 0;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, category_name
                 FROM {$wpdb->prefix}aboyuma_amway_product_category
                 WHERE parent_id = %d",
				$parent_id
			)
		);

		wp_send_json($results);
	}
}
