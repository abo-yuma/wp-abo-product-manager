<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product Property</h1>
    <hr class="wp-header-end">

    <?php
    $status  = isset($_GET['status']) ? sanitize_key($_GET['status']) : '';
    $message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';

    if ($status && $message) :
    ?>
        <div class="notice notice-<?php echo esc_attr($status); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

        <input type="hidden" name="action" value="aboyuma_property_edit">
        <input type="hidden" name="property_id" value="<?php echo esc_attr($property_id); ?>">

        <?php wp_nonce_field(
            'aboyuma_product_property_edit',
            'aboyuma_product_property_edit_nonce'
        ); ?>

        <table class="form-table">

            <!-- Property Key (Readonly) -->
            <tr>
                <th scope="row">
                    <label for="property_key">Property Key</label>
                </th>
                <td>
                    <input
                        type="text"
                        id="property_key"
                        class="regular-text"
                        value="<?php echo esc_attr($properties->property_key); ?>"
                        readonly />

                    <p class="description">
                        Property key cannot be changed after creation.
                    </p>
                </td>
            </tr>

            <!-- Property Name -->
            <tr>
                <th scope="row">
                    <label for="property_name">Property Name *</label>
                </th>
                <td>
                    <input
                        type="text"
                        name="property_name"
                        id="property_name"
                        class="regular-text"
                        value="<?php echo esc_attr($properties->property_name); ?>"
                        required />

                    <p class="description">
                        ชื่อที่จะแสดงผลในระบบและหน้าสินค้า
                    </p>
                </td>
            </tr>

            <!-- Property Type -->
            <tr>
                <th scope="row">
                    <label for="property_type">Property Type *</label>
                </th>
                <td>
                    <select
                        name="property_type"
                        id="property_type"
                        required>

                        <option value="">-- กรุณาเลือกประเภท --</option>

                        <option value="additive"
                            <?php selected($properties->property_type, 'additive'); ?>>
                            Additive (สารประกอบ)
                        </option>

                        <option value="diet"
                            <?php selected($properties->property_type, 'diet'); ?>>
                            Diet (รูปแบบการรับประทาน)
                        </option>

                        <option value="program"
                            <?php selected($properties->property_type, 'program'); ?>>
                            Program (โปรแกรม)
                        </option>

                        <option value="feature"
                            <?php selected($properties->property_type, 'feature'); ?>>
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
                <th scope="row">
                    <label for="property_description">Description</label>
                </th>
                <td>
                    <?php
                    wp_editor(
                        $properties->property_description,
                        'property_description',
                        [
                            'textarea_name' => 'property_description',
                            'textarea_rows' => 8,
                            'media_buttons' => true,
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

        <p class="submit">
            <button type="submit" class="button button-primary">
                Update Property
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-property-list')); ?>"
                class="button button-secondary"
                style="margin-left:8px;">
                Cancel
            </a>
        </p>

    </form>
</div>