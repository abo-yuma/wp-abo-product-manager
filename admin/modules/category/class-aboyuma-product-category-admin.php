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

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-add.php';
    }

    /**
     * Handle Category Add POST
     *
     * @since 1.0.0
     */
    public function handle_category_add_post() {

        if (
            ! isset($_POST['aboyuma_product_category_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_category_add_nonce'],
                'aboyuma_product_category_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $category_name = sanitize_text_field($_POST['category_name']);
        $parent_id     = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
            ? intval($_POST['parent_id'])
            : null;

        // Validation
        if ($category_name === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-category-add&status=error&message=' .
                        urlencode('Category name is required.')
                )
            );
            exit;
        }

        /**
         * Limit to 2 Levels Only
         *
         * When select parent:
         *  - top-level only
         *  - If parent has parent → not create
         */
        if ($parent_id !== null) {

            $parent = $this->wpdb->get_row(
                $this->wpdb->prepare(
                    "SELECT parent_id FROM {$this->table} WHERE id = %d",
                    $parent_id
                )
            );

            // ถ้า parent ไม่มีอยู่จริง
            if (! $parent) {
                wp_safe_redirect(
                    admin_url(
                        'admin.php?page=aboyuma-product-category-add&status=error&message=' .
                            urlencode('Invalid parent category.')
                    )
                );
                exit;
            }

            /**
             * If the selected parent already has its own parent,
             * it means the selected parent is a Level 2 category.
             *
             * Allowing this would create a Level 3 category,
             * which is not allowed because we restrict the hierarchy
             * to a maximum of 2 levels. 
             */
            if ($parent->parent_id !== null) {
                wp_safe_redirect(
                    admin_url(
                        'admin.php?page=aboyuma-product-category-add&status=error&message=' .
                            urlencode('You can only create 2 levels of categories.')
                    )
                );
                exit;
            }
        }

        // Insert
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
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-category-add&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-category-list&status=success&message=' .
                    urlencode('Category added successfully.')
            )
        );
        exit;
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

        // Target category
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

        // dropdown list
        $categories = $this->get_categories_dropdown();
        if (! is_array($categories)) {
            $categories = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-edit.php';
    }

    /**
     * Handle Category Edit POST
     *
     * @since 1.0.0
     */
    public function handle_category_edit_post() {

        if (
            ! isset($_POST['aboyuma_product_category_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_category_edit_nonce'],
                'aboyuma_product_category_edit'
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

        $category_name = sanitize_text_field($_POST['category_name']);
        $parent_id     = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
            ? intval($_POST['parent_id'])
            : null;

        // Validation: Required Name
        if ($category_name === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-category-edit&status=error&message=' .
                        urlencode('Category name is required.')
                )
            );
            exit;
        }

        // Validation: Cannot Be Own Parent
        if ($parent_id === $category_id) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-category-edit&status=error&message=' .
                        urlencode('Category cannot be its own parent.')
                )
            );
            exit;
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

        /**
         * Limit to 2 Levels Only (Edit)
         */
        if ($parent_id !== null) {

            $parent = $this->wpdb->get_row(
                $this->wpdb->prepare(
                    "SELECT parent_id FROM {$this->table} WHERE id = %d",
                    $parent_id
                )
            );

            if (! $parent) {
                wp_safe_redirect(
                    admin_url(
                        'admin.php?page=aboyuma-product-category-edit&status=error&message=' .
                            urlencode('Invalid parent category.')
                    )
                );
                exit;
            }

            /**
             * If the selected parent already has its own parent,
             * it means the selected parent is a Level 2 category.
             *
             * Allowing this would create a Level 3 category,
             * which is not allowed because we restrict the hierarchy
             * to a maximum of 2 levels. 
             */
            if ($parent->parent_id !== null) {
                wp_safe_redirect(
                    admin_url(
                        'admin.php?page=aboyuma-product-category-edit&status=error&message=' .
                            urlencode('You can only create 2 levels of categories.')
                    )
                );
                exit;
            }
        }

        if ($updated === false) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-category-edit&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-category-list&status=success&message=' .
                    urlencode('Category updated successfully.')
            )
        );
        exit;
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
     * Get Top-Level Categories for Dropdown
     * 
     * @return array
     *
     * @since 1.0.0
     */
    private function get_categories_dropdown() {

        $results = $this->wpdb->get_results(
            "SELECT id, category_name
            FROM {$this->table}
            WHERE parent_id IS NULL
            ORDER BY category_name ASC",
            ARRAY_A
        );

        return is_array($results) ? $results : [];
    }
}
