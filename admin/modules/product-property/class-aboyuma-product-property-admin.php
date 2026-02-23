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
     * Brand List Page
     *
     * @since 1.0.0
     */
    public function property_list() {

        $rows = $this->wpdb->get_results("SELECT * FROM $this->table ORDER BY id DESC");

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-list.php';
    }

    /**
     * Property Add Page
     *
     * @since 1.0.0
     */
    public function property_add() {

        $errors               = [];
        $property_key         = '';
        $property_name        = '';
        $property_type        = '';
        $property_description = '';

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_wpnonce']) &&
            wp_verify_nonce(
                $_POST['_wpnonce'],
                'aboyuma_amway_product_property_add_nonce'
            )
        ) {

            $property_key = isset($_POST['property_key'])
                ? sanitize_key(trim($_POST['property_key']))
                : '';

            $property_name = isset($_POST['property_name'])
                ? sanitize_text_field(trim($_POST['property_name']))
                : '';

            $property_type = isset($_POST['property_type'])
                ? sanitize_text_field(trim($_POST['property_type']))
                : '';

            $property_description = isset($_POST['property_description'])
                ? wp_kses_post($_POST['property_description'])
                : '';

            // Validation
            if (empty($property_key)) {
                $errors[] = 'Property key is required.';
            }

            if (! preg_match('/^[a-z0-9\-]+$/', $property_key)) {
                $errors[] = 'Property key must contain only lowercase letters, numbers and hyphen (-).';
            }

            if (empty($property_name)) {
                $errors[] = 'Property name is required.';
            }

            if (empty($property_type)) {
                $errors[] = 'Property type is required.';
            }

            if (empty($errors)) {

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

                if ($inserted) {
                    echo '<script>
                        window.location.href = "' . esc_url_raw(
                        admin_url('admin.php?page=aboyuma-product-property-list&message=added')
                    ) . '";
                    </script>';
                    exit;
                } else {

                    // Handle duplicate unique key error gracefully
                    if (strpos($this->wpdb->last_error, 'Duplicate entry') !== false) {
                        $errors[] = 'Property key already exists. It must be unique.';
                    } else {
                        $errors[] = 'Database error: ' . $this->wpdb->last_error;
                    }
                }
            }
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-add.php';
    }


    /**
     * Property Edit Page
     *
     * @since 1.0.0
     */
    public function property_edit() {

        $errors = [];

        // Get ID
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (! $id) {
            echo '<div class="notice notice-error"><p>Invalid property ID.</p></div>';
            return;
        }

        // Load existing row
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            )
        );

        if (! $row) {
            echo '<div class="notice notice-error"><p>Property not found.</p></div>';
            return;
        }

        // Handle POST
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            isset($_POST['_wpnonce']) &&
            wp_verify_nonce(
                $_POST['_wpnonce'],
                'aboyuma_amway_product_property_edit_nonce'
            )
        ) {

            $property_name = isset($_POST['property_name'])
                ? sanitize_text_field(trim($_POST['property_name']))
                : '';

            $property_type = isset($_POST['property_type'])
                ? sanitize_text_field(trim($_POST['property_type']))
                : '';

            $property_description = isset($_POST['property_description'])
                ? wp_kses_post($_POST['property_description'])
                : '';

            // Validation
            if (empty($property_name)) {
                $errors[] = 'Property name is required.';
            }

            if (empty($property_type)) {
                $errors[] = 'Property type is required.';
            }

            if (empty($errors)) {

                $updated = $this->wpdb->update(
                    $this->table,
                    [
                        'property_name'        => $property_name,
                        'property_type'        => $property_type,
                        'property_description' => $property_description,
                        'modified'             => current_time('mysql'),
                    ],
                    [
                        'id' => $id
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ],
                    [
                        '%d'
                    ]
                );

                if ($updated !== false) {

                    // JS redirect (consistent with your add page)
                    $redirect_url = admin_url(
                        'admin.php?page=aboyuma-product-property-list&message=updated'
                    );

                    echo '<script>
                    window.location.replace("' . esc_url_raw($redirect_url) . '");
                </script>';

                    exit;
                } else {

                    $errors[] = 'Database error: ' . $this->wpdb->last_error;
                }
            }

        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-edit.php';
    }
}
