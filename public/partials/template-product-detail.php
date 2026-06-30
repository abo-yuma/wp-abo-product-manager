<?php

/** @var stdClass $product */
?>
<div class="product-detail-wrapper">

    <section class="product-hero-section mb-3">
        <!-- Product Title -->
        <h1 class="product-title mb-2">
            <?php echo esc_html($product->product_name); ?>
        </h1>
        <div class="product-meta mb-2">
            <span class="text-muted small">

                <span class="me-2">
                    🗓 Posted on
                    <?php echo esc_html(date('F j, Y', strtotime($product->created))); ?>
                </span>

                <?php if ($product->modified && $product->modified !== $product->created) : ?>
                    <span>
                        (📝 Edit:
                        <?php echo esc_html(date('F j, Y', strtotime($product->modified))); ?>)
                    </span>
                <?php endif; ?>

            </span>
        </div>

        <!-- Brand + Category -->
        <div class="d-flex flex-wrap gap-2 mb-3">

            <?php if (!empty($product->brand_name)) : ?>
                <span class="badge text-bg-secondary">
                    🏷️ <?php echo esc_html($product->brand_name); ?>
                </span>
            <?php endif; ?>

            <?php if (!empty($product->category_name)) : ?>
                <span class="badge text-bg-secondary">
                    📂 <?php echo esc_html($product->category_name); ?>
                </span>
            <?php endif; ?>

        </div>

        <div class="product-media mb-3">

            <div class="product-gallery">

                <!-- Main Image -->
                <div class="product-main-image text-center mb-3">

                    <?php if (!empty($product->main_image_id)) :

                        $main_full = wp_get_attachment_image_url($product->main_image_id, 'full');
                        $main_img  = wp_get_attachment_image_url($product->main_image_id, 'product-400');
                    ?>

                        <a
                            href="<?php echo esc_url($main_full); ?>"
                            class="product-lightbox"
                            data-gallery="product-gallery"
                            id="main-product-link">

                            <img
                                src="<?php echo esc_url($main_img); ?>"
                                id="main-product-image"
                                class="rounded"
                                alt="<?php echo esc_attr($product->product_name); ?>"
                                loading="lazy">

                        </a>

                    <?php endif; ?>

                </div>


                <!-- Thumbnails -->
                <?php if (!empty($gallery_images)) : ?>

                    <div class="product-thumbnails">

                        <?php foreach ($gallery_images as $attachment_id) :

                            $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                            $medium = wp_get_attachment_image_url($attachment_id, 'product-400');
                            $full = wp_get_attachment_image_url($attachment_id, 'full');
                        ?>

                            <a
                                href="<?php echo esc_url($full); ?>"
                                class="product-thumb product-lightbox"
                                data-gallery="product-gallery"
                                data-image="<?php echo esc_url($medium); ?>">

                                <img
                                    src="<?php echo esc_url($thumb); ?>"
                                    alt="<?php echo esc_attr($product->product_name); ?>"
                                    loading="lazy">

                            </a>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

        <!-- Shop Button -->
        <div class="shop-button mb-3">
            <?php if (!empty($product->product_shop_link)) : ?>
                <div class="d-grid">
                    <a href="<?php echo esc_url($product->product_shop_link); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-primary btn-sm rounded-3">
                        ดูสินค้าที่ Amway
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-end mb-2">
            <a href="<?php echo esc_url(home_url('/amway-products/')); ?>" class="btn btn-outline-secondary btn-sm">
                ← กลับหน้ารายการสินค้า
            </a>
        </div>

        <div class="product-summary">
            <!-- Unit -->
            <?php if (!empty($product->product_unit_count)) : ?>

                <?php
                $unit_map = [
                    'tablet'  => 'เม็ด',
                    'capsule' => 'แคปซูล',
                    'sachet'  => 'ซอง',
                    'g'       => 'กรัม',
                    'ml'      => 'มิลลิลิตร'
                ];

                $unit_type = $product->product_unit_type;
                $unit_th = $unit_map[$unit_type] ?? $unit_type;
                ?>

                <span class="product-unit-count small">
                    <?php echo "บรรจุ : " . esc_html($product->product_unit_count . ' ' . $unit_th); ?>
                </span>

            <?php endif; ?>

            <div class="product-description mb-3">
                <?php echo wp_kses_post($product->product_description); ?>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">

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

            <?php
            if (!function_exists('render_property_group')) {
                function render_property_group($title, $properties, $icon) {
                    if (empty($properties)) {
                        return;
                    }

                    echo '<div class="mb-1">';
                    echo '<span class="me-2 small">'
                        . esc_html($title) . ' : </span>';

                    foreach ($properties as $property) {

                        $label = ucfirst($property->property_name);

                        echo '<span class="me-2">';
                        echo $icon . ' ' . esc_html($label);

                        if (!empty($property->property_description)) {
                            echo '<i class="fa-solid fa-circle-info ms-1"
                                style="cursor:pointer;"
                                data-bs-toggle="modal"
                                data-bs-target="#propertyModal-'
                                . esc_attr($property->property_key) . '">
                            </i>';
                        }
                        echo '</span>';
                    }
                    echo '</div>';
                }
            }
            ?>
            <div class="product-property mb-3">

                <?php if (!empty($selected_properties)) : ?>

                    <?php
                    $groups = [
                        'diet' => [],
                        'program' => [],
                        'additive' => [],
                        'feature' => []
                    ];

                    foreach ($selected_properties as $property) {

                        if (isset($groups[$property->property_type])) {
                            $groups[$property->property_type][] = $property;
                        }
                    }
                    ?>

                    <?php render_property_group('Diet', $groups['diet'], '🥗'); ?>
                    <?php render_property_group('Program', $groups['program'], '📋'); ?>
                    <?php render_property_group('Additive', $groups['additive'], '🧪'); ?>
                    <?php render_property_group('Feature', $groups['feature'], '⭐'); ?>

                <?php endif; ?>

            </div>

        </div>

    </section>



    <section class="product-details mt-4">

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="productTab" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                    id="detail-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#detail"
                    type="button"
                    role="tab">
                    📝 รายละเอียด
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link"
                    id="spec-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#spec"
                    type="button"
                    role="tab">
                    📋 ข้อมูลจำเพาะ
                </button>
            </li>

            <?php if (!empty($product->product_comment)) : ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        id="comment-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#comment"
                        type="button"
                        role="tab">
                        ⚠️ หมายเหตุ
                    </button>
                </li>
            <?php endif; ?>

            <?php if (!empty($product->product_review)) : ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                        id="review-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#review"
                        type="button"
                        role="tab">
                        💬 รีวิว
                    </button>
                </li>
            <?php endif; ?>

        </ul>

        <!-- Tabs Content -->
        <div class="tab-content border border-top-0 p-4">

            <!-- Detail -->
            <div class="tab-pane fade show active"
                id="detail"
                role="tabpanel">
                <?php echo wpautop(wp_kses_post($product->product_detail)); ?>
            </div>

            <!-- Specification -->
            <div class="tab-pane fade"
                id="spec"
                role="tabpanel">
                <?php echo wpautop(wp_kses_post($product->product_specification)); ?>
            </div>

            <!-- Comment -->
            <?php if (!empty($product->product_comment)) : ?>
                <div class="tab-pane fade"
                    id="comment"
                    role="tabpanel">
                    <?php echo wpautop(wp_kses_post($product->product_comment)); ?>
                </div>
            <?php endif; ?>

            <!-- Review -->
            <?php if (!empty($product->product_review)) : ?>
                <div class="tab-pane fade"
                    id="review"
                    role="tabpanel">
                    <?php echo wpautop(wp_kses_post($product->product_review)); ?>
                </div>
            <?php endif; ?>

        </div>

    </section>

    <section class="product-videos my-4">

        <h2 class="h6 fw-semibold mb-3">▶️ วิดีโอที่เกี่ยวข้อง</h2>

        <?php if (!empty($videos)) : ?>

            <?php foreach ($videos as $video) : ?>

                <?php
                $video_url = esc_url($video->video_url);
                $comment   = esc_html($video->video_comment);

                // Extract YouTube ID
                preg_match('/(?:youtu\.be\/|v=)([A-Za-z0-9_-]+)/', $video_url, $matches);
                $youtube_id = $matches[1] ?? '';

                if (!$youtube_id) continue;

                $thumbnail = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
                ?>

                <div class="row g-3 mb-4">

                    <!-- Thumbnail -->
                    <div class="col-md-4">

                        <a href="https://www.youtube.com/watch?v=<?php echo $youtube_id; ?>"
                            data-fancybox
                            class="text-decoration-none">

                            <div class="position-relative">

                                <img
                                    src="<?php echo esc_url($thumbnail); ?>"
                                    class="img-fluid rounded"
                                    loading="lazy"
                                    alt="Product video">

                                <img
                                    class="video-play-icon"
                                    src="https://api.iconify.design/logos/youtube-icon.svg"
                                    alt="Play">

                            </div>

                        </a>

                    </div>

                    <!-- Description -->
                    <div class="col-md-8">

                        <?php if (!empty($comment)) : ?>

                            <div class="pt-1 text-body-secondary">
                                <span class="me-1">📝</span>
                                <?php echo $comment; ?>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else : ?>

            <div class="alert alert-secondary text-center">
                ยังไม่มีวิดีโอที่เกี่ยวข้อง
            </div>

        <?php endif; ?>

    </section>

    <hr>
    <div class="d-flex justify-content-end mb-2">
        <a href="<?php echo esc_url(home_url('/amway-products/')); ?>"
            class="btn btn-outline-secondary btn-sm">
            ← กลับหน้ารายการสินค้า
        </a>
    </div>
</div> <!-- product-detail-wrapper -->

<!-- Modal section -->
<?php if (!empty($selected_properties)) : ?>
    <?php foreach ($selected_properties as $property) : ?>
        <?php if (!empty($property->property_description)) : ?>
            <div class="modal fade"
                id="propertyModal-<?php echo esc_attr($property->property_key); ?>"
                tabindex="-1">

                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <?php echo esc_html(ucfirst($property->property_key)); ?>
                            </h5>
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">
                            <?php echo wp_kses_post($property->property_description); ?>
                        </div>

                    </div>
                </div>

            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>