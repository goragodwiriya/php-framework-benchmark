<?php
// modules/edocument/admin_write_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, admin
if (gcms::isReferer() && gcms::canConfig($config, 'edocument_moderator')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		// ค่าที่ส่งมา
		$save = array();
		$save['document_no'] = $db->sql_trim_str($_POST, 'edocument_no');
		$save['topic'] = $db->sql_trim_str($_POST, 'edocument_topic');
		$save['detail'] = gcms::ckClean($_POST['edocument_detail']);
		if (isset($_POST['edocument_reciever'])) {
			$save['reciever'] = implode(',', $_POST['edocument_reciever']);
		}
		$id = gcms::getVars($_POST, 'write_id', 0);
		$file = $_FILES['edocument_file'];
		// ตรวจสอบค่าที่ส่งมา
		$error = false;
		$input = false;
		if ($id > 0) {
			// แก้ไข
			$sql = "SELECT D.*,M.`module`";
			$sql .= " FROM `".DB_EDOCUMENT."` AS D";
			$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`id`=D.`module_id`";
			$sql .= " WHERE D.`id`='$id' AND M.`owner`='edocument' LIMIT 1";
		} else {
			// ใหม่
			$sql = "SELECT M.`module`,M.`id` AS `module_id`";
			$sql .= ",(SELECT MAX(`id`) FROM `".DB_EDOCUMENT."` WHERE `module_id`=M.`id`) AS `id`";
			$sql .= " FROM `".DB_MODULES."` AS M WHERE M.`owner`='edocument' LIMIT 1";
		}
		$index = $db->customQuery($sql);
		if (sizeof($index) == 0) {
			$error = 'ACTION_ERROR';
		} else {
			$index = $index[0];
			// topic
			if ($save['document_no'] == '') {
				$ret['ret_edocument_no'] = 'EDOCUMENT_NO_EMPTY';
				$input = !$input ? 'edocument_no' : $input;
				$error = !$error ? 'EDOCUMENT_NO_EMPTY' : $error;
			} else {
				// ค้นหาเลขที่เอกสารซ้ำ
				$sql = "SELECT `id` FROM `".DB_EDOCUMENT."` WHERE `document_no`='$save[document_no]' LIMIT 1";
				$search = $db->customQuery($sql);
				if (sizeof($search) > 0 && ($id == 0 || $id != $search[0]['id'])) {
					$ret['ret_edocument_no'] = 'EDOCUMENT_NO_EXISTS';
					$input = !$input ? 'edocument_no' : $input;
					$error = !$error ? 'EDOCUMENT_NO_EXISTS' : $error;
				} else {
					$ret['ret_edocument_no'] = '';
				}
			}
			// reciever
			if (empty($save['reciever'])) {
				$ret['ret_edocument_reciever'] = 'EDOCUMENT_RECIEVER_EMPTY';
				$input = !$input ? 'edocument_reciever' : $input;
				$error = !$error ? 'EDOCUMENT_RECIEVER_EMPTY' : $error;
			} else {
				$ret['ret_edocument_reciever'] = '';
			}
			// file
			if ($id == 0 && $file['tmp_name'] == '') {
				$ret['ret_edocument_file'] = 'EDOCUMENT_FILE_EMPTY';
				$input = !$input ? 'edocument_file' : $input;
				$error = !$error ? 'EDOCUMENT_FILE_EMPTY' : $error;
			} elseif ($file['tmp_name'] != '') {
				if (preg_match('/^(.*)\.(.*)$/', $file['name'], $match)) {
					$file_name = $match[1];
					$save['ext'] = $match[2];
				}
				// ตรวจสอบไฟล์อัปโหลด
				if ($file_name == '' || !in_array($save['ext'], $config['edocument_file_typies'])) {
					$ret['ret_edocument_file'] = 'INVALID_FILE_TYPE';
					$input = !$input ? 'edocument_file' : $input;
					$error = !$error ? 'INVALID_FILE_TYPE' : $error;
				} elseif ($file['size'] > ($config['edocument_upload_size'])) {
					$ret['ret_edocument_file'] = 'FILE_TOO_BIG';
					$input = !$input ? 'edocument_file' : $input;
					$error = !$error ? 'FILE_TOO_BIG' : $error;
				} else {
					// อัปโหลด
					$save['file'] = "$mmktime.$save[ext]";
					while (file_exists(DATA_PATH."edocument/$save[file]")) {
						$mmktime++;
						$save['file'] = "$mmktime.$save[ext]";
					}
					if (!@copy($file['tmp_name'], DATA_PATH."edocument/$save[file]")) {
						$ret['ret_edocument_file'] = 'DO_NOT_UPLOAD';
						$input = !$input ? 'edocument_file' : $input;
						$error = !$error ? 'DO_NOT_UPLOAD' : $error;
					} else {
						if ($save['topic'] == '') {
							$save['topic'] = $file_name;
						}
						$save['size'] = $file['size'];
						if (isset($index['file']) && $save['file'] != $index['file']) {
							@unlink(DATA_PATH."edocument/$index[file]");
						}
						$ret['ret_edocument_file'] = '';
					}
				}
			}
			if (!$error) {
				// บันทึกข้อมูล
				$save['ip'] = gcms::getip();
				$save['last_update'] = $mmktime;
				if ($id == 0) {
					$save['sender_id'] = $_SESSION['login']['id'];
					$save['id'] = $index['id'] + 1;
					$save['module_id'] = $index['module_id'];
					$id = $db->add(DB_EDOCUMENT, $save);
					$ret['error'] = 'ADD_COMPLETE';
				} else {
					$db->edit(DB_EDOCUMENT, $id, $save);
					$ret['error'] = 'EDIT_SUCCESS';
				}
				if ($config['sendmail'] == 1 && isset($_POST['send_email']) && $_POST['send_email'] == 1) {
					// ส่งอีเมล์แจ้งสมาชิก
					$reciever = array();
					foreach (explode(',', $save['reciever']) AS $item) {
						if ($item != -1) {
							$reciever[$item] = $item;
						}
					}
					if (sizeof($reciever) > 0) {
						$sql = "SELECT `fname`,`lname`,`email` FROM `".DB_USER."` WHERE `status` IN (".implode(',', $reciever).")";
						foreach ($db->customQuery($sql) AS $item) {
							// ส่งอีเมล์
							$replace = array();
							$replace['/%FNAME%/'] = $item['fname'];
							$replace['/%LNAME%/'] = $item['lname'];
							$replace['/%URL%/'] = WEB_URL."/index.php?module=$index[module]";
							gcms::sendMail(1, 'edocument', $replace, $item['email']);
						}
						$ret['error'] = 'EDOCUMENT_SAVE_AND_SEND_SUCCESS';
					}
				}
				// ส่งค่ากลับ
				$ret['location'] = gcms::retURL(WEB_URL.'/admin/index.php', array('module' => 'edocument-setup'));
			} else {
				$ret['error'] = $error;
				$ret['input'] = $input;
			}
		}
	}
} else {
	$ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
