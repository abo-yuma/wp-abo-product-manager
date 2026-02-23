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

        // Retrieve notice (shared for add/edit/delete)
        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        $rows = $this->wpdb->get_results(
            "
        SELECT 
            id,
            property_key,
            property_name,
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

        $transient_key  = Aboyuma_Flash_Helper::get_flash_key();
        $status_message = get_transient($transient_key);

        if ($status_message !== false) {
            delete_transient($transient_key);
        }

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-add.php';
    }

    /**
     * Handle Property Add POST
     *
     * @since 1.0.0
     */
    public function handle_property_add_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_property_add_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_property_add_nonce'],
                'aboyuma_amway_product_property_add'
            )
        ) {
            wp_die('Security check failed.');
        }

        $property_key         = sanitize_key($_POST['property_key'] ?? '');
        $property_name        = sanitize_text_field($_POST['property_name'] ?? '');
        $property_type        = sanitize_text_field($_POST['property_type'] ?? '');
        $property_description = wp_kses_post($_POST['property_description'] ?? '');

        // -------------------------
        // Validation (Guard Clause)
        // -------------------------

        if ($property_key === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property key is required.',
                admin_url('admin.php?page=aboyuma-product-property-add')
            );
        }

        if (! preg_match('/^[a-z0-9\-]+$/', $property_key)) {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property key must contain only lowercase letters, numbers and hyphen (-).',
                admin_url('admin.php?page=aboyuma-product-property-add')
            );
        }

        if ($property_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property name is required.',
                admin_url('admin.php?page=aboyuma-product-property-add')
            );
        }

        if ($property_type === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property type is required.',
                admin_url('admin.php?page=aboyuma-product-property-add')
            );
        }

        // -------------------------
        // Insert
        // -------------------------

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
                Aboyuma_Flash_Helper::redirect(
                    'error',
                    'Property key already exists. It must be unique.',
                    admin_url('admin.php?page=aboyuma-product-property-add')
                );
            }

            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $this->wpdb->last_error,
                admin_url('admin.php?page=aboyuma-product-property-add')
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Property added successfully.',
            admin_url('admin.php?page=aboyuma-product-property-list')
        );
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
        $property = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $property_id
            )
        );

        if (! $property) {
            echo '<div class="notice notice-error is-dismissible">
            <p>Property not found.</p>
        </div>';
            return;
        }

        // Flash data
        $transient_key = Aboyuma_Flash_Helper::get_flash_key();
        $status_data   = get_transient($transient_key);

        if ($status_data !== false) {
            delete_transient($transient_key);
        }

        $status_message = $status_data['status'] ?? null;
        $old_input      = $status_data['old_input'] ?? [];

        // Clean form data (NO $_POST in template)
        $form_data = [
            'property_name'        => $old_input['property_name'] ?? $property->property_name,
            'property_type'        => $old_input['property_type'] ?? $property->property_type,
            'property_description' => $old_input['property_description'] ?? $property->property_description,
        ];

        require plugin_dir_path(__FILE__) . 'partials/template-product-property-edit.php';
    }

    /**
     * Handle Property Edit POST
     *
     * @since 1.0.0
     */
    public function handle_property_edit_post() {

        if (
            ! isset($_POST['aboyuma_amway_product_property_edit_nonce']) ||
            ! wp_verify_nonce(
                $_POST['aboyuma_amway_product_property_edit_nonce'],
                'aboyuma_amway_product_property_edit'
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

        $property_name = sanitize_text_field($_POST['property_name'] ?? '');
        $property_type = sanitize_text_field($_POST['property_type'] ?? '');
        $property_description = wp_kses_post($_POST['property_description'] ?? '');

        $edit_url = admin_url(
            'admin.php?page=aboyuma-product-property-edit&id=' . $property_id
        );

        // Validation
        if ($property_name === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property name is required.',
                $edit_url,
                [
                    'old_input' => [
                        'property_name'        => $property_name,
                        'property_type'        => $property_type,
                        'property_description' => $property_description,
                    ],
                ]
            );
        }

        if ($property_type === '') {
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Property type is required.',
                $edit_url,
                [
                    'old_input' => [
                        'property_name'        => $property_name,
                        'property_type'        => $property_type,
                        'property_description' => $property_description,
                    ],
                ]
            );
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
            Aboyuma_Flash_Helper::redirect(
                'error',
                'Database error: ' . $this->wpdb->last_error,
                $edit_url,
                [
                    'old_input' => [
                        'property_name'        => $property_name,
                        'property_type'        => $property_type,
                        'property_description' => $property_description,
                    ],
                ]
            );
        }

        // Success
        Aboyuma_Flash_Helper::redirect(
            'success',
            'Property updated successfully.',
            admin_url('admin.php?page=aboyuma-product-property-list')
        );
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
