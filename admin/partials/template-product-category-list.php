<div class="wrap">
    <h1 class="wp-heading-inline">Product Categories</h1>
    <a href="<?php echo admin_url('admin.php?page=aboyuma-category-add'); ?>"
        class="page-title-action">
        Add New
    </a>
    <hr class="wp-header-end">

    <table id="aboyuma-product-category-table" class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th width="80">ID</th>
                <th>Category Name</th>
                <th width="100">Parent ID</th>
                <th width="160">Created</th>
                <th width="160">Modified</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table = $wpdb->prefix . 'aboyuma_product_category';
            $results = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id DESC");

            if ($results) :
                foreach ($results as $row) :
            ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td>
                            <strong><?php echo esc_html($row->category_name); ?></strong>
                        </td>
                        <td><?php echo esc_html($row->parent_id ?: '-'); ?></td>
                        <td><?php echo esc_html($row->created); ?></td>
                        <td><?php echo esc_html($row->modified); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=aboyuma-category-edit&id=' . $row->id); ?>">
                                Edit
                            </a> |
                            <a href="#" class="delete-category-link" data-id="<?php echo $row->id; ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>