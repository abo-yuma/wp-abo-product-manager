<div class="wrap">
    <h1 class="wp-heading-inline">Add New Product Category</h1>
    <hr class="wp-header-end">

    <form method="post">
        <table class="form-table">
            </tr>
            <!-- Category Name -->
            <tr>
                <th scope="row"><label for="category_name">Category Name</label></th>
                <td>
                    <input type="text" name="category_name" id="category_name" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="parent_id">Parent Category</label></th>
                <td>
                    <select name="parent_id" id="parent_id" class="regular-text">
                        <option value="">-- None (Top-level category) --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat['id']); ?>" <?php selected($_POST['parent_id'] ?? '', $cat['id']); ?>>
                                <?php echo esc_html($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php wp_nonce_field('aboyuma_amway_product_category_add', 'aboyuma_amway_product_category_add_nonce'); ?>
        <button type="submit" name="submit" id="submit" class="button button-primary">Add Category</button>
        <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
</div>