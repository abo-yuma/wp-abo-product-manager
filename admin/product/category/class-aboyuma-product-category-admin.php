<?php

/**
 * Category Admin functionality
 *
 * Handles category admin pages:
 * - List
 * - Add
 * - (Extendable for Edit / Delete / AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin
 */

if (! defined('ABSPATH')) {
    exit; // Prevent direct access.
}

class Aboyuma_Product_Category_Admin {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'aboyuma_product_category';

        // AJAX delete hook
        add_action('wp_ajax_aboyuma_product_category_delete', [$this, 'category_delete_ajax_handler']);
    }

    /**
     * Category List Page
     *
     * @since 1.0.0
     */
    public function category_list() {

        // Success message after redirect
        if (isset($_GET['message']) && $_GET['message'] === 'added') {
            echo '<div class="notice notice-success is-dismissible">
                    <p>Category added successfully!</p>
                  </div>';
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
            $categories = array();
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-list.php';
    }

    /**
     * Category Add Page
     *
     * @since 1.0.0
     */
    public function category_add() {

        $errors        = [];
        $category_name = '';
        $parent_id     = 0;

        // Always define categories for template
        $categories = $this->get_categories_dropdown();

        if (! is_array($categories)) {
            $categories = [];
        }

        // Handle POST
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['aboyuma_amway_product_category_add_nonce']) &&
            wp_verify_nonce(
                $_POST['aboyuma_amway_product_category_add_nonce'],
                'aboyuma_amway_product_category_add'
            )
        ) {

            $category_name = isset($_POST['category_name'])
                ? sanitize_text_field(trim($_POST['category_name']))
                : '';

            $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
                ? intval($_POST['parent_id'])
                : null;

            // Validation
            if (empty($category_name)) {
                $errors[] = 'Category name is required.';
            }

            if (empty($errors)) {

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
                        '%d',
                        '%s',
                        '%s',
                    ]
                );

                if ($inserted) {

                    echo '<script>
                        window.location.href = "' . esc_url(
                        admin_url('admin.php?page=aboyuma-product-category-list&message=added')
                    ) . '";
                    </script>';
                    exit;

                    exit;
                } else {

                    $errors[] = 'Database error: ' . $this->wpdb->last_error;
                }
            }
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-add.php';
    }

    /**
     * Category Edit Page
     *
     * @since 1.0.0
     */
    public function category_edit() {

        $errors = [];

        // Get ID safely
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

        // Prepare form values
        $category_name = $category->category_name;
        $parent_id     = $category->parent_id;

        // Get categories for dropdown
        $categories = $this->get_categories_dropdown();

        if (! is_array($categories)) {
            $categories = [];
        }

        // Handle POST
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['aboyuma_amway_product_category_edit_nonce']) &&
            wp_verify_nonce(
                $_POST['aboyuma_amway_product_category_edit_nonce'],
                'aboyuma_amway_product_category_edit'
            )
        ) {

            $category_name = isset($_POST['category_name'])
                ? sanitize_text_field(trim($_POST['category_name']))
                : '';

            $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
                ? intval($_POST['parent_id'])
                : null;

            if (empty($category_name)) {
                $errors[] = 'Category name is required.';
            }

            // Prevent selecting itself as parent
            if ($parent_id === $category_id) {
                $errors[] = 'Category cannot be its own parent.';
            }

            if (empty($errors)) {

                $updated = $this->wpdb->update(
                    $this->table,
                    [
                        'category_name' => $category_name,
                        'parent_id'     => $parent_id,
                        'modified'      => current_time('mysql'),
                    ],
                    ['id' => $category_id],
                    ['%s', '%d', '%s'],
                    ['%d']
                );

                if ($updated !== false) {

                    echo '<script>
                        window.location.href = "' . esc_url(
                        admin_url('admin.php?page=aboyuma-product-category-list&message=updated')
                    ) . '";
                    </script>';
                    exit;
                } else {

                    $errors[] = 'Database error: ' . $this->wpdb->last_error;
                }
            }
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-category-edit.php';
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
