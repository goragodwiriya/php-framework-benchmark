<?php
// modules/member/sendmail.php
if (defined('MAIN_INIT')) {
	// id ของผู้ที่ต้องการส่งข้อความถึง ส่งได้หลายคน คั่นด้วย ,
	// admin สำหรับส่งเมล์ไปยัง admin
	$tos = array();
	foreach (explode(',', $_REQUEST['to']) AS $item) {
		if ($item == 'admin') {
			$tos[] = "`id` IN (SELECT `id` FROM `".DB_USER."` WHERE `status`='1')";
		} else {
			$d = (int)$item;
			if ($d > 0) {
				$tos[] = "`id`=$d";
			}
		}
	}
	// array ของผู้รับ
	$emails = array();
	$ids = array();
	$status = array();
	if (sizeof($tos) > 0) {
		// อ่านและตรวจสอบอีเมล์ของผู้รับ
		$sql = "SELECT `id`,`status`,`email`,`displayname` FROM `".DB_USER."` WHERE ".implode(' OR ', $tos);
		foreach ($db->customQuery($sql) AS $item) {
			// ไม่สามารถส่งถึงตัวเองได้
			if ($isMember && $item['email'] != $_SESSION['login']['email']) {
				$emails[] = $item['displayname'] == '' ? $item['email'] : $item['displayname'];
				$ids[] = $item['id'];
				$status[] = $item['status'];
			}
		}
	}
	if (sizeof($ids) == 0) {
		// ไม่มีผู้รับ
		$title = $lng['SEND_MAIL_ERROR'];
		$content = '<div class=error>'.$title.'</div>';
	} else {
		// antispam
		$register_antispamchar = gcms::rndname(32);
		$_SESSION[$register_antispamchar] = gcms::rndname(4);
		// title
		$title = $lng['LNG_SENDMAIL_TITLE'];
		// breadcrumbs
		$breadcrumb = gcms::loadtemplate('', '', 'breadcrumb');
		$breadcrumbs = array();
		// หน้าหลัก
		$breadcrumbs['HOME'] = gcms::breadcrumb('icon-home', WEB_URL.'/index.php', $install_modules[$module_list[0]]['menu_tooltip'], $install_modules[$module_list[0]]['menu_text'], $breadcrumb);
		// แสดงผล member/sendmail.html
		$patt = array('/{BREADCRUMS}/', '/{(LNG_[A-Z0-9_]+)}/e', '/{TITLE}/', '/{SENDER}/',
			'/{RECIEVER}/', '/{RECIEVERID}/', '/{ANTISPAM}/', '/{ANTISPAMVAL}/');
		$replace = array();
		$replace[] = implode("\n", $breadcrumbs);
		$replace[] = OLD_PHP ? '$lng[\'$1\']' : 'gcms::getLng';
		$replace[] = $title;
		$replace[] = $isMember ? $_SESSION['login']['email'] : '';
		$replace[] = implode(',', $emails);
		$replace[] = implode(',', $ids);
		$replace[] = $register_antispamchar;
		$replace[] = $isAdmin ? $_SESSION[$register_antispamchar] : '';
		$content = gcms::pregReplace($patt, $replace, gcms::loadtemplate('member', 'member', 'sendmail'));
		// เลือกเมนู
		$menu = 'sendmail';
	}
} else {
	$title = $lng['LNG_NOT_LOGIN'];
	$content = '<div class=error>'.$title.'</div>';
}
