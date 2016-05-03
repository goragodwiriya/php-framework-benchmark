<?php
// modules/board/stories.php
if (defined('MAIN_INIT') && is_array($index)) {
	// query หลัก
	$where = $cat > 0 ? "Q.`category_id`='$cat' AND " : '';
	$where .= "Q.`module_id`='$index[id]'";
	// อ่านจำนวนกระทู้ทั้งหมด
	$sql = "SELECT COUNT(*) AS `count` FROM `".DB_BOARD_Q."` AS Q WHERE $where";
	// ตรวจสอบข้อมูลจาก cache
	$count = $cache->get($sql);
	if (!$count) {
		$count = $db->customQuery($sql);
		$count = $count[0];
		$cache->save($sql, $count);
	}
	// หน้าที่เรียก
	$index['list_per_page'] = max(1, $index['list_per_page']);
	$totalpage = round($count['count'] / $index['list_per_page']);
	$totalpage += ($totalpage * $index['list_per_page'] < $count['count']) ? 1 : 0;
	$page = $page > $totalpage ? $totalpage : $page;
	$page = $page < 1 ? 1 : $page;
	$start = $index['list_per_page'] * ($page - 1);
	// กำหนดเวลาสำหรับการแสดงเครื่องหมายกระทู้ใหม่
	$valid_date = $mmktime - $index['new_date'];
	// อ่านรายการลงใน $list
	$pins = array();
	$listitem = gcms::loadtemplate($index['module'], 'board', 'listitem');
	$patt = array('/{ID}/', '/{PICTURE}/', '/{URL}/', '/{TOPIC}/', '/{UID}/',
		'/{SENDER}/', '/{STATUS}/', '/{DATE}/', '/{DATEISO}/', '/{VISITED}/',
		'/{REPLY}/', '/{REPLYDATE}/', '/{REPLYER}/', '/{RID}/', '/{STATUS2}/', '/{ICON}/');
	// pin
	$sql1 = "SELECT Q.*,U1.`status`,U2.`status` AS `replyer_status`";
	$sql1 .= ",(CASE WHEN Q.`comment_date` > 0 THEN Q.`comment_date` ELSE Q.`last_update` END) AS `d`";
	$sql1 .= ",(CASE WHEN ISNULL(U1.`id`) THEN Q.`email` WHEN U1.`displayname`='' THEN U1.`email` ELSE U1.`displayname` END) AS `sender`";
	$sql1 .= ",(CASE WHEN ISNULL(U2.`id`) THEN Q.`commentator` WHEN U2.`displayname`='' THEN U2.`email` ELSE U2.`displayname` END) AS `commentator`";
	$sql = " $sql1 FROM `".DB_BOARD_Q."` AS Q";
	$sql .= " LEFT JOIN `".DB_USER."` AS U1 ON U1.`id`=Q.`member_id`";
	$sql .= " LEFT JOIN `".DB_USER."` AS U2 ON U2.`id`=Q.`commentator_id`";
	$sql .= " WHERE Q.`pin`='1' AND $where";
	$sql .= " ORDER BY Q.`id` DESC";
	boardList($sql, $pins);
	// แสดงรายการแบบแบ่งหน้า
	$sql = "$sql1 FROM `".DB_BOARD_Q."` AS Q";
	$sql .= " LEFT JOIN `".DB_USER."` AS U1 ON U1.`id`=Q.`member_id`";
	$sql .= " LEFT JOIN `".DB_USER."` AS U2 ON U2.`id`=Q.`commentator_id`";
	$sql .= " WHERE $where AND Q.`pin`='0'";
	$sql .= " ORDER BY `d` DESC";
	$sql .= " LIMIT $start,$index[list_per_page]";
	boardList($sql, $list);
	if (sizeof($list) > 0) {
		// แบ่งหน้า
		$url = '<a href="'.gcms::getURL($index['module'], '', $cat, 0, 'page=%d').'">%d</a>';
		$splitpage = gcms::pagination($totalpage, $page, $url);
	} else {
		$splitpage = '';
	}
	// รวมข้อมูล pin และ กระทู้ปกติ
	$list = array_merge($pins, $list);
	// canonical
	$canonical = gcms::getURL($index['module'], '', $cat, 0, "page=$page");
}

function boardList($sql, &$list)
{
	global $db, $cache, $patt, $listitem, $config, $cat, $valid_date, $index, $page;
	$datas = $cache->get($sql);
	if (!$datas) {
		$datas = $db->customQuery($sql);
		$cache->save($sql, $datas);
	}
	foreach ($datas AS $item) {
		$ctiime = $item['comment_date'] > 0 ? $item['comment_date'] : $item['last_update'];
		$replace = array();
		$replace[] = $item['id'];
		if ($item['pin'] > 0) {
			$replace[] = WEB_URL.'/'.SKIN.'board/img/pin.png';
		} elseif (is_file(DATA_PATH."board/thumb-$item[picture]")) {
			$replace[] = DATA_URL."board/thumb-$item[picture]";
		} elseif (is_file(DATA_PATH."board/$item[picture]")) {
			$replace[] = DATA_URL."board/$item[picture]";
		} else {
			$replace[] = WEB_URL."/$index[default_icon]";
		}
		$replace[] = gcms::getURL($index['module'], '', $cat, 0, "wbid=$item[id]");
		$replace[] = $item['topic'];
		$replace[] = (int)$item['member_id'];
		$replace[] = $item['sender'];
		$replace[] = $item['status'];
		$replace[] = gcms::mktime2date($item['create_date']);
		$replace[] = date(DATE_ISO8601, $item['create_date']);
		$replace[] = number_format($item['visited']);
		$replace[] = number_format($item['comments']);
		$replace[] = $item['comment_date'] == 0 ? '&nbsp;' : gcms::mktime2date($item['comment_date']);
		$replace[] = $item['comment_date'] == 0 ? '&nbsp;' : $item['commentator'];
		$replace[] = (int)$item['commentator_id'];
		$replace[] = $item['replyer_status'];
		$replace[] = $ctiime >= $valid_date ? ($item['comment_date'] > 0 ? ' update' : ' new') : '';
		$list[] = preg_replace($patt, $replace, $listitem);
	}
}
