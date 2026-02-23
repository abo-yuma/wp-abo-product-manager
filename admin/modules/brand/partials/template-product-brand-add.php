<div class="wrap">
    <h1 class="wp-heading-inline">Add New Brand</h1>
    <hr class="wp-header-end">

    <form method="post">
        <table class="form-table">
            <!-- Product Image URL -->
            <tr>
                <th scope="row"><label for="brand_image_url">Brand Image URL</label></th>
                <td>
                    <input type="url" name="brand_image_url" id="brand_image_url" class="regular-text" required>
                    <p class="detail">Enter a valid URL pointing to the product image (e.g., https://example.com/image.jpg).</p>
                </td>
            </tr>
            </tr>
            <!-- Product Name -->
            <tr>
                <th scope="row"><label for="brand_name">Brand Name</label></th>
                <td>
                    <input type="text" name="brand_name" id="brand_name" class="regular-text" required>
                </td>
            </tr>
        </table>
        <?php wp_nonce_field('aboyuma_amway_product_brand_add', 'aboyuma_amway_product_brand_add_nonce'); ?>
        <button type="submit" name="submit" id="submit" class="button button-primary">Add Brand</button>
        <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
</div>