<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product Category</h1>
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

        <input type="hidden" name="action" value="aboyuma_category_edit">
        <input type="hidden" name="category_id" value="<?php echo esc_attr($category_id); ?>">

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
                        value="<?php echo esc_attr($category->category_name); ?>"
                        required>
                    <p class="description">
                        ชื่อหมวดหมู่สินค้า
                    </p>
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
                                <?php selected($category->parent_id, $cat['id']); ?>>
                                <?php echo esc_html($cat['category_name']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                    <p class="description">
                        เลือกหมวดหมู่นี้เป็นหมวดหมู่ย่อยของหมวดหมู่อะไร หากไม่เลือก หมวดหมู่นี้จะกลายเป็นหมวดหมู่หลัก
                    </p>
                </td>
            </tr>

        </table>

        <?php wp_nonce_field(
            'aboyuma_product_category_edit',
            'aboyuma_product_category_edit_nonce'
        ); ?>

        <p class="submit">
            <button type="submit" class="button button-primary">
                Update Category
            </button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-category-list')); ?>"
                class="button button-secondary"
                style="margin-left:8px;">
                Cancel
            </a>
        </p>

    </form>
</div>