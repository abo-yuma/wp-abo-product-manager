<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product Category</h1>
    <hr class="wp-header-end">

    <?php if (!empty($errors)) : ?>
        <div class="notice notice-error is-dismissible">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo esc_html($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">

        <table class="form-table">

            <!-- Category Name -->
            <tr>
                <th scope="row">
                    <label for="category_name">Category Name</label>
                </th>
                <td>
                    <input
                        type="text"
                        name="category_name"
                        id="category_name"
                        class="regular-text"
                        value="<?php echo esc_attr($category_name); ?>"
                        required>
                </td>
            </tr>

            <!-- Parent Category -->
            <tr>
                <th scope="row">
                    <label for="parent_id">Parent Category</label>
                </th>
                <td>
                    <select name="parent_id" id="parent_id" class="regular-text">
                        <option value="">-- None (Top-level category) --</option>

                        <?php foreach ($categories as $cat) : ?>

                            <?php if ($cat['id'] == $category_id) continue; ?>

                            <option
                                value="<?php echo esc_attr($cat['id']); ?>"
                                <?php selected($parent_id, $cat['id']); ?>>
                                <?php echo esc_html($cat['category_name']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </td>
            </tr>

        </table>

        <?php wp_nonce_field(
            'aboyuma_amway_product_category_edit',
            'aboyuma_amway_product_category_edit_nonce'
        ); ?>

        <button type="submit" class="button button-primary">
            Update Category
        </button>

        <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-category-list')); ?>"
            class="button button-secondary">
            Cancel
        </a>

    </form>
</div>