<?php

/**
 * Product Property Admin functionality.
 *
 * Handles product property admin :
 * - List
 * - Add
 * - Edit
 * - Delete (AJAX)
 *
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/admin/modules/product-property
 */

if (! defined('ABSPATH')) {
    exit; // Prevent direct access.
}

class Aboyuma_Product_Property_Admin {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'aboyuma_product_property';
    }

    /**
     * Property List Page
     *
     * @since 1.0.0
     */
    public function property_list() {

        $rows = $this->wpdb->get_results(
            "
        SELECT 
            id,
            property_key,
            property_name,
            property_type,
            created,
            modified
        FROM {$this->table}
        ORDER BY id DESC
        "
        );

        if (! is_array($rows)) {
            $rows = [];
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-list.php';
    }

    /**
     * Property Add Page
     *
     * @since 1.0.0
     */
    public function property_add() {

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-add.php';
    }

    /**
     * Handle Property Add POST
     *
     * @since 1.0.0
     */
    public function handle_property_add_post() {

        if (
            ! isset($_POST['aboyuma_product_property_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_property_add_nonce'],
                'aboyuma_product_property_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $property_key         = sanitize_key($_POST['property_key']);
        $property_name        = sanitize_text_field($_POST['property_name']);
        $property_type        = sanitize_text_field($_POST['property_type']);
        $property_description = wp_kses_post($_POST['property_description']);

        // Validation (Guard Clause)
        if ($property_key === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                        urlencode('Property key is required.')
                )
            );
            exit;
        }

        if (! preg_match('/^[a-z0-9\-]+$/', $property_key)) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                        urlencode('Property key must contain only lowercase letters, numbers and hyphen (-).')
                )
            );
            exit;
        }

        if ($property_name === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                        urlencode('Property name is required.')
                )
            );
            exit;
        }

        if ($property_type === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                        urlencode('Property type is required.')
                )
            );
            exit;
        }

        // Insert
        $inserted = $this->wpdb->insert(
            $this->table,
            [
                'property_key'         => $property_key,
                'property_name'        => $property_name,
                'property_type'        => $property_type,
                'property_description' => $property_description,
                'created'              => current_time('mysql'),
                'modified'             => current_time('mysql'),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );

        if ($inserted === false) {

            // Duplicate key handling
            if (strpos($this->wpdb->last_error, 'Duplicate entry') !== false) {
                wp_safe_redirect(
                    admin_url(
                        'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                            urlencode('Property key already exists. It must be unique.')
                    )
                );
                exit;
            }

            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-add&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-property-list&status=success&message=' .
                    urlencode('Property added successfully.')
            )
        );
        exit;
    }

    /**
     * Property Edit Page
     *
     * @since 1.0.0
     */
    public function property_edit() {

        $property_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

        if (! $property_id) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Invalid property ID.</p>
        </div>';
            return;
        }

        // Get existing property
        $properties = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $property_id
            )
        );

        if (!$properties) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Property not found.</p>
        </div>';
            return;
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-edit.php';
    }

    /**
     * Handle Property Edit POST
     *
     * @since 1.0.0
     */
    public function handle_property_edit_post() {

        if (
            ! isset($_POST['aboyuma_product_property_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_product_property_edit_nonce'],
                'aboyuma_product_property_edit'
            )
        ) {
            wp_die('Security check failed.');
        }

        $property_id = isset($_POST['property_id'])
            ? absint($_POST['property_id'])
            : 0;

        if (! $property_id) {
            wp_die('Invalid property ID.');
        }

        $property_name = sanitize_text_field($_POST['property_name']);
        $property_type = sanitize_text_field($_POST['property_type']);
        $property_description = wp_kses_post($_POST['property_description']);

        $edit_url = admin_url(
            'admin.php?page=aboyuma-product-property-edit&id=' . $property_id
        );

        // Validation
        if ($property_name === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-edit&status=error&message=' .
                        urlencode('Property name is required.')
                )
            );
            exit;
        }

        if ($property_type === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-edit&status=error&message=' .
                        urlencode('Property type is required.')
                )
            );
            exit;
        }

        $updated = $this->wpdb->update(
            $this->table,
            [
                'property_name'        => $property_name,
                'property_type'        => $property_type,
                'property_description' => $property_description,
                'modified'             => current_time('mysql'),
            ],
            ['id' => $property_id],
            [
                '%s',
                '%s',
                '%s',
                '%s',
            ],
            ['%d']
        );

        if ($updated === false) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=aboyuma-product-property-edit&status=error&message=' .
                        urlencode('Database error: ' . $this->wpdb->last_error)
                )
            );
            exit;
        }

        // Success
        wp_safe_redirect(
            admin_url(
                'admin.php?page=aboyuma-product-property-list&status=success&message=' .
                    urlencode('Property updated successfully.')
            )
        );
        exit;
    }

    /**
     * Property Delete Operation (AJAX)
     *
     * @since 1.0.0
     */
    public function property_delete_ajax_handler() {

        check_ajax_referer('aboyuma_product_property_delete', 'nonce');

        $property_id = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if (! $property_id) {
            wp_send_json_error('Invalid property ID.');
        }

        $deleted = $this->wpdb->delete(
            $this->table,
            ['id' => $property_id],
            ['%d']
        );

        if ($deleted !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(
                'Failed to delete the property. Please try again.'
            );
        }
    }
}
