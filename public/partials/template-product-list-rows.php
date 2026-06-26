<?php

/**
 * Product table rows template.
 *
 * Variables passed from caller:
 *
 * @var array $products
 */

foreach ($products as $product):
?>

    <tr style="height:100%">

        <!-- Product Column -->
        <td class="text-center align-middle">

            <?php
            $image_url = '';

            if (!empty($product->main_image_id)) {
                $image_url = wp_get_attachment_image_url(
                    $product->main_image_id,
                    'medium'
                );
            }
            ?>

            <?php if ($image_url): ?>
                <img
                    src="<?php echo esc_url($image_url); ?>"
                    alt="<?php echo esc_attr($product->product_name); ?>"
                    class="img-fluid mb-2">
            <?php else: ?>
                <div class="mb-2 text-muted small">
                    No image
                </div>
            <?php endif; ?>

            <div class="fw-semibold">
                <?php echo esc_html($product->product_name); ?>
            </div>

        </td>

        <!-- Description Column -->
        <td>
            <div class="d-flex flex-column h-100">
                <!-- Brand + Category -->
                <div class="d-flex flex-wrap gap-2 mb-2">

                    <?php if (!empty($product->brand_name)) : ?>
                        <span class="badge text-bg-secondary">
                            🏷️ <?php echo esc_html($product->brand_name); ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($product->category_name)) : ?>
                        <span class="badge text-bg-secondary">
                            🗂️ <?php echo esc_html($product->category_name); ?>
                        </span>
                    <?php endif; ?>

                </div>

                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

                    <?php if ($product->product_member_price > 0) : ?>

                        <!-- Regular Price -->
                        <span class="small">
                            ราคา :
                            <del>
                                ฿<?php echo number_format($product->product_price, 2); ?>
                            </del>
                        </span>

                        <!-- Member Price -->
                        <span class="fw-semibold">
                            💳 ฿<?php echo number_format($product->product_member_price, 2); ?>
                            <span class="text-danger small">*เฉพาะสมาชิก</span>
                        </span>

                    <?php else : ?>

                        <!-- Regular Price Only -->
                        <span class="fw-semibold text-dark">
                            ฿<?php echo number_format($product->product_price, 2); ?>
                        </span>

                    <?php endif; ?>

                </div>

                <div class="mb-2">
                    <?php
                    echo esc_html(
                        wp_trim_words($product->product_description, 25, '...')
                    );
                    ?>
                </div>

                <div class="d-flex justify-content-end mt-auto">
                    <a href="<?php echo esc_url(
                                    add_query_arg(
                                        'product',
                                        $product->product_slug,
                                        get_permalink(get_option('aboyuma_product_page_id'))
                                    )
                                ); ?>"
                        class="btn btn-sm btn-outline-primary">
                        ดูรายละเอียด →
                    </a>
                </div>
            </div>

        </td>

    </tr>

<?php endforeach; ?>