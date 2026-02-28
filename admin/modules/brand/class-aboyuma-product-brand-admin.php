<?php

/**
 * Product Brand Admin functionality.
 *
 * Handles product brand admin :
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin/modules/brand
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
    }

    /**
     * Brand List Page
     *
     * @since 1.0.0
     */
    public function brand_list() {

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
            $brands = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-list.php';
    }

    /**
     * Brand Add Page
     *
     * @since 1.0.0
     */
    public function brand_add() {

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-add.php';
    }

    /**
     * Handle Brand Add POST
     *
     * @since 1.0.0
     */
    public function handle_brand_add_post() {

        if (
            ! isset($_POST['aboyuma_product_brand_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_brand_add_nonce'],
                'aboyuma_product_brand_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $brand_name = sanitize_text_field($_POST['brand_name'] ?? '');
        $brand_image_url = esc_url_raw($_POST['brand_image_url'] ?? '');

        // Validation (Guard Clause)
        if ($brand_name === '') {
            wp_safe_redirect(
                admin_url('admin.php?page=aboyuma-product-brand-add&status=error&message='
                    . urlencode('Brand name is required.'))
            );
            exit;
        }

        if ($brand_image_url === '') {
            wp_safe_redirect(
                admin_url('admin.php?page=aboyuma-product-brand-add&status=error&message='
                    . urlencode('Brand image URL is required.'))
            );
            exit;
        }

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

        if ($inserted === false) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-brand-add&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url('admin.php?page=aboyuma-product-brand-list&status=success&message='
                . urlencode('Brand added successfully.'))
        );
        exit;
    }

    /**
     * Brand Edit Page
     *
     * @since 1.0.0
     */
    public function brand_edit() {

        $brand_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

        if (! $brand_id) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Invalid brand ID.</p>
        </div>';
            return;
        }

        // Get existing brand
        $brands = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $brand_id
            )
        );

        if (! $brands) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Brand not found.</p>
        </div>';
            return;
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-edit.php';
    }

    /**
     * Handle Brand Edit POST
     *
     * @since 1.0.0
     */
    public function handle_brand_edit_post() {

        if (
            ! isset($_POST['aboyuma_product_brand_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_brand_edit_nonce'],
                'aboyuma_product_brand_edit'
            )
        ) {
            wp_die('Security check failed.');
        }

        $brand_id = isset($_POST['brand_id'])
            ? absint($_POST['brand_id'])
            : 0;

        if (! $brand_id) {
            wp_die('Invalid brand ID.');
        }

        $brand_name      = sanitize_text_field($_POST['brand_name'] ?? '');
        $brand_image_url = esc_url_raw($_POST['brand_image_url'] ?? '');

        // Validation
        if ($brand_name === '') {
            wp_safe_redirect(
                admin_url('admin.php?page=aboyuma-product-brand-edit&status=error&message='
                    . urlencode('Brand name is required.'))
            );
            exit;
        }

        if ($brand_image_url === '') {
            wp_safe_redirect(
                admin_url('admin.php?page=aboyuma-product-brand-edit&status=error&message='
                    . urlencode('Brand image URL is required.'))
            );
            exit;
        }

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

        if ($updated === false) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-brand-edit&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url('admin.php?page=aboyuma-product-brand-list&status=success&message='
                . urlencode('Brand updated successfully.'))
        );
        exit;
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
