<div class="wrap">
    <h1 class="wp-heading-inline">Products</h1>

    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-add')); ?>"
        class="page-title-action">
        Add New
    </a>

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

    <table id="aboyuma-product-table"
        class="wp-list-table widefat striped">

        <thead>
            <tr>
                <th>Image</th>
                <th>Description</th>
                <th>Created</th>
                <th>Modified</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            <?php if (! empty($products)) : ?>
                <?php foreach ($products as $row) : ?>

                    <tr>
                        <td class="column-image">
                            <?php
                            $image_url = wp_get_attachment_image_url(
                                $row->main_image_id,
                                'thumbnail'
                            );
                            ?>

                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>"
                                    width="100"
                                    height="auto">
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->product_description); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->created); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->modified); ?>
                        </td>

                        <td>
                            <a href="<?php echo esc_url(
                                            admin_url(
                                                'admin.php?page=aboyuma-product-edit&product_id=' . $row->id
                                            )
                                        ); ?>">
                                Edit
                            </a>
                            |
                            <a href="#"
                                class="delete-product-button"
                                data-id="<?php echo esc_attr($row->id); ?>">
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            <?php else : ?>

                <tr>
                    <td colspan="5">No products found.</td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>
</div>