<?php

/**
 * Product list template.
 *
 * Variables passed from caller:
 *
 * @var array $products
 * @var array $categories
 */
if (empty($products)) return;
?>

<div class="aboyuma-products-list">

    <div class="card border-0 mb-4">

        <div class="card-body p-0">

            <h2 class="h5 mb-2">
                🔍 ค้นหาสินค้าที่ต้องการ
            </h2>

            <p class="text-muted mb-3">
                กรอกปัญหา หรือชื่อสินค้าที่กำลังมองหา
            </p>

            <form id="aboyuma-product-search-form">

                <!-- Category Row -->
                <div class="row g-2 mb-2">

                    <!-- Parent Category -->
                    <div class="col-auto">

                        <select
                            id="parent_category"
                            class="form-select form-select-sm"
                            style="width:170px;">

                            <option value="">
                                ทุกหมวดหมู่หลัก
                            </option>

                            <?php foreach ($categories as $category) : ?>

                                <?php if ((int) $category->parent_id === 0) : ?>

                                    <option value="<?php echo esc_attr($category->id); ?>">
                                        <?php echo esc_html($category->category_name); ?>
                                    </option>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Subcategory -->
                    <div class="col-auto">

                        <select
                            id="product_subcategory"
                            class="form-select form-select-sm"
                            style="width:170px;">

                            <option value="">
                                ทุกหมวดหมู่ย่อย
                            </option>

                            <?php foreach ($categories as $category) : ?>

                                <?php if ((int) $category->parent_id > 0) : ?>

                                    <option
                                        value="<?php echo esc_attr($category->id); ?>"
                                        data-parent-id="<?php echo esc_attr($category->parent_id); ?>"
                                        style="display:none;">
                                        <?php echo esc_html($category->category_name); ?>
                                    </option>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <!-- Search Row -->
                <div class="row g-2">

                    <!-- Keyword -->
                    <div class="col">

                        <input
                            type="text"
                            id="aboyuma-product-search-keyword"
                            class="form-control form-control-sm"
                            placeholder="เช่น อ่อนเพลีย, ผิวแห้ง, โปรตีน">

                    </div>

                    <!-- Search Button -->
                    <div class="col-auto">

                        <button
                            id="aboyuma-product-search-button"
                            type="button"
                            class="btn btn-sm btn-light">

                            ค้นหา

                        </button>

                        <button
                            type="reset"
                            class="btn btn-sm btn-light">

                            รีเซ็ต

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div id="aboyuma-product-table-wrapper">

        <div id="aboyuma-product-search-message"></div>
        <table id="aboyuma-product-list-table"
            class="table"
            style="height:fit-content">

            <thead>
                <tr>
                    <th>🛍️ สินค้า</th>
                    <th>📝 รายละเอียด</th>
                </tr>
            </thead>

            <tbody>
                <?php require plugin_dir_path(__FILE__) .
                    'template-product-list-rows.php'; ?>
            </tbody>

        </table>
    </div>
</div>