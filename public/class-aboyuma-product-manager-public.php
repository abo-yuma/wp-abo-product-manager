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
	 * WordPress database instance.
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Brand table name.
	 *
	 * @var string
	 */
	private $table_brand;

	/**
	 * Category table name.
	 *
	 * @var string
	 */
	private $table_category;

	/**
	 * Product table name.
	 *
	 * @var string
	 */
	private $table_product;

	/**
	 * Product image table name.
	 *
	 * @var string
	 */
	private $table_image;

	/**
	 * Product property table name.
	 *
	 * @var string
	 */
	private $table_property;

	/**
	 * Product property pivot table name.
	 *
	 * @var string
	 */
	private $table_property_pivot;

	/**
	 * Product tag table name.
	 *
	 * @var string
	 */
	private $table_tag;

	/**
	 * Product video table name.
	 *
	 * @var string
	 */
	private $table_video;

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
		$this->wpdb = $wpdb;

		$this->table_brand          = $wpdb->prefix . 'aboyuma_product_brand';
		$this->table_category       = $wpdb->prefix . 'aboyuma_product_category';
		$this->table_product        = $wpdb->prefix . 'aboyuma_product';
		$this->table_image          = $wpdb->prefix . 'aboyuma_product_image';
		$this->table_property       = $wpdb->prefix . 'aboyuma_product_property';
		$this->table_property_pivot = $wpdb->prefix . 'aboyuma_product_property_pivot';
		$this->table_tag            = $wpdb->prefix . 'aboyuma_product_tag';
		$this->table_video          = $wpdb->prefix . 'aboyuma_product_video';
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

		$page_id = (int) get_option('aboyuma_product_page_id');

		if (get_queried_object_id() != $page_id) {
			return;
		}

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-public.css', array(), $this->version, 'all');

		// DataTables Bootstrap 5 CSS
		wp_enqueue_style(
			'aboyuma-product-datatables-bootstrap5',
			'https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css',
			array(),
			'2.2.2'
		);

		// FixedColumns Bootstrap 5 CSS
		wp_enqueue_style(
			'aboyuma-product-datatables-fixedcolumns-bootstrap5',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/css/fixedColumns.bootstrap5.css',
			array('aboyuma-product-datatables-bootstrap5'),
			'5.0.4'
		);

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-public.css',
			array(),
			$this->version,
			'all'
		);

		// Font Awesome
		wp_enqueue_style(
			'aboyuma-product-fontawesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
			array(),
			'6.5.1'
		);

		// SimpleLightbox CSS
		wp_enqueue_style(
			'aboyuma-product-simplelightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.3/simple-lightbox.min.css',
			[],
			'2.14.3'
		);

		// Fancybox
		wp_enqueue_style(
			'aboyuma-product-fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6/dist/fancybox/fancybox.css',
			array(),
			'6.1.14'
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

		$page_id = (int) get_option('aboyuma_product_page_id');

		if (get_queried_object_id() != $page_id) {
			return;
		}

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-public.js', array('jquery'), $this->version, false);

		// DataTables Core
		wp_enqueue_script(
			'aboyuma-datatables-core',
			'https://cdn.datatables.net/2.2.2/js/dataTables.js',
			array('jquery'),
			'2.2.2',
			true
		);

		// DataTables Bootstrap 5 integration
		wp_enqueue_script(
			'aboyuma-datatables-bootstrap5',
			'https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js',
			array('aboyuma-datatables-core'),
			'2.2.2',
			true
		);

		// FixedColumns Core
		wp_enqueue_script(
			'aboyuma-datatables-fixedcolumns',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js',
			array('aboyuma-datatables-core'),
			'5.0.4',
			true
		);

		// FixedColumns Bootstrap 5 integration
		wp_enqueue_script(
			'aboyuma-datatables-fixedcolumns-bootstrap5',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.bootstrap5.js',
			array(
				'aboyuma-datatables-fixedcolumns',
				'aboyuma-datatables-bootstrap5'
			),
			'5.0.4',
			true
		);

		// SimpleLightbox JS
		wp_enqueue_script(
			'aboyuma-product-simplelightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.3/simple-lightbox.jquery.min.js',
			array('jquery'),
			'2.14.3',
			true
		);

		// Fancybox
		wp_enqueue_script(
			'aboyuma-product-fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6/dist/fancybox/fancybox.umd.js',
			array(),
			'6.1.14',
			true
		);

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/aboyuma-product-manager-public.js',
			array('jquery', 'datatables-bootstrap-js'),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'productSearch',
			[
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('aboyuma_product_search'),
			]
		);
	}

	/**
	 * Register custom query variables.
	 *
	 * WordPress only allows predefined query variables for security reasons.
	 * Any custom query var added via rewrite rules will be stripped out
	 * unless it is explicitly registered using the 'query_vars' filter.
	 *
	 * This method registers product slug so it can be accessed via:
	 * get_query_var( 'product' )
	 *
	 * @since 1.0.0
	 *
	 * @param array $vars Existing public query variables.
	 * @return array Modified array including 'product_slug'.
	 */
	public function register_query_vars($vars) {

		$vars[] = 'product';

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
	 * v1.0.0
	 * Product Page
	 *      -> get_products()
	 *      -> template-product-list.php
	 *
	 * Product Detail
	 *      -> get_product_relational_data()
	 *      -> template-product-detail.php
	 *
	 * v1.1.0
	 * Product Search
	 *      -> ajax_search_products()
	 *      -> get_products()
	 *      -> template-product-list-rows.php
	 *      -> JSON Response
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Original page content.
	 * @return string Modified content.
	 */
	public function render_product_content($content) {

		$page_id = (int) get_option('aboyuma_product_page_id');

		if (get_queried_object_id() !== $page_id) {
			return $content;
		}

		global $wpdb, $wp_query;
		$product_slug = sanitize_title(
			get_query_var('product')
		);

		/**
		 * Product list page
		 */
		if (!$product_slug) {

			$products = $this->get_products();
			// Prepare category list for search filter dropdown.
			$categories = $this->wpdb->get_results(
				"SELECT
					id,
					category_name,
					parent_id
				FROM {$this->table_category}
				ORDER BY parent_id ASC, category_name ASC
				"
			) ?: [];

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
		$product = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT 
				p.*,
				b.brand_name,
				c.category_name
				FROM {$this->table_product} p
				LEFT JOIN {$this->table_brand} b 
					ON p.product_brand_id = b.id
				LEFT JOIN {$this->table_category} c 
					ON p.product_category_id = c.id
				WHERE p.product_slug = %s
				LIMIT 1",
				$product_slug
			)
		);

		if ($product) {

			// TODO: Move relational logic to Repository layer later
			$relational_data = $this->get_product_relational_data($product->id);

			$selected_properties = $relational_data['selected_properties'];
			$gallery_images      = $relational_data['gallery_images'];
			$tags_string         = $relational_data['tags_string'];
			$videos              = $relational_data['videos'];

			global $post;

			$page_id = (int) get_option('aboyuma_product_page_id');
			$current_id = get_queried_object_id();

			if ($product_slug && $post && $current_id === $page_id) {
				$post->post_title = '';
			}

			/**
			 * Render template
			 */
			ob_start();
			require plugin_dir_path(__FILE__) . 'partials/template-product-detail.php';
			return ob_get_clean();
		} else {

			// Proper 404 handling
			$wp_query->set_404();
			status_header(404);
			nocache_headers();

			return '';
		}
	}

	/**
	 * AJAX product search handler.
	 *
	 * Search products by keyword, parent category,
	 * and subcategory from the public product
	 * search form.
	 *
	 * Returns rendered HTML table rows for
	 * updating the product list without
	 * reloading the page.
	 * 
	 * Flow:
	 * Search Form
	 *      -> ajax_search_products()
	 *      -> get_products()
	 *      -> template-product-list-rows.php
	 *      -> wp_send_json_success()
	 * 
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function ajax_search_products() {

		check_ajax_referer(
			'aboyuma_product_search',
			'nonce'
		);

		$keyword = sanitize_text_field(
			$_POST['keyword'] ?? ''
		);

		$parent_category_id = absint(
			$_POST['parent_category_id'] ?? 0
		);

		$subcategory_id = absint(
			$_POST['subcategory_id'] ?? 0
		);

		$products = $this->get_products(
			$keyword,
			$parent_category_id,
			$subcategory_id
		);

		// Render filtered product rows.
		ob_start();

		require plugin_dir_path(__FILE__) .
			'partials/template-product-list-rows.php';

		$html = ob_get_clean();

		wp_send_json_success([
			'html' => $html,
			'count' => count($products),
		]);
	}

	/**
	 * Get products for public listing.
	 *
	 * @since 1.1.0
	 *
	 * @param string $keyword Search keyword.
	 * @param int    $parent_category_id Parent category ID.
	 * @param int    $subcategory_id Subcategory ID.
	 * @return array
	 */
	private function get_products(
		string $keyword = '',
		int $parent_category_id = 0,
		int $subcategory_id = 0
	): array {

		$sql = "
			SELECT
				p.product_name,
				p.main_image_id,
				p.product_description,
				p.product_price,
				p.product_member_price,
				p.product_shop_link,
				p.product_slug,
				p.product_search_keywords,
				b.brand_name,
				c.id AS category_id,
				c.parent_id,
				c.category_name
			FROM {$this->table_product} p
			LEFT JOIN {$this->table_brand} b
				ON p.product_brand_id = b.id
			LEFT JOIN {$this->table_category} c
				ON p.product_category_id = c.id
			WHERE 1 = 1";

		$params = [];

		// Keyword filter
		if (!empty($keyword)) {

			$sql .= " AND (p.product_name LIKE %s OR p.product_search_keywords LIKE %s)";

			$like = '%' . $this->wpdb->esc_like($keyword) . '%';

			$params[] = $like;
			$params[] = $like;
		}

		// Subcategory filter
		if ($subcategory_id > 0) {

			$sql .= " AND p.product_category_id = %d";

			$params[] = $subcategory_id;
		}

		// Parent category filter
		elseif ($parent_category_id > 0) {

			$sql .= " AND (p.product_category_id = %d OR c.parent_id = %d)";

			$params[] = $parent_category_id;
			$params[] = $parent_category_id;
		}

		$sql .= " ORDER BY p.product_name ASC";

		if (!empty($params)) {

			return $this->wpdb->get_results(
				$this->wpdb->prepare($sql, $params)
			) ?: [];
		}

		return $this->wpdb->get_results($sql) ?: [];
	}

	/**
	 * Get relational data specific to a product.
	 * 
	 * NOTE:
	 * Temporarily placed in the Public class.
	 * This duplicates similar logic in the product module and
	 * should be extracted into a dedicated Repository layer.
	 *
	 * @since  1.0.0
	 *
	 * @param int $product_id Product ID.
	 * @return array
	 */
	private function get_product_relational_data($product_id) {

		// Selected properties.
		$selected_properties = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT 
				p.property_key,
				p.property_name,
                p.property_type, 
				p.property_description
				FROM {$this->table_property_pivot} pp
				INNER JOIN {$this->table_property} p
				ON p.id = pp.property_id
				WHERE pp.product_id = %d
				ORDER BY FIELD(p.property_type, 'diet', 'program'), p.property_key",
				$product_id
			)
		) ?: array();

		// Gallery images (normalize to int array)
		$gallery_rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT attachment_id, image_order
         FROM {$this->table_image}
         WHERE product_id = %d
         ORDER BY image_order ASC",
				$product_id
			)
		) ?: array();

		$gallery_images = array_map(
			function ($row) {
				return absint($row->attachment_id);
			},
			$gallery_rows
		);

		// Tags.
		$tags = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT tag_name
             FROM {$this->table_tag}
             WHERE product_id = %d",
				$product_id
			)
		) ?: array();

		$tags_string = implode(',', $tags);

		// Videos.
		$videos = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT video_url, video_comment
             FROM {$this->table_video}
             WHERE product_id = %d",
				$product_id
			)
		) ?: array();

		return array(
			'selected_properties' => $selected_properties,
			'gallery_images'      => $gallery_images,
			'tags_string'         => $tags_string,
			'videos'              => $videos,
		);
	}

	/**
	 * Generate product page URL.
	 *
	 * Supports both:
	 * - Plain permalinks
	 * - Pretty permalinks
	 *
	 * Avoid hardcoding page slugs or URL structures.
	 *
	 * @since 1.0.1
	 *
	 * @param string $product_slug Product slug.
	 * @return string
	 */
	private function get_product_url($product_slug) {

		$page_id = (int) get_option('aboyuma_product_page_id');

		return add_query_arg(
			'product',
			$product_slug,
			get_permalink($page_id)
		);
	}

	/**
	 * Register custom image sizes for product display.
	 *
	 * This image size is used for rendering the main product image.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function register_image_sizes() {

		add_image_size(
			'product-400',
			400,
			400,
			false // Soft crop (maintain aspect ratio).
		);
	}

	/**
	 * Prepare product detail page context.
	 *
	 * This method runs on `template_redirect` and prepares
	 * the environment before the theme template is loaded.
	 *
	 * When a product_slug query variable is present and
	 * the current page matches the registered product page,
	 * the default page title will be suppressed.
	 *
	 * This ensures that the theme does not render
	 * the original page title (e.g., "Amway Products")
	 * on single product detail views.
	 *
	 * ---------------------------------------------------------------------
	 * Execution Flow:
	 *
	 * Plugin Load
	 *     ↓
	 * Loader registers hook (template_redirect)
	 *     ↓
	 * WordPress fires template_redirect
	 *     ↓
	 * setup_product_detail_page()
	 *     ↓
	 * add_filter('the_title')
	 *     ↓
	 * Theme renders template
	 *     ↓
	 * the_title() outputs empty string
	 * ---------------------------------------------------------------------
	 *
	 * Hooked to: template_redirect
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_product_detail_page() {

		// Ensure we are on a page request.
		if (!is_page()) {
			return;
		}

		// Retrieve product slug from query string.
		$product_slug = sanitize_title(
			get_query_var('product')
		);

		// Retrieve stored product page ID.
		$page_id = (int) get_option('aboyuma_product_page_id');

		// Stop if this is not a product detail request.
		if (empty($product_slug)) {
			return;
		}

		// Ensure the current queried page matches the product page.
		if (get_queried_object_id() !== $page_id) {
			return;
		}

		/**
		 * LOAD PRODUCT
		 */
		$product = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT product_name, product_description, product_slug
			FROM {$this->table_product}
			WHERE product_slug = %s
			LIMIT 1",
				$product_slug
			)
		);

		if (!$product) {
			return;
		}

		/**
		 * SEO title adjustment
		 */
		add_filter('document_title_parts', function ($title) use ($product) {
			$title['title'] = $product->product_name;
			return $title;
		});

		/**
		 * [ADDED] SEO meta description + canonical
		 */
		add_action('wp_head', function () use ($product) {

			$description = $product->product_name . ' - ' .
				wp_trim_words($product->product_description, 25, '');

			$canonical = $this->get_product_url($product->product_slug);

			printf(
				'<meta name="description" content="%s">' . "\n" .
					'<link rel="canonical" href="%s">' . "\n",
				esc_attr($description),
				esc_url($canonical)
			);
		}, 1); // Low priority to load before SEO plugin

		/**
		 * Suppress the default page title.
		 *
		 * The theme calls the_title() before the_content(),
		 * so we attach this filter early via template_redirect.
		 */
		add_filter(
			'the_title',
			function ($title, $post_id) use ($page_id) {

				if ($post_id === $page_id) {
					return '';
				}

				return $title;
			},
			99,
			2
		);
	}
}
