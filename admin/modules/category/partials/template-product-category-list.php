<div class="wrap">
    <h1 class="wp-heading-inline">Product Categories</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-category-add')); ?>"
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

    <?php if (!empty($categories)) : ?>
        <table id="aboyuma-product-category-table" class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Category Name</th>
                    <th width="100">Parent Name</th>
                    <th width="160">Created</th>
                    <th width="160">Modified</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($categories as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td>
                            <strong><?php echo esc_html($row->category_name); ?></strong>
                        </td>
                        <td><?php echo esc_html($row->parent_name ?: '-'); ?></td>
                        <td><?php echo esc_html($row->created); ?></td>
                        <td><?php echo esc_html($row->modified); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-category-edit&id=' . $row->id)); ?>">
                                Edit
                            </a> |
                            <a href="#" class="delete-category-button" data-id="<?php echo esc_attr($row->id); ?>">
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
                You have no categories yet.
                Please create your first category.
            </p>
        </div>
    <?php endif; ?>
</div>