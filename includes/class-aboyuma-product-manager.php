<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://aboyuma.com
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
		 * ABO YUMA Custom Flash helper
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/helpers/class-aboyuma-flash-helper.php';

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

		// Get category admin instance from manager admin instance
		$category_admin = $plugin_admin->get_category_admin();

		/**
		 * Category Add POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_category_add',
			$category_admin,
			'handle_category_add_post'
		);

		/**
		 * Category Edit POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_category_edit',
			$category_admin,
			'handle_category_edit_post'
		);

		/**
		 * Category Delete AJAX
		 */
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_category_delete',
			$category_admin,
			'category_delete_ajax_handler'
		);

		// Retrieve shared Brand Admin instance
		$brand_admin = $plugin_admin->get_brand_admin();

		/**
		 * Brand Add POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_brand_add',
			$brand_admin,
			'handle_brand_add_post'
		);

		/**
		 * Brand Edit POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_brand_edit',
			$brand_admin,
			'handle_brand_edit_post'
		);

		/**
		 * Brand Delete AJAX
		 */
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_brand_delete',
			$brand_admin,
			'brand_delete_ajax_handler'
		);

		// Retrieve shared Property Admin instance
		$property_admin = $plugin_admin->get_property_admin();

		/**
		 * Property Add POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_product_property_add',
			$property_admin,
			'handle_property_add_post'
		);

		/**
		 * Property Edit POST
		 */
		$this->loader->add_action(
			'admin_post_aboyuma_product_property_edit',
			$property_admin,
			'handle_property_edit_post'
		);

		/**
		 * Property Delete AJAX
		 */
		$this->loader->add_action(
			'wp_ajax_aboyuma_product_property_delete',
			$property_admin,
			'property_delete_ajax_handler'
		);

		// Add admin menu action
		$this->loader->add_action('admin_menu', $plugin_admin, 'aboyuma_product_menu');
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
