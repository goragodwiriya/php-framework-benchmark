<?php
// modules/event/get.php
header("content-type: text/html; charset=UTF-8");
// ตัวแปรหลัก
include ('../../bin/inint.php');
if (gcms::isReferer()) {
	// ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
	DEFINE('MAIN_INIT', __FILE__);
	// โหลด calendar
	include (ROOT_PATH.'modules/event/calendar.php');
	// คืนค่า HTML
	echo implode('', $calendar);
}
