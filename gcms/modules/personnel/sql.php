<?php die('File not found !')?>
define("DB_PERSONNEL", PREFIX."_personnel");
DROP TABLE IF EXISTS `{prefix}_personnel`;
CREATE TABLE IF NOT EXISTS `{prefix}_personnel` (`id` int(11) unsigned NOT NULL auto_increment,`module_id` int(11) unsigned NOT NULL,`category_id` int(11) unsigned NOT NULL,`name` varchar(50) collate utf8_general_ci NOT NULL,`position` varchar(100) collate utf8_general_ci NOT NULL,`detail` varchar(255) collate utf8_general_ci NOT NULL,`address` varchar(255) collate utf8_general_ci NOT NULL,`phone` varchar(20) collate utf8_general_ci NOT NULL,`email` varchar(255) collate utf8_general_ci NOT NULL,`picture` varchar(15) collate utf8_general_ci NOT NULL,`order` tinyint(2) unsigned NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
DELETE FROM `{prefix}_language` WHERE `owner`='personnel';
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_ORDER','text','personnel','0','ลำดับ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL','text','personnel','0','บุคลากร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_CAN_WRITE_COMMENT','text','personnel','0','ขีดถูกเพื่อให้สมาชิกระดับนี้ สามารถเพิ่ม - แก้ไข รายละเอียดต่างๆของบุคลากรได้');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_CATEGORY','text','personnel','0','กลุ่มบุคลากร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_CATEGORY_COMMENT','text','personnel','0','จัดการประเภทหรือกลุ่มของบุคลากร, คลิกที่ข้อความเพื่อแก้ไข');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_CONFIG','text','personnel','0','ตั้งค่าระบบบุคลากร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_DETAILS','text','personnel','0','รายละเอียดบุคลากร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_IMAGE_COMMENT','text','personnel','0','ขนาดของรูปภาพที่ถูกจัดเก็บ สำหรับแสดงเป็นรูปภาพของบุคลากร, ปรับขนาดอัตโนมัติ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_LIST','text','personnel','0','รายชื่อบุคลากร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PERSONNEL_ORDER_COMMENT','text','personnel','0','กรอกตัวเลขมากกว่า 0 เพื่อจัดลำดับความสำคัญ เรียงจากน้อยไปหามาก โดยที่เลข 1 จะมีความสำคัญสูงสุด เช่นผู้บริหาร');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_POSITION','text','personnel','0','ตำแหน่ง');