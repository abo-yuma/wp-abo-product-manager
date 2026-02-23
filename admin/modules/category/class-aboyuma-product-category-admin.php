<?php

/**
 * Product Category Admin functionality.
 *
 * Handles product category admin :
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin/modules/category
 */

if (! defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Aboyuma_Product_Category_Admin {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'aboyuma_product_category';
    }

    /**
     * Category List Page
     *
     * @since 1.0.0
     */
    public function category_list() {

        // Retrieve notice (shared for add/edit/delete)
        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        $categories = $this->wpdb->get_results(
            "
        SELECT 
            c.id,
            c.category_name,
            c.parent_id,
            c.created,
            c.modified,
            p.category_name AS parent_name
        FROM {$this->table} c
        LEFT JOIN {$this->table} p ON c.parent_id = p.id
        ORDER BY c.id DESC
        "
        );

        if (! is_array($categories)) {
            $categories = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-list.php';
    }

    /**
     * Category Add Page
     *
     * @since 1.0.0
     */
    public function category_add() {

        $categories = $this->get_categories_dropdown();
        if (! is_array($categories)) {
            $categories = [];
        }

        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-add.php';
    }

    /**
     * Handle Category Add POST
     *
     * @since 1.0.0
     */
    public function handle_category_add_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_category_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_category_add_nonce'],
                'aboyuma_amway_product_category_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $category_name = sanitize_text_field($_POST['category_name'] ?? '');
        $parent_id     = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
            ? intval($_POST['parent_id'])
            : null;

        // Validation (Guard Clause)
        if ($category_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Category name is required.',
                admin_url('admin.php?page=aboyuma-product-category-add')
            );
        }

        $inserted = $this->wpdb->insert(
            $this->table,
            [
                'category_name' => $category_name,
                'parent_id'     => $parent_id,
                'created'       => current_time('mysql'),
                'modified'      => current_time('mysql'),
            ],
            [
                '%s',
                $parent_id === null ? null : '%d',
                '%s',
                '%s',
            ]
        );

        if ($inserted === false) {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $this->wpdb->last_error,
                admin_url('admin.php?page=aboyuma-product-category-add')
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Category added successfully.',
            admin_url('admin.php?page=aboyuma-product-category-list')
        );
    }

    /**
     * Category Edit Page
     *
     * @since 1.0.0
     */
    public function category_edit() {

        $category_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

        if (! $category_id) {
            echo '<div class="notice notice-error is-dismissible">
                <p>Invalid category ID.</p>
              </div>';
            return;
        }

        // Get existing category
        $category = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $category_id
            )
        );

        if (! $category) {
            echo '<div class="notice notice-error is-dismissible">
                <p>Category not found.</p>
              </div>';
            return;
        }

        // Get dropdown categories
        $categories = $this->get_categories_dropdown();
        if (! is_array($categories)) {
            $categories = [];
        }

        // Transient (status + old form data)
        $transient_key = Aboyuma_Flash_Helper::get_flash_key();
        $status_data   = get_transient($transient_key);

        if ($status_data !== false) {
            delete_transient($transient_key);
        }

        $status_message = $status_data['status'] ?? null;
        $old_input      = $status_data['old_input'] ?? [];

        // Prepare clean form data (NO $_POST in template)
        $form_data = [
            'category_name' => $old_input['category_name'] ?? $category->category_name,
            'parent_id'     => array_key_exists('parent_id', $old_input)
                ? $old_input['parent_id']
                : $category->parent_id,
        ];

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-edit.php';
    }

    /**
     * Handle Category Edit POST
     *
     * @since 1.0.0
     */
    public function handle_category_edit_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_category_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_category_edit_nonce'],
                'aboyuma_amway_product_category_edit'
            )
        ) {
            wp_die('Security check failed.');
        }

        $category_id = isset($_POST['category_id'])
            ? absint($_POST['category_id'])
            : 0;

        if (! $category_id) {
            wp_die('Invalid category ID.');
        }

        $category_name = sanitize_text_field($_POST['category_name'] ?? '');
        $parent_id     = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
            ? intval($_POST['parent_id'])
            : null;

        $edit_url = admin_url(
            'admin.php?page=aboyuma-product-category-edit&id=' . $category_id
        );

        // Validation: Required Name
        if ($category_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Category name is required.',
                $edit_url,
                [
                    'old_input' => [
                        'category_name' => $category_name,
                        'parent_id'     => $parent_id,
                    ],
                ]
            );
        }

        // Validation: Cannot Be Own Parent
        if ($parent_id === $category_id) {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Category cannot be its own parent.',
                $edit_url,
                [
                    'old_input' => [
                        'category_name' => $category_name,
                        'parent_id'     => $parent_id,
                    ],
                ]
            );
        }

        $updated = $this->wpdb->update(
            $this->table,
            [
                'category_name' => $category_name,
                'parent_id'     => $parent_id,
                'modified'      => current_time('mysql'),
            ],
            ['id' => $category_id],
            [
                '%s',
                $parent_id === null ? null : '%d',
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
                        'category_name' => $category_name,
                        'parent_id'     => $parent_id,
                    ],
                ]
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Category updated successfully.',
            admin_url('admin.php?page=aboyuma-product-category-list')
        );
    }

    /**
     * Category Delete Operation
     *
     * @since 1.0.0
     */
    public function category_delete_ajax_handler() {

        check_ajax_referer('aboyuma_product_category_delete', 'nonce');

        $category_id = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if (! $category_id) {
            wp_send_json_error('Invalid category ID.');
        }

        $deleted = $this->wpdb->delete(
            $this->table,
            ['id' => $category_id],
            ['%d']
        );

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(
                'Failed to delete the category. Please try again.'
            );
        }
    }

    /**
     * Get all categories for dropdown (id + name)
     *
     * @return array
     *
     * @since 1.0.0
     */
    private function get_categories_dropdown() {

        $categories = $this->wpdb->get_results(
            "SELECT id, category_name FROM {$this->table} ORDER BY category_name ASC",
            ARRAY_A
        );

        if (! is_array($categories)) {
            return array();
        }

        return $categories;
    }
}
