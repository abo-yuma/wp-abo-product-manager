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

    <?php if (! empty($products)) : ?>
        <table id="aboyuma-product-table"
            class="wp-list-table widefat striped">

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>


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
                            <?php echo esc_html($row->product_name); ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                wp_trim_words(
                                    wp_strip_all_tags($row->product_description),
                                    20,
                                    '...'
                                )
                            );
                            ?>
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


            </tbody>

        </table>
    <?php else : ?>

        <div class="notice notice-info inline">
            <p>
                You have no products yet.
                Please create your first product.
            </p>
        </div>

    <?php endif; ?>
</div>