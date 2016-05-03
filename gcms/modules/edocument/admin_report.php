<?php
// modules/edocument/admin_report.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'edocument_moderator')) {
	// ตรวจสอบโมดูล และรายการที่เรียก
	$sql = "SELECT D.*,M.`module`";
	$sql .= " FROM `".DB_MODULES."` AS M";
	$sql .= " INNER JOIN `".DB_EDOCUMENT."` AS D ON D.`id`='".(int)$_GET['id']."' AND D.`module_id`=M.`id`";
	$sql .= " WHERE M.`owner`='edocument' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// sql
		$sqls = array();
		$sqls[] = "D.`document_id`='$index[id]'";
		$sqls[] = "D.`module_id`='$index[module_id]'";
		$where = ' WHERE '.implode(' AND ', $sqls);
		// จำนวน
		$sql = "SELECT COUNT(*) AS `count` FROM `".DB_EDOCUMENT_DOWNLOAD."` AS D $where";
		$count = $db->customQuery($sql);
		// รายการต่อหน้า
		$list_per_page = gcms::getVars('GET,COOKIE', 'count,edocument_report_listperpage', 30);
		$list_per_page = max(10, $list_per_page);
		// หน้าที่เลือก
		$page = max(1, gcms::getVars($_GET, 'page', 1));
		// ตรวจสอบหน้าที่เลือกสูงสุด
		$totalpage = round($count[0]['count'] / $list_per_page);
		$totalpage += ($totalpage * $list_per_page < $count[0]['count']) ? 1 : 0;
		$page = max(1, $page > $totalpage ? $totalpage : $page);
		$start = $list_per_page * ($page - 1);
		// คำนวณรายการที่แสดง
		$s = $start < 0 ? 0 : $start + 1;
		$e = min($count[0]['count'], $s + $list_per_page - 1);
		$patt2 = array('/{SEARCH}/', '/{COUNT}/', '/{PAGE}/', '/{TOTALPAGE}/', '/{START}/', '/{END}/');
		$replace2 = array($search, $count[0]['count'], $page, $totalpage, $s, $e);
		// คำสั่งที่ทำงานล่าสุด
		$action = trim(gcms::getVars($_GET, 'action', ''));
		// save ฟิลเตอร์ลง cookie
		setCookie('edocument_report_listperpage', $list_per_page, time() + 3600 * 24 * 365);
		// title
		$title = "$lng[LNG_EDOCUMENT_DOWNLOAD_DETAILS] $index[topic].$index[ext]";
		$a = array();
		$a[] = '<span class=icon-edocument>{LNG_MODULES}</span>';
		$a[] = '<a href="index.php?module=edocument-config">{LNG_EDOCUMENT}</a>';
		$a[] = "<a href='index.php?module=edocument-setup'>$index[topic].$index[ext]</a>";
		$a[] = '{LNG_EDOCUMENT_DOWNLOAD_DETAILS}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-summary>'.$title.'</h1></header>';
		// filter
		$content[] = '<form class=table_nav method=get action=index.php>';
		// รายการต่อหน้า
		$content[] = '<fieldset>';
		$content[] = '<label>{LNG_LIST_PER_PAGE} <select name=count>';
		foreach (array(10, 20, 30, 40, 50, 100) AS $item) {
			$sel = $item == $list_per_page ? ' selected' : '';
			$content[] = '<option value='.$item.$sel.'>'.$item.' {LNG_ITEMS}</option>';
		}
		$content[] = '</select></label>';
		$content[] = '</fieldset>';
		// submit
		$content[] = '<fieldset>';
		$content[] = '<label><input type=submit class="button go" value="{LNG_GO}"></label>';
		$content[] = '<input type=hidden name=module value=edocument-report>';
		$content[] = '<input type=hidden name=id value='.$index['id'].'>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		// ตารางข้อมูล
		$content[] = '<table id=tbl_edocument class="tbl_list fullwidth">';
		$content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_ITEMS']).'</caption>';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th id=c0 scope=col>{LNG_FNAME} {LNG_LNAME}</th>';
		$content[] = '<th id=c1 scope=col class="center mobile">{LNG_EDOCUMENT_STATUS}</th>';
		$content[] = '<th id=c2 scope=col class=center>{LNG_CREATED}</th>';
		$content[] = '<th id=c3 scope=col class="center mobile">{LNG_COUNT}</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		// guest
		$config['member_status'][$item['status']] = $lng['LNG_GUEST'];
		// query
		$sql = "SELECT D.*,U.`fname`,U.`lname`,U.`email`,U.`status` FROM `".DB_EDOCUMENT_DOWNLOAD."` AS D";
		$sql .= " LEFT JOIN `".DB_USER."` AS U ON U.`id`=D.`member_id`";
		$sql .= " $where ORDER BY D.`last_update` DESC LIMIT $start,$list_per_page";
		foreach ($db->customQuery($sql) AS $item) {
			$tr = '<tr id=M_'.$item['id'].'>';
			if ($item['member_id'] == 0) {
				$sender = '-';
			} else {
				$sender = trim("$item[fname] $item[lname]");
				$sender = $sender == '' ? $item['email'] : $sender;
			}
			$tr .= '<th headers=c0 id=r'.$id.' scope=row class=topic><a href="index.php?id='.$item['sender_id'].'&module=editprofile&src=edocument-setup" class="cuttext status'.$item['status'].'">'.$sender.'</a></th>';
			$tr .= '<td headers="r'.$id.' c1" class="center status'.$item['status'].' mobile">'.$config['member_status'][$item['status']].'</td>';
			$tr .= '<td headers="r'.$id.' c2" class=date>'.gcms::mktime2date($item['last_update']).'</td>';
			$tr .= '<td headers="r'.$id.' c3" class="visited mobile">'.$item['downloads'].'</td>';
			$tr .= '</tr>';
			$content[] = $tr;
		}
		$content[] = '</tbody>';
		$content[] = '</table>';
		// แบ่งหน้า
		$maxlink = 9;
		$url = '<a href="{URLQUERY?module=edocument-report&page=%d}" title="{LNG_DISPLAY_PAGE} %d">%d</a>';
		if ($totalpage > $maxlink) {
			$start = $page - floor($maxlink / 2);
			if ($start < 1) {
				$start = 1;
			} elseif ($start + $maxlink > $totalpage) {
				$start = $totalpage - $maxlink + 1;
			}
		} else {
			$start = 1;
		}
		$splitpage = ($start > 2) ? str_replace('%d', 1, $url) : '';
		for ($i = $start; $i <= $totalpage && $maxlink > 0; $i++) {
			$splitpage .= ($i == $page) ? '<strong title="{LNG_DISPLAY_PAGE} '.$i.'">'.$i.'</strong>' : str_replace('%d', $i, $url);
			$maxlink--;
		}
		$splitpage .= ($i < $totalpage) ? str_replace('%d', $totalpage, $url) : '';
		$splitpage = $splitpage == '' ? '<strong title="{LNG_DISPLAY_PAGE} '.$i.'">1</strong>' : $splitpage;
		$content[] = '<div class=splitpage>'.$splitpage.'</div>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = 'inintTR("tbl_edocument", /M_[0-9]+/);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'edocument-report';
		$url_query['page'] = $page;
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
