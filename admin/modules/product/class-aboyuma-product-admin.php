<?php

/**
 * Product Admin functionality.
 *
 * Handles Product admin operations:
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin/modules/product
 * @since      1.0.0
 */
if (! defined('ABSPATH')) {
    exit; // Prevent direct access.
}

/**
 * Class Aboyuma_Product_Admin
 *
 * Responsible for managing Product administration:
 * - Rendering admin pages
 * - Handling form submissions
 * - Performing database operations
 *
 * @since 1.0.0
 */
class Aboyuma_Product_Admin {

    /**
     * WordPress database object.
     *
     * @var wpdb
     */
    private $wpdb;

    /**
     * Product table name.
     *
     * @var string
     */
    private $table_product;

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
     * Property table name.
     *
     * @var string
     */
    private $table_property;

    /**
     * Property pivot table name.
     *
     * @var string
     */
    private $table_property_pivot;

    /**
     * Product image table name.
     *
     * @var string
     */
    private $table_image;

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
     * Constructor.
     *
     * Initializes database connection and table names.
     *
     * @since 1.0.0
     */
    public function __construct() {

        global $wpdb;
        $this->wpdb  = $wpdb;

        $this->table_product         = $wpdb->prefix . 'aboyuma_product';
        $this->table_brand           = $wpdb->prefix . 'aboyuma_product_brand';
        $this->table_category        = $wpdb->prefix . 'aboyuma_product_category';
        $this->table_property        = $wpdb->prefix . 'aboyuma_product_property';
        $this->table_property_pivot  = $wpdb->prefix . 'aboyuma_product_property_pivot';
        $this->table_image           = $wpdb->prefix . 'aboyuma_product_image';
        $this->table_tag             = $wpdb->prefix . 'aboyuma_product_tag';
        $this->table_video           = $wpdb->prefix . 'aboyuma_product_video';
    }

    /**
     * Render Product List page.
     *
     * Retrieves products and loads the list template.
     *
     * @since  1.0.0
     * @return void
     */
    public function product_list() {

        $products = $this->wpdb->get_results(
            "
        SELECT
            id,
            main_image_id,
            product_description,
            created,
            modified
        FROM {$this->table_product}
        ORDER BY id DESC
        "
        );

        if (! is_array($products)) {
            $products = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-list.php';
    }

    /**
     * Render Product Add page.
     *
     * Responsible for:
     * - Fetching required relational data
     * - Rendering the add template
     *
     * @since  1.0.0
     * @return void
     */
    public function product_add() {

        // Fetch relational form data Categories, Brands, Properties
        $relational_data = $this->get_form_relational_data();

        $brands     = $relational_data['brands'];
        $categories = $relational_data['categories'];
        $properties = $relational_data['properties'];

        require plugin_dir_path(__FILE__) . 'partials/template-product-add.php';
    }

    /**
     * Handle Product Add form submission.
     *
     * - Verifies nonce
     * - Sanitizes input
     * - Validates data
     * - Inserts product and related data
     * - Performs database transaction
     *
     * @since  1.0.0
     * @return void
     */
    public function handle_product_add_post() {

        if (
            ! isset($_POST['aboyuma_product_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_add_nonce'],
                'aboyuma_product_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $data   = $this->sanitize_product($_POST);
        $errors = $this->validate_product($data);

        if (!empty($errors)) {
            $error_string = implode(' | ', $errors);

            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-add&status=error&message=' .
                        urlencode($error_string)
                )
            );
            exit;
        }

        $this->wpdb->query('START TRANSACTION');

        $inserted = $this->wpdb->insert(
            $this->table_product,
            [
                'main_image_id'            => $data['main_image_id'],
                'product_code'             => $data['product_code'],
                'product_brand_id'         => $data['product_brand_id'] ?: null,
                'product_category_id'      => $data['product_category_id'] ?: null,
                'product_name'             => $data['product_name'],
                'product_unit_count'       => $data['product_unit_count'],
                'product_unit_type'        => $data['product_unit_type'],
                'product_unit_usage'       => $data['product_unit_usage'],
                'product_description'      => $data['product_description'],
                'product_detail'           => $data['product_detail'],
                'product_specification'    => $data['product_specification'],
                'product_comment'          => $data['product_comment'],
                'product_review'           => $data['product_review'],
                'product_price'            => number_format($data['product_price'], 2, '.', ''),
                'product_member_price'     => number_format($data['product_member_price'], 2, '.', ''),
                'product_shop_link'        => $data['product_shop_link'],
                'product_sale_article_link' => $data['product_sale_article_link'],
                'product_is_pinned'        => $data['product_is_pinned'],
                'product_slug'             => $data['product_slug'],
                'created'                  => current_time('mysql'),
                'modified'                 => current_time('mysql'),
            ],
            [
                '%d', // main_image_id
                '%s', // product_code
                '%d', // product_brand_id
                '%d', // product_category_id
                '%s', // product_name
                '%d', // product_unit_count
                '%s', // product_unit_type
                '%s', // product_unit_usage
                '%s', // product_description
                '%s', // product_detail
                '%s', // product_specification
                '%s', // product_comment
                '%s', // product_review
                '%f', // product_price
                '%f', // product_member_price
                '%s', // product_shop_link
                '%s', // product_sale_article_link
                '%d', // product_is_pinned
                '%s', // product_slug
                '%s', // created
                '%s', // modified
            ]
        );

        if (! $inserted) {
            $this->wpdb->query('ROLLBACK');
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-add&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        $inserted_product_id = $this->wpdb->insert_id;

        $relations_inserted = $this->insert_product_relations($inserted_product_id, $_POST);

        if ($relations_inserted === false) {
            $this->wpdb->query('ROLLBACK');
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-add&status=error&message=' .
                        urlencode('Failed to insert product relations.')
                )
            );
            exit;
        }

        $this->wpdb->query('COMMIT');

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-list&status=success&message=' .
                    urlencode('Product added successfully.')
            )
        );
        exit;
    }

    /**
     * Render Product Edit page.
     *
     * Responsible for:
     * - Validating product ID
     * - Fetching product data
     * - Fetching relational data
     * - Rendering the edit template
     *
     * @since  1.0.0
     * @return void
     */
    public function product_edit() {

        // Validate product ID
        $product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;

        if (!$product_id) {
            wp_die(__('Invalid product ID.', 'aboyuma-product-manager'));
        }

        // Fetch product
        $product = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_product} WHERE id = %d",
                $product_id
            )
        );

        if (!$product) {
            wp_die(__('Product not found.', 'aboyuma-product-manager'));
        }

        // Fetch relational form data Categories, Brands, Properties
        $relational_data = $this->get_form_relational_data();

        // Brands, Categories, Properties
        $brands     = $relational_data['brands'];
        $categories = $relational_data['categories'];
        $properties = $relational_data['properties'];

        // Fetch product relational data
        $product_relational = $this->get_product_relational_data($product_id);

        // Product-specific relational data
        $selected_properties = $product_relational['selected_properties'];
        $gallery_images = $product_relational['gallery_images'];
        $tags_string = $product_relational['tags_string'];
        $videos = $product_relational['videos'];

        require plugin_dir_path(__FILE__) . 'partials/template-product-edit.php';
    }

    /**
     * Handle Product Edit form submission.
     *
     * - Verifies nonce
     * - Validates product ID
     * - Sanitizes input
     * - Updates product
     * - Resets relational data (delete + rebuild)
     * - Uses transaction
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_product_edit_post() {

        // Nonce
        if (
            ! isset($_POST['aboyuma_product_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_edit_nonce'],
                'aboyuma_product_edit'
            )
        ) {
            wp_die('Security check failed.');
        }

        // Validate product ID
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

        if (! $product_id) {
            wp_die('Invalid product ID.');
        }

        // Sanitize + Validate
        $data   = $this->sanitize_product($_POST);
        $errors = $this->validate_product($data);

        if (!empty($errors)) {
            $error_string = implode(' | ', $errors);

            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-edit&product_id=' . $product_id .
                        '&status=error&message=' . urlencode($error_string)
                )
            );
            exit;
        }

        $this->wpdb->query('START TRANSACTION');

        // UPDATE product
        $updated = $this->wpdb->update(
            $this->table_product,
            [
                'main_image_id'            => $data['main_image_id'],
                'product_code'             => $data['product_code'],
                'product_brand_id'         => $data['product_brand_id'] ?: null,
                'product_category_id'      => $data['product_category_id'] ?: null,
                'product_name'             => $data['product_name'],
                'product_unit_count'       => $data['product_unit_count'],
                'product_unit_type'        => $data['product_unit_type'],
                'product_unit_usage'       => $data['product_unit_usage'],
                'product_description'      => $data['product_description'],
                'product_detail'           => $data['product_detail'],
                'product_specification'    => $data['product_specification'],
                'product_comment'          => $data['product_comment'],
                'product_review'           => $data['product_review'],
                'product_price'            => number_format($data['product_price'], 2, '.', ''),
                'product_member_price'     => number_format($data['product_member_price'], 2, '.', ''),
                'product_shop_link'        => $data['product_shop_link'],
                'product_sale_article_link' => $data['product_sale_article_link'],
                'product_is_pinned'        => $data['product_is_pinned'],
                'product_slug'             => $data['product_slug'],
                'modified'                 => current_time('mysql'),
            ],
            ['id' => $product_id],
            [
                '%d',
                '%s',
                '%d',
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
                '%f',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s'
            ],
            ['%d']
        );

        if ($updated === false) {
            $this->wpdb->query('ROLLBACK');
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-edit&product_id=' . $product_id .
                        '&status=error&message=' . urlencode('Database update failed.')
                )
            );
            exit;
        }

        // RESET relations
        $deleted = $this->delete_product_relations($product_id);

        if (! $deleted) {
            $this->wpdb->query('ROLLBACK');
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-edit&product_id=' . $product_id .
                        '&status=error&message=' . urlencode('Failed to reset relations.')
                )
            );
            exit;
        }

        // Rebuild relations
        $relations_inserted = $this->insert_product_relations($product_id, $_POST);

        if ($relations_inserted === false) {
            $this->wpdb->query('ROLLBACK');
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-edit&product_id=' . $product_id .
                        '&status=error&message=' . urlencode('Failed to rebuild relations.')
                )
            );
            exit;
        }

        $this->wpdb->query('COMMIT');

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-list&status=success&message=' .
                    urlencode('Product updated successfully.')
            )
        );
        exit;
    }

    /**
     * Product Delete Operation
     *
     * @since 1.0.0
     */
    public function product_delete_ajax_handler() {

        check_ajax_referer('aboyuma_product_delete', 'nonce');

        $product_id = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if (! $product_id) {
            wp_send_json_error('Invalid product ID.');
        }

        $deleted = $this->wpdb->delete(
            $this->table_product,
            ['id' => $product_id],
            ['%d']
        );

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(
                'Failed to delete the product. Please try again.'
            );
        }
    }

    /**
     * Get relational data for product form.
     *
     * Used for dropdowns and selectable fields.
     *
     * @since  1.0.0
     *
     * @return array
     */
    private function get_form_relational_data() {

        $brands = $this->wpdb->get_results(
            "SELECT id, brand_name
         FROM {$this->table_brand}
         ORDER BY brand_name ASC"
        ) ?: array();

        $categories = $this->wpdb->get_results(
            "SELECT id, category_name, parent_id
         FROM {$this->table_category}
         ORDER BY parent_id ASC, category_name ASC"
        ) ?: array();

        $properties = $this->wpdb->get_results(
            "SELECT id, property_key, property_name, property_type
         FROM {$this->table_property}
         ORDER BY property_type ASC, property_name ASC"
        ) ?: array();

        return array(
            'brands'     => $brands,
            'categories' => $categories,
            'properties' => $properties,
        );
    }

    /**
     * Get relational data specific to a product.
     *
     * @since  1.0.0
     *
     * @param int $product_id Product ID.
     * @return array
     */
    private function get_product_relational_data($product_id) {

        // Selected properties.
        $selected_properties = $this->wpdb->get_col(
            $this->wpdb->prepare(
                "SELECT p.property_key
             FROM {$this->table_property_pivot} pp
             INNER JOIN {$this->table_property} p
             ON p.id = pp.property_id
             WHERE pp.product_id = %d",
                $product_id
            )
        ) ?: array();

        // Gallery images (normalize to int array)
        $gallery_rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT attachment_id
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
     * Insert product relational data.
     *
     * Inserts:
     * - Properties
     * - Gallery images
     * - Tags
     * - Videos
     *
     * @since  1.0.0
     *
     * @param int   $product_id Product ID.
     * @param array $post       Raw POST data.
     *
     * @return bool True on success, false on failure.
     */
    private function insert_product_relations($product_id, $post) {

        // Insert product properties
        if (! empty($post['product_properties'])) {

            foreach ($post['product_properties'] as $key) {

                $property = $this->wpdb->get_row(
                    $this->wpdb->prepare(
                        "SELECT id FROM {$this->table_property} WHERE property_key = %s",
                        sanitize_text_field($key)
                    )
                );

                if ($property) {

                    $result = $this->wpdb->query(
                        $this->wpdb->prepare(
                            "INSERT INTO {$this->table_property_pivot}
                        (product_id, property_id)
                        VALUES (%d, %d)",
                            $product_id,
                            $property->id
                        )
                    );

                    if ($result === false) {
                        return false;
                    }
                }
            }
        }

        // Insert gallery images
        if (! empty($post['gallery_image_ids'])) {

            foreach ($post['gallery_image_ids'] as $index => $attachment_id) {

                $result = $this->wpdb->query(
                    $this->wpdb->prepare(
                        "INSERT INTO {$this->table_image}
                    (product_id, attachment_id, image_order)
                    VALUES (%d, %d, %d)",
                        $product_id,
                        absint($attachment_id),
                        intval($index)
                    )
                );

                if ($result === false) {
                    return false;
                }
            }
        }

        // Insert product tags
        if (! empty($post['product_tags'])) {

            $tags = array_filter(
                array_map(
                    'trim',
                    explode(',', sanitize_text_field($post['product_tags']))
                )
            );

            foreach ($tags as $tag) {

                $result = $this->wpdb->query(
                    $this->wpdb->prepare(
                        "INSERT INTO {$this->table_tag}
                    (product_id, tag_name)
                    VALUES (%d, %s)",
                        $product_id,
                        $tag
                    )
                );

                if ($result === false) {
                    return false;
                }
            }
        }

        // Insert related videos
        if (! empty($post['video_datas'])) {

            foreach ($post['video_datas'] as $video) {

                $video_url     = esc_url_raw($video['video_url'] ?? '');
                $video_comment = sanitize_text_field($video['video_comment'] ?? '');

                if ($video_url) {

                    $inserted = $this->wpdb->insert(
                        $this->table_video,
                        [
                            'product_id'   => $product_id,
                            'video_url'    => $video_url,
                            'video_comment' => $video_comment
                        ],
                        ['%d', '%s', '%s']
                    );

                    if (! $inserted) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Delete all product relational data.
     *
     * Deletes:
     * - Properties (pivot)
     * - Gallery images
     * - Tags
     * - Videos
     *
     * This method is used before rebuilding relations
     * during product update (reset strategy).
     *
     * @since  1.0.0
     *
     * @param int $product_id Product ID.
     *
     * @return bool True on success, false on failure.
     */
    private function delete_product_relations($product_id) {

        $product_id = absint($product_id);

        // Delete property relations (pivot table)
        $deleted = $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table_property_pivot}
             WHERE product_id = %d",
                $product_id
            )
        );

        if ($deleted === false) {
            return false;
        }

        // Delete gallery images
        $deleted = $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table_image}
             WHERE product_id = %d",
                $product_id
            )
        );

        if ($deleted === false) {
            return false;
        }

        // Delete tags
        $deleted = $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table_tag}
             WHERE product_id = %d",
                $product_id
            )
        );

        if ($deleted === false) {
            return false;
        }

        // Delete videos
        $deleted = $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table_video}
             WHERE product_id = %d",
                $product_id
            )
        );

        if ($deleted === false) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize product form data.
     *
     * @since  1.0.0
     *
     * @param array $post Raw POST data.
     *
     * @return array Sanitized product data.
     */
    public function sanitize_product($post) {

        $allowed_types = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
        $allowed_usage = ['content', 'capacity'];

        $parent_category     = absint($post['parent_category'] ?? 0);
        $product_subcategory = absint($post['product_subcategory'] ?? 0);

        $unit_type_raw  = sanitize_text_field($post['product_unit_type'] ?? '');
        $unit_usage_raw = sanitize_text_field($post['product_unit_usage'] ?? '');

        return [
            'main_image_id'        => absint($post['main_image_id'] ?? 0),
            'product_code'         => sanitize_text_field(trim($post['product_code'] ?? '')),
            'product_brand_id'     => absint($post['product_brand'] ?? 0) ?: null,
            'product_category_id'  => $product_subcategory ?: $parent_category,
            'product_name'         => sanitize_text_field(trim($post['product_name'] ?? '')),
            'product_unit_count'   => absint($post['product_unit_count'] ?? 0) ?: null,

            'product_unit_type' => in_array($unit_type_raw, $allowed_types, true)
                ? $unit_type_raw
                : null,

            'product_unit_usage' => in_array($unit_usage_raw, $allowed_usage, true)
                ? $unit_usage_raw
                : 'content',

            'product_description'       => wp_kses_post($post['product_description'] ?? ''),
            'product_detail'            => wp_kses_post($post['product_detail'] ?? ''),
            'product_specification'     => wp_kses_post($post['product_specification'] ?? ''),
            'product_comment'           => wp_kses_post($post['product_comment'] ?? ''),
            'product_review'            => wp_kses_post($post['product_review'] ?? ''),

            'product_price'        => (float) ($post['product_price'] ?? 0),
            'product_member_price' => (float) ($post['product_member_price'] ?? 0),

            'product_shop_link'          => esc_url_raw($post['product_shop_link'] ?? ''),
            'product_sale_article_link'  => ! empty($post['product_sale_article_link']) ? esc_url_raw($post['product_sale_article_link']) : null,

            'product_is_pinned' => ! empty($post['product_is_pinned']) ? 1 : 0,

            'product_slug' => sanitize_title($post['product_slug'] ?? ''),
        ];
    }

    /**
     * Validate sanitized product data.
     *
     * @since  1.0.0
     *
     * @param array $data Sanitized product data.
     *
     * @return array List of validation error messages.
     */
    public function validate_product($data) {

        $errors = [];

        extract($data);

        $required = [
            'Main image'   => $main_image_id,
            'Product code' => $product_code,
            'Brand'        => $product_brand_id,
            'Category'     => $product_category_id,
            'Product name' => $product_name,
            'Slug'         => $product_slug,
            'Unit type'    => $product_unit_type,
            'Shop link'    => $product_shop_link,
        ];

        foreach ($required as $label => $value) {
            if (empty($value)) {
                $errors[] = "$label required.";
            }
        }

        $rich_required = [
            'Description'       => $product_description,
            'Detail'            => $product_detail,
            'Specification'     => $product_specification,
        ];

        foreach ($rich_required as $label => $value) {
            if (empty(wp_strip_all_tags($value))) {
                $errors[] = "$label required.";
            }
        }

        if (!$product_unit_count || $product_unit_count <= 0) {
            $errors[] = 'Unit count must be greater than 0.';
        }

        if ($product_price < 0 || $product_member_price < 0) {
            $errors[] = 'Invalid price.';
        }

        if ($product_member_price > $product_price) {
            $errors[] = 'Member price cannot exceed normal price.';
        }

        if ($exists > 0) {
            $errors[] = 'Slug or product code already exists.';
        }

        return $errors;
    }

    /**
     * AJAX handler: Get subcategories by parent ID.
     *
     * @since  1.0.0
     * @return void
     */
    public function get_subcategories() {
        $parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT id, category_name FROM {$this->wpdb->prefix}aboyuma_product_category WHERE parent_id = %d", $parent_id)
        );
        wp_send_json($results);
    }
}
