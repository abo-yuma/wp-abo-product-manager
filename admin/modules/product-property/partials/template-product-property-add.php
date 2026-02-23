<div class="wrap">
    <h1>Add New Property</h1>

    <form method="post">
        <?php wp_nonce_field('aboyuma_amway_product_property_add_nonce'); ?>

        <table class="form-table">

            <!-- Property Key -->
            <tr>
                <th>
                    <label for="property_key">Property Key *</label>
                </th>
                <td>
                    <input type="text"
                        name="property_key"
                        id="property_key"
                        class="regular-text"
                        required>

                    <p class="description">
                        รหัสเฉพาะของคุณสมบัติ (ห้ามซ้ำ) ใช้ตัวอักษรภาษาอังกฤษพิมพ์เล็ก และเครื่องหมายขีดกลาง (-) เท่านั้น
                        ตัวอย่าง: <code>vegan</code>, <code>halal-certified</code>
                        *ไม่สามารถแก้ไขภายหลังได้*
                    </p>
                </td>
            </tr>

            <!-- Property Name -->
            <tr>
                <th>
                    <label for="property_name">Property Name *</label>
                </th>
                <td>
                    <input type="text"
                        name="property_name"
                        id="property_name"
                        class="regular-text"
                        required>

                    <p class="description">
                        ชื่อที่จะแสดงผลในระบบและหน้าสินค้า
                        ตัวอย่าง: <strong>สินค้าเหมาะสำหรับผู้ทานมังสวิรัติ</strong>
                    </p>
                </td>
            </tr>

            <!-- Property Type -->
            <tr>
                <th>
                    <label for="property_type">Property Type *</label>
                </th>
                <td>
                    <select name="property_type" id="property_type" required>
                        <option value="">-- กรุณาเลือกประเภท --</option>
                        <option value="additive">Additive (สารประกอบ)</option>
                        <option value="diet">Diet (รูปแบบการรับประทาน)</option>
                        <option value="program">Program (โปรแกรม)</option>
                        <option value="feature">Feature (คุณสมบัติพิเศษ)</option>
                    </select>

                    <p class="description">
                        ใช้สำหรับจัดกลุ่มคุณสมบัติ เพื่อการแสดงผลและการกรองข้อมูลสินค้า
                    </p>
                </td>
            </tr>

            <!-- Description -->
            <tr>
                <th>
                    <label for="property_description">Description</label>
                </th>
                <td>
                    <?php
                    wp_editor(
                        '',
                        'property_description',
                        [
                            'textarea_name' => 'property_description',
                            'textarea_rows' => 8,
                            'media_buttons' => false
                        ]
                    );
                    ?>

                    <p class="description">
                        รายละเอียดเพิ่มเติมของคุณสมบัติ (ไม่บังคับกรอก)
                        ข้อมูลนี้สามารถนำไปแสดงในหน้ารายละเอียดสินค้าได้
                    </p>
                </td>
            </tr>

        </table>

        <p>
            <button type="submit"
                name="submit_property"
                class="button button-primary">
                Save Property
            </button>

            <a href="<?php echo admin_url('admin.php?page=aboyuma-product-property-list'); ?>"
                class="button">
                Cancel
            </a>
        </p>
    </form>
</div>