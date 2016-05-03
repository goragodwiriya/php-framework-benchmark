<?php
// modules/edocument/admin_action.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
// referer, admin
if (gcms::isReferer() && gcms::canConfig($config, 'edocument_moderator')) {
	if (empty($_SESSION['login']['account']) || $_SESSION['login']['account'] != 'demo') {
		// ค่าที่ส่งมา
		$action = gcms::getVars($_POST, 'action', '');
		$ids = array();
		foreach (explode(',', $_POST['id']) AS $id) {
			$ids[] = (int)$id;
		}
		$ids = implode(',', $ids);
		if ($action == 'delete' && $ids != '') {
			$sql = "SELECT `id` AS `module_id`,`module` FROM `".DB_MODULES."` WHERE `owner`='edocument' LIMIT 1";
			$index = $db->customQuery($sql);
			if (sizeof($index) == 1) {
				$index = $index[0];
				// ลบไฟล์
				$sql = "SELECT `file` FROM `".DB_EDOCUMENT."` WHERE `id` IN ($ids) AND `module_id`='$index[module_id]'";
				foreach ($db->customQuery($sql) AS $item) {
					@unlink(DATA_PATH."edocument/$item[file]");
				}
				// ลบ
				$db->query("DELETE FROM `".DB_EDOCUMENT."` WHERE `id` IN ($ids) AND `module_id`='$index[module_id]'");
				$db->query("DELETE FROM `".DB_EDOCUMENT_DOWNLOAD."` WHERE `document_id` IN ($ids) AND `module_id`='$index[module_id]'");
			}
		}
	}
}
