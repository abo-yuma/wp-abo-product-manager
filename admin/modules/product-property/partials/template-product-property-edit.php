<?php
// Expect $row to be passed from controller
if (! isset($row)) {
    echo '<div class="notice notice-error"><p>Invalid property.</p></div>';
    return;
}

/**
 * Prepare form values
 * Priority:
 * 1. POST (if validation failed)
 * 2. Database value
 */
$name = isset($_POST['property_name'])
    ? sanitize_text_field($_POST['property_name'])
    : $row->property_name;

$type = isset($_POST['property_type'])
    ? sanitize_text_field($_POST['property_type'])
    : $row->property_type;

$description = isset($_POST['property_description'])
    ? wp_kses_post($_POST['property_description'])
    : $row->property_description;
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Edit Property</h1>

    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-property-list')); ?>"
        class="page-title-action">
        Back to List
    </a>

    <hr class="wp-header-end">

    <form method="post">

        <?php wp_nonce_field('aboyuma_amway_product_property_edit_nonce'); ?>

        <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">

        <table class="form-table">

            <!-- Property Key (Readonly) -->
            <tr>
                <th>
                    <label for="property_key">Property Key</label>
                </th>
                <td>
                    <input type="text"
                        name="property_key"
                        id="property_key"
                        class="regular-text"
                        value="<?php echo esc_attr($row->property_key); ?>"
                        readonly>

                    <p class="description">
                        Property key cannot be changed after creation.
                    </p>
                </td>
            </tr>

            <!-- Property Name -->
            <tr>
                <th>
                    <label for="property_name">Property Name *</label>
                </th>
                <td>
                    <input type="text"
                        name="property_name"
                        id="property_name"
                        class="regular-text"
                        value="<?php echo esc_attr($name); ?>"
                        required>

                    <p class="description">
                        ชื่อที่จะแสดงผลในระบบและหน้าสินค้า
                    </p>
                </td>
            </tr>

            <!-- Property Type -->
            <tr>
                <th>
                    <label for="property_type">Property Type *</label>
                </th>
                <td>
                    <select name="property_type" id="property_type" required>
                        <option value="">-- กรุณาเลือกประเภท --</option>

                        <option value="additive" <?php selected($type, 'additive'); ?>>
                            Additive (สารประกอบ)
                        </option>

                        <option value="diet" <?php selected($type, 'diet'); ?>>
                            Diet (รูปแบบการรับประทาน)
                        </option>

                        <option value="program" <?php selected($type, 'program'); ?>>
                            Program (โปรแกรม)
                        </option>

                        <option value="feature" <?php selected($type, 'feature'); ?>>
                            Feature (คุณสมบัติพิเศษ)
                        </option>

                    </select>

                    <p class="description">
                        ใช้สำหรับจัดกลุ่มคุณสมบัติ เพื่อการแสดงผลและการกรองข้อมูลสินค้า
                    </p>
                </td>
            </tr>

            <!-- Description -->
            <tr>
                <th>
                    <label for="property_description">Description</label>
                </th>
                <td>
                    <?php
                    wp_editor(
                        $description,
                        'property_description',
                        [
                            'textarea_name' => 'property_description',
                            'textarea_rows' => 8,
                            'media_buttons' => false,
                            'teeny'         => true,
                            'tinymce'       => [
                                'content_style' => 'body { padding: 12px !important; }'
                            ]
                        ]
                    );
                    ?>

                    <p class="description">
                        รายละเอียดเพิ่มเติมของคุณสมบัติ (ไม่บังคับกรอก)
                    </p>
                </td>
            </tr>

        </table>

        <p>
            <button type="submit"
                name="update_property"
                class="button button-primary">
                Update Property
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-property-list')); ?>"
                class="button">
                Cancel
            </a>
        </p>

    </form>
</div>