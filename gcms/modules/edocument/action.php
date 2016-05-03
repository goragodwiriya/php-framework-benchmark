<?php
// modules/edocument/action.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
// referer
if (gcms::isReferer() && preg_match('/^(icon\-)?(download|downloading|delete)\s([0-9]+)$/', $_POST['id'], $match)) {
	// ค่าที่ส่งมา
	$action = $match[2];
	$id = $match[3];
	// login
	$login = gcms::getVars($_SESSION, 'login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''));
	// guest = -1
	$status = isset($login['status']) ? $login['status'] : -1;
	if ($action == 'download' || $action == 'downloading') {
		// ไฟล์ดาวน์โหลด
		$sql = "SELECT D.*,N.`id` AS `download_id`,N.`downloads` FROM `".DB_EDOCUMENT."` AS D";
		$sql .= " LEFT JOIN `".DB_EDOCUMENT_DOWNLOAD."` AS N ON N.`document_id`=D.`id` AND N.`member_id`=".(int)$login['id'];
		$sql .= " WHERE D.`id`=".(int)$id." LIMIT 1";
		$download = $db->customQuery($sql);
		$download = sizeof($download) == 1 ? $download[0] : false;
		$file_path = DATA_PATH."edocument/$download[file]";
		// ตรวจสอบสถานะการดาวน์โหลด
		if (!$download || !is_file($file_path)) {
			$ret['error'] = 'DOWNLOAD_FILE_NOT_FOUND';
		} elseif (!in_array($status, explode(',', $download['reciever']))) {
			$ret['error'] = 'DO_NOT_DOWNLOAD';
		} elseif ($action == 'download') {
			$ret['confirm'] = 'CONFIRM_DOWNLOAD';
		} elseif ($action == 'downloading') {
			// อัปเดทดาวน์โหลด
			$save = array();
			$save['last_update'] = $mmktime;
			$save['downloads'] = $download['downloads'] + 1;
			if ($download['download_id'] == 0) {
				$save['module_id'] = $download['module_id'];
				$save['document_id'] = $download['id'];
				$save['member_id'] = $login['id'];
				$db->add(DB_EDOCUMENT_DOWNLOAD, $save);
			} else {
				$db->edit(DB_EDOCUMENT_DOWNLOAD, $download['download_id'], $save);
			}
			// URL สำหรับดาวน์โหลด
			$fid = gcms::rndname(32);
			$_SESSION[$fid]['file'] = $file_path;
			$_SESSION[$fid]['size'] = $download['size'];
			$_SESSION[$fid]['name'] = "$download[topic].$download[ext]";
			$_SESSION[$fid]['status'] = $status;
			// คืนค่า URL สำหรับดาวน์โหลด
			$ret['href'] = rawurlencode(WEB_URL."/modules/edocument/filedownload.php?id=$fid");
			$ret['downloads'] = $save['downloads'];
		}
	} elseif ($action == 'delete') {
		$download = $db->getRec(DB_EDOCUMENT, $id);
		if ($download) {
			$db->query("DELETE FROM `".DB_EDOCUMENT."` WHERE `id`='$download[id]' LIMIT 1");
			$db->query("DELETE FROM `".DB_EDOCUMENT_DOWNLOAD."` WHERE `document_id`='$download[id]'");
			// ลบสำเร็จ
			$ret['error'] = 'DELETE_SUCCESS';
		}
	}
	$ret['action'] = $action;
	$ret['id'] = $id;
	// คืนค่าเป็น JSON
	echo json_encode($ret);
}
