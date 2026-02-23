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

        // Retrieve notice (shared for add/edit/delete)
        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
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

        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-add.php';
    }

    /**
     * Handle Brand Add POST
     *
     * @since 1.0.0
     */
    public function handle_brand_add_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_brand_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_brand_add_nonce'],
                'aboyuma_amway_product_brand_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $brand_name = sanitize_text_field($_POST['brand_name'] ?? '');
        $brand_image_url = esc_url_raw($_POST['brand_image_url'] ?? '');

        // Validation (Guard Clause)
        if ($brand_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Brand name is required.',
                admin_url('admin.php?page=aboyuma-product-brand-add')
            );
        }

        if ($brand_image_url === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Brand image URL is required.',
                admin_url('admin.php?page=aboyuma-product-brand-add')
            );
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
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $this->wpdb->last_error,
                admin_url('admin.php?page=aboyuma-product-brand-add')
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Brand added successfully.',
            admin_url('admin.php?page=aboyuma-product-brand-list')
        );
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
        $brand = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $brand_id
            )
        );

        if (! $brand) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Brand not found.</p>
        </div>';
            return;
        }

        // Transient (status + old form data)
        $transient_key = Aboyuma_Flash_Helper::get_flash_key();
        $status_data   = get_transient($transient_key);

        if ($status_data !== false) {
            delete_transient($transient_key);
        }

        $status_message = $status_data['status'] ?? null;
        $old_input      = $status_data['old_input'] ?? [];

        // Clean form data (NO $_POST in template)
        $form_data = [
            'brand_name'      => $old_input['brand_name'] ?? $brand->brand_name,
            'brand_image_url' => $old_input['brand_image_url'] ?? $brand->brand_image_url,
        ];

        require plugin_dir_path(__FILE__) . 'partials/template-product-brand-edit.php';
    }

    /**
     * Handle Brand Edit POST
     *
     * @since 1.0.0
     */
    public function handle_brand_edit_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_brand_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_brand_edit_nonce'],
                'aboyuma_amway_product_brand_edit'
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

        $edit_url = admin_url(
            'admin.php?page=aboyuma-product-brand-edit&id=' . $brand_id
        );

        // Validation
        if ($brand_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Brand name is required.',
                $edit_url,
                [
                    'old_input' => [
                        'brand_name'      => $brand_name,
                        'brand_image_url' => $brand_image_url,
                    ],
                ]
            );
        }

        if ($brand_image_url === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Brand image URL is required.',
                $edit_url,
                [
                    'old_input' => [
                        'brand_name'      => $brand_name,
                        'brand_image_url' => $brand_image_url,
                    ],
                ]
            );
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
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $this->wpdb->last_error,
                $edit_url,
                [
                    'old_input' => [
                        'brand_name'      => $brand_name,
                        'brand_image_url' => $brand_image_url,
                    ],
                ]
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Brand updated successfully.',
            admin_url('admin.php?page=aboyuma-product-brand-list')
        );
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
