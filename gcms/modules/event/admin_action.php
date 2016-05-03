<?php
// modules/event/admin_action.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, member
if (gcms::isReferer() && gcms::canConfig($config, 'event_can_write')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		// ค่าที่ส่งมา
		$action = gcms::getVars($_POST, 'action', '');
		// ตรวจสอบ id
		$ids = array();
		foreach (explode(',', $_POST['id']) AS $id) {
			$ids[] = (int)$id;
		}
		$ids = implode(',', $ids);
		if ($ids != '') {
			$module = $db->basicSearch(DB_MODULES, 'owner', 'event');
			if ($module) {
				if ($action == 'published') {
					// published
					$db->query("UPDATE `".DB_EVENTCALENDAR."` SET `published`=".(int)$_POST['value']." WHERE `id` IN($ids) AND `module_id`='$module[id]'");
				} elseif ($action == 'delete') {
					// ลบ
					$db->query("DELETE FROM `".DB_EVENTCALENDAR."` WHERE `id` IN($ids) AND `module_id`='$module[id]'");
				}
			} else {
				$ret['error'] = 'ACTION_ERROR';
			}
		}
	}
} else {
	$ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
