<div class="wrap">
    <h1 class="wp-heading-inline">Add New Brand</h1>
    <hr class="wp-header-end">

    <?php if (
        is_array($status_message) &&
        isset($status_message['status']['type'], $status_message['status']['message'])
    ) : ?>
        <div class="notice notice-<?php echo esc_attr($status_message['status']['type']); ?> is-dismissible">
            <p><?php echo esc_html($status_message['status']['message']); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aboyuma_brand_add">

        <table class="form-table">
            <!-- Brand Image URL -->
            <tr>
                <th scope="row"><label for="brand_image_url">Brand Image URL</label></th>
                <td>
                    <input type="url" name="brand_image_url" id="brand_image_url" class="regular-text" required>
                    <p class="description">ป้อน URL ที่ถูกต้องซึ่งชี้ไปยังรูปภาพของแบรนด์ แนะนำให้ใช้ลิงก์จาก official Amway website</p>
                </td>
            </tr>
            </tr>
            <!-- Brand Name -->
            <tr>
                <th scope="row"><label for="brand_name">Brand Name</label></th>
                <td>
                    <input type="text" name="brand_name" id="brand_name" class="regular-text" required>
                    <p class="description">ชื่อแบรนด์ เช่น Nutrilite แนะนำให้ใช้เป็นภาษาอังกฤษ</p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field(
            'aboyuma_product_brand_add',
            'aboyuma_product_brand_add_nonce'
        ); ?>

        <p class="submit">
            <button type="submit"
                name="submit"
                id="submit"
                class="button button-primary">
                Add Brand
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-brand-list')); ?>"
                class="button button-secondary">
                Cancel
            </a>
        </p>

    </form>
</div>