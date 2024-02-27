-- MySQL dump 10.13  Distrib 8.0.33, for macos13.3 (arm64)
--
-- Host: localhost    Database: kuuwaa
-- ------------------------------------------------------
-- Server version	8.0.33

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
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D4E6F81E946114A` (`province_id`),
  KEY `IDX_D4E6F81F92F3E70` (`country_id`),
  CONSTRAINT `FK_D4E6F81E946114A` FOREIGN KEY (`province_id`) REFERENCES `province` (`id`),
  CONSTRAINT `FK_D4E6F81F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (1,1,1,'Ingende','10','null'),(2,1,1,'qwee','12','ewewrer'),(3,1,1,'qweqe','12','qweq'),(4,1,1,'Ingende','10',''),(5,1,1,'Ingende','10','Test'),(6,2,1,'Ingende','10','Test'),(7,1,1,'Ingende','10','Test');
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beverage`
--

DROP TABLE IF EXISTS `beverage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beverage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_taken` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3D8CACBB71F7E88B` (`event_id`),
  CONSTRAINT `FK_3D8CACBB71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beverage`
--

LOCK TABLES `beverage` WRITE;
/*!40000 ALTER TABLE `beverage` DISABLE KEYS */;
INSERT INTO `beverage` VALUES (18,'rrtr',0,22),(19,'Fanta',0,22);
/*!40000 ALTER TABLE `beverage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `invitation_id` int DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CBE5A33171F7E88B` (`event_id`),
  KEY `IDX_CBE5A331A35D7AF0` (`invitation_id`),
  CONSTRAINT `FK_CBE5A33171F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `FK_CBE5A331A35D7AF0` FOREIGN KEY (`invitation_id`) REFERENCES `invitation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book`
--

LOCK TABLES `book` WRITE;
/*!40000 ALTER TABLE `book` DISABLE KEYS */;
/*!40000 ALTER TABLE `book` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_initial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'République Démocratique du Congo','+243','cd');
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `event_pack_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `deleted_by` datetime DEFAULT NULL,
  `paid_event` tinyint(1) DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tables_count` int DEFAULT NULL,
  `invitations_count` int DEFAULT NULL,
  `beverages_count` int DEFAULT NULL,
  `admins_count` int DEFAULT NULL,
  `address_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3BAE0AA7F5B7AF75` (`address_id`),
  KEY `IDX_3BAE0AA712469DE2` (`category_id`),
  KEY `IDX_3BAE0AA7A76ED395` (`user_id`),
  KEY `IDX_3BAE0AA79C4E1BB8` (`event_pack_id`),
  CONSTRAINT `FK_3BAE0AA712469DE2` FOREIGN KEY (`category_id`) REFERENCES `event_category` (`id`),
  CONSTRAINT `FK_3BAE0AA79C4E1BB8` FOREIGN KEY (`event_pack_id`) REFERENCES `event_pack` (`id`),
  CONSTRAINT `FK_3BAE0AA7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_3BAE0AA7F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (22,2,19,2,'Daniel & Dainella','2024-02-01 01:23:00','Test','wainting',NULL,NULL,NULL,NULL,1,'waiting','/events/images/65b078d45cedf9.66907186.jpg',NULL,2,1,2,NULL,7),(23,1,19,1,'qweqw','2024-01-28 01:41:00','qweqwe','wainting',NULL,NULL,NULL,NULL,1,'waiting','/events/images/65b05d6c6a5bf9.89529456.jpg',NULL,NULL,0,NULL,NULL,2),(24,2,19,3,'qweqeweqw','2024-01-27 02:16:00','qwewqeqewqwe','wainting',NULL,NULL,NULL,NULL,1,'waiting','/events/images/65b0652b5342a2.90762531.jpg',NULL,NULL,0,NULL,NULL,3);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_admin`
--

DROP TABLE IF EXISTS `event_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C915BC5A71F7E88B` (`event_id`),
  CONSTRAINT `FK_C915BC5A71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_admin`
--

LOCK TABLES `event_admin` WRITE;
/*!40000 ALTER TABLE `event_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_category`
--

DROP TABLE IF EXISTS `event_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid` tinyint(1) DEFAULT NULL,
  `validated` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_40A0F011727ACA70` (`parent_id`),
  CONSTRAINT `FK_40A0F011727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `event_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_category`
--

LOCK TABLES `event_category` WRITE;
/*!40000 ALTER TABLE `event_category` DISABLE KEYS */;
INSERT INTO `event_category` VALUES (1,NULL,'Anniversaire',0,1),(2,NULL,'Mariage réligieux',0,1),(3,NULL,'Mariage civil',0,1),(4,NULL,'Mariage coutimieux',0,1),(5,NULL,'Conférence',0,1),(6,NULL,'Formation',1,1),(7,NULL,'Concert',1,1),(8,NULL,'Cinema',1,1);
/*!40000 ALTER TABLE `event_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_feature`
--

DROP TABLE IF EXISTS `event_feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_feature` (
  `event_id` int NOT NULL,
  `feature_id` int NOT NULL,
  PRIMARY KEY (`event_id`,`feature_id`),
  KEY `IDX_9A80DCC971F7E88B` (`event_id`),
  KEY `IDX_9A80DCC960E4B879` (`feature_id`),
  CONSTRAINT `FK_9A80DCC960E4B879` FOREIGN KEY (`feature_id`) REFERENCES `feature` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9A80DCC971F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_feature`
--

LOCK TABLES `event_feature` WRITE;
/*!40000 ALTER TABLE `event_feature` DISABLE KEYS */;
INSERT INTO `event_feature` VALUES (22,1),(22,2),(22,3),(23,1);
/*!40000 ALTER TABLE `event_feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_pack`
--

DROP TABLE IF EXISTS `event_pack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_pack` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `max_tickets` int DEFAULT NULL,
  `paid` tinyint(1) DEFAULT NULL,
  `pack_order` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_pack`
--

LOCK TABLES `event_pack` WRITE;
/*!40000 ALTER TABLE `event_pack` DISABLE KEYS */;
INSERT INTO `event_pack` VALUES (1,'Essential',59,150,0,2),(2,'Premium',99,NULL,0,3),(3,'Starter',19,50,0,1);
/*!40000 ALTER TABLE `event_pack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature`
--

DROP TABLE IF EXISTS `feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature`
--

LOCK TABLES `feature` WRITE;
/*!40000 ALTER TABLE `feature` DISABLE KEYS */;
INSERT INTO `feature` VALUES (1,'Gestion des tables',20,'Les invités sont automatiquement classés par table'),(2,'Gestion des boissons',25,'Les invités choisissent en avance les boissons qu\'ils prendront et vous savez quelle quantité pour chaque boisson il faudra.'),(3,'Livre d\'or',15,'Tous les invités vous laisseront un mot dans le livre d\'or que vous pouvez consulter à tout moment et avec possibilité de le télécharger');
/*!40000 ALTER TABLE `feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invitation`
--

DROP TABLE IF EXISTS `invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invitation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `civility` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invitation_table_id` int DEFAULT NULL,
  `beverage_id` int DEFAULT NULL,
  `code_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `book_sent` tinyint(1) DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_F11D61A271F7E88B` (`event_id`),
  KEY `IDX_F11D61A2CCEB50C1` (`invitation_table_id`),
  KEY `IDX_F11D61A249F6E812` (`beverage_id`),
  CONSTRAINT `FK_F11D61A249F6E812` FOREIGN KEY (`beverage_id`) REFERENCES `beverage` (`id`),
  CONSTRAINT `FK_F11D61A271F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
  CONSTRAINT `FK_F11D61A2CCEB50C1` FOREIGN KEY (`invitation_table_id`) REFERENCES `table` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invitation`
--

LOCK TABLES `invitation` WRITE;
/*!40000 ALTER TABLE `invitation` DISABLE KEYS */;
INSERT INTO `invitation` VALUES (42,22,'Mwema','Mr.',1,'814 063 056','sent',24,NULL,'/events/invitations/qr_code/65b08fafaa70c4.29546307.png','929159',NULL,'Bonjour Mr. Mwema,\nVous êtes invité à l\'évenement \"Daniel & Dainella\" le Jeudi 01 Février 2024.\n\nPour accéder à votre invitation, veuillez ouvrir le lien suivant.\nhttps://kuuwaa.com/invitations/42\n\nSi vous rencontrez des difficultés pour ouvrir le lien ou pour accéder à l\'application KuuWaa, veuillez retenir le code d\'invitation à 6 chiffres (929159) et présentez-le lors de la vérification à l\'entrée de l\'événement.\n\nCeci est un message automatique, veuillez ne pas y repondre.');
/*!40000 ALTER TABLE `invitation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp`
--

DROP TABLE IF EXISTS `otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `deleted_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `deleted_by` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A79C98C1A76ED395` (`user_id`),
  CONSTRAINT `FK_A79C98C1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp`
--

LOCK TABLES `otp` WRITE;
/*!40000 ALTER TABLE `otp` DISABLE KEYS */;
INSERT INTO `otp` VALUES (151,22,'44692','2024-01-14 21:24:53',NULL,NULL,NULL),(155,19,'70219','2024-01-15 15:01:28',NULL,NULL,NULL),(161,19,'58783','2024-01-15 22:18:28',NULL,NULL,NULL),(165,19,'38776','2024-01-16 00:16:32',NULL,NULL,NULL),(166,20,'97949','2024-01-16 00:18:10',NULL,NULL,NULL),(170,19,'49414','2024-01-16 01:29:40',NULL,NULL,NULL),(174,23,'02999','2024-01-16 01:45:24',NULL,NULL,NULL),(186,23,'12891','2024-01-16 12:57:51',NULL,NULL,NULL),(190,23,'41409','2024-01-19 08:25:50',NULL,NULL,NULL),(191,19,'27572','2024-01-20 03:16:05',NULL,NULL,NULL),(194,19,'33745','2024-01-24 00:37:08',NULL,NULL,NULL);
/*!40000 ALTER TABLE `otp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6D28840D71F7E88B` (`event_id`),
  CONSTRAINT `FK_6D28840D71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `province`
--

DROP TABLE IF EXISTS `province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `province` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4ADAD40BF92F3E70` (`country_id`),
  CONSTRAINT `FK_4ADAD40BF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `province`
--

LOCK TABLES `province` WRITE;
/*!40000 ALTER TABLE `province` DISABLE KEYS */;
INSERT INTO `province` VALUES (1,'Kinshasa',1),(2,'Bas-Uele',1),(3,'Équateur',1),(4,'Haut-Katanga',1),(5,'Haut-Lomami',1),(6,'Haut-Uele',1),(7,'Ituri',1),(8,'Kasaï',1),(9,'Kasaï central',1),(10,'Kasaï oriental',1),(11,'Kongo-Central',1),(12,'Kwango',1),(13,'Kwilu',1),(14,'Lomami',1),(15,'Lualaba',1),(16,'Mai-Ndombe',1),(17,'Maniema',1),(18,'Mongala',1),(19,'Nord-Kivu',1),(20,'Nord-Ubangi',1),(21,'Sankuru',1),(22,'Sud-Kivu',1),(23,'Sud-Ubangi',1),(24,'Tanganyika',1),(25,'Tshopo',1),(26,'Tshuapa',1);
/*!40000 ALTER TABLE `province` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `table`
--

DROP TABLE IF EXISTS `table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `table` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int NOT NULL,
  `count_taken` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F6298F4671F7E88B` (`event_id`),
  CONSTRAINT `FK_F6298F4671F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table`
--

LOCK TABLES `table` WRITE;
/*!40000 ALTER TABLE `table` DISABLE KEYS */;
INSERT INTO `table` VALUES (24,22,'Table 1',10,1),(25,22,'Table 2',20,0);
/*!40000 ALTER TABLE `table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D6496B01BC5B` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (19,'814 063 056','[]','Daniel','Mwema','Kindele',NULL),(20,'812 434 586','[]','Emmanuel','Velu','Unikin',NULL),(21,'820 793 972','[]','Patrice','Tshitenga','Unikin',NULL),(22,'814 064 056','[]',NULL,NULL,NULL,NULL),(23,'811 111 111','[]','Tete','Toto','Unikin',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-24 13:09:34
