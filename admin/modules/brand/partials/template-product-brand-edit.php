<div class="wrap">
    <h1>Edit Brand</h1>

    <?php if (!empty($errors)) : ?>
        <div class="notice notice-error">
            <?php foreach ($errors as $error) : ?>
                <p><?php echo esc_html($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">

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
                        value="<?php echo esc_attr($brand_name); ?>"
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
                        value="<?php echo esc_attr($brand_image_url); ?>"
                        required />
                </td>
            </tr>
        </table>

        <?php submit_button('Update Brand', 'primary', 'submit', false); ?>

        <a href="<?php echo esc_url(
                        admin_url('admin.php?page=aboyuma-product-brand-list')
                    ); ?>"
            class="button button-secondary"
            style="margin-left:8px;">
            Cancel
        </a>

    </form>
</div>