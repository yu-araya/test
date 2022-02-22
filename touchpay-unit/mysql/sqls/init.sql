-- MySQL dump 10.13  Distrib 5.5.62, for Linux (x86_64)
--
-- Host: touchpay.cozf2wgeljkt.ap-northeast-1.rds.amazonaws.com    Database: standard_tpay
-- ------------------------------------------------------
-- Server version	5.5.61-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrators`
--

DROP TABLE IF EXISTS `administrators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administrators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(10) NOT NULL COMMENT '管理者ID',
  `password` varchar(10) NOT NULL COMMENT 'パスワード',
  `belonging_kbn` char(1) NOT NULL COMMENT '所属区分',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='管理者マスタ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrators`
--

LOCK TABLES `administrators` WRITE;
/*!40000 ALTER TABLE `administrators` DISABLE KEYS */;
INSERT INTO `administrators` VALUES 
(1,'sysadmin','initpasswd','1','2019-04-16 07:23:27','2019-04-16 07:23:27',0);
/*!40000 ALTER TABLE `administrators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corp_reservation_infos`
--

DROP TABLE IF EXISTS `login_historys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_historys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(10) NOT NULL COMMENT '管理者ID',
  `login_datetime` datetime NOT NULL COMMENT 'ログイン日付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='ログイン履歴';
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `corp_reservation_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corp_reservation_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_kbn` int(5) NOT NULL COMMENT '拠点区分',
  `corp_kbn` int(5) NOT NULL COMMENT '業者区分',
  `reservation_date` date NOT NULL COMMENT '予約日付',
  `receipt_count` int(5) NOT NULL DEFAULT '0' COMMENT '受付件数',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='業者別予約情報';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corp_reservation_infos`
--

LOCK TABLES `corp_reservation_infos` WRITE;
/*!40000 ALTER TABLE `corp_reservation_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `corp_reservation_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `day_off_calendars`
--

DROP TABLE IF EXISTS `day_off_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `day_off_calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_kbn` int(5) NOT NULL COMMENT '拠点区分',
  `day_off_datetime` datetime NOT NULL COMMENT '休日',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COMMENT='休日カレンダーマスタ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `day_off_calendars`
--

LOCK TABLES `day_off_calendars` WRITE;
/*!40000 ALTER TABLE `day_off_calendars` DISABLE KEYS */;
/*!40000 ALTER TABLE `day_off_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_infos`
--

DROP TABLE IF EXISTS `employee_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(10) DEFAULT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) DEFAULT NULL COMMENT '社員区分',
  `employee_name1` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '氏名',
  `employee_name2` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '所属',
  `password` char(10) DEFAULT NULL COMMENT '暗証番号',
  `dining_license_flg` char(1) DEFAULT '0' COMMENT '社員食堂使用許可フラグ',
  `dining_licensed_date` date DEFAULT NULL COMMENT '社員食堂使用不可設定日',
  `ic_card_number` varchar(16) DEFAULT NULL COMMENT 'ICカード番号',
  `iccard_valid_s_time` datetime DEFAULT NULL COMMENT '有効期間（開始）',
  `iccard_valid_e_time` datetime DEFAULT NULL COMMENT '有効期間（終了）',
  `ic_card_number2` varchar(16) DEFAULT NULL COMMENT 'ICカード番号２',
  `iccard_valid_s_time2` datetime DEFAULT NULL COMMENT '有効期間（開始）２',
  `iccard_valid_e_time2` datetime DEFAULT NULL COMMENT '有効期間（終了）２',
  `memo` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '備考',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='社員情報';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_infos`
--

LOCK TABLES `employee_infos` WRITE;
/*!40000 ALTER TABLE `employee_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_kbns`
--

DROP TABLE IF EXISTS `employee_kbns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_kbns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_kbn` varchar(2) NOT NULL COMMENT '社員区分',
  `employee_kbn_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '社員区分名',
  `food_allowance_flg` char(1) NOT NULL DEFAULT '0' COMMENT '食事手当フラグ',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_kbn` (`employee_kbn`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='社員区分マスタ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_kbns`
--

LOCK TABLES `employee_kbns` WRITE;
/*!40000 ALTER TABLE `employee_kbns` DISABLE KEYS */;
INSERT INTO `employee_kbns` VALUES 
(1,'1','社員','0','2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(2,'2','パート','0','2019-05-20 09:06:36','2019-05-27 13:13:35',0)
,(3,'3','派遣','0','2019-05-20 09:06:36','2019-05-27 13:13:35',0)
,(4,'4','工事業社','0','2019-05-20 09:06:36','2019-05-27 13:13:35',0);
/*!40000 ALTER TABLE `employee_kbns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_divisions`
--

DROP TABLE IF EXISTS `food_divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `food_divisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `food_division_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '食事区分名',
  `food_cost` decimal(7,0) NOT NULL COMMENT '金額',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `food_division` (`food_division`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='食事マスタ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_divisions`
--

LOCK TABLES `food_divisions` WRITE;
/*!40000 ALTER TABLE `food_divisions` DISABLE KEYS */;
INSERT INTO `food_divisions` VALUES (1,1,'定食',300,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(2,2,'丼',350,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(3,3,'工場定食',300,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(4,4,'工場丼',320,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(5,5,'定食予約',300,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(6,6,'丼予約',350,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(7,7,'工場定食予約',300,'2019-04-16 07:23:27','2019-04-16 07:23:27',0)
,(8,8,'工場丼予約',320,'2019-04-16 07:23:27','2019-04-16 07:23:27',0);
/*!40000 ALTER TABLE `food_divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_history_infos`
--

DROP TABLE IF EXISTS `food_history_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `food_history_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '食事履歴番号',
  `employee_id` varchar(10) NOT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) NOT NULL COMMENT '社員区分',
  `ic_card_number` varchar(16) NOT NULL COMMENT 'ICカード番号',
  `instrument_division` int(5) NOT NULL COMMENT '機器区分',
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `reason` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '理由',
  `card_recept_time` datetime NOT NULL COMMENT 'カード受付時間',
  `state_flg` char(1) NOT NULL DEFAULT '0' COMMENT '状態フラグ',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97967 DEFAULT CHARSET=utf8 COMMENT='食事履歴情報';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_history_infos`
--

LOCK TABLES `food_history_infos` WRITE;
/*!40000 ALTER TABLE `food_history_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `food_history_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `food_history_reservations`
--

DROP TABLE IF EXISTS `food_history_reservations`;
/*!50001 DROP VIEW IF EXISTS `food_history_reservations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `food_history_reservations` (
  `employee_id` tinyint NOT NULL,
  `employee_kbn` tinyint NOT NULL,
  `food_division` tinyint NOT NULL,
  `target_date` tinyint NOT NULL,
  `reason` tinyint NOT NULL,
  `data_type` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `instrument_divisions`
--

DROP TABLE IF EXISTS `instrument_divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instrument_divisions` (
  `instrument_division` int(5) NOT NULL COMMENT '機器区分',
  `instrument_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '機器区分名',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`instrument_division`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='機器マスタ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instrument_divisions`
--

LOCK TABLES `instrument_divisions` WRITE;
/*!40000 ALTER TABLE `instrument_divisions` DISABLE KEYS */;
INSERT INTO `instrument_divisions` VALUES (1,'本社','2019-04-16 07:23:27','2019-04-16 07:23:27',0);
/*!40000 ALTER TABLE `instrument_divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_decisions`
--

DROP TABLE IF EXISTS `reservation_decisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_decisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_date` datetime NOT NULL COMMENT '予約日付',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='予約確定';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_decisions`
--

LOCK TABLES `reservation_decisions` WRITE;
/*!40000 ALTER TABLE `reservation_decisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_decisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_infos`
--

DROP TABLE IF EXISTS `reservation_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(10) NOT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) NOT NULL COMMENT '社員区分',
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `reason` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '理由',
  `reservation_date` datetime NOT NULL COMMENT '予約日付',
  `state_flg` char(1) NOT NULL DEFAULT '0' COMMENT '状態フラグ',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='予約情報';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_infos`
--

LOCK TABLES `reservation_infos` WRITE;
/*!40000 ALTER TABLE `reservation_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_infos` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `regist_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regist_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `occurrence_datetime` datetime NOT NULL COMMENT '発生日時',
  `function_name` varchar(10) NOT NULL COMMENT '処理内容',
  `error_level` varchar(50) NOT NULL COMMENT 'エラーレベル',
  `reason` varchar(50) NOT NULL COMMENT 'エラー内容',
  `employee_id` varchar(10) DEFAULT NULL COMMENT '社員番号',
  `ic_card_number` varchar(16) DEFAULT NULL COMMENT 'ICカード番号',
  `instrument_division` int(5) DEFAULT NULL COMMENT '機器番号',
  `food_division` int(5) DEFAULT NULL COMMENT 'メニューID',
  `card_recept_time` datetime DEFAULT NULL COMMENT 'カードタッチ日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='登録エラー管理';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regist_errors`
--

LOCK TABLES `regist_errors` WRITE;
UNLOCK TABLES;

CREATE TABLE `contents_set_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terminal_id` varchar(10) NOT NULL COMMENT '機器ID',
  `contents_type` varchar(10) NOT NULL COMMENT 'コンテンツタイプ',
  `version` decimal(5,2) NOT NULL COMMENT 'バージョン',
  `revision` varchar(5) NOT NULL COMMENT 'リビジョン',
  `created` datetime NOT NULL COMMENT '更新日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='コンテンツセット管理';


--
-- Final view structure for view `food_history_reservations`
--

/*!50001 DROP TABLE IF EXISTS `food_history_reservations`*/;
/*!50001 DROP VIEW IF EXISTS `food_history_reservations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dining_standard`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `food_history_reservations` AS select `food_history_infos`.`employee_id` AS `employee_id`,`food_history_infos`.`employee_kbn` AS `employee_kbn`,`food_history_infos`.`food_division` AS `food_division`,`food_history_infos`.`card_recept_time` AS `target_date`,`food_history_infos`.`reason` AS `reason`,'1' AS `data_type` from `food_history_infos` where ((`food_history_infos`.`state_flg` in ('0','1')) and (`food_history_infos`.`delete_flg` = 0)) union all select `reservation_infos`.`employee_id` AS `employee_id`,`reservation_infos`.`employee_kbn` AS `employee_kbn`,`reservation_infos`.`food_division` AS `food_division`,`reservation_infos`.`reservation_date` AS `target_date`,`reservation_infos`.`reason` AS `reason`,'2' AS `data_type` from `reservation_infos` where ((`reservation_infos`.`state_flg` in ('0','1')) and (`reservation_infos`.`delete_flg` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-19 18:26:44
