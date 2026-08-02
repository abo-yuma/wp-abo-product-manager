<div class="wrap">
    <h1 class="wp-heading-inline">Product Brands</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-brand-add')); ?>"
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

    <?php if (!empty($brands)) : ?>
        <table id="aboyuma-product-brand-table" class="wp-list-table widefat striped">

            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($brands as $row) : ?>
                    <tr>
                        <td class="column-image">
                            <img src="<?php echo esc_url($row->brand_image_url); ?>"
                                width="100%" height="auto">
                        </td>

                        <td>
                            <?php echo esc_html($row->brand_name); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->created); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->modified); ?>
                        </td>

                        <td>
                            <a href="<?php echo admin_url(
                                            'admin.php?page=aboyuma-product-brand-edit&id=' . $row->id
                                        ); ?>">
                                Edit
                            </a>
                            |
                            <a href="#"
                                class="delete-brand-button"
                                data-id="<?php echo esc_attr($row->id); ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        <?php else : ?>
            <div class="notice notice-info inline">
                <p>
                    You have no brands yet.
                    Please create your first brand.
                </p>
            </div>
        <?php endif; ?>
        </table>
</div>