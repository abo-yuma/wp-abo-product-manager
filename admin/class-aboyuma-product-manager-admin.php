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
	}

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
		/*
		if you do not add the add_submenu_page for "All Products," WordPress will automatically 
		create a submenu item for the main menu. This default submenu item will have the same title 
		as the main menu ("aboyuma Product" in your case) and will point to the same slug and callback function.
		Behavior Without the Submenu Code:
		WordPress automatically mirrors the main menu as the first submenu item.
		The submenu item will have:
		Menu title: "aboyuma Product" (same as the main menu title).
		Slug: The same slug as the main menu (aboyuma-product).
		Callback function: The same callback function as the main menu.
		*/
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



}
