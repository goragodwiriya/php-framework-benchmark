<?php
// modules/event/main.php
if (defined('MAIN_INIT')) {
	// เลือกไฟล์
	if (isset($_REQUEST['id'])) {
		// แสดง event ตาม id
		include (ROOT_PATH.'modules/event/view.php');
	} elseif (isset($_REQUEST['d'])) {
		// แสดง event รายวัน
		include (ROOT_PATH.'modules/event/day.php');
	} else {
		// แสดง event รายเดือน
		include (ROOT_PATH.'modules/event/month.php');
	}
}
