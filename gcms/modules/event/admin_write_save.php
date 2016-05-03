<?php
// modules/event/admin_write_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// ตรวจสอบ referer และ สมาชิก
if (gcms::isReferer() && gcms::canConfig($config, 'event_can_write')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		// ค่าที่ส่งมา
		$save['topic'] = gcms::getTags($_POST['write_topic']);
		$keywords = gcms::getTags($_POST['write_keywords']);
		$save['keywords'] = $db->sql_clean(gcms::cutstring(preg_replace('/[\'\"\r\n\s]{1,}/isu', ' ', ($keywords == '' ? $save['topic'] : $keywords)), 149));
		$description = trim(gcms::getVars($_POST, 'write_description', ''));
		$save['description'] = $db->sql_trim_str(gcms::cutstring(gcms::html2txt($description == '' ? $_POST['write_detail'] : $description), 149));
		$save['detail'] = gcms::ckDetail($_POST['write_detail']);
		$save['published_date'] = $db->sql_trim_str($_POST, 'write_published_date');
		$save['published'] = $_POST['write_published'] == '1' ? '1' : '0';
		$save['begin_date'] = "$_POST[write_d] $_POST[write_h]:$_POST[write_m]:00";
		$save['color'] = $db->sql_trim_str($_POST, 'write_color');
		$id = gcms::getVars($_POST, 'write_id', 0);
		if ($id > 0) {
			// ตรวจสอบโมดูล หรือ เรื่องที่เลือก (แก้ไข)
			$sql = "SELECT I.`module_id`,M.`module`";
			$sql .= " FROM `".DB_EVENTCALENDAR."` AS I";
			$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`owner`='event' AND M.`id`=I.`module_id`";
			$sql .= " WHERE I.`id`='$id'";
			$sql .= " LIMIT 1";
		} else {
			// ตรวจสอบโมดูล (ใหม่)
			$sql = "SELECT `id` AS `module_id`,`module` FROM `".DB_MODULES."` WHERE `owner`='event' LIMIT 1";
		}
		$index = $db->customQuery($sql);
		if (sizeof($index) == 0) {
			$ret['error'] = 'ACTION_ERROR';
		} else {
			$index = $index[0];
			// login
			$login = gcms::getVars($_SESSION, 'login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''));
			// ตรวจสอบข้อมูลที่กรอก
			$input = false;
			$error = false;
			// topic, ตัวพิมพ์เล็กสำหรับตรวจสอบ
			$topic = strtolower(stripslashes($save['topic']));
			if ($topic == '') {
				$ret['ret_write_topic'] = 'TOPIC_EMPTY';
				$input = !$input ? 'write_topic' : $input;
				$error = !$error ? 'TOPIC_EMPTY' : $error;
			} elseif (mb_strlen($topic) < 3) {
				$ret['ret_write_topic'] = 'TOPIC_SHORT';
				$input = !$input ? 'write_topic' : $input;
				$error = !$error ? 'TOPIC_SHORT' : $error;
			} else {
				$ret['ret_write_topic'] = '';
			}
			// detail
			if ($save['detail'] == '') {
				$error = !$error ? 'DETAIL_EMPTY' : $error;
			}
			if (!$error) {
				// บันทึก
				$save['last_update'] = $mmktime;
				if ($id == 0) {
					// เขียนเรื่องใหม่
					$save['create_date'] = $mmktime;
					$save['member_id'] = $login['id'];
					$save['module_id'] = $index['module_id'];
					$id = $db->add(DB_EVENTCALENDAR, $save);
				} else {
					// แก้ไขเรื่อง
					$db->edit(DB_EVENTCALENDAR, $id, $save);
				}
				// คืนค่า
				$ret['error'] = 'SAVE_COMPLETE';
				$ret['location'] = 'back';
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
