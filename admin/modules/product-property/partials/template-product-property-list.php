<div class="wrap">
    <h1 class="wp-heading-inline">Product Properties</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-property-add')); ?>"
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
    
    <?php if (! empty($rows)) : ?>
        <table id="aboyuma-product-property-table" class="wp-list-table widefat striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Property key</th>
                    <th>Property Name</th>
                    <th>Property Type</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td>
                            <?php echo esc_html($row->id); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->property_key); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->property_name); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->property_type); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->created); ?>
                        </td>

                        <td>
                            <?php echo esc_html($row->modified); ?>
                        </td>

                        <td>
                            <a href="<?php echo esc_url(
                                            admin_url('admin.php?page=aboyuma-product-property-edit&id=' . $row->id)
                                        ); ?>">
                                Edit
                            </a>
                            |
                            <a href="#"
                                class="delete-property-button"
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
                    You have no properties yet.
                    Please create your first property.
                </p>
            </div>
        <?php endif; ?>
        </table>
</div>