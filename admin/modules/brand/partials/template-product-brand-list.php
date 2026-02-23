<div class="wrap">
    <h1 class="wp-heading-inline">Product Brands</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-brand-add')); ?>"
        class="page-title-action">
        Add New
    </a>
    <hr class="wp-header-end">

    <?php if (
        ! empty($status_message['status']['type'])
        && ! empty($status_message['status']['message'])
    ) : ?>
        <div class="notice notice-<?php echo esc_attr($status_message['status']['type']); ?> is-dismissible">
            <p><?php echo esc_html($status_message['status']['message']); ?></p>
        </div>
    <?php endif; ?>

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
            <?php if (!empty($brands)) : ?>
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
            <?php else : ?>
                <tr>
                    <td colspan="5">No brands found.</td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>
</div>