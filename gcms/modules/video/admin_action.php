<?php
// modules/video/admin_action.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
// referer, member
if (gcms::isReferer() && gcms::canConfig($config, 'video_can_write')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		echo gcms::array2json(array('error' => 'EX_MODE_ERROR'));
	} else {
		$action = gcms::getVars($_POST, 'action', '');
		$ids = array();
		foreach (explode(',', $_POST['id']) AS $id) {
			$ids[] = (int)$id;
		}
		if (sizeof($ids) > 0) {
			$ids = implode(',', $ids);
			if ($action == 'delete') {
				// ลบอัลบัม, ตรวจสอบ id
				$sql = "SELECT `youtube`,`module_id` FROM `".DB_VIDEO."` WHERE `id` IN ($ids) AND `module_id`=(SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='video')";
				foreach ($db->customQuery($sql) AS $item) {
					// ลบรูป
					unlink(DATA_PATH."video/$item[youtube].jpg");
				}
				$db->query("DELETE FROM `".DB_VIDEO."` WHERE `id` IN ($ids) AND `module_id`=(SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='video')");
			}
		}
	}
}
