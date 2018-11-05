<?php
	// bin/vars.php
	// โฟลเดอร์สำหรับเก็บไอคอนของสมาชิก
	define('USERICON_PATH', DATA_FOLDER.'member/');
	// นับจาก root ของ server
	define('USERICON_FULLPATH', ROOT_PATH.USERICON_PATH);
	// เวอร์ชั่นของ gcms
	define('VERSION', '10.1.2');
	// ชื่อตัวแปรสำหรับเติมค่าตัวแปรต่างๆ
	// เช่น session หรือ db
	// เพื่อให้เป็นตัวแปรเฉพาะของเว็บไซต์เท่านั้น
	define('PREFIX', 'plus');
	// ชื่อตารางฐานข้อมูลพื้นฐานต่างๆ
	// ตารางสมาชิก
	define('DB_USER', PREFIX.'_user');
	// ตารางเนื้อหา
	define('DB_MODULES', PREFIX.'_modules');
	define('DB_INDEX', PREFIX.'_index');
	define('DB_INDEX_DETAIL', PREFIX.'_index_detail');
	define('DB_MENUS', PREFIX.'_menus');
	define('DB_COMMENT', PREFIX.'_comment');
	define('DB_CATEGORY', PREFIX.'_category');
	define('DB_BOARD_R', PREFIX.'_board_r');
	define('DB_BOARD_Q', PREFIX.'_board_q');
	// ตาราง ภาษา
	define('DB_LANGUAGE', PREFIX.'_language');
	// ตาราง Email
	define('DB_EMAIL_TEMPLATE', PREFIX.'_emailtemplate');
	// ตาราง counter
	define('DB_COUNTER', PREFIX.'_counter');
	// ตาราง useronline
	define('DB_USERONLINE', PREFIX.'_useronline');
	// ตำบล อำเภอ จังหวัด
	define('DB_PROVINCE', PREFIX.'_province');
	define('DB_COUNTRY', PREFIX.'_country');
	// ค่าคีย์สำหรับการเข้ารหัส
	define('EN_KEY', 6116);
	// ตารางอื่นๆ
	define("DB_DOWNLOAD", PREFIX."_download");
	define("DB_EDOCUMENT", PREFIX."_edocument");
	define("DB_EDOCUMENT_DOWNLOAD", PREFIX."_edocument_download");
	define("DB_EVENTCALENDAR", PREFIX."_eventcalendar");
	define('DB_GALLERY', PREFIX.'_gallery');
	define('DB_GALLERY_ALBUM', PREFIX.'_gallery_album');
	define("DB_PERSONNEL", PREFIX."_personnel");
	define('DB_VIDEO', PREFIX.'_video');
	define("DB_CHAT", PREFIX."_chat");
	define("DB_SHOUTBOX", PREFIX."_shoutbox");
	define("DB_TAGS", PREFIX."_tags");
	define("DB_TEXTLINK", PREFIX."_textlink");