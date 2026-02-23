<div class="wrap">

    <h1 class="wp-heading-inline">Product Categories</h1>
    <a href="<?php echo admin_url('admin.php?page=aboyuma-product-property-add'); ?>"
        class="page-title-action">
        Add New
    </a>
    <hr class="wp-header-end">
    <?php if (isset($_GET['message']) && $_GET['message'] === 'added') : ?>
        <div class="notice notice-success is-dismissible">
            <p>Property added successfully.</p>
        </div>
    <?php endif; ?>

    <table id="aboyuma-product-property-table" class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Key</th>
                <th>Name</th>
                <th>Type</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($rows)) : ?>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><?= esc_html($row->id) ?></td>
                        <td><?= esc_html($row->property_key) ?></td>
                        <td><?= esc_html($row->property_name) ?></td>
                        <td><?= esc_html($row->property_type) ?></td>
                        <td><?= esc_html($row->created) ?></td>
                        <td>
                            <a href="admin.php?page=aboyuma-product-property-edit&id=<?= $row->id ?>" class="button">
                                Edit
                            </a>

                            <a href="#"
                                class="button product-property-delete-button"
                                data-id="<?= $row->id ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>