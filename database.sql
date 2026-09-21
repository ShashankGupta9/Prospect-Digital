-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: prospect_digital
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_activity_logs`
--

DROP TABLE IF EXISTS `admin_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_activity_logs`
--

LOCK TABLES `admin_activity_logs` WRITE;
/*!40000 ALTER TABLE `admin_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','staff') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiries`
--

DROP TABLE IF EXISTS `enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(40) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `budget` varchar(60) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','contacted','in_progress','completed','spam') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiries`
--

LOCK TABLES `enquiries` WRITE;
/*!40000 ALTER TABLE `enquiries` DISABLE KEYS */;
INSERT INTO `enquiries` VALUES (2,'PD-260919-9CF0','Aarav Mehta','aarav.mehta@example.com','9876543210','Mehta Logistics','Software Development','₹1,50,000 – ₹5,00,000','We need an ERP and delivery dispatch management software built for our team.','new','2026-09-19 07:27:13','2026-09-19 07:27:13'),(3,'PD-260919-BE7D','Vikram Malhotra','vikram@malhotralabs.com','9826012345','Malhotra Labs','Software Development','₹50,000 – ₹1,50,000','We need a custom inventory and ERP management system for our logistics warehouse.','new','2026-09-19 07:49:07','2026-09-19 07:49:07'),(4,'PD-260919-9617','Ananya Sen','ananya@senbiotech.in','9819055443','Sen Biotech','Software Development','₹1,50,000 – ₹5,00,000','We are seeking a bespoke web-based laboratory sample tracking portal with secure client reporting.','new','2026-09-19 08:15:04','2026-09-19 08:15:04'),(5,'PD-260919-12F6','Harsh','harsh123@gmail.com','4651667856','PropNEW','IT & Cloud','To be discussed','asdd fsda gasdf er gd','new','2026-09-19 08:35:33','2026-09-19 08:35:33'),(6,'PD-260919-4437','Shashank Gupta','shashank164@gmail.com','+916266806450','digiNEW','Software Development','To be discussed','Want the best solutions for my company..','new','2026-09-19 10:08:21','2026-09-19 10:08:21'),(7,'PD-260921-E4A7','Abhay','abhay098@gmail.com','587941257','PropNEW','Software Development','To be discussed','asfasfdsfad','new','2026-09-21 10:32:30','2026-09-21 10:32:30'),(8,'PD-260921-4A95','Abhay','abhay098@gmail.com','587941257','PropNEW','Website Development','To be discussed','kjhygtfrdes','new','2026-09-21 10:33:07','2026-09-21 10:33:07'),(9,'PD-260921-BBA0','Abhay','abhay098@gmail.com','587941257','PropNEW','Growth Strategy','To be discussed','sdfhy str srt stryuj srt utr t','new','2026-09-21 10:37:31','2026-09-21 10:37:31'),(10,'PD-260921-274D','Abhay','abhay098@gmail.com','587941257','PropNEW','Paid Ads','To be discussed','cccccccccccccvg h tr e j y t','new','2026-09-21 11:44:13','2026-09-21 11:44:13');
/*!40000 ALTER TABLE `enquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pd_admin_users`
--

DROP TABLE IF EXISTS `pd_admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pd_admin_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pd_admin_users`
--

LOCK TABLES `pd_admin_users` WRITE;
/*!40000 ALTER TABLE `pd_admin_users` DISABLE KEYS */;
INSERT INTO `pd_admin_users` VALUES (1,'admin','$2y$10$nvL64RB/3zenCxgSPaBiEuppofH7cOweTRrDtzbkcgDGj5jHsmFjG',1,'2026-09-17 22:10:52','2026-09-18 07:37:38');
/*!40000 ALTER TABLE `pd_admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pd_leads`
--

DROP TABLE IF EXISTS `pd_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pd_leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(160) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `company` varchar(140) NOT NULL DEFAULT '',
  `service` varchar(120) NOT NULL,
  `message` text NOT NULL,
  `source` varchar(100) NOT NULL DEFAULT '',
  `session_id` varchar(100) NOT NULL DEFAULT '',
  `page_url` varchar(500) NOT NULL DEFAULT '',
  `ip` varchar(45) NOT NULL DEFAULT '',
  `user_agent` varchar(300) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_id` (`lead_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pd_leads`
--

LOCK TABLES `pd_leads` WRITE;
/*!40000 ALTER TABLE `pd_leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `pd_leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pd_newsletter`
--

DROP TABLE IF EXISTS `pd_newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pd_newsletter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(160) NOT NULL,
  `created_at` datetime NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `user_agent` varchar(300) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_newsletter_email` (`email`),
  KEY `idx_newsletter_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pd_newsletter`
--

LOCK TABLES `pd_newsletter` WRITE;
/*!40000 ALTER TABLE `pd_newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `pd_newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pd_site_ctas`
--

DROP TABLE IF EXISTS `pd_site_ctas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pd_site_ctas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ckey` varchar(64) NOT NULL,
  `label` varchar(120) NOT NULL,
  `link` varchar(300) NOT NULL DEFAULT '',
  `style` varchar(60) NOT NULL DEFAULT 'btn--red',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cta_key` (`ckey`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pd_site_ctas`
--

LOCK TABLES `pd_site_ctas` WRITE;
/*!40000 ALTER TABLE `pd_site_ctas` DISABLE KEYS */;
INSERT INTO `pd_site_ctas` VALUES (1,'header_cta','Let\'s talk','/contact','btn--red btn--sm',1,1,'2026-09-17 22:10:52'),(2,'hero_primary','Start a conversation','/contact','btn--red btn--lg btn--arrow btn--pulse',2,1,'2026-09-17 22:10:52'),(3,'hero_secondary','Explore our work','/#work','btn--ghost btn--lg',3,1,'2026-09-17 22:10:52'),(4,'demo_request','Request a demo','','btn--red',4,1,'2026-09-17 22:10:52'),(5,'guides_cta','Talk to us','/contact','btn--red btn--lg',5,1,'2026-09-17 22:10:52');
/*!40000 ALTER TABLE `pd_site_ctas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_categories`
--

DROP TABLE IF EXISTS `store_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(64) NOT NULL DEFAULT 'grid',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_cat_slug` (`slug`),
  KEY `idx_cat_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_categories`
--

LOCK TABLES `store_categories` WRITE;
/*!40000 ALTER TABLE `store_categories` DISABLE KEYS */;
INSERT INTO `store_categories` VALUES (1,'hardware-iot','Hardware & IoT Kits','Industrial controllers, sensors, gateway devices, and smart automation modules.','cpu',1,1,'2026-09-19 16:05:09','2026-09-19 16:05:09'),(2,'software-licenses','Software Licenses','Enterprise license keys, self-hosted deployment packages, and subscriptions.','shield',1,2,'2026-09-19 16:05:09','2026-09-19 16:05:09'),(3,'developer-tools','Developer & API Tools','Developer software suites, API connector bridges, and hardware debuggers.','code',1,3,'2026-09-19 16:05:09','2026-09-19 16:05:09'),(4,'cloud-appliances','Cloud Appliances','Pre-configured on-premise cloud servers and private backup appliances.','cloud',1,4,'2026-09-19 16:05:09','2026-09-19 16:05:09');
/*!40000 ALTER TABLE `store_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_customers`
--

DROP TABLE IF EXISTS `store_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cust_email` (`email`),
  KEY `idx_cust_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_customers`
--

LOCK TABLES `store_customers` WRITE;
/*!40000 ALTER TABLE `store_customers` DISABLE KEYS */;
INSERT INTO `store_customers` VALUES (6,5,'Vikram Malhotra','vikram.malhotra@example.com','9876543210','2026-09-19 17:30:03','2026-09-19 17:30:03');
/*!40000 ALTER TABLE `store_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_order_items`
--

DROP TABLE IF EXISTS `store_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` varchar(64) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(64) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_item_order` (`order_id`),
  KEY `idx_item_product` (`product_id`),
  CONSTRAINT `fk_store_items_order` FOREIGN KEY (`order_id`) REFERENCES `store_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_store_items_product` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_order_items`
--

LOCK TABLES `store_order_items` WRITE;
/*!40000 ALTER TABLE `store_order_items` DISABLE KEYS */;
INSERT INTO `store_order_items` VALUES (6,6,NULL,'Industrial IoT Edge Gateway','PD-GW-2026',3999.00,1,3999.00,'2026-09-19 17:30:03');
/*!40000 ALTER TABLE `store_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_orders`
--

DROP TABLE IF EXISTS `store_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(64) NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `customer_name` varchar(128) NOT NULL,
  `customer_email` varchar(190) NOT NULL,
  `customer_phone` varchar(40) NOT NULL,
  `shipping_address_line1` varchar(255) NOT NULL,
  `shipping_address_line2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(100) NOT NULL,
  `shipping_pincode` varchar(20) NOT NULL,
  `shipping_country` varchar(80) NOT NULL DEFAULT 'India',
  `payment_method` varchar(50) NOT NULL DEFAULT 'upi',
  `payment_status` enum('pending','authorized','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'confirmed',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_order_num` (`order_number`),
  KEY `idx_order_email` (`customer_email`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_order_payment` (`payment_status`),
  KEY `idx_order_created` (`created_at`),
  KEY `fk_store_orders_customer` (`customer_id`),
  CONSTRAINT `fk_store_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `store_customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_orders`
--

LOCK TABLES `store_orders` WRITE;
/*!40000 ALTER TABLE `store_orders` DISABLE KEYS */;
INSERT INTO `store_orders` VALUES (6,'PD-ORD-260919-A2E10',6,'Vikram Malhotra','vikram.malhotra@example.com','9876543210','42 Silicon Valley Boulevard, Tech Zone, Suite 101','','Bengaluru','Karnataka','560100','India','upi','paid','shipped',3999.00,719.82,0.00,0.00,4718.82,'','2026-09-19 17:30:03','2026-09-19 17:32:39');
/*!40000 ALTER TABLE `store_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_product_features`
--

DROP TABLE IF EXISTS `store_product_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_product_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(64) NOT NULL,
  `feature_text` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_feat_product` (`product_id`),
  CONSTRAINT `fk_store_features_product` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_product_features`
--

LOCK TABLES `store_product_features` WRITE;
/*!40000 ALTER TABLE `store_product_features` DISABLE KEYS */;
INSERT INTO `store_product_features` VALUES (5,'prod_f8d1ceb4ff','Complete fingerprint-based attendance solution with device connectivity, real-time attendance tracking and centralized web dashboard',0);
/*!40000 ALTER TABLE `store_product_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_product_images`
--

DROP TABLE IF EXISTS `store_product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(64) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_img_product` (`product_id`),
  CONSTRAINT `fk_store_images_product` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_product_images`
--

LOCK TABLES `store_product_images` WRITE;
/*!40000 ALTER TABLE `store_product_images` DISABLE KEYS */;
INSERT INTO `store_product_images` VALUES (7,'prod_f8d1ceb4ff','https://chatgpt.com/backend-api/estuary/content?id=file_00000000093c81fabc77aea896cd4626&ts=497215&p=fs&cid=1&sig=e04f7b09e147ade5deeb8c9de04c83ecfcacf4a6dc9c50b221e9616ba2acba85&v=0',1,0,'2026-09-21 12:36:42');
/*!40000 ALTER TABLE `store_product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_product_specs`
--

DROP TABLE IF EXISTS `store_product_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_product_specs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(64) NOT NULL,
  `spec_key` varchar(128) NOT NULL,
  `spec_value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_specs_product` (`product_id`),
  CONSTRAINT `fk_store_specs_product` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_product_specs`
--

LOCK TABLES `store_product_specs` WRITE;
/*!40000 ALTER TABLE `store_product_specs` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_product_specs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_products`
--

DROP TABLE IF EXISTS `store_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_products` (
  `id` varchar(64) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `category_name` varchar(128) NOT NULL DEFAULT 'General',
  `short_description` varchar(500) DEFAULT NULL,
  `full_description` longtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `stock_status` enum('in_stock','out_of_stock','preorder','discontinued') NOT NULL DEFAULT 'in_stock',
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_prod_slug` (`slug`),
  KEY `idx_prod_sku` (`sku`),
  KEY `idx_prod_published` (`is_published`),
  KEY `idx_prod_category` (`category_id`),
  KEY `idx_prod_stock` (`stock_status`),
  KEY `idx_prod_featured` (`is_featured`),
  CONSTRAINT `fk_store_products_category` FOREIGN KEY (`category_id`) REFERENCES `store_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_products`
--

LOCK TABLES `store_products` WRITE;
/*!40000 ALTER TABLE `store_products` DISABLE KEYS */;
INSERT INTO `store_products` VALUES ('prod_f8d1ceb4ff','PD-FAS-001','Industrial Fingerprint Attendance System','industrial-fingerprint-attendance-system',1,'Hardware & IoT Kits','Complete fingerprint-based attendance solution with device connectivity, real-time attendance tracking and centralized web dashboard','The Prospect Industrial Attendance System is designed for\r\nbusinesses that need reliable employee attendance tracking.\r\n\r\nKey Features\r\n\r\n• Fingerprint authentication\r\n• Real-time attendance synchronization\r\n• Centralized web dashboard\r\n• Employee management\r\n• Attendance reports\r\n• Database integration\r\n• Secure admin access',5000.00,3000.00,100,'in_stock',1,0,'Industrial Fingerprint Attendance System | Prospect Digital Store','Complete fingerprint-based attendance solution with device connectivity, real-time attendance tracking and centralized web dashboard','2026-09-21 11:58:34','2026-09-21 12:36:42');
/*!40000 ALTER TABLE `store_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Shashank Gupta','shashank164@gmail.com','+916266806450','$2y$10$plKTHx72FEntye2/9ygvrunfUD5nIrI6Im.hoJg2WCKAUNWwg/V4C','active','2026-09-19 11:41:49','2026-09-20 15:14:36'),(4,'Harsh','harsh123@gmail.com','4651667856','$2y$10$JWmDn.MSpotOync9WDBBO.nG8RWZLm1epfIBde5zxEsLvLyZ2.UP6','active','2026-09-19 13:19:25','2026-09-19 13:19:25'),(5,'Vikram Malhotra','vikram.malhotra@example.com','9876543210','$2y$10$.aR6bLeEWEJ0TVentNzS9OdhHDT5hdDWx.Xh94nMaU7QY5AVrF0um','active','2026-09-19 17:12:01','2026-09-19 17:12:01'),(6,'Deev','deev123@gmail.com','159753655','$2y$10$DWE9OcexP2Q9UY24CVtUs.aHhisznscnMHsuey2oc1c0HDC4wp8D6','active','2026-09-20 15:00:27','2026-09-20 15:00:27'),(7,'Abhay','abhay098@gmail.com','587941257','$2y$10$VD4fuSeWJgGLmEKMz4zuiehgBix6Fwax4rMlqmUY89EhndMMK7RY.','active','2026-09-21 11:54:27','2026-09-21 11:54:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-21 17:16:44
