<div class="wrap">
    <h1 class="wp-heading-inline">Edit Product</h1>
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

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="aboyuma_product_edit">

        <input type="hidden" name="product_id"
            value="<?php echo esc_attr($product->id); ?>">

        <table class="form-table">

            <!-- MAIN IMAGE -->
            <?php
            // Prepare image URL
            $main_image_url = wp_get_attachment_image_url($product->main_image_id, 'medium');
            ?>

            <tr>
                <th>
                    <label for="main_image_id">Main Image</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <!-- Hidden field -->
                    <input
                        type="hidden"
                        name="main_image_id"
                        id="main_image_id"
                        value="<?php echo esc_attr($product->main_image_id); ?>">

                    <!-- Select button -->
                    <button type="button" class="button" id="select-main-image">
                        Select / Replace Image
                    </button>

                    <!-- Preview (always visible in edit mode) -->
                    <div id="main-image-wrapper" class="main-image">
                        <img
                            id="main-image-preview"
                            src="<?php echo esc_url($main_image_url); ?>"
                            style="max-width: 300px; height: auto; display: block; margin-top: 10px;">
                    </div>

                    <p class="description">
                        เลือกรูปภาพหลักของสินค้า รูปนี้จะแสดงบนหน้ารายการสินค้าและหน้ารายละเอียดสินค้า
                    </p>
                </td>
            </tr>

            <!-- GALLERY -->
            <tr>
                <th>
                    <label for="gallery_image_ids">Gallery Images</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>

                    <!-- Add Button -->
                    <button type="button" class="button" id="add-gallery-images">
                        Add Gallery Images
                    </button>

                    <!-- Gallery Container -->
                    <div id="gallery-container" class="gallery" style="margin-top:15px; display:flex; flex-wrap:wrap; gap:12px;">

                        <?php foreach ($gallery_images as $image_id) :
                            $thumb_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        ?>
                            <div class="gallery-item" style="position:relative;">

                                <!-- Remove Button -->
                                <span class="remove-image"
                                    style="position:absolute; top:4px; right:6px; cursor:pointer; font-weight:bold; color:#fff; background:#d63638; border-radius:50%; width:18px; height:18px; text-align:center; line-height:18px;">
                                    &times;
                                </span>

                                <!-- Image -->
                                <img
                                    src="<?php echo esc_url($thumb_url); ?>"
                                    style="width:120px; height:120px; object-fit:cover; border:1px solid #ddd; border-radius:4px;">

                                <!-- Hidden Input -->
                                <input
                                    type="hidden"
                                    name="gallery_image_ids[]"
                                    value="<?php echo esc_attr($image_id); ?>">
                            </div>

                        <?php endforeach; ?>

                    </div>

                    <p class="description">
                        เพิ่มรูปภาพเพิ่มเติมของสินค้า สามารถลากเพื่อจัดเรียงลำดับ และกด × เพื่อลบรูปออก
                    </p>

                </td>
            </tr>

            <!-- PRODUCT CODE -->
            <tr>
                <th>
                    <label for="product_code">Product Code</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <input
                        type="text"
                        name="product_code"
                        id="product_code"
                        class="regular-text"
                        value="<?php echo esc_attr($product->product_code); ?>"
                        required>
                    <p class="description">
                        กรอกรหัสสินค้าสำหรับใช้อ้างอิงภายในระบบ เช่น SKU หรือรหัสสต็อก
                    </p>
                </td>
            </tr>

            <!-- NAME -->
            <tr>
                <th>
                    <label for="product_name">Product Name</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <input
                        type="text"
                        name="product_name"
                        id="product_name"
                        class="regular-text"
                        value="<?php echo esc_attr($product->product_name); ?>"
                        required>
                    <p class="description">
                        กรอกชื่อสินค้า แนะนำให้ใช้ชื่อทางการเป็นภาษาอังกฤษ เช่น Nutrilite Double X
                    </p>
                </td>
            </tr>

            <!-- BRAND -->
            <tr>
                <th>
                    <label for="product_brand">Product Brand</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <select name="product_brand" id="product_brand" class="regular-text" required>
                        <option value="">-- Select Brand --</option>

                        <?php foreach ($brands as $brand) : ?>
                            <option
                                value="<?php echo esc_attr($brand->id); ?>"
                                <?php selected($product->product_brand_id, $brand->id); ?>>
                                <?php echo esc_html($brand->brand_name); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <p class="description">
                        เลือกแบรนด์ของสินค้า **หากยังไม่มีแบรนด์ กรุณาเพิ่มแบรนด์ก่อน
                    </p>
                </td>
            </tr>

            <!-- CATEGORY -->
            <?php
            /**
             * ---------------------------------------------
             * CATEGORY RELATION LOGIC (Edit Mode)
             * ---------------------------------------------
             *
             * Product table stores ONLY subcategory ID.
             * It does NOT store parent category ID.
             *
             * So we must:
             * 1. Get the saved subcategory ID from product
             * 2. Find its parent category from $categories
             * 3. Use that parent ID to:
             *    - Pre-select parent dropdown
             *    - Filter subcategory dropdown
             */

            // Subcategory ID stored in product table
            $current_subcategory_id = $product->product_category_id;

            // Default parent ID (fallback)
            $current_parent_id = 0;

            /**
             * Loop through all categories to find
             * the selected subcategory object.
             *
             * Once found, read its parent_id.
             *
             * Example:
             * If subcategory ID = 10
             * And category row:
             *   id = 10
             *   parent_id = 1
             *
             * Then:
             *   $current_parent_id = 1
             */
            foreach ($categories as $category) {
                if ($category->id == $current_subcategory_id) {
                    $current_parent_id = $category->parent_id;
                    break; // Stop loop once found
                }
            }
            ?>
            <!-- Parent category -->
            <tr>
                <th>
                    <label for="parent_category">Parent Category</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <select name="parent_category" id="parent_category" class="regular-text" required>
                        <option value="">-- Select Parent Category --</option>

                        <?php
                        /**
                         * Loop all categories.
                         * Only show categories where parent_id == 0.
                         *
                         * Because:
                         * parent_id == 0 means this is a top-level category.
                         */
                        foreach ($categories as $category) :
                            if ($category->parent_id == 0) :
                        ?>
                                <option
                                    value="<?php echo esc_attr($category->id); ?>"
                                    <?php
                                    /**
                                     * Pre-select parent that matches
                                     * detected parent of current subcategory.
                                     */
                                    selected($current_parent_id, $category->id);
                                    ?>>
                                    <?php echo esc_html($category->category_name); ?>
                                </option>
                        <?php
                            endif;
                        endforeach;
                        ?>

                    </select>

                    <p class="description">
                        เลือกหมวดหมู่หลักของสินค้า ใช้สำหรับจัดกลุ่มสินค้าในระบบ
                    </p>
                </td>
            </tr>

            <!-- Subcategory -->
            <tr>
                <th>
                    <label for="product_subcategory">Subcategory</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <select name="product_subcategory" id="product_subcategory" class="regular-text" required>
                        <option value="">-- Select Subcategory --</option>

                        <?php
                        /**
                         * Loop all categories again.
                         * But this time:
                         *
                         * Only show categories where
                         * parent_id equals the detected parent.
                         *
                         * This ensures:
                         * - Only children of selected parent appear
                         * - Correct subcategory is pre-selected
                         */
                        foreach ($categories as $category) :
                            if ($category->parent_id == $current_parent_id) :
                        ?>
                                <option
                                    value="<?php echo esc_attr($category->id); ?>"
                                    <?php
                                    /**
                                     * Pre-select saved subcategory
                                     * from product table.
                                     */
                                    selected($current_subcategory_id, $category->id);
                                    ?>>
                                    <?php echo esc_html($category->category_name); ?>
                                </option>
                        <?php
                            endif;
                        endforeach;
                        ?>

                    </select>

                    <p class="description">
                        เลือกหมวดหมู่ย่อยของสินค้า ระบบจะแสดงตัวเลือกตามหมวดหมู่หลัก
                    </p>
                </td>
            </tr>

            <!-- UNIT COUNT -->
            <tr>
                <th>
                    <label for="product_unit_count">Unit Count</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <input type="number"
                        name="product_unit_count"
                        id="product_unit_count"
                        value="<?php echo esc_attr($product->product_unit_count); ?>"
                        required>

                    <p class="description">
                        ระบุจำนวนหน่วยของสินค้า เช่น 30, 60, 100 เป็นต้น
                    </p>
                </td>
            </tr>

            <!-- UNIT TYPE -->
            <tr>
                <th>
                    <label for="product_unit_type">Unit Type</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <select name="product_unit_type" required>
                        <?php foreach (['tablet', 'capsule', 'sachet', 'g', 'ml'] as $type): ?>
                            <option value="<?php echo esc_attr($type); ?>"
                                <?php selected($product->product_unit_type, $type); ?>>
                                <?php echo ucfirst($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        เลือกประเภทหน่วยของสินค้า เช่น เม็ด (tablet), แคปซูล (capsule), กรัม (g), มิลลิลิตร (ml)
                    </p>
                </td>
            </tr>

            <!-- UNIT USAGE -->
            <tr>
                <th>
                    <label for="product_unit_usage">Unit Usage</label>
                    <span style="color: #d63638;"> *Required</span>
                </th>
                <td>
                    <select name="product_unit_usage" required>
                        <option value="content"
                            <?php selected($product->product_unit_usage, 'content'); ?>>
                            บรรจุ / ปริมาณสินค้า
                        </option>

                        <option value="capacity"
                            <?php selected($product->product_unit_usage, 'capacity'); ?>>
                            ความจุ (ภาชนะ)
                        </option>
                    </select>
                    <p class="description">
                        เลือกว่าหน่วยที่ระบุเป็นปริมาณบรรจุสินค้า หรือความจุของภาชนะ
                    </p>
                </td>
            </tr>

            <!-- WYSIWYG FIELDS -->
            <?php
            $editors = [
                'product_description' => [
                    'rows' => 5,
                    'media' => false,
                    'required' => true,
                    'description' => 'รายละเอียดภาพรวมของสินค้า แสดงในหน้ารายละเอียดสินค้า'
                ],
                'product_detail' => [
                    'rows' => 20,
                    'media' => true,
                    'required' => true,
                    'description' => 'ข้อมูลรายละเอียดเชิงลึกของสินค้า เช่น วิธีใช้ หรือข้อมูลเพิ่มเติม'
                ],
                'product_specification' => [
                    'rows' => 20,
                    'media' => true,
                    'required' => true,
                    'description' => 'ข้อมูลสเปกสินค้า เช่น ขนาด น้ำหนัก ส่วนประกอบ'
                ],
                'product_comment' => [
                    'rows' => 10,
                    'media' => true,
                    'required' => false,
                    'description' => 'ข้อมูลเพิ่มเติมหรือหมายเหตุเกี่ยวกับสินค้า'
                ],
                'product_review' => [
                    'rows' => 20,
                    'media' => true,
                    'required' => false,
                    'description' => 'ข้อมูลรีวิวหรือคำแนะนำเกี่ยวกับสินค้า'
                ],
            ];

            ?>

            <?php foreach ($editors as $field => $config): ?>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr($field); ?>">
                            <?php echo esc_html(ucwords(str_replace('_', ' ', $field))); ?>

                            <?php if (!empty($config['required'])) : ?>
                                <span style="color: #d63638;"> *Required</span>
                            <?php endif; ?>
                        </label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $product->$field ?? '',
                            $field,
                            [
                                'textarea_name' => $field,
                                'textarea_rows' => $config['rows'],
                                'media_buttons' => $config['media'],
                            ]
                        );
                        ?>

                        <p class="description">
                            <?php echo esc_html($config['description']); ?>
                        </p>

                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <th scope="row">
                    <label for="product_price">Product Price</label>
                </th>
                <td>
                    <input type="number"
                        id="product_price"
                        name="product_price"
                        class="regular-text"
                        step="0.01"
                        min="0"
                        value="<?php echo esc_attr($product->product_price); ?>"
                        required>
                    <p class="description">Regular selling price (e.g. 199.00)</p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="product_member_price">Member Price</label>
                </th>
                <td>
                    <input type="number"
                        id="product_member_price"
                        name="product_member_price"
                        class="regular-text"
                        step="0.01"
                        min="0"
                        value="<?php echo esc_attr($product->product_member_price); ?>">
                    <p class="description">Optional discounted price for members</p>
                </td>
            </tr>

            <!-- PROPERTIES (Grouped) -->
            <tr>
                <th>Product Properties</th>
                <td>
                    <?php
                    $grouped = [];
                    foreach ($properties as $property) {
                        $grouped[$property->property_type][] = $property;
                    }

                    foreach ($grouped as $type => $items):
                    ?>
                        <p style="margin-top:15px;">
                            <?php echo esc_html(ucfirst($type)); ?>
                        </p>

                        <?php foreach ($items as $property): ?>
                            <label style="margin-left: 15px;">
                                <input type="checkbox"
                                    name="product_properties[]"
                                    value="<?php echo esc_attr($property->property_key); ?>"
                                    <?php checked(in_array($property->property_key, $selected_properties)); ?>>
                                <?php echo esc_html($property->property_name); ?>
                            </label>
                        <?php endforeach; ?>

                    <?php endforeach; ?>
                </td>
            </tr>

            <!-- TAGS -->
            <tr>
                <th>Product Tags</th>
                <td>
                    <input type="text" name="product_tags" class="regular-text"
                        value="<?php echo esc_attr($tags_string); ?>">
                    <p class="description">
                        ระบุคำค้น (Tag) เพื่อช่วยในการค้นหาสินค้า คั่นแต่ละคำด้วย comma (,)
                    </p>
                </td>
            </tr>

            <!-- LINKS -->
            <?php
            $link_fields = [
                'product_shop_link' => [
                    'label' => 'Product Shop Link',
                    'required' => true,
                    'description' => 'ลิงก์ไปยังหน้าซื้อสินค้าหรือร้านค้าออนไลน์'
                ],
                'product_sale_article_link' => [
                    'label' => 'Product Sale Article Link',
                    'required' => false,
                    'description' => 'ลิงก์ไปยังบทความแนะนำหรือบทความส่งเสริมการขายสินค้า'
                ],
            ];
            ?>

            <?php foreach ($link_fields as $name => $config): ?>
                <tr>
                    <th>
                        <label for="<?php echo esc_attr($name); ?>">
                            <?php echo esc_html($config['label']); ?>

                            <?php if (!empty($config['required'])) : ?>
                                <span style="color:#d63638; font-weight:500; margin-left:6px;">
                                    *Required
                                </span>
                            <?php endif; ?>
                        </label>
                    </th>
                    <td>
                        <input type="url"
                            id="<?php echo esc_attr($name); ?>"
                            name="<?php echo esc_attr($name); ?>"
                            class="regular-text"
                            value="<?php echo esc_url($product->$name); ?>"
                            <?php echo !empty($config['required']) ? 'required' : ''; ?>>

                        <p class="description">
                            <?php echo esc_html($config['description']); ?>
                        </p>
                    </td>
                </tr>
            <?php endforeach; ?>

            <!-- PINNED -->
            <tr>
                <th>Product is pinned</th>
                <td>
                    <label>
                        <input type="radio" name="product_is_pinned" value="1"
                            <?php checked($product->product_is_pinned, '1'); ?>>
                        Yes
                    </label>

                    <label>
                        <input type="radio" name="product_is_pinned" value="0"
                            <?php checked($product->product_is_pinned, '0'); ?>>
                        No
                    </label>
                </td>
            </tr>

            <!-- SLUG -->
            <tr>
                <th>
                    <label for="product_slug">Product Slug</label>
                    <span style="color:#d63638; font-weight:500; margin-left:6px;">
                        *Required
                    </span>
                </th>
                <td>
                    <input type="text" name="product_slug" id="product_slug"
                        class="regular-text"
                        value="<?php echo esc_attr($product->product_slug); ?>" readonly>
                    <p class="description">
                        Slug ของสินค้านี้ถูกล็อกไว้เพื่อป้องกันการเปลี่ยน URL
                        ซึ่งอาจทำให้ลิงก์เดิมและ SEO เสียหาย
                        หากจำเป็นต้องแก้ไข กรุณาดำเนินการผ่าน Database โดยผู้ดูแลระบบ
                    </p>
                </td>
            </tr>

            <!-- Videos -->
            <tr>
                <th scope="row">
                    <label>Product Videos</label>
                </th>
                <td>

                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <fieldset style="margin-bottom:15px;">
                            <strong>Video <?php echo $i + 1; ?></strong>

                            <p>
                                <label>Video URL</label><br>
                                <input type="url"
                                    name="video_datas[<?php echo $i; ?>][video_url]"
                                    class="regular-text"
                                    value="<?php echo esc_attr($videos[$i]->video_url ?? ''); ?>"
                                    placeholder="https://youtube.com/...">
                            </p>

                            <p>
                                <label>Video Comment</label><br>
                                <input type="text"
                                    name="video_datas[<?php echo $i; ?>][video_comment]"
                                    class="regular-text"
                                    value="<?php echo esc_attr($videos[$i]->video_comment ?? ''); ?>"
                                    placeholder="Optional description">
                            </p>
                        </fieldset>
                    <?php endfor; ?>

                </td>
            </tr>

        </table>

        <?php wp_nonce_field(
            'aboyuma_product_edit',
            'aboyuma_product_edit_nonce'
        ); ?>

        <p class="submit">

            <button type="submit" class="button button-primary">Update Product</button>

            <a href="<?php echo esc_url(admin_url('admin.php?page=aboyuma-product-list')); ?>"
                class="button button-secondary">Cancel</a>
        </p>

    </form>
</div>