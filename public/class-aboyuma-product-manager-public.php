<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://aboyuma.com
 * @since      1.0.0
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/public
 * @author     Yuma <yuma.dev03@gmail.com>
 */
class Aboyuma_Product_Manager_Public {

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
	 * Product table name.
	 *
	 * @var string
	 */
	private $table_product;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		global $wpdb;
		$this->table_product = $wpdb->prefix . 'aboyuma_product';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-public.css', array(), $this->version, 'all');

		// DataTables Bootstrap 5 CSS
		wp_enqueue_style(
			'datatables-bootstrap-css',
			'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css',
			array(),
			'1.13.8'
		);

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-public.js', array('jquery'), $this->version, false);

		// DataTables core
		wp_enqueue_script(
			'datatables-js',
			'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js',
			array('jquery'),
			'1.13.8',
			true
		);

		// DataTables Bootstrap 5 integration
		wp_enqueue_script(
			'datatables-bootstrap-js',
			'https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js',
			array('datatables-js'),
			'1.13.8',
			true
		);

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-public.js',
			array('jquery', 'datatables-bootstrap-js'),
			$this->version,
			true
		);
	}

	/**
	 * Register custom rewrite rule for single product page.
	 *
	 * Create virtual URLs that map to custom query vars.
	 *
	 * Resulting URLs:
	 *
	 * /amway-products       → Product list page
	 * /amway-product/double-x  → Single product detail page
	 *
	 * The request is routed through WordPress front controller (index.php)
	 * so it can be parsed by WP_Query and template loader.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_product_page_rewrite_rule() {

		/**
		 * Single Product Route (Detail Page)
		 *
		 * Matches:
		 *   /amway-products/{product-slug}
		 *
		 * Example:
		 *   /amway-products/double-x
		 *
		 * Captures the slug and passes it to:
		 *   amway_product_slug
		 *
		 * We will use this slug to query the custom product table.
		 */
		add_rewrite_rule(
			'^amway-products/([^/]+)/?$',
			'index.php?pagename=amway-products&product_slug=$matches[1]',
			'top'
		);
	}

	/**
	 * Register custom query variables.
	 *
	 * WordPress only allows predefined query variables for security reasons.
	 * Any custom query var added via rewrite rules will be stripped out
	 * unless it is explicitly registered using the 'query_vars' filter.
	 *
	 * This method registers 'product_slug' so it can be accessed via:
	 * get_query_var( 'product_slug' )
	 *
	 * Required because our custom rewrite rule maps:
	 * /amway-products/{product-slug}/
	 * to:
	 * index.php?pagename=amway-products&product_slug={product-slug}
	 *
	 * @since 1.0.0
	 *
	 * @param array $vars Existing public query variables.
	 * @return array Modified array including 'product_slug'.
	 */
	public function register_query_vars($vars) {

		$vars[] = 'product_slug';

		return $vars;
	}

	/**
	 * Render product templates on the public-facing side.
	 *
	 * For the product list page, the template is appended
	 * after the original page content.
	 *
	 * For the single product detail (virtual page),
	 * display template only.
	 * 
	 * @since 1.0.0
	 *
	 * @param string $content Original page content.
	 * @return string Modified content.
	 */
	public function render_product_content($content) {

		if (!is_page('amway-products')) {
			return $content;
		}

		global $wpdb, $wp_query;
		$product_slug = get_query_var('product_slug');

		/**
		 * Product list page
		 */
		if (!$product_slug) {

			$products = $wpdb->get_results(
				"SELECT * FROM {$this->table_product} ORDER BY id DESC"
			);

			if (!empty($products)) {

				ob_start();
				require plugin_dir_path(__FILE__) . 'partials/template-product-list.php';
				$products_html = ob_get_clean();

				return $content . $products_html;
			}

			return $content;
		}

		/**
		 * Product detail page (has product_slug)
		 */
		$product = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_product} WHERE product_slug = %s LIMIT 1",
				$product_slug
			)
		);

		if ($product) {
			ob_start();
			require plugin_dir_path(__FILE__) . 'partials/template-product-detail.php';
			return ob_get_clean();
		} else {
			$wp_query->set_404();
			status_header(404);
			nocache_headers();
			return '';
		}
	}
}
