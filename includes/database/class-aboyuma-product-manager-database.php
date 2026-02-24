<?php

/**
 * Custom database creation class.
 *
 * This is used to create database schema when plugin is activated.
 *
 *
 * @since      1.0.0
 * @package    Aboyuma_Product_Manager
 * @subpackage Aboyuma_Product_Manager/includes
 * @author     Yuma <yuma.dev03@gmail.com>
 */

defined('ABSPATH') || exit;

class Aboyuma_Product_Manager_Database {

    const DB_VERSION = '1.0.0';
    const OPTION_KEY = 'aboyuma_product_manager_db_version';

    /**
     * Run on plugin activation
     */
    public static function install() {
        self::create_tables();
        self::create_foreign_keys();

        update_option(self::OPTION_KEY, self::DB_VERSION);
    }

    /**
     * Verify database version and upgrade if required.
     */
    public static function check_database_version() {
        $installed_version = get_option(self::OPTION_KEY);

        if (!$installed_version || version_compare($installed_version, self::DB_VERSION, '<')) {
            self::install();
        }
    }

    /**
     * Create tables using dbDelta
     */
    private static function create_tables() {

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        global $wpdb;

        $productTable              = $wpdb->prefix . 'aboyuma_product';
        $productPropertyTable      = $wpdb->prefix . 'aboyuma_product_property';
        $productPropertyPivotTable = $wpdb->prefix . 'aboyuma_product_property_pivot';
        $productBrandTable         = $wpdb->prefix . 'aboyuma_product_brand';
        $productCategoryTable      = $wpdb->prefix . 'aboyuma_product_category';
        $productTagTable           = $wpdb->prefix . 'aboyuma_product_tag';
        $productImageTable         = $wpdb->prefix . 'aboyuma_product_image';
        $productVideoTable         = $wpdb->prefix . 'aboyuma_product_video';

        $charset_collate = $wpdb->get_charset_collate();

        $queries = [];

        // -------------------------------
        // BRAND
        // -------------------------------
        $queries[] = "CREATE TABLE $productBrandTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			brand_image_url VARCHAR(255) NOT NULL,
			brand_name VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY idx_brand_name (brand_name)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // CATEGORY
        // -------------------------------
        $queries[] = "CREATE TABLE $productCategoryTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			category_name VARCHAR(255) NOT NULL,
			parent_id INT UNSIGNED DEFAULT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY idx_parent_id (parent_id),
			KEY idx_category_name (category_name)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // PRODUCT
        // -------------------------------
        $queries[] = "CREATE TABLE $productTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			main_image_id BIGINT UNSIGNED NOT NULL,
			product_code VARCHAR(50) NOT NULL,
			product_brand_id INT UNSIGNED NOT NULL,
			product_category_id INT UNSIGNED NOT NULL,
			product_name VARCHAR(255) NOT NULL,
			product_unit_count INT UNSIGNED NOT NULL,
			product_unit_type ENUM('tablet','capsule','sachet','g','ml') NOT NULL,
			product_unit_usage ENUM('content','capacity') NOT NULL DEFAULT 'content',
			product_short_description LONGTEXT NOT NULL,
			product_description LONGTEXT NOT NULL,
			product_benefit LONGTEXT NOT NULL,
			product_detail LONGTEXT NOT NULL,
			product_specification LONGTEXT NOT NULL,
			product_comment TEXT NULL,
			product_review LONGTEXT NULL,
			product_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			product_member_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			product_sop_link VARCHAR(255) NULL,
			product_shop_link VARCHAR(255) NOT NULL,
			product_sale_article_link VARCHAR(255) NULL,
			product_is_pinned TINYINT(1) NOT NULL DEFAULT 0,
			product_slug VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY uq_product_slug (product_slug),
			UNIQUE KEY uq_product_code (product_code),
			KEY idx_brand_id (product_brand_id),
			KEY idx_category_id (product_category_id),
			KEY idx_main_image_id (main_image_id),
			KEY idx_price (product_price),
			KEY idx_is_pinned (product_is_pinned)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // PROPERTY
        // -------------------------------
        $queries[] = "CREATE TABLE $productPropertyTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			property_key VARCHAR(50) NOT NULL,
			property_name VARCHAR(100) NOT NULL,
			property_type ENUM('additive','diet','program','feature') NOT NULL,
			property_description LONGTEXT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY uq_property_key (property_key),
			KEY idx_property_type (property_type)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // PROPERTY PIVOT
        // -------------------------------
        $queries[] = "CREATE TABLE $productPropertyPivotTable (
			product_id INT UNSIGNED NOT NULL,
			property_id INT UNSIGNED NOT NULL,
			PRIMARY KEY (product_id, property_id),
			KEY idx_property_id (property_id)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // TAG
        // -------------------------------
        $queries[] = "CREATE TABLE $productTagTable (
			product_id INT UNSIGNED NOT NULL,
			tag_name VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (product_id, tag_name),
			KEY idx_tag_name (tag_name)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // IMAGE
        // -------------------------------
        $queries[] = "CREATE TABLE $productImageTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id INT UNSIGNED NOT NULL,
			attachment_id BIGINT UNSIGNED NOT NULL,
			image_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY uq_product_attachment (product_id, attachment_id),
			KEY idx_product_order (product_id, image_order),
			KEY idx_attachment_id (attachment_id)
		) ENGINE=InnoDB $charset_collate;";

        // -------------------------------
        // VIDEO
        // -------------------------------
        $queries[] = "CREATE TABLE $productVideoTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id INT UNSIGNED NOT NULL,
			video_url VARCHAR(255) NOT NULL,
			video_comment VARCHAR(255) NOT NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY idx_product_id (product_id)
		) ENGINE=InnoDB $charset_collate;";

        foreach ($queries as $sql) {
            dbDelta($sql);
        }
    }

    /**
     * Create foreign keys safely
     */
    private static function create_foreign_keys() {

        global $wpdb;

        $productTable              = $wpdb->prefix . 'aboyuma_product';
        $productPropertyTable      = $wpdb->prefix . 'aboyuma_product_property';
        $productPropertyPivotTable = $wpdb->prefix . 'aboyuma_product_property_pivot';
        $productBrandTable         = $wpdb->prefix . 'aboyuma_product_brand';
        $productCategoryTable      = $wpdb->prefix . 'aboyuma_product_category';
        $productTagTable           = $wpdb->prefix . 'aboyuma_product_tag';
        $productImageTable         = $wpdb->prefix . 'aboyuma_product_image';
        $productVideoTable         = $wpdb->prefix . 'aboyuma_product_video';

        $foreign_keys = [

            [
                $productCategoryTable,
                'fk_aboyuma_category_parent',
                "ALTER TABLE $productCategoryTable
                ADD CONSTRAINT fk_aboyuma_category_parent
                FOREIGN KEY (parent_id)
                REFERENCES $productCategoryTable(id)
                ON DELETE SET NULL"
            ],

            [
                $productPropertyPivotTable,
                'fk_aboyuma_property_product',
                "ALTER TABLE $productPropertyPivotTable
                ADD CONSTRAINT fk_aboyuma_property_product
                FOREIGN KEY (product_id)
                REFERENCES $productTable(id)
                ON DELETE CASCADE"
            ],

            [
                $productPropertyPivotTable,
                'fk_aboyuma_property_definition',
                "ALTER TABLE $productPropertyPivotTable
                ADD CONSTRAINT fk_aboyuma_property_definition
                FOREIGN KEY (property_id)
                REFERENCES $productPropertyTable(id)
                ON DELETE CASCADE"
            ],

            [
                $productTable,
                'fk_aboyuma_product_brand',
                "ALTER TABLE $productTable
                ADD CONSTRAINT fk_aboyuma_product_brand
                FOREIGN KEY (product_brand_id)
                REFERENCES $productBrandTable(id)
                ON DELETE SET NULL"
            ],

            [
                $productTable,
                'fk_aboyuma_product_category',
                "ALTER TABLE $productTable
                ADD CONSTRAINT fk_aboyuma_product_category
                FOREIGN KEY (product_category_id)
                REFERENCES $productCategoryTable(id)
                ON DELETE SET NULL"
            ],

            [
                $productTagTable,
                'fk_aboyuma_tag_product',
                "ALTER TABLE $productTagTable
                ADD CONSTRAINT fk_aboyuma_tag_product
                FOREIGN KEY (product_id)
                REFERENCES $productTable(id)
                ON DELETE CASCADE"
            ],

            [
                $productImageTable,
                'fk_aboyuma_image_product',
                "ALTER TABLE $productImageTable
                ADD CONSTRAINT fk_aboyuma_image_product
                FOREIGN KEY (product_id)
                REFERENCES $productTable(id)
                ON DELETE CASCADE"
            ],

            [
                $productVideoTable,
                'fk_aboyuma_video_product',
                "ALTER TABLE $productVideoTable
                ADD CONSTRAINT fk_aboyuma_video_product
                FOREIGN KEY (product_id)
                REFERENCES $productTable(id)
                ON DELETE CASCADE"
            ],
        ];

        foreach ($foreign_keys as [$table, $fk_name, $sql]) {
            if (!self::foreign_key_exists($table, $fk_name)) {
                $wpdb->query($sql);
            }
        }
    }

    private static function foreign_key_exists($table, $fk_name) {
        global $wpdb;

        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT CONSTRAINT_NAME
			 FROM information_schema.TABLE_CONSTRAINTS
			 WHERE TABLE_SCHEMA = DATABASE()
			 AND TABLE_NAME = %s
			 AND CONSTRAINT_NAME = %s",
            $table,
            $fk_name
        ));

        return !empty($result);
    }
}
