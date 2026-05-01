<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product Brand</h1>
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

        <input type="hidden" name="action" value="aboyuma_brand_edit">
        <input type="hidden" name="brand_id" value="<?php echo esc_attr($brand_id); ?>">

        <?php wp_nonce_field(
            'aboyuma_product_brand_edit',
            'aboyuma_product_brand_edit_nonce'
        ); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="brand_image_url">Brand Image URL</label>
                </th>
                <td>
                    <input
                        type="text"
                        name="brand_image_url"
                        id="brand_image_url"
                        class="regular-text"
                        value="<?php echo esc_attr($brands->brand_image_url); ?>"
                        required />
                    <p class="description">ป้อน URL ที่ถูกต้องซึ่งชี้ไปยังรูปภาพของแบรนด์ แนะนำให้ใช้ลิงก์จาก official Amway website</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="brand_name">Brand Name</label>
                </th>
                <td>
                    <input
                        type="text"
                        name="brand_name"
                        id="brand_name"
                        class="regular-text"
                        value="<?php echo esc_attr($brands->brand_name); ?>"
                        required />
                    <p class="description">ชื่อแบรนด์ เช่น Nutrilite แนะนำให้ใช้เป็นภาษาอังกฤษ</p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                Update Brand
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-brand-list')); ?>"
                class="button button-secondary"
                style="margin-left:8px;">
                Cancel
            </a>
        </p>

    </form>
</div>