<?php

/**
 * Product Admin functionality.
 *
 * Handles product admin :
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin/modules/product
 */
if (! defined('ABSPATH')) {
    exit; // Prevent direct access.
}

class Aboyuma_Product_Admin {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'aboyuma_product';
    }

    /**
     * Product List Page
     *
     * @since 1.0.0
     */
    public function product_list() {

        // Retrieve flash message (shared add/edit/delete)
        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        $products = $this->wpdb->get_results(
            "
        SELECT
            id,
            main_image_id,
            product_description,
            created,
            modified
        FROM {$this->table}
        ORDER BY id DESC
        "
        );

        if (! is_array($products)) {
            $products = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-list.php';
    }

    /**
     * Product Add Page
     *
     * Responsible ONLY for:
     * - Fetching required data
     * - Rendering template
     *
     * @since 1.0.0
     */
    public function product_add() {

        // Flash message
        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        // Fetch brands
        $brand_table = $this->wpdb->prefix . 'aboyuma_product_brand';

        $brands = $this->wpdb->get_results(
            "SELECT id, brand_name 
         FROM {$brand_table}
         ORDER BY brand_name ASC"
        );

        $brands = is_array($brands) ? $brands : [];

        // Fetch categories
        $category_table = $this->wpdb->prefix . 'aboyuma_product_category';

        $categories = $this->wpdb->get_results(
            "SELECT id, category_name, parent_id 
         FROM {$category_table}
         ORDER BY parent_id ASC, category_name ASC"
        );

        $categories = is_array($categories) ? $categories : [];

        // Fetch properties
        $property_table = $this->wpdb->prefix . 'aboyuma_product_property';

        $properties = $this->wpdb->get_results(
            "SELECT id, property_key, property_name, property_type
         FROM {$property_table}
         ORDER BY property_type ASC, property_name ASC"
        );

        $properties = is_array($properties) ? $properties : [];

        require plugin_dir_path(__FILE__) . 'partials/template-product-add.php';
    }

    /**
     * Handle Product Add POST
     *
     * @since 1.0.0
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

        global $wpdb;

        $table = $wpdb->prefix . 'aboyuma_product';

        $data   = $this->sanitize_product($_POST);
        $errors = $this->validate_product($data, $table);

        if (! empty($errors)) {
            Aboyuma_Flash_Helper::redirect(
                'error',
                implode(' ', $errors),
                admin_url('admin.php?page=aboyuma-product-add')
            );
        }

        $wpdb->query('START TRANSACTION');

        $inserted = $wpdb->insert(
            $table,
            [
                'main_image_id'            => $data['main_image_id'],
                'product_code'             => $data['product_code'],
                'product_brand_id'         => $data['product_brand_id'] ?: null,
                'product_category_id'      => $data['product_category_id'] ?: null,
                'product_name'             => $data['product_name'],
                'product_unit_count'       => $data['product_unit_count'],
                'product_unit_type'        => $data['product_unit_type'],
                'product_unit_usage'       => $data['product_unit_usage'],
                'product_short_description' => $data['product_short_description'],
                'product_description'      => $data['product_description'],
                'product_benefit'          => $data['product_benefit'],
                'product_detail'           => $data['product_detail'],
                'product_specification'    => $data['product_specification'],
                'product_comment'          => $data['product_comment'],
                'product_review'           => $data['product_review'],
                'product_price'            => number_format($data['product_price'], 2, '.', ''),
                'product_member_price'     => number_format($data['product_member_price'], 2, '.', ''),
                'product_shop_link'        => $data['product_shop_link'],
                'product_sop_link'         => $data['product_sop_link'],
                'product_sale_article_link' => $data['product_sale_article_link'],
                'product_is_pinned'        => $data['product_is_pinned'],
                'product_slug'             => $data['product_slug'],
                'created'                  => current_time('mysql'),
                'modified'                 => current_time('mysql'),
            ],
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
                '%s',
                '%s',
                '%f',
                '%f',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s'
            ]
        );

        if (! $inserted) {
            $wpdb->query('ROLLBACK');

            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $wpdb->last_error,
                admin_url('admin.php?page=aboyuma-product-add')
            );
        }

        $product_id = $wpdb->insert_id;

        $relations_inserted = $this->insert_product_relations($product_id, $_POST);

        if ($relations_inserted === false) {
            $wpdb->query('ROLLBACK');

            Aboyuma_Flash_Helper::redirect(
                'error',
                'Failed to insert product relations.',
                admin_url('admin.php?page=aboyuma-product-add')
            );
        }

        $wpdb->query('COMMIT');

        Aboyuma_Flash_Helper::redirect(
            'success',
            'Product added successfully.',
            admin_url('admin.php?page=aboyuma-product-list')
        );
    }

    /**
     * Insert product relations:
     * - Properties
     * - Gallery
     * - Tags
     * - Videos
     */
    private function insert_product_relations($product_id, $post) {

        global $wpdb;

        // Insert product properties
        if (! empty($post['product_properties'])) {

            $pivot_table = $wpdb->prefix . 'aboyuma_product_property_pivot';
            $def_table   = $wpdb->prefix . 'aboyuma_product_property';

            foreach ($post['product_properties'] as $key) {

                $property = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT id FROM {$def_table} WHERE property_key = %s",
                        sanitize_text_field($key)
                    )
                );

                if ($property) {

                    $result = $wpdb->query(
                        $wpdb->prepare(
                            "INSERT IGNORE INTO {$pivot_table}
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

            $gallery_table = $wpdb->prefix . 'aboyuma_product_image';

            foreach ($post['gallery_image_ids'] as $index => $attachment_id) {

                $result = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT IGNORE INTO {$gallery_table}
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

            $tag_table = $wpdb->prefix . 'aboyuma_product_tag';

            $tags = array_filter(
                array_map(
                    'trim',
                    explode(',', sanitize_text_field($post['product_tags']))
                )
            );

            foreach ($tags as $tag) {

                $result = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT IGNORE INTO {$tag_table}
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

            $video_table = $wpdb->prefix . 'aboyuma_product_video';

            foreach ($post['video_datas'] as $video) {

                $video_url     = esc_url_raw($video['video_url'] ?? '');
                $video_comment = sanitize_text_field($video['video_comment'] ?? '');

                if ($video_url) {

                    $inserted = $wpdb->insert(
                        $video_table,
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

    public function sanitize_product($post) {

        $allowed_types  = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
        $allowed_usage  = ['content', 'capacity'];

        $parent_category      = isset($post['parent_category']) ? absint($post['parent_category']) : 0;
        $product_subcategory  = isset($post['product_subcategory']) ? absint($post['product_subcategory']) : 0;

        return [
            'main_image_id' => isset($post['main_image_id']) ? absint($post['main_image_id']) : 0,
            'product_code'  => sanitize_text_field(trim($post['product_code'] ?? '')),
            'product_brand_id' => isset($post['product_brand']) ? absint($post['product_brand']) : null,
            'product_category_id' => $product_subcategory ?: $parent_category,
            'product_name' => sanitize_text_field(trim($post['product_name'] ?? '')),
            'product_unit_count' => isset($post['product_unit_count']) ? absint($post['product_unit_count']) : null,
            'product_unit_type' => in_array($post['product_unit_type'] ?? '', $allowed_types)
                ? sanitize_text_field($post['product_unit_type'])
                : null,
            'product_unit_usage' => in_array($post['product_unit_usage'] ?? '', $allowed_usage)
                ? sanitize_text_field($post['product_unit_usage'])
                : 'content',
            'product_short_description' => wp_kses_post($post['product_short_description'] ?? ''),
            'product_description' => wp_kses_post($post['product_description'] ?? ''),
            'product_benefit' => wp_kses_post($post['product_benefit'] ?? ''),
            'product_detail' => wp_kses_post($post['product_detail'] ?? ''),
            'product_specification' => wp_kses_post($post['product_specification'] ?? ''),
            'product_comment' => wp_kses_post($post['product_comment'] ?? ''),
            'product_review' => wp_kses_post($post['product_review'] ?? ''),
            'product_price' => isset($post['product_price']) ? floatval($post['product_price']) : 0,
            'product_member_price' => isset($post['product_member_price']) ? floatval($post['product_member_price']) : 0,
            'product_shop_link' => esc_url_raw($post['product_shop_link'] ?? ''),
            'product_sop_link' => !empty($post['product_sop_link']) ? esc_url_raw($post['product_sop_link']) : null,
            'product_sale_article_link' => !empty($post['product_sale_article_link']) ? esc_url_raw($post['product_sale_article_link']) : null,
            'product_is_pinned' => isset($post['product_is_pinned']) ? intval($post['product_is_pinned']) : 0,
            'product_slug' => sanitize_title($post['product_slug'] ?? ''),
        ];
    }

    public function validate_product($data, $table_name, $is_edit = false, $current_id = 0) {

        global $wpdb;

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
            'Short description' => $product_short_description,
            'Description'       => $product_description,
            'Benefit'           => $product_benefit,
            'Detail'            => $product_detail,
            'Specification'     => $product_specification,
            'Comment'           => $product_comment,
            'Review'            => $product_review,
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

        // Force test error
        $errors[] = 'Test validation error.';

        return $errors;
    }

    public function get_subcategories() {
        $parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT id, category_name FROM {$this->wpdb->prefix}aboyuma_product_category WHERE parent_id = %d", $parent_id)
        );
        wp_send_json($results);
    }
}
