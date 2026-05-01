<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://aboyuma.com
 * @since      1.0.0
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/includes
 * @author     Yuma <yuma.dev03@gmail.com>
 */
class Aboyuma_Product_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aboyuma_Product_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('ABOYUMA_PRODUCT_MANAGER_VERSION')) {
			$this->version = ABOYUMA_PRODUCT_MANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'aboyuma-product-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aboyuma_Product_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Aboyuma_Product_Manager_i18n. Defines internationalization functionality.
	 * - Aboyuma_Product_Manager_Admin. Defines all hooks for the admin area.
	 * - Aboyuma_Product_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-aboyuma-product-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-aboyuma-product-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-aboyuma-product-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-aboyuma-product-manager-public.php';

		$this->loader = new Aboyuma_Product_Manager_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Aboyuma_Product_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Aboyuma_Product_Manager_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aboyuma_Product_Manager_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		/**
		 * Register Admin Menu (Page Rendering)
		 *
		 * Hooks into admin_menu to register plugin admin pages.
		 * Responsible only for page registration and rendering callbacks.
		 */
		$this->loader->add_action('admin_menu', $plugin_admin, 'aboyuma_product_menu');

		/**
		 * Admin Module – POST & AJAX Processing Hooks
		 *
		 * Registers form submission and AJAX handlers
		 * for admin CRUD operations across modules.
		 *
		 * - admin_post_*  → Handles Add/Edit form POST requests
		 * - wp_ajax_*     → Handles Delete via AJAX
		 *
		 * Note:
		 * Page rendering is handled separately via admin_menu callbacks.
		 */

		// Category
		$plugin_category_admin = $plugin_admin->get_category_admin();
		$this->loader->add_action(
			'admin_post_aboyuma_category_add',
			$plugin_category_admin,
			'handle_category_add_post'
		);
		$this->loader->add_action(
			'admin_post_aboyuma_category_edit',
			$plugin_category_admin,
			'handle_category_edit_post'
		);
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_category_delete',
			$plugin_category_admin,
			'category_delete_ajax_handler'
		);

		// Brand
		$plugin_brand_admin = $plugin_admin->get_brand_admin();
		$this->loader->add_action(
			'admin_post_aboyuma_brand_add',
			$plugin_brand_admin,
			'handle_brand_add_post'
		);
		$this->loader->add_action(
			'admin_post_aboyuma_brand_edit',
			$plugin_brand_admin,
			'handle_brand_edit_post'
		);
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_brand_delete',
			$plugin_brand_admin,
			'brand_delete_ajax_handler'
		);

		// Property
		$plugin_property_admin = $plugin_admin->get_property_admin();
		$this->loader->add_action(
			'admin_post_aboyuma_property_add',
			$plugin_property_admin,
			'handle_property_add_post'
		);
		$this->loader->add_action(
			'admin_post_aboyuma_property_edit',
			$plugin_property_admin,
			'handle_property_edit_post'
		);
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_property_delete',
			$plugin_property_admin,
			'property_delete_ajax_handler'
		);

		// Product
		$plugin_product_admin = $plugin_admin->get_product_admin();
		$this->loader->add_action(
			'wp_ajax_get_subcategories',
			$plugin_product_admin,
			'get_subcategories'
		);
		$this->loader->add_action(
			'admin_post_aboyuma_product_add',
			$plugin_product_admin,
			'handle_product_add_post'
		);
		$this->loader->add_action(
			'admin_post_aboyuma_product_edit',
			$plugin_product_admin,
			'handle_product_edit_post'
		);
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_delete',
			$plugin_product_admin,
			'product_delete_ajax_handler'
		);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aboyuma_Product_Manager_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_filter('query_vars', $plugin_public, 'register_query_vars');

		/**
		 * IMPORTANT: Execution order for virtual product pages
		 *
		 * We must register SEO metadata BEFORE the theme outputs <head>.
		 *
		 * Hook order in WordPress lifecycle:
		 *
		 * 1. template_redirect
		 *    - Runs after WordPress resolves the queried object
		 *    - Allows us to detect the virtual product page via query var (product_slug)
		 *    - Used to register:
		 *        • dynamic document title
		 *        • meta description
		 *        • canonical URL
		 *        • page title suppression
		 *
		 * 2. wp_head
		 *    - Triggered inside theme header.php
		 *    - Outputs SEO meta tags added in template_redirect
		 *
		 * 3. the_content
		 *    - Runs later when rendering page body
		 *    - Used to output product templates (list or detail)
		 *
		 * If SEO logic is placed inside the_content,
		 * it will execute too late because <head> has already been rendered.
		 *
		 * Therefore:
		 * template_redirect → prepare SEO
		 * the_content       → render UI
		 */
		$this->loader->add_action(
			'template_redirect',
			$plugin_public,
			'setup_product_detail_page'
		);

		/**
		 * Render product templates.
		 *
		 * This filter appends the product list template
		 * or replaces the content with the product detail template
		 * depending on the presence of product_slug.
		 */
		$this->loader->add_filter(
			'the_content',
			$plugin_public,
			'render_product_content'
		);

		$this->loader->add_action('after_setup_theme', $plugin_public, 'register_image_sizes');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aboyuma_Product_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
