<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product Brand</h1>
    <hr class="wp-header-end">

    <?php if (! empty($status_message)) : ?>
        <div class="notice notice-<?php echo esc_attr($status_message['type']); ?> is-dismissible">
            <p><?php echo esc_html($status_message['message']); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

        <input type="hidden" name="action" value="aboyuma_brand_edit">
        <input type="hidden" name="brand_id" value="<?php echo esc_attr($brand_id); ?>">

        <?php wp_nonce_field(
            'aboyuma_amway_product_brand_edit',
            'aboyuma_amway_product_brand_edit_nonce'
        ); ?>

        <table class="form-table">
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
                        value="<?php echo esc_attr($form_data['brand_name']); ?>"
                        required />
                </td>
            </tr>

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
                        value="<?php echo esc_attr($form_data['brand_image_url']); ?>"
                        required />
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                Update Category
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-brand-list')); ?>"
                class="button button-secondary"
                style="margin-left:8px;">
                Cancel
            </a>
        </p>

    </form>
</div>