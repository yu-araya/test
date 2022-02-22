--
-- Table structure for table `administrators`
--

DROP TABLE IF EXISTS `administrators`;
CREATE TABLE `administrators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(10) NOT NULL COMMENT '管理者ID',
  `password` varchar(10) NOT NULL COMMENT 'パスワード',
  `role` char(1) NOT NULL COMMENT 'ロール',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='管理者マスタ';

--
-- Dumping data for table `administrators`
--

LOCK TABLES `administrators` WRITE;
INSERT INTO `administrators` VALUES
(1,'sysadmin','initpasswd','1',now(),now(),0),
(2,'sysguest','initpasswd','2',now(),now(),0);
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `category` int(5) NOT NULL COMMENT '分類',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `food_division` (`food_division`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='分類マスタ';

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
INSERT INTO `categories` VALUES 
(1,1,1,now(),now(),0),
(2,2,1,now(),now(),0),
(3,3,2,now(),now(),0),
(4,4,2,now(),now(),0),
(5,5,0,now(),now(),0),
(6,6,0,now(),now(),0),
(7,7,2,now(),now(),0),
(8,8,0,now(),now(),0);
UNLOCK TABLES;

--
-- Table structure for table `contents_set_versions`
--

DROP TABLE IF EXISTS `contents_set_versions`;
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
-- Dumping data for table `contents_set_versions`
--

LOCK TABLES `contents_set_versions` WRITE;
INSERT INTO `contents_set_versions` VALUES 
('1', '1', 'tablet', '1.01', '1', now(), now(), '0'),
('2', '2', 'user', '1.01', '1', now(), now(), '0'),
('3', '3', 'forkitchen', '1.01', '1', now(), now(), '0');
UNLOCK TABLES;


--
-- Table structure for table `day_off_calendars`
--

DROP TABLE IF EXISTS `day_off_calendars`;
CREATE TABLE `day_off_calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_kbn` int(5) NOT NULL COMMENT '拠点区分',
  `day_off_datetime` datetime NOT NULL COMMENT '休日',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COMMENT='休日カレンダーマスタ';

--
-- Dumping data for table `day_off_calendars`
--

LOCK TABLES `day_off_calendars` WRITE;
/*!40000 ALTER TABLE `day_off_calendars` DISABLE KEYS */;
INSERT INTO `day_off_calendars` VALUES 
(126,1,'2020-05-03 00:00:00',now(),now()),
(127,1,'2020-05-04 00:00:00',now(),now()),
(128,1,'2020-05-05 00:00:00',now(),now()),
(129,1,'2020-05-06 00:00:00',now(),now()),
(130,1,'2020-05-02 00:00:00',now(),now()),
(131,1,'2020-05-10 00:00:00',now(),now()),
(132,1,'2020-05-17 00:00:00',now(),now()),
(133,1,'2020-05-24 00:00:00',now(),now()),
(134,1,'2020-05-31 00:00:00',now(),now()),
(135,1,'2020-05-09 00:00:00',now(),now()),
(136,1,'2020-05-16 00:00:00',now(),now()),
(137,1,'2020-05-23 00:00:00',now(),now()),
(138,1,'2020-05-30 00:00:00',now(),now()),
(139,1,'2020-04-05 00:00:00',now(),now()),
(140,1,'2020-04-12 00:00:00',now(),now()),
(141,1,'2020-04-19 00:00:00',now(),now()),
(142,1,'2020-04-26 00:00:00',now(),now()),
(143,1,'2020-04-04 00:00:00',now(),now()),
(144,1,'2020-04-11 00:00:00',now(),now()),
(145,1,'2020-04-18 00:00:00',now(),now()),
(146,1,'2020-04-25 00:00:00',now(),now()),
(147,1,'2020-04-29 00:00:00',now(),now()),
(148,1,'2020-03-01 00:00:00',now(),now()),
(149,1,'2020-03-08 00:00:00',now(),now()),
(150,1,'2020-03-15 00:00:00',now(),now()),
(151,1,'2020-03-22 00:00:00',now(),now()),
(152,1,'2020-03-29 00:00:00',now(),now()),
(153,1,'2020-03-20 00:00:00',now(),now()),
(154,1,'2020-03-07 00:00:00',now(),now()),
(155,1,'2020-03-14 00:00:00',now(),now()),
(156,1,'2020-03-21 00:00:00',now(),now()),
(157,1,'2020-03-28 00:00:00',now(),now()),
(158,1,'2020-06-07 00:00:00',now(),now()),
(159,1,'2020-06-14 00:00:00',now(),now()),
(160,1,'2020-06-21 00:00:00',now(),now()),
(161,1,'2020-06-28 00:00:00',now(),now()),
(162,1,'2020-06-06 00:00:00',now(),now()),
(163,1,'2020-06-13 00:00:00',now(),now()),
(164,1,'2020-06-20 00:00:00',now(),now()),
(165,1,'2020-06-27 00:00:00',now(),now()),
(166,1,'2020-07-23 00:00:00',now(),now()),
(167,1,'2020-07-24 00:00:00',now(),now()),
(168,1,'2020-07-04 00:00:00',now(),now()),
(169,1,'2020-07-11 00:00:00',now(),now()),
(170,1,'2020-07-18 00:00:00',now(),now()),
(171,1,'2020-07-25 00:00:00',now(),now()),
(172,1,'2020-07-05 00:00:00',now(),now()),
(173,1,'2020-07-12 00:00:00',now(),now()),
(174,1,'2020-07-19 00:00:00',now(),now()),
(175,1,'2020-07-26 00:00:00',now(),now()),
(176,1,'2020-08-02 00:00:00',now(),now()),
(177,1,'2020-08-09 00:00:00',now(),now()),
(178,1,'2020-08-16 00:00:00',now(),now()),
(179,1,'2020-08-23 00:00:00',now(),now()),
(180,1,'2020-08-30 00:00:00',now(),now()),
(181,1,'2020-08-10 00:00:00',now(),now()),
(182,1,'2020-08-01 00:00:00',now(),now()),
(183,1,'2020-08-08 00:00:00',now(),now()),
(184,1,'2020-08-15 00:00:00',now(),now()),
(185,1,'2020-08-22 00:00:00',now(),now()),
(186,1,'2020-08-29 00:00:00',now(),now()),
(187,1,'2020-05-08 00:00:00',now(),now());
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deveice_id` varchar(50) NOT NULL COMMENT '端末ID',
  `client_id` varchar(50) NOT NULL COMMENT '顧客ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `deveice_id` (`deveice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='顧客マスタ';

--
-- Table structure for table `employee_infos`
--

DROP TABLE IF EXISTS `employee_infos`;
CREATE TABLE `employee_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(10) DEFAULT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) DEFAULT NULL COMMENT '社員区分',
  `employee_name1` varchar(50) DEFAULT NULL COMMENT '氏名',
  `employee_name2` varchar(50) DEFAULT NULL COMMENT '所属',
  `password` char(10) DEFAULT NULL COMMENT '暗証番号',
  `dining_license_flg` char(1) DEFAULT '0' COMMENT '社員食堂使用許可フラグ',
  `dining_licensed_date` date DEFAULT NULL COMMENT '社員食堂使用不可設定日',
  `ic_card_number` varchar(16) DEFAULT NULL COMMENT 'ICカード番号',
  `iccard_valid_s_time` datetime DEFAULT NULL COMMENT '有効期間（開始）',
  `iccard_valid_e_time` datetime DEFAULT NULL COMMENT '有効期間（終了）',
  `ic_card_number2` varchar(16) DEFAULT NULL COMMENT 'ICカード番号２',
  `iccard_valid_s_time2` datetime DEFAULT NULL COMMENT '有効期間（開始）２',
  `iccard_valid_e_time2` datetime DEFAULT NULL COMMENT '有効期間（終了）２',
  `memo` varchar(40) DEFAULT NULL COMMENT '備考',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='社員情報';

--
-- Dumping data for table `employee_infos`
--

LOCK TABLES `employee_infos` WRITE;
INSERT INTO `employee_infos` VALUES
(1,'1','1','正カード正常社員','営業本部','0000','0',NULL,'9924FF35','2018-01-01 00:00:00','2099-01-01 00:00:00','0127005CA4D93695','2018-01-01 00:00:00','2099-01-01 00:00:00','',now(),now(),0),
(2,'2','1','E2Eテスト用2','営業本部','0000','0',NULL,'1234','2018-01-01 00:00:00','2099-01-01 00:00:00','0127005CA4D93695','2018-01-01 00:00:00','2099-01-01 00:00:00','',now(),now(),0),
(3,'3','1','E2Eテスト用3','営業本部','0000','0',NULL,'5678','2018-01-01 00:00:00','2099-01-01 00:00:00','0127005CA4D93695','2018-01-01 00:00:00','2099-01-01 00:00:00','',now(),now(),0),
(4,'4','1','E2Eテスト用4','営業本部','0000','0',NULL,'9012','2018-01-01 00:00:00','2099-01-01 00:00:00','0127005CA4D93695','2018-01-01 00:00:00','2099-01-01 00:00:00','',now(),now(),0),
(7,'7','1','カード情報なし','営業本部','0000','0',NULL,'',NULL,NULL,'',NULL,NULL,'',now(),now(),0),
(8,'8','1','削除済','営業本部','0000','0',NULL,'9924FF35','2018-01-01 00:00:00','2099-01-01 00:00:00','',NULL,NULL,'',now(),now(),1);
UNLOCK TABLES;

--
-- Table structure for table `employee_kbns`
--

DROP TABLE IF EXISTS `employee_kbns`;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='社員区分マスタ';

--
-- Dumping data for table `employee_kbns`
--

LOCK TABLES `employee_kbns` WRITE;
INSERT INTO `employee_kbns` VALUES
(1,'1','社員','0',now(),now(),0),
(2,'2','パート','0',now(),now(),0),
(3,'3','派遣','0',now(),now(),0),
(4,'4','工事業社','0',now(),now(),0);
UNLOCK TABLES;

--
-- Table structure for table `food_divisions`
--

DROP TABLE IF EXISTS `food_divisions`;
CREATE TABLE `food_divisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `food_division_name` varchar(50) NOT NULL COMMENT '食事区分名',
  `instrument_division` int(5) NOT NULL COMMENT '機器区分',
  `food_cost` decimal(7,0) NOT NULL COMMENT '金額',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `reserve_food_division` int(5) NOT NULL DEFAULT '0' COMMENT '予約食事区分',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `food_division` (`food_division`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='食事マスタ';

--
-- Dumping data for table `food_divisions`
--

LOCK TABLES `food_divisions` WRITE;
INSERT INTO `food_divisions` VALUES 
(1,1,'定食',1,300,now(),now(),0,0),
(2,2,'丼',1,350,now(),now(),0,0),
(3,3,'定食',2,300,now(),now(),0,0),
(4,4,'丼',2,320,now(),now(),0,0),
(5,5,'定食予約',1,300,now(),now(),1,0),
(6,6,'丼予約',1,350,now(),now(),2,0),
(7,7,'定食予約',2,300,now(),now(),3,0),
(8,8,'丼予約',2,320,now(),now(),4,0);
UNLOCK TABLES;

--
-- Table structure for table `food_history_infos`
--

DROP TABLE IF EXISTS `food_history_infos`;
CREATE TABLE `food_history_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '食事履歴番号',
  `employee_id` varchar(10) NOT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) NOT NULL COMMENT '社員区分',
  `ic_card_number` varchar(16) NOT NULL COMMENT 'ICカード番号',
  `instrument_division` int(5) NOT NULL COMMENT '機器区分',
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `reason` varchar(50) DEFAULT NULL COMMENT '理由',
  `card_recept_time` datetime NOT NULL COMMENT 'カード受付時間',
  `state_flg` char(1) NOT NULL DEFAULT '0' COMMENT '状態フラグ',
  `food_cost` decimal(7,0) NOT NULL COMMENT '金額',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='食事履歴情報';

--
-- Table structure for table `food_periods`
--

DROP TABLE IF EXISTS `food_periods`;
CREATE TABLE `food_periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `start_date` date NOT NULL COMMENT '開始日',
  `food_period_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '食事名',
  `food_price` decimal(7,0) NOT NULL COMMENT '価格',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='食事期間マスタ';

--
-- Table structure for table `instrument_divisions`
--

DROP TABLE IF EXISTS `instrument_divisions`;
CREATE TABLE `instrument_divisions` (
  `instrument_division` int(5) NOT NULL COMMENT '機器区分',
  `instrument_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '機器区分名',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`instrument_division`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='機器マスタ';

--
-- Dumping data for table `instrument_divisions`
--

LOCK TABLES `instrument_divisions` WRITE;
INSERT INTO `instrument_divisions` VALUES 
(1,'本社',now(),now(),0),
(2,'工場',now(),now(),0);
UNLOCK TABLES;

--
-- Table structure for table `login_historys`
--
DROP TABLE IF EXISTS `login_historys`;
CREATE TABLE `login_historys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(10) NOT NULL COMMENT '管理者ID',
  `login_datetime` datetime NOT NULL COMMENT 'ログイン日付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='ログイン履歴';

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(5) NOT NULL COMMENT 'オプションID',
  `option_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'オプション名称',
  `option_state` int(1) NOT NULL DEFAULT '1' COMMENT 'オプション有無',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='オプションマスタ';

--
-- Dumping data for table `options`
--

LOCK TABLES `options` WRITE;
INSERT INTO `options` VALUES 
(1,1,'userReservationFlg',1,now(),now(),0),
(2,2,'userFoodHistoryFlg',1,now(),now(),0);
UNLOCK TABLES;

--
-- Table structure for table `regist_errors`
--

DROP TABLE IF EXISTS `regist_errors`;
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

--
-- Table structure for table `reservation_decisions`
--

DROP TABLE IF EXISTS `reservation_decisions`;
CREATE TABLE `reservation_decisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_date` datetime NOT NULL COMMENT '予約日付',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='予約確定';

--
-- Table structure for table `reservation_infos`
--

DROP TABLE IF EXISTS `reservation_infos`;
CREATE TABLE `reservation_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(10) NOT NULL COMMENT '社員コード',
  `employee_kbn` varchar(2) NOT NULL COMMENT '社員区分',
  `food_division` int(5) NOT NULL COMMENT '食事区分',
  `reason` varchar(50) DEFAULT NULL COMMENT '理由',
  `reservation_date` datetime NOT NULL COMMENT '予約日付',
  `state_flg` char(1) NOT NULL DEFAULT '0' COMMENT '状態フラグ',
  `food_cost` decimal(7,0) NOT NULL COMMENT '金額',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='予約情報';

--
-- Table structure for table `tabs`
--

DROP TABLE IF EXISTS `tabs`;
CREATE TABLE `tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tab_id` int(5) NOT NULL COMMENT '分類',
  `tab_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'タブ名称',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(11) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tab_id` (`tab_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='タブマスタ';

--
-- Dumping data for table `tabs`
--

LOCK TABLES `tabs` WRITE;
INSERT INTO `tabs` VALUES
(1,1,'本社メニュー',now(),now(),0),
(2,2,'工場メニュー',now(),now(),0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(5) NOT NULL COMMENT 'オプションID',
  `option_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL UNIQUE COMMENT 'オプション名称',
  `option_state` int(1) NOT NULL DEFAULT '1' COMMENT 'オプション有無',
  `created` datetime NOT NULL COMMENT '登録日',
  `modified` datetime NOT NULL COMMENT '更新日',
  `delete_flg` int(1) NOT NULL DEFAULT '0' COMMENT '削除フラグ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='オプションマスタ';

--
-- Dumping data for table `options`
--

LOCK TABLES `options` WRITE;
INSERT INTO `options` VALUES
(1,1,'userReservationFlg',1 ,'2020-06-23 00:00:00','2020-06-23 00:00:00',0),
(2,2,'userFoodHistoryFlg',1 ,'2020-06-23 00:00:00','2020-06-23 00:00:00',0),
(3,3,'reservation',0 ,'2020-06-23 00:00:00','2020-06-23 00:00:00',0),
(4,4,'qrcodeFlg',0,'2020-06-23 00:00:00','2020-06-23 00:00:00',0);
UNLOCK TABLES;


--
-- Table structurefor table `food_history_reservations`
--

CREATE VIEW `food_history_reservations` AS
select `food_history_infos`.`employee_id` AS `employee_id`,`food_history_infos`.`employee_kbn` AS `employee_kbn`,`food_history_infos`.`food_division` AS `food_division`,`food_history_infos`.`card_recept_time` AS `target_date`,`food_history_infos`.`reason` AS `reason`,'1' AS `data_type`,`food_history_infos`.`food_cost` AS `food_cost`
from `food_history_infos` where ((`food_history_infos`.`state_flg` in ('0','1')) and (`food_history_infos`.`delete_flg` = 0)) union all
select `reservation_infos`.`employee_id`  AS `employee_id`,`reservation_infos`.`employee_kbn`  AS `employee_kbn`,`reservation_infos`.`food_division`  AS `food_division`,`reservation_infos`.`reservation_date`  AS `target_date`,`reservation_infos`.`reason`  AS `reason`,'2' AS `data_type`,`reservation_infos`.`food_cost` AS `food_cost`
from `reservation_infos`  where ((`reservation_infos`.`state_flg` in ('0','1'))  and (`reservation_infos`.`delete_flg` = 0));
