<?php die('File not found !')?>
define('DB_VIDEO', PREFIX.'_video');
DROP TABLE IF EXISTS `{prefix}_video`;
CREATE TABLE IF NOT EXISTS `{prefix}_video` (`id` int(11) unsigned NOT NULL auto_increment,`module_id` int(11) unsigned NOT NULL,`youtube` varchar(11) collate utf8_unicode_ci NOT NULL,`topic` text collate utf8_unicode_ci NOT NULL,`description` text collate utf8_unicode_ci NOT NULL,`views` int(11) unsigned NOT NULL,`last_update` int(11) unsigned NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DELETE FROM `{prefix}_language` WHERE `owner`='video';
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO','text','video','0','วีดีโอ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_CAN_WRITE_COMMENT','text','video','0','ขีดถูกเพื่อให้สมาชิกระดับนี้สามารถเพิ่มหรือแก้ไขวีดีโอได้');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_DESCRIPTION_COMMENT','text','video','0','คำอธิบายสั้นๆเกี่ยวกับวีดีโอ (ถ้าไม่กรอกจะใช้รายละเอียดที่ได้จาก Youtube)');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_ID','text','video','0','Youtube ID');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_ID_COMMENT','text','video','0','กรอก ID ของ วีดีโอจาก Youtube 11 ตัวอักษร เช่น 17IKhjQWT9M (โดยไม่ต้องนำ URL มาด้วย)');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_LIST','text','video','0','รายการวีดีโอ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_VIDEO_TOPIC_COMMENT','text','video','0','ชื่อของวีดีโอ (ถ้าไม่กรอกจะใช้ชื่อที่ได้จาก Youtube)');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('VIDEO_EXISTS','text','video','1','มีวีดีโอนี้อยู่ก่อนแล้ว');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('VIDEO_NOT_FOUND','text','video','1','ไม่พบวีดีโอที่ต้องการ');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('VIDEO_SERVER_ERROR','text','video','1','ไม่สามารถตรวจสอบวีดีโอได้ในขณะนี้ กรุณาลองใหม่ในภายหลัง');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('YOUTUBE_INVALID','text','video','1','ID ของ Youtube ไม่ถูกต้อง');