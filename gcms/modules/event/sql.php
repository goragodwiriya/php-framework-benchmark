<?php die('File not found !')?>
define("DB_EVENTCALENDAR", PREFIX."_eventcalendar");
DROP TABLE IF EXISTS `{prefix}_eventcalendar`;
CREATE TABLE IF NOT EXISTS `{prefix}_eventcalendar` (`id` int(11) unsigned NOT NULL auto_increment,`module_id` int(11) unsigned NOT NULL,`topic` varchar(64) collate utf8_unicode_ci NOT NULL,`detail` text collate utf8_unicode_ci NOT NULL,`description` varchar(149) collate utf8_unicode_ci NOT NULL,`keywords` varchar(149) collate utf8_unicode_ci NOT NULL,`member_id` int(11) unsigned NOT NULL,`create_date` int(11) unsigned NOT NULL,`last_update` int(11) unsigned NOT NULL,`begin_date` datetime NOT NULL,`color` varchar(7) collate utf8_unicode_ci NOT NULL,`published` tinyint(1) unsigned NOT NULL,`published_date` date NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DELETE FROM `{prefix}_language` WHERE `owner`='event';
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('EVENT_DESCRIPTION','text','event','0','Event Calendar');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EVENT','text','event','0','Event');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EVENT_COLOR','text','event','0','สีของอีเว้นต์');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EVENT_COLOR_COMMENT','text','event','0','เลือกสีที่จะแสดงในปฏิทินเหตุการณ์ ของรายการนี้');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EVENT_DATE_COMMENT','text','event','0','ระบุวันที่และเวลาเริ่มต้นของอีเว้นต์');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_EVENT_LIST','text','event','0','รายการอีเว้นต์');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_NEXT_MONTH','text','event','0','Next Month');
INSERT INTO `{prefix}_language` (`key`, `type`, `owner`, `js`, `th`) VALUES ('LNG_PREV_MONTH','text','event','0','Prev Month');