<?php
// modules/member/usericon.php
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
// inint
include '../../bin/inint.php';
// referer
if (isset($_GET['id'])) {
	// อ่านรูปจาก id ของสมาชิก
	$sql = "SELECT `icon` FROM `".DB_USER."` WHERE `id`='".(int)$_GET['id']."' LIMIT 1";
	// อ่านจาก cache
	$result = $cache->get($sql);
	if (!$result) {
		// อ่านจาก db
		$result = $db->customQuery($sql);
		if (sizeof($result) == 1) {
			$result = $result[0];
			$cache->save($sql, $result);
		}
	}
	// ไม่มีรูปใช้รูป default
	$picture = (!empty($result['icon'])) ? USERICON_FULLPATH.$result['icon'] : ROOT_PATH.'skin/img/noicon.jpg';
	$picture = is_file($picture) ? $picture : ROOT_PATH.'skin/img/noicon.jpg';
} else {
	$picture = ROOT_PATH.'skin/img/noicon.jpg';
}
// ตรวจสอบรูป
$info = getImageSize($picture);
if (empty($info['error'])) {
	header("Content-type: $info[mime]");
	echo file_get_contents($picture);
}
