<?php
// modules/personnel/admin_action.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, member
if (gcms::isReferer() && gcms::canConfig($config, 'personnel_can_write')) {
	if (empty($_SESSION['login']['account']) || $_SESSION['login']['account'] != 'demo') {
		// ตรวจสอบ id
		$ids = array();
		foreach (explode(',', $_POST['id']) AS $id) {
			$ids[] = (int)$id;
		}
		// id ของ สมาชิกทั้งหมดที่ส่งมา
		$ids = implode(',', $ids);
		if ($_POST['action'] == 'delete' && $ids != '') {
			$sql = "SELECT `picture` FROM `".DB_PERSONNEL."` WHERE `id` IN ($ids) AND `module_id`=";
			$sql .= "(SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='personnel')";
			foreach ($db->customQuery($sql) AS $item) {
				@unlink(DATA_PATH."personnel/$item[picture]");
			}
			// ลบ db
			$sql = "DELETE FROM `".DB_PERSONNEL."` WHERE `id` IN ($ids) AND `module_id`=";
			$sql .= "(SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='personnel')";
			$db->query($sql);
		} elseif (preg_match('/^order_([0-9]+)$/', $_POST['id'], $match)) {
			$ret["order_$match[1]"] = gcms::getVars($_POST, 'value', 0);
			$db->edit(DB_PERSONNEL, $match[1], array('order' => $ret["order_$match[1]"]));
		} else {
			print_r($_POST);
		}
	}
} else {
	$ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
