<?php
// modules/member/mailto.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer
if (gcms::isReferer()) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		// ค่าที่ส่งมา
		$topic = $db->sql_trim_str($_POST, 'mail_topic');
		$detail = gcms::ckClean($_POST['mail_detail']);
		$sender = gcms::getVars($_SESSION, 'login', array());
		$to = $db->sql_trim_str($_POST, 'mail_to');
		if (!preg_match('/[0-9,]{1,}/', $to)) {
			$ret['error'] = 'EMAIL_RECIEVER_NOT_FOUND';
		} else {
			// อีเมล์ของผู้รับ
			$emails = array();
			// อ่านและตรวจสอบอีเมล์ของผู้รับ
			$sql = "SELECT `email` FROM `".DB_USER."` WHERE `id` IN ($to)";
			foreach ($db->customQuery($sql) AS $item) {
				$emails[] = $item['email'];
			}
			// ตรวจสอบค่าที่ส่งมา
			if (empty($sender['email'])) {
				$ret['error'] = 'SENDER_EMPTY';
				$ret['input'] = 'mail_sender';
			} elseif (!gcms::validMail($sender['email'])) {
				$ret['error'] = 'REGISTER_INVALID_EMAIL';
				$ret['input'] = 'mail_sender';
			} elseif (sizeof($emails) == 0) {
				$ret['error'] = 'EMAIL_RECIEVER_NOT_FOUND';
			} elseif ($topic == '') {
				$ret['error'] = 'TOPIC_EMPTY';
				$ret['input'] = 'mail_topic';
			} elseif ($detail == '') {
				$ret['error'] = 'DETAIL_EMPTY';
			} elseif ($_POST['mail_antispam'] != $_SESSION[$_POST['antispam']]) {
				$ret['ret_mail_antispam'] = 'this';
				$ret['input'] = 'mail_antispam';
			} else {
				// ส่งอีเมล์
				$error = gcms::customMail(implode(',', $emails), "$sender[email]<$sender[displayname]>", $topic, $detail);
				// clear antispam
				unset($_SESSION['emails']);
				unset($_SESSION[$_POST['antispam']]);
				// คืนค่า
				if ($error == '') {
					$ret['error'] = 'EMAIL_SEND_SUCCESS';
					$ret['location'] = 'back';
				} else {
					$ret['alert'] = rawurlencode($error);
				}
			}
		}
	}
	// คืนค่าเป็น JSON
	echo gcms::array2json($ret);
}
