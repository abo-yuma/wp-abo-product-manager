<?php

/**
 * Brand Admin functionality.
 *
 * Handles Brand admin pages:
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Admin/Product/Brand
 */

if (! defined('ABSPATH')) {
    exit; // Prevent direct access.
}

class Aboyuma_Product_Brand_Admin {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'aboyuma_product_brand';

        // AJAX delete hook
        add_action('wp_ajax_aboyuma_product_brand_delete', [$this, 'brand_delete_ajax_handler']);
    }

    /**
     * Brand List Page
     *
     * @since 1.0.0
     */
    public function brand_list() {

        // Success message after redirect
        if (isset($_GET['message']) && $_GET['message'] === 'added') {
            echo '<div class="notice notice-success is-dismissible">
                <p>Brand added successfully!</p>
              </div>';
        }

        $brands = $this->wpdb->get_results(
            "
        SELECT 
            id,
            brand_name,
            brand_image_url,
            created,
            modified
        FROM {$this->table}
        ORDER BY id DESC
        "
        );

        if (! is_array($brands)) {
            $brands = array();
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-list.php';
    }

    /**
     * Brand Add Page
     *
     * @since 1.0.0
     */
    public function brand_add() {

        $errors          = [];
        $brand_name      = '';
        $brand_image_url = '';

        // Handle POST
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['aboyuma_amway_product_brand_add_nonce']) &&
            wp_verify_nonce(
                $_POST['aboyuma_amway_product_brand_add_nonce'],
                'aboyuma_amway_product_brand_add'
            )
        ) {

            $brand_name = isset($_POST['brand_name'])
                ? sanitize_text_field(trim($_POST['brand_name']))
                : '';

            $brand_image_url = isset($_POST['brand_image_url'])
                ? esc_url_raw(trim($_POST['brand_image_url']))
                : '';

            // Validation
            if (empty($brand_name)) {
                $errors[] = 'Brand name is required.';
            }

            if (empty($brand_image_url)) {
                $errors[] = 'Brand image URL is required.';
            }

            if (empty($errors)) {

                $inserted = $this->wpdb->insert(
                    $this->table,
                    [
                        'brand_name'      => $brand_name,
                        'brand_image_url' => $brand_image_url,
                        'created'         => current_time('mysql'),
                        'modified'        => current_time('mysql'),
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ]
                );

                if ($inserted) {

                    echo '<script>
                    window.location.href = "' . esc_url(
                        admin_url('admin.php?page=aboyuma-product-brand-list&message=added')
                    ) . '";
                </script>';
                    exit;
                } else {

                    $errors[] = 'Database error: ' . $this->wpdb->last_error;
                }
            }
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-add.php';
    }

    /**
     * Brand Edit Page
     *
     * @since 1.0.0
     */
    public function brand_edit() {

        $errors          = [];
        $brand_id        = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $brand_name      = '';
        $brand_image_url = '';

        if (!$brand_id) {
            wp_die('Invalid brand ID.');
        }

        // Get existing brand
        $brand = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $brand_id
            )
        );

        if (!$brand) {
            wp_die('Brand not found.');
        }

        // Default values for form
        $brand_name      = $brand->brand_name;
        $brand_image_url = $brand->brand_image_url;

        // Handle POST
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['aboyuma_amway_product_brand_edit_nonce']) &&
            wp_verify_nonce(
                $_POST['aboyuma_amway_product_brand_edit_nonce'],
                'aboyuma_amway_product_brand_edit'
            )
        ) {

            $brand_name = isset($_POST['brand_name'])
                ? sanitize_text_field(trim($_POST['brand_name']))
                : '';

            $brand_image_url = isset($_POST['brand_image_url'])
                ? esc_url_raw(trim($_POST['brand_image_url']))
                : '';

            // Validation
            if (empty($brand_name)) {
                $errors[] = 'Brand name is required.';
            }

            if (empty($brand_image_url)) {
                $errors[] = 'Brand image URL is required.';
            }

            if (empty($errors)) {

                $updated = $this->wpdb->update(
                    $this->table,
                    [
                        'brand_name'      => $brand_name,
                        'brand_image_url' => $brand_image_url,
                        'modified'        => current_time('mysql'),
                    ],
                    ['id' => $brand_id],
                    [
                        '%s',
                        '%s',
                        '%s',
                    ],
                    ['%d']
                );

                if ($updated !== false) {

                    echo '<script>
                        window.location.href = "' . esc_url(
                        admin_url('admin.php?page=aboyuma-product-brand-list&updated-success=true')
                    ) . '";
                    </script>';
                    exit;
                } else {
                    $errors[] = 'Database error: ' . $this->wpdb->last_error;
                }
            }
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-edit.php';
    }

    /**
     * Brand Delete Operation (AJAX)
     *
     * @since 1.0.0
     */
    public function brand_delete_ajax_handler() {

        check_ajax_referer('aboyuma_product_brand_delete', 'nonce');

        $brand_id = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if (! $brand_id) {
            wp_send_json_error('Invalid brand ID.');
        }

        $deleted = $this->wpdb->delete(
            $this->table,
            ['id' => $brand_id],
            ['%d']
        );

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(
                'Failed to delete the brand. Please try again.'
            );
        }
    }
}
