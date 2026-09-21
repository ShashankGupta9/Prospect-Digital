-- =========================================================================
-- Prospect Digital — Store & E-Commerce Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.3+ / MySQL 8.0+
-- =========================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------------------
-- 1. Store Categories Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(64) NOT NULL UNIQUE,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(64) NOT NULL DEFAULT 'grid',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cat_slug` (`slug`),
    INDEX `idx_cat_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 2. Store Products Master Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_products` (
    `id` VARCHAR(64) NOT NULL PRIMARY KEY,
    `sku` VARCHAR(64) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(190) NOT NULL UNIQUE,
    `category_id` INT UNSIGNED NULL,
    `category_name` VARCHAR(128) NOT NULL DEFAULT 'General',
    `short_description` VARCHAR(500) NULL,
    `full_description` LONGTEXT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_price` DECIMAL(10,2) NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `stock_status` ENUM('in_stock', 'out_of_stock', 'preorder', 'discontinued') NOT NULL DEFAULT 'in_stock',
    `is_published` TINYINT(1) NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `seo_title` VARCHAR(255) NULL,
    `seo_description` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_prod_slug` (`slug`),
    INDEX `idx_prod_sku` (`sku`),
    INDEX `idx_prod_published` (`is_published`),
    INDEX `idx_prod_category` (`category_id`),
    INDEX `idx_prod_stock` (`stock_status`),
    INDEX `idx_prod_featured` (`is_featured`),
    CONSTRAINT `fk_store_products_category`
        FOREIGN KEY (`category_id`) REFERENCES `store_categories`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 3. Product Gallery Images Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_product_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` VARCHAR(64) NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_img_product` (`product_id`),
    CONSTRAINT `fk_store_images_product`
        FOREIGN KEY (`product_id`) REFERENCES `store_products`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 4. Product Technical Specifications Key-Value Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_product_specs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` VARCHAR(64) NOT NULL,
    `spec_key` VARCHAR(128) NOT NULL,
    `spec_value` TEXT NOT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    INDEX `idx_specs_product` (`product_id`),
    CONSTRAINT `fk_store_specs_product`
        FOREIGN KEY (`product_id`) REFERENCES `store_products`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 5. Product Key Features Bullet Points Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_product_features` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` VARCHAR(64) NOT NULL,
    `feature_text` TEXT NOT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    INDEX `idx_feat_product` (`product_id`),
    CONSTRAINT `fk_store_features_product`
        FOREIGN KEY (`product_id`) REFERENCES `store_products`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 6. Store Customers Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_customers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `name` VARCHAR(128) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `phone` VARCHAR(40) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_cust_email` (`email`),
    INDEX `idx_cust_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 7. Store Orders Master Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(64) NOT NULL UNIQUE,
    `customer_id` INT UNSIGNED NULL,
    `customer_name` VARCHAR(128) NOT NULL,
    `customer_email` VARCHAR(190) NOT NULL,
    `customer_phone` VARCHAR(40) NOT NULL,
    `shipping_address_line1` VARCHAR(255) NOT NULL,
    `shipping_address_line2` VARCHAR(255) NULL,
    `shipping_city` VARCHAR(100) NOT NULL,
    `shipping_state` VARCHAR(100) NOT NULL,
    `shipping_pincode` VARCHAR(20) NOT NULL,
    `shipping_country` VARCHAR(80) NOT NULL DEFAULT 'India',
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'upi',
    `payment_status` ENUM('pending', 'authorized', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `order_status` ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'confirmed',
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_order_num` (`order_number`),
    INDEX `idx_order_email` (`customer_email`),
    INDEX `idx_order_status` (`order_status`),
    INDEX `idx_order_payment` (`payment_status`),
    INDEX `idx_order_created` (`created_at`),
    CONSTRAINT `fk_store_orders_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `store_customers`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 8. Store Order Line Items Table
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `store_order_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` VARCHAR(64) NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_sku` VARCHAR(64) NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `quantity` INT NOT NULL DEFAULT 1,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_item_order` (`order_id`),
    INDEX `idx_item_product` (`product_id`),
    CONSTRAINT `fk_store_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `store_orders`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_store_items_product`
        FOREIGN KEY (`product_id`) REFERENCES `store_products`(`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------------------
-- Seed Standard Default Categories (Zero Products Added)
-- -------------------------------------------------------------------------
INSERT INTO `store_categories` (`slug`, `name`, `description`, `icon`, `is_active`, `display_order`)
VALUES
('hardware-iot', 'Hardware & IoT Kits', 'Industrial controllers, sensors, gateway devices, and smart automation modules.', 'cpu', 1, 1),
('software-licenses', 'Software Licenses', 'Enterprise license keys, self-hosted deployment packages, and subscriptions.', 'shield', 1, 2),
('developer-tools', 'Developer & API Tools', 'Developer software suites, API connector bridges, and hardware debuggers.', 'code', 1, 3),
('cloud-appliances', 'Cloud Appliances', 'Pre-configured on-premise cloud servers and private backup appliances.', 'cloud', 1, 4)
ON DUPLICATE KEY UPDATE
`name` = VALUES(`name`),
`description` = VALUES(`description`),
`icon` = VALUES(`icon`);
