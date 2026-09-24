-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: marketlink
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_by` bigint unsigned NOT NULL,
  `published_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_publisher_fk` (`published_by`),
  KEY `announcements_published_at_index` (`published_at`),
  KEY `announcements_status_index` (`status`),
  CONSTRAINT `announcements_publisher_fk` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'Saturday peak hours','Expect busy pickup windows between 10–12. Arrive in your booked slot.',1,'2026-09-23 09:47:37',NULL,'published','medium','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'New farmers onboarded','Welcome Herb Haven and Sunrise Dairy to MarketLink this week.',1,'2026-09-22 09:47:37',NULL,'published','low','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,'Holiday market hours','Some markets close early on holidays — check each market page.',1,'2026-09-24 09:47:37','2026-10-24 09:47:37','published','high','2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('app_settings','a:4:{s:12:\"site_tagline\";s:32:\"Fresh from local farmers markets\";s:13:\"support_email\";s:22:\"support@marketlink.com\";s:20:\"default_cutoff_hours\";s:2:\"24\";s:19:\"low_stock_threshold\";s:1:\"5\";}',1790243569);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_product_id_unique` (`cart_id`,`product_id`),
  KEY `cart_items_cart_id_index` (`cart_id`),
  KEY `cart_items_product_id_index` (`product_id`),
  CONSTRAINT `cart_items_cart_fk` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_customer_id_unique` (`customer_id`),
  CONSTRAINT `carts_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,3,'2026-09-24 09:47:36','2026-09-24 09:47:36');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Vegetables','Leafy greens and garden vegetables','bi-carrot','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'Fruits','Seasonal orchard fruits','bi-apple','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,'Dairy','Milk, cheese, yogurt','bi-cup-straw','2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,'Baked Goods','Fresh breads and pastries','bi-basket2','2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,'Herbs','Culinary and medicinal herbs','bi-flower1','2026-09-24 09:47:37','2026-09-24 09:47:37'),(6,'Eggs','Farm-fresh eggs','bi-egg','2026-09-24 09:47:37','2026-09-24 09:47:37'),(7,'Honey','Local raw honey','bi-droplet','2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_faqs`
--

DROP TABLE IF EXISTS `chatbot_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_faqs_question_index` (`question`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_faqs`
--

LOCK TABLES `chatbot_faqs` WRITE;
/*!40000 ALTER TABLE `chatbot_faqs` DISABLE KEYS */;
INSERT INTO `chatbot_faqs` VALUES (1,'How do I place a pre-order?','Browse products, add items to your cart, then checkout with a pickup date and time slot. Pay in person at the stall.','orders','[\"pre-order\", \"order\", \"checkout\", \"cart\"]','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'Do you deliver?','No. MarketLink is pickup-only at the farmers market stall. There is no delivery.','pickup','[\"deliver\", \"delivery\", \"shipping\"]','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,'How do I pay?','There is no online payment. Bring cash or card as accepted by the farmer and pay when you pick up.','payment','[\"pay\", \"payment\", \"cash\", \"card\"]','2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,'Can I cancel my order?','Yes, you can modify or cancel before the order cutoff time shown on your order page.','orders','[\"cancel\", \"modify\", \"change\", \"cutoff\"]','2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,'How do farmers get approved?','Farmers register with stall details. An admin reviews and approves before products appear publicly.','farmers','[\"approve\", \"farmer\", \"register\", \"approval\"]','2026-09-24 09:47:37','2026-09-24 09:47:37'),(6,'Where is my pickup location?','Each order lists the market and farmer stall. Use the market map for directions via OpenStreetMap.','pickup','[\"pickup\", \"location\", \"stall\", \"map\"]','2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `chatbot_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farmer_markets`
--

DROP TABLE IF EXISTS `farmer_markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer_markets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint unsigned NOT NULL,
  `market_id` bigint unsigned NOT NULL,
  `stall_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_day` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `farmer_markets_farmer_id_market_id_unique` (`farmer_id`,`market_id`),
  KEY `farmer_markets_farmer_id_index` (`farmer_id`),
  KEY `farmer_markets_market_id_index` (`market_id`),
  CONSTRAINT `fm_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fm_market_fk` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farmer_markets`
--

LOCK TABLES `farmer_markets` WRITE;
/*!40000 ALTER TABLE `farmer_markets` DISABLE KEYS */;
INSERT INTO `farmer_markets` VALUES (1,1,1,'A12','Saturday','Pickup at stall A12 near the fountain.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,1,2,'B4','Wednesday','Look for the green canopy.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,2,3,'D2','Sunday',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,2,2,'C1','Saturday',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,3,4,'H7','Saturday',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(6,3,5,'T3','Wednesday',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `farmer_markets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farmer_profiles`
--

DROP TABLE IF EXISTS `farmer_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `stall_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_description` text COLLATE utf8mb4_unicode_ci,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operating_days` json DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farmer_profiles_user_id_index` (`user_id`),
  KEY `farmer_profiles_approval_status_index` (`approval_status`),
  CONSTRAINT `farmer_profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farmer_profiles`
--

LOCK TABLES `farmer_profiles` WRITE;
/*!40000 ALTER TABLE `farmer_profiles` DISABLE KEYS */;
INSERT INTO `farmer_profiles` VALUES (1,2,'Green Valley Produce','Family-grown vegetables and seasonal fruits from our valley farm.','Green Valley Farmer','[\"Wednesday\", \"Saturday\"]','42 Orchard Lane, Brooklyn, NY',40.67820000,-73.94420000,'approved',NULL,NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,4,'Sunrise Dairy Co-op','Fresh milk, cheese, eggs, and honey from pasture-raised animals.','Maria Santos','[\"Saturday\", \"Sunday\"]','15 Creamery Rd, Queens, NY',40.72820000,-73.79490000,'approved',NULL,NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,5,'Herb Haven','Culinary herbs, bunches, and baked goods made same-morning.','James Chen','[\"Friday\", \"Saturday\"]','9 Greenhouse Way, Manhattan, NY',40.75800000,-73.98550000,'approved',NULL,NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `farmer_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `farmer_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `market_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_unique` (`customer_id`,`farmer_id`,`product_id`,`market_id`),
  KEY `favorites_customer_id_index` (`customer_id`),
  KEY `favorites_farmer_id_index` (`farmer_id`),
  KEY `favorites_product_id_index` (`product_id`),
  KEY `favorites_market_id_index` (`market_id`),
  CONSTRAINT `favorites_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_market_fk` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `markets`
--

DROP TABLE IF EXISTS `markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `markets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operating_days` json DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `map_provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OpenStreetMap',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `markets_name_index` (`name`),
  KEY `markets_city_index` (`city`),
  KEY `markets_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `markets`
--

LOCK TABLES `markets` WRITE;
/*!40000 ALTER TABLE `markets` DISABLE KEYS */;
INSERT INTO `markets` VALUES (1,'Brooklyn Greenmarket','Grand Army Plaza','Brooklyn','[\"Saturday\"]','08:00:00','15:00:00',40.67300000,-73.97000000,'OpenStreetMap','active',NULL,'Weekend market with local produce under the trees.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'Union Square Farmers Market','E 17th St & Union Square W','Manhattan','[\"Monday\", \"Wednesday\", \"Friday\", \"Saturday\"]','08:00:00','18:00:00',40.73590000,-73.99110000,'OpenStreetMap','active',NULL,'Iconic year-round market in the heart of the city.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,'Queens Fresh Fair','Downtown Flushing Plaza','Queens','[\"Sunday\"]','09:00:00','14:00:00',40.75900000,-73.83000000,'OpenStreetMap','active',NULL,'Community fair featuring dairy, eggs, and greens.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,'Hudson River Market','Pier 57 Riverside','Manhattan','[\"Thursday\", \"Saturday\"]','10:00:00','16:00:00',40.74300000,-74.00800000,'OpenStreetMap','active',NULL,'Waterfront stalls with herbs and baked goods.','2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,'Prospect Park Todmorden','Near Lincoln Rd entrance','Brooklyn','[\"Wednesday\"]','08:00:00','14:00:00',40.66020000,-73.96900000,'OpenStreetMap','active',NULL,'Midweek market for greens and pantry staples.','2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `markets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2024_01_01_000001_create_users_table',1),(4,'2024_01_01_000002_create_farmer_profiles_table',1),(5,'2024_01_01_000003_create_markets_table',1),(6,'2024_01_01_000004_create_farmer_markets_table',1),(7,'2024_01_01_000005_create_categories_table',1),(8,'2024_01_01_000006_create_products_table',1),(9,'2024_01_01_000007_create_carts_table',1),(10,'2024_01_01_000008_create_cart_items_table',1),(11,'2024_01_01_000009_create_orders_table',1),(12,'2024_01_01_000010_create_order_items_table',1),(13,'2024_01_01_000011_create_reviews_table',1),(14,'2024_01_01_000012_create_favorites_table',1),(15,'2024_01_01_000013_create_notifications_table',1),(16,'2024_01_01_000014_create_announcements_table',1),(17,'2024_01_01_000015_create_reports_table',1),(18,'2024_01_01_000016_create_chatbot_faqs_table',1),(19,'2024_01_01_000017_create_settings_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_type_index` (`type`),
  KEY `notifications_is_read_index` (`is_read`),
  CONSTRAINT `notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,'welcome','Welcome to MarketLink','Browse nearby markets and place your first pre-order for pickup.','[]',0,'2026-09-24 09:47:37'),(2,2,'welcome','Stall ready','Your farmer account is approved. Start managing stock and orders.','[]',0,'2026-09-24 09:47:37');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_product_id_index` (`product_id`),
  CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'Heirloom Tomatoes',4.50,2,9.00,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,1,2,'Baby Spinach',3.25,2,6.50,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,2,9,'Free-Range Eggs',6.00,2,12.00,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `farmer_id` bigint unsigned NOT NULL,
  `market_id` bigint unsigned NOT NULL,
  `pickup_date` date NOT NULL,
  `pickup_slot` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('placed','accepted','declined','ready_for_pickup','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'placed',
  `total_amount` decimal(10,2) NOT NULL,
  `customer_note` text COLLATE utf8mb4_unicode_ci,
  `cutoff_time` datetime DEFAULT NULL,
  `farmer_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_customer_id_index` (`customer_id`),
  KEY `orders_farmer_id_index` (`farmer_id`),
  KEY `orders_market_id_index` (`market_id`),
  KEY `orders_pickup_date_index` (`pickup_date`),
  KEY `orders_status_index` (`status`),
  CONSTRAINT `orders_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_market_fk` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'ML-20260924-94811C',3,1,1,'2026-09-21','10:00-12:00','completed',15.50,'Please include a paper bag.','2026-09-20 09:47:37',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'ML-20260924-94AB99',3,2,2,'2026-09-26','12:00-14:00','accepted',12.00,NULL,'2026-09-25 18:00:00',NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint unsigned NOT NULL,
  `market_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `unit` enum('kg','gram','dozen','bunch','litre','piece','pack') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `stock_quantity` int unsigned NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_sold_out` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `views_count` int unsigned NOT NULL DEFAULT '0',
  `weekly_stock_template` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_farmer_id_index` (`farmer_id`),
  KEY `products_market_id_index` (`market_id`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_name_index` (`name`),
  KEY `products_price_index` (`price`),
  KEY `products_stock_quantity_index` (`stock_quantity`),
  KEY `products_is_available_index` (`is_available`),
  KEY `products_is_sold_out_index` (`is_sold_out`),
  KEY `products_is_featured_index` (`is_featured`),
  CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_market_fk` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,1,'Heirloom Tomatoes','Sun-ripened mixed heirloom tomatoes.',4.50,'kg',40,NULL,1,0,1,43,40,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,1,1,1,'Baby Spinach','Tender baby spinach bunches.',3.25,'bunch',30,NULL,1,0,1,71,30,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,1,2,1,'Rainbow Carrots','Sweet rainbow carrots, scrubbed clean.',2.75,'kg',50,NULL,1,0,0,22,50,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,1,2,2,'Honeycrisp Apples','Crisp autumn apples.',3.99,'kg',60,NULL,1,0,1,12,60,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,1,1,2,'Blueberries','Pint packs of local blueberries.',5.50,'pack',25,NULL,1,0,0,105,25,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(6,1,1,1,'Zucchini','Firm summer squash.',2.20,'kg',35,NULL,1,0,0,28,35,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(7,2,3,3,'Fresh Whole Milk','Non-homogenized whole milk.',4.00,'litre',40,NULL,1,0,1,14,40,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(8,2,3,3,'Farmhouse Cheddar','Aged 6-month cheddar wedges.',8.50,'piece',20,NULL,1,0,0,19,20,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(9,2,2,6,'Free-Range Eggs','Pasture-raised large eggs.',6.00,'dozen',45,NULL,1,0,1,102,45,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(10,2,2,7,'Wildflower Honey','Raw wildflower honey jars.',9.00,'pack',18,NULL,1,0,1,33,18,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(11,2,3,3,'Cultured Butter','Cultured butter, lightly salted.',5.75,'pack',22,NULL,1,0,0,28,22,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(12,3,4,5,'Basil Bunch','Fragrant Genovese basil.',2.50,'bunch',40,NULL,1,0,1,61,40,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(13,3,4,5,'Rosemary','Woody rosemary sprigs.',2.00,'bunch',28,NULL,1,0,0,113,28,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(14,3,4,4,'Sourdough Loaf','Naturally leavened sourdough.',7.00,'piece',15,NULL,1,0,1,58,15,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(15,3,5,4,'Berry Muffins','Pack of 4 berry muffins.',4.50,'pack',20,NULL,1,0,0,112,20,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(16,3,5,5,'Mint','Cooling spearmint bunches.',1.75,'bunch',32,NULL,1,0,0,33,32,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(17,1,2,1,'Kale','Curly kale — low stock.',2.40,'bunch',5,NULL,1,0,0,101,5,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(18,2,2,6,'Duck Eggs','Rich duck eggs, limited supply.',8.00,'dozen',12,NULL,1,0,0,58,12,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generated_by` bigint unsigned NOT NULL,
  `data` json NOT NULL,
  `generated_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_generator_fk` (`generated_by`),
  KEY `reports_report_type_index` (`report_type`),
  KEY `reports_generated_at_index` (`generated_at`),
  CONSTRAINT `reports_generator_fk` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `farmer_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `farmer_reply` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `helpful_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_customer_id_index` (`customer_id`),
  KEY `reviews_farmer_id_index` (`farmer_id`),
  KEY `reviews_product_id_index` (`product_id`),
  KEY `reviews_order_id_index` (`order_id`),
  KEY `reviews_rating_index` (`rating`),
  KEY `reviews_status_index` (`status`),
  CONSTRAINT `reviews_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,3,1,1,1,5,'Best tomatoes I have tasted this season!','Thank you for supporting our stall!','approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,3,1,2,1,4,'Fresh spinach, great for salads.',NULL,'approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,3,2,9,1,5,'Eggs were perfect — rich yolks.','Thank you for supporting our stall!','approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,3,2,10,1,5,'Honey is incredible on toast.','Thank you for supporting our stall!','approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,3,3,14,1,4,'Crusty sourdough, well baked.',NULL,'approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(6,3,3,12,1,5,'Basil smelled amazing.','Thank you for supporting our stall!','approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(7,3,1,4,1,4,'Crisp apples, kids loved them.',NULL,'approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(8,3,2,8,1,3,'Good cheddar but a bit pricey.',NULL,'approved',0,'2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('djKp9fJr0JlcPUANMBX3L8abPc4YxmCkszO7DY3r',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWV2NHJPeXNmY2hoek5ZRExkc0d6ZWNDSEVGQ29IeXM1azRFWlY0RiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mYXJtZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1790243269),('i6fVYvTgTzeEOCMoosyDkX2E1p4l47CFXGzn1sFt',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGRpenhPSU1DdlBYY3JWNTNwbzRZUzZVcmFpRXB3WlUxZmV4MDZsQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWN0cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790243269),('jc5MW5FBaqO8r5c81GdFKZMghV5egETW16Hx9Mu5',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3ZBZDdVbmtxNjZzWjRTQ1ZrcXZ1TW45aEI2N2FZYUVqVHVob21BZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYXJrZXRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1790243269),('XxmUXEpUM8nBakaMj8W0nLwPBpdryrwBVQdc3Rrj',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRjd3MFZpV0JuZTNoaEdTM0tTa1R6c0JocmIzUFA4eXIzUXE5Qk42VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790243269),('zJqCZadj7CZhm10IO3JQL9G0MzIhtRbkQAaUcre5',NULL,'127.0.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVpCOGFIeE5iWWFFVFJ0eWlFb0F5ZGZCTXFIM1g2WU4wdlF1c2FwYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790243269);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_tagline','Fresh from local farmers markets','2026-09-24 09:47:37','2026-09-24 09:47:37'),(2,'support_email','support@marketlink.com','2026-09-24 09:47:37','2026-09-24 09:47:37'),(3,'default_cutoff_hours','24','2026-09-24 09:47:37','2026-09-24 09:47:37'),(4,'low_stock_threshold','5','2026-09-24 09:47:37','2026-09-24 09:47:37');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','farmer','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive','pending','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'MarketLink Admin','admin@marketlink.com','555-0100','1 Admin Plaza','$2y$12$TkDZUVLiOzyG1YzDhxxOyOWu.GlBbQAw6oLuqb3g4B8sSvFMOEc3a','admin','active',NULL,'2026-09-24 09:47:36',NULL,'2026-09-24 09:47:36','2026-09-24 09:47:36'),(2,'Green Valley Farmer','farmer@marketlink.com','555-0200','42 Orchard Lane','$2y$12$F.n93ExQ6tuT3PcP1sLjEuKwFfhYHXe65gSwDr1HVTLRZtogoNwg6','farmer','active',NULL,'2026-09-24 09:47:36',NULL,'2026-09-24 09:47:36','2026-09-24 09:47:36'),(3,'Demo Customer','customer@marketlink.com','555-0300','88 Maple Street','$2y$12$Nql7iuXlGh.sx4PB5rFXqejEkfomd/bo3xAmWattknG.aG3Xiabhe','customer','active',NULL,'2026-09-24 09:47:36',NULL,'2026-09-24 09:47:36','2026-09-24 09:47:36'),(4,'Sunrise Dairy','sunrise@marketlink.com','555-0201',NULL,'$2y$12$NDfjVckJU4nWtpXlt8HbjOyZSYuOr/W7oOkF2nz2yFzSEwIalcV4C','farmer','active',NULL,NULL,NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37'),(5,'Herb Haven','herbs@marketlink.com','555-0202',NULL,'$2y$12$E/bRqX4ClIrpYCrY83pW9eraMx2qVpbkSDq/8BBWJz2d2pFrsccJ.','farmer','active',NULL,NULL,NULL,'2026-09-24 09:47:37','2026-09-24 09:47:37');
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

-- Dump completed on 2026-09-24  9:47:59
