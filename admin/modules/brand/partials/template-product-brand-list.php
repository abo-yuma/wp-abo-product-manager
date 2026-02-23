<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') : ?>
    <div class="notice notice-success is-dismissible">
        <p>Brand updated successfully!</p>
    </div>
<?php endif; ?>

<div class="wrap">
    <h1 class="wp-heading-inline">Product Brand</h1>

    <a href="<?php echo admin_url('admin.php?page=aboyuma-product-brand-add'); ?>"
        class="page-title-action">
        Add New
    </a>

    <hr class="wp-header-end">

    <table id="aboyuma-product-brand-table" class="wp-list-table widefat fixed striped">

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
                        <td>
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