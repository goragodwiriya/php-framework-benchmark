<?php
// modules/edocument/admin_download.php
// inint
include '../../bin/inint.php';
// referer, can download
if (gcms::isReferer() && gcms::canConfig($config, 'edocument_moderator')) {
	// อ่านไฟล์ดาวน์โหลด
	$file = $db->getRec(DB_EDOCUMENT, $_GET['id']);
	$file_path = DATA_PATH."edocument/$file[file]";
	if ($file && is_file($file_path)) {
		// ดาวน์โหลดไฟล์
		header('Cache-Control: private');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');
		header("Content-Disposition: attachment; filename=\"$file[topic].$file[ext]\"");
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		set_time_limit(0);
		readfile($file_path);
	} else {
		header("HTTP/1.0 404 Not Found");
	}
} else {
	header("HTTP/1.0 404 Not Found");
}
