<?php if (empty($products)) return; ?>

<div class="aboyuma-products-list my-4">



    <table id="aboyuma-product-list-table"
        class="table table-striped table-bordered align-middle"
        style="width:100%">

        <thead>
            <tr>
                <th style="width:30%;">สินค้า</th>
                <th>คำอธิบาย</th>
                <th style="width:120px;">รายละเอียด</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product): ?>


                <tr>

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
                                class="img-fluid mb-2"
                                style="max-height: 120px; width: auto; height: auto;">
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
                        <?php
                        echo esc_html(
                            wp_trim_words($product->product_description, 25, '...')
                        );
                        ?>
                    </td>

                    <!-- View Button -->
                    <td class="text-center">
                        <a href="<?php echo esc_url(home_url('/amway-products/'
                                        . $product->product_slug)); ?>"
                            class="btn btn-outline-primary btn-sm">
                            ดูรายละเอียด
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>
        </tbody>

    </table>



</div>