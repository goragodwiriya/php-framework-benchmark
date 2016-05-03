<?php
// modules/personnel/admin_write_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, member
if (gcms::isReferer() && gcms::canConfig($config, 'personnel_can_write')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		$error = false;
		// ค่าที่ส่งมา
		$save = array();
		$save['name'] = $db->sql_trim_str($_POST, 'write_name');
		$save['email'] = $db->sql_trim_str($_POST, 'write_email');
		$save['position'] = $db->sql_trim_str($_POST, 'write_position');
		$save['phone'] = $db->sql_trim_str($_POST, 'write_phone');
		$save['address'] = $db->sql_trim_str($_POST, 'write_address');
		$save['detail'] = $db->sql_trim_str($_POST, 'write_detail');
		$save['category_id'] = gcms::getVars($_POST, 'write_category', 0);
		$save['order'] = min(99, max(0, (int)$_POST['write_order']));
		$icon = $_FILES['write_picture'];
		$id = gcms::getVars($_POST, 'write_id', 0);
		// ตรวจสอบค่าที่ส่งมา
		if ($id > 0) {
			$sql = "SELECT C.*,M.`module` FROM `".DB_MODULES."` AS M";
			$sql .= " INNER JOIN `".DB_PERSONNEL."` AS C ON C.`module_id`=M.`id` AND C.`id`=$id";
		} else {
			$sql1 = "SELECT MAX(`id`)+1 FROM `".DB_PERSONNEL."` WHERE `module_id`=M.`id`";
			$sql = "SELECT IFNULL(($sql1),1) AS `id`,M.`id` AS `module_id`,M.`module` FROM `".DB_MODULES."` AS M";
		}
		$sql .= " WHERE M.`owner`='personnel' LIMIT 1";
		$index = $db->customQuery($sql);
		// ตรวจสอบค่าที่ส่งมา
		if (sizeof($index) == 0) {
			$ret['error'] = 'ACTION_ERROR';
		} elseif ($save['name'] == '') {
			$ret['ret_write_name'] = 'FNAME_EMPTY';
			$ret['error'] = 'FNAME_EMPTY';
			$ret['input'] = 'write_name';
		} elseif ($icon['tmp_name'] == '' && $id == 0) {
			// ใหม่ ต้องมีรูปภาพเสมอ
			$ret['ret_write_picture'] = 'REQUIRE_PICTURE';
			$ret['error'] = 'REQUIRE_PICTURE';
			$ret['input'] = 'write_picture';
		} else {
			// อัปโหลดรูปภาw
			if ($icon['tmp_name'] != '') {
				// ตรวจสอบไฟล์อัปโหลด
				$info = gcms::isValidImage(array('jpg', 'gif', 'png'), $icon);
				if (!$info) {
					$ret['ret_write_picture'] = 'INVALID_FILE_TYPE';
					$input = 'write_picture';
					$error = 'INVALID_FILE_TYPE';
				} else {
					$save['picture'] = $index[0]['id'].'.jpg';
					// อัปโหลด
					if (!gcms::cropImage($icon['tmp_name'], DATA_PATH."personnel/$save[picture]", $info, $config['personnel_image_w'], $config['personnel_image_h'])) {
						$ret['ret_write_picture'] = 'DO_NOT_UPLOAD';
						$input = 'write_picture';
						$error = 'DO_NOT_UPLOAD';
					} else {
						$ret['imgIcon'] = rawurlencode(DATA_URL."personnel/$save[picture]?$mmktime");
					}
					// ลบไฟล์เดิม
					if ($save['picture'] != $index[0]['picture']) {
						@unlink(DATA_PATH.'personnel/'.$index[0]['picture']);
					}
				}
			}
			if (!$error) {
				if ($id == 0) {
					// ใหม่
					$save['module_id'] = $index[0]['module_id'];
					$db->add(DB_PERSONNEL, $save);
					// คืนค่า
					$ret['error'] = 'ADD_COMPLETE';
					$ret['location'] = rawurlencode('index.php?module=personnel-setup');
				} else {
					// แก้ไข
					$db->edit(DB_PERSONNEL, $index[0]['id'], $save);
					// คืนค่า
					$ret['error'] = 'EDIT_SUCCESS';
				}
				$ret['write_order'] = $save['order'];
			} else {
				if ($input) {
					$ret['input'] = $input;
				}
				$ret['error'] = $error;
			}
		}
	}
} else {
	$ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
