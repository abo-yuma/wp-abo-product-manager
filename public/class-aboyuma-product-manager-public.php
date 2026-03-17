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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aboyuma-product-manager-public.css', array(), $this->version, 'all');

		// DataTables Bootstrap 5 CSS
		wp_enqueue_style(
			'aboyuma-datatables-bootstrap5',
			'https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css',
			array(),
			'2.2.2'
		);

		// FixedColumns Bootstrap 5 CSS
		wp_enqueue_style(
			'aboyuma-datatables-fixedcolumns-bootstrap5',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/css/fixedColumns.bootstrap5.css',
			array('aboyuma-datatables-bootstrap5'),
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
			'fontawesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
			array(),
			'6.5.1'
		);

		// SimpleLightbox CSS
		wp_enqueue_style(
			'simplelightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.3/simple-lightbox.min.css',
			[],
			'2.14.3'
		);

		// Fancybox
		wp_enqueue_style(
			'fancybox-css',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css',
			array(),
			'5.0'
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
			'simplelightbox',
			'https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.3/simple-lightbox.jquery.min.js',
			array('jquery'),
			'2.14.3',
			true
		);

		// Fancybox
		wp_enqueue_script(
			'fancybox-js',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js',
			array(),
			'5.0',
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
	 * Register custom query variables.
	 *
	 * WordPress only allows predefined query variables for security reasons.
	 * Any custom query var added via rewrite rules will be stripped out
	 * unless it is explicitly registered using the 'query_vars' filter.
	 *
	 * This method registers 'product_slug' so it can be accessed via:
	 * get_query_var( 'product_slug' )
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

				$canonical = home_url('/amway-products/?product_slug=' . $product->product_slug);
?>
				<meta name="description" content="<?php echo esc_attr($description); ?>">
				<link rel="canonical" href="<?php echo esc_url($canonical); ?>">
<?php
			}, 1); // priority ต่ำเพื่อให้โหลดก่อน plugin SEO


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
		$product_slug = get_query_var('product_slug');

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
