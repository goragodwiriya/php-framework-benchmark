<?php
// modules/personnel/main.php
if (defined('MAIN_INIT')) {
	// เลือกไฟล์
	if (isset($_REQUEST['id'])) {
		// แสดง personnel ตาม id
		include (ROOT_PATH.'modules/personnel/view.php');
	} else {
		// แสดงหมวด หรือ ลิสต์รายการ
		include (ROOT_PATH.'modules/personnel/list.php');
	}
}
