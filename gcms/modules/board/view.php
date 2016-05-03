<?php
// modules/board/view.php
if (defined('MAIN_INIT')) {
	// ค่า ที่ส่งมา
	$id = gcms::getVars('REQUEST,REQUEST', 'id,wbid', 0);
	$page = gcms::getVars($_REQUEST, 'page', 0);
	$cat = gcms::getVars($_REQUEST, 'cat', 0);
	$search = preg_replace('/[+\s]+/u', ' ', gcms::getVars($_REQUEST, 'q', ''));
	// query ข้อมูล
	$sql = "SELECT I.*,U.`status`,U.`id` AS `member_id`,M.`module`";
	$sql .= ",CASE WHEN ISNULL(C.`config`) THEN M.`config` ELSE CONCAT(M.`config`,'\n',C.`config`) END AS `config`";
	$sql .= ",C.`topic` AS `category`,C.`detail` AS `cat_tooltip`";
	$sql .= ",(CASE WHEN ISNULL(U.`id`) THEN I.`email` WHEN U.`displayname`='' THEN U.`email` ELSE U.`displayname` END) AS `displayname`";
	$sql .= " FROM `".DB_BOARD_Q."` AS I";
	$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`id`=I.`module_id` AND M.`owner`='board'";
	$sql .= " LEFT JOIN `".DB_USER."` AS U ON U.`id`=I.`member_id`";
	$sql .= " LEFT JOIN `".DB_CATEGORY."` AS C ON C.`category_id`=I.`category_id` AND C.`module_id`=I.`module_id`";
	$sql .= " WHERE I.`id`='$id' LIMIT 1";
	if (isset($_REQUEST['visited'])) {
		// มาจากการ post ไม่ต้องโหลดจากแคช
		$index = $db->customQuery($sql);
		$index = sizeof($index) == 0 ? false : $index[0];
	} else {
		$index = $cache->get($sql);
		if (!$index) {
			$index = $db->customQuery($sql);
			$index = sizeof($index) == 0 ? false : $index[0];
		}
	}
	if (!$index) {
		$title = $lng['PAGE_NOT_FOUND'];
		$content = '<div class=error>'.$title.'</div>';
	} else {
		// login
		$login = gcms::getVars($_SESSION, 'login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''));
		// config
		gcms::r2config($index['config'], $index);
		// แสดงความคิดเห็นได้
		$canReply = in_array($login['status'], explode(',', $index['can_reply']));
		// สถานะสมาชิกที่สามารถเปิดดูกระทู้ได้
		$canView = in_array($login['status'], explode(',', $index['can_view']));
		if ($canView || $index['viewing'] == 1) {
			// ผู้ดูแล
			$moderator = $isMember && gcms::canConfig($index, 'moderator');
			// สามารถลบได้ (mod=ลบ,สมาชิก=แจ้งลบ)
			$canDelete = $moderator || ($isMember && defined('DB_PM'));
			// อัปเดทการเปิดดู
			if (!isset($_REQUEST['visited'])) {
				$index['visited'] ++;
				$db->edit(DB_BOARD_Q, $index['id'], array('visited' => $index['visited']));
			}
			// บันทึก cache หลังจากอัปเดทการเปิดดูแล้ว
			$cache->save($sql, $index);
			// breadcrumbs
			$breadcrumb = gcms::loadtemplate($index['module'], '', 'breadcrumb');
			$breadcrumbs = array();
			// หน้าหลัก
			$breadcrumbs['HOME'] = gcms::breadcrumb('icon-home', $canonical, $install_modules[$module_list[0]]['menu_tooltip'], $install_modules[$module_list[0]]['menu_text'], $breadcrumb);
			// โมดูล
			if ($index['module'] != $module_list[0]) {
				if (isset($install_modules[$index['module']]['menu_text'])) {
					$m = $install_modules[$index['module']]['menu_text'];
					$t = $install_modules[$index['module']]['menu_tooltip'];
				} else {
					$m = ucwords($index['module']);
					$t = $m;
				}
				$canonical = gcms::getURL($index['module']);
				$breadcrumbs['MODULE'] = gcms::breadcrumb('', $canonical, $t, $m, $breadcrumb);
			}
			// category
			if ($index['category_id'] > 0 && $index['category'] != '') {
				$breadcrumbs['CATEGORY'] = gcms::breadcrumb('', gcms::getURL($index['module'], '', $index['category_id']), gcms::ser2Str($index, 'cat_tooltip'), gcms::ser2Str($index, 'category'), $breadcrumb);
			}
			// dir ของรูปภาพอัปโหลด
			$imagedir = DATA_PATH.'board/';
			$imageurl = DATA_URL.'board/';
			// ความคิดเห็น
			$comments = array();
			$patt = array('/(edit-{QID}-{RID}-{NO}-{MODULE})/', '/(delete-{QID}-{RID}-{NO}-{MODULE})/',
				'/{DETAIL}/', '/{UID}/', '/{DISPLAYNAME}/', '/{STATUS}/', '/{DATE}/', '/{DATEISO}/', '/{IP}/', '/{NO}/', '/{RID}/');
			$skin = gcms::loadtemplate($index['module'], 'board', 'commentitem');
			$sql = "SELECT C.*,U.`status`";
			$sql .= ",(CASE WHEN ISNULL(U.`id`) THEN C.`email` WHEN U.`displayname`='' THEN U.`email` ELSE U.`displayname` END) AS `displayname`";
			$sql .= " FROM `".DB_BOARD_R."` AS C";
			$sql .= " LEFT JOIN `".DB_USER."` AS U ON U.`id`=C.`member_id`";
			$sql .= " WHERE C.`index_id`='$index[id]' AND C.`module_id`='$index[module_id]'";
			$sql .= " ORDER BY C.`id` ASC";
			if (isset($_REQUEST['visited'])) {
				$datas = $db->customQuery($sql);
				$cache->save($sql, $datas);
			} else {
				$datas = $cache->get($sql);
				if (!$datas) {
					$datas = $db->customQuery($sql);
					$cache->save($sql, $datas);
				}
			}
			foreach ($datas AS $i => $item) {
				// moderator และ เจ้าของ สามารถแก้ไขความคิดเห็นได้
				$canEdit = $moderator || ($isMember && $login['id'] == $item['member_id']);
				$picture = $item['picture'] != '' && is_file($imagedir.$item['picture']) ? '<figure class=center><img src="'.$imageurl.$item['picture'].'" alt="'.$index['topic'].'"></figure>' : '';
				$replace = array();
				$replace[] = $canEdit ? '\\1' : 'hidden';
				$replace[] = $canDelete ? '\\1' : 'hidden';
				$replace[] = $picture.gcms::HighlightSearch(gcms::showDetail($item['detail'], $canView, true, true), $search);
				$replace[] = (int)$item['member_id'];
				$replace[] = $item['displayname'];
				$replace[] = $item['status'];
				$replace[] = gcms::mktime2date($item['last_update']);
				$replace[] = date(DATE_ISO8601, $item['last_update']);
				$replace[] = gcms::showip($item['ip']);
				$replace[] = $i + 1;
				$replace[] = $item['id'];
				$comments[] = preg_replace($patt, $replace, $skin);
			}
			// url ของหน้านี้
			$canonical = gcms::getURL($index['module'], '', $index['category_id'], 0, "wbid=$index[id]");
			// แก้ไขบอร์ด (mod หรือ ตัวเอง)
			$canEdit = $moderator || ($isMember && $login['id'] == $index['member_id']);
			// antispam
			$register_antispamchar = gcms::rndname(32);
			$_SESSION[$register_antispamchar] = gcms::rndname(4);
			// แทนที่ลงใน template ของโมดูล
			$patt = array('/{BREADCRUMS}/', '/{COMMENTLIST}/', '/(edit-{QID}-0-0-{MODULE})/', '/(delete-{QID}-0-0-{MODULE})/',
				'/(quote-{QID}-([0-9]+)-([0-9]+)-{MODULE})/', '/(pin-{QID}-0-0-{MODULE})/', '/(lock-{QID}-0-0-{MODULE})/', '/{URL}/', '/{TOPIC}/',
				'/{PIN}/', '/{LOCK}/', '/{PIN_TITLE}/', '/{LOCK_TITLE}/', '/{DETAIL}/', '/{UID}/', '/{DISPLAYNAME}/', '/{STATUS}/',
				'/{DATE}/', '/{DATEISO}/', '/{VISITED}/', '/{COMMENTS}/', '/{REPLYFORM}/', '/<MEMBER>(.*)<\/MEMBER>/s',
				'/<UPLOAD>(.*)<\/UPLOAD>/s', '/{LOGIN_PASSWORD}/', '/{LOGIN_EMAIL}/', '/{ANTISPAM}/', '/{ANTISPAMVAL}/',
				'/{QID}/', '/{DELETE}/');
			if ($index['picture'] != '' && is_file($imagedir.$index['picture'])) {
				$image_src = $imageurl.$index['picture'];
				$picture = '<figure class=center><img src="'.$image_src.'" alt="'.$index['topic'].'"></figure>';
			} else {
				$picture = '';
				$image_src = WEB_URL."/$index[default_icon]";
			}
			// รายละเอียดเนื้อหา
			$detail = gcms::showDetail($index['detail'], $canView, true, true);
			$replace = array();
			$replace[] = implode("\n", $breadcrumbs);
			$replace[] = implode("\n", $comments);
			$replace[] = $canEdit ? '\\1' : 'hidden';
			$replace[] = $canDelete ? '\\1' : 'hidden';
			$replace[] = !$canReply || $index['locked'] == 1 ? 'hidden' : '\\1';
			$replace[] = $moderator ? '\\1' : 'hidden';
			$replace[] = $moderator ? '\\1' : 'hidden';
			$replace[] = $canonical;
			$replace[] = $index['topic'];
			$replace[] = $index['pin'] == 0 ? 'un' : '';
			$replace[] = $index['locked'] == 0 ? 'un' : '';
			$replace[] = $index['pin'] == 0 ? $lng['LNG_PIN'] : $lng['LNG_UNPIN'];
			$replace[] = $index['locked'] == 0 ? $lng['LNG_LOCK'] : $lng['LNG_UNLOCK'];
			$replace[] = $picture.gcms::HighlightSearch($detail, $search);
			$replace[] = (int)$index['member_id'];
			$replace[] = $index['displayname'];
			$replace[] = $index['status'];
			$replace[] = gcms::mktime2date($index['create_date']);
			$replace[] = date(DATE_ISO8601, $index['create_date']);
			$replace[] = number_format($index['visited']);
			$replace[] = number_format($index['comments']);
			$replace[] = !$canReply || $index['locked'] == 1 ? '' : gcms::loadtemplate($index['module'], 'board', 'reply');
			$replace[] = $isMember ? '' : '$1';
			$replace[] = $index['img_upload_type'] == '' ? '' : '$1';
			$replace[] = $login['password'];
			$replace[] = $login['email'];
			$replace[] = $register_antispamchar;
			$replace[] = $isAdmin ? $_SESSION[$register_antispamchar] : '';
			$replace[] = $index['id'];
			$replace[] = $moderator ? '{LNG_DELETE}' : '{LNG_SEND_DELETE}';
			$content = gcms::pregReplace($patt, $replace, gcms::loadtemplate($index['module'], 'board', 'view'));
			// ตัวแปรหลังจากแสดงผลแล้ว
			$custom_patt['/{MODULE}/'] = $index['module'];
			$custom_patt['/{MODULEID}/'] = $index['module_id'];
			$custom_patt['/{SIZE}/'] = $index['img_upload_size'];
			$custom_patt['/{TYPE}/'] = $index['img_upload_type'];
			// title,keywords,description
			$title = $index['topic'];
			$keywords = $index['topic'];
			$description = gcms::cutstring(strip_tags(preg_replace('/[\r\n\{\}]+/', ' ', $detail)), 149);
		} else {
			$title = $lng['LNG_NOT_LOGIN'];
			$content = '<div class=error>'.$title.'</div>';
		}
		// เลือกเมนู
		$menu = empty($install_modules[$index['module']]['alias']) ? $index['module'] : $install_modules[$index['module']]['alias'];
	}
}
