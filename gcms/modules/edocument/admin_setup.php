<?php
// modules/edocument/admin_setup.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'edocument_moderator')) {
	unset($url_query['id']);
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id`,`module` FROM `".DB_MODULES."` WHERE `owner`='edocument' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// sql
		$q = array("D.`module_id`='$index[id]'");
		// ค้นหาจาก นามสกุลของไฟล์, เลขที่, ชื่อเอกสาร
		$search = $db->sql_trim_str($_GET, 'search');
		if ($search != '') {
			$q[] = "(`ext`='$search' OR `document_no` LIKE '%$search%' OR `topic` LIKE '%$search%')";
			$url_query['search'] = urlencode($search);
		}
		$where = 'WHERE '.implode(' AND ', $q);
		// ทั้งหมด
		$sql = "SELECT COUNT(*) AS `count` FROM `".DB_EDOCUMENT."` AS D $where";
		$count = $db->customQuery($sql);
		// รายการต่อหน้า
		$list_per_page = gcms::getVars('GET,COOKIE', 'count,edocument_listperpage', 30);
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
		// save cookie
		setCookie('edocument_listperpage', $list_per_page, time() + 3600 * 24 * 365);
		// title
		$m = ucwords($index['module']);
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_EDOCUMENT_LIST]";
		$a = array();
		$a[] = '<span class=icon-edocument>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=edocument-config}">'.$m.'</a>';
		$a[] = '{LNG_EDOCUMENT_LIST}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-list>'.$title.'</h1></header>';
		// form
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
		$content[] = '<input type=submit class="button go" value="{LNG_GO}">';
		$content[] = '</fieldset>';
		// search
		$content[] = '<fieldset class=search>';
		$content[] = '<label accesskey=f><input type=text name=search value="'.$search.'" placeholder="{LNG_SEARCH_TITLE}" title="{LNG_SEARCH_TITLE}"></label>';
		$content[] = '<input type=submit value="&#xE607;" title="{LNG_SEARCH}">';
		$content[] = '<input type=hidden name=module value=edocument-setup>';
		$content[] = '<input type=hidden name=page value=1>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		// ตารางข้อมูล
		$content[] = '<table id=tbl_edocument class="tbl_list fullwidth">';
		$content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_ITEMS']).'</caption>';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th id=c0 scope=col colspan=2>{LNG_DOWNLOAD_NAME}</th>';
		$content[] = '<th id=c1 scope=col class=check-column><a class="checkall icon-uncheck"></a></th>';
		$content[] = '<th id=c2 scope=col class=tablet>{LNG_DESCRIPTION}</th>';
		$content[] = '<th id=c3 scope=col class=mobile>{LNG_SENDER}</th>';
		$content[] = '<th id=c4 scope=col class="center tablet">{LNG_SIZE_OF} {LNG_FILE}</th>';
		$content[] = '<th id=c5 scope=col class="center mobile">{LNG_DOWNLOAD_FILE_TIME}</th>';
		$content[] = '<th id=c6 scope=col class="center mobile">{LNG_DOWNLOAD}</th>';
		$content[] = '<th id=c7 scope=col>&nbsp;</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		$sql = "SELECT D.*,U.`fname`,U.`lname`,U.`email`,U.`status`";
		$sql .= ",(SELECT SUM(`downloads`) FROM `".DB_EDOCUMENT_DOWNLOAD."` WHERE `document_id`=D.`id`) AS `downloads`";
		$sql .= " FROM `".DB_EDOCUMENT."` AS D";
		$sql .= " LEFT JOIN `".DB_USER."` AS U ON U.`id`=D.`sender_id`";
		$sql .= " $where ORDER BY D.`last_update` DESC LIMIT $start,$list_per_page";
		foreach ($db->customQuery($sql) AS $item) {
			$id = $item['id'];
			$file_exists = file_exists(iconv('UTF-8', 'TIS-620', DATA_PATH."edocument/$item[file]"));
			$tr = '<tr id="M_'.$id.'">';
			$tr .= '<th headers=c0 id=r'.$id.' scope=row class=topic><span class=cuttext>'.$item['topic'].'.'.$item['ext'].'</span></th>';
			$icon = "skin/ext/$item[ext].png";
			$icon = WEB_URL.(is_file(ROOT_PATH.$icon) ? "/$icon" : "/skin/ext/file.png");
			$tr .= '<td headers="r'.$id.' c0" class=menu>';
			$tr .= $file_exists ? '<a href="'.WEB_URL.'/modules/edocument/admin_download.php?id='.$id.'" target=_blank title="{LNG_CLICK_TO} {LNG_DOWNLOAD}"><img src="'.$icon.'" alt='.$item['ext'].'></a>' : '';
			$tr .= '</td>';
			$tr .= '<td headers="r'.$id.' c1" class=check-column><a id=check_'.$id.' class=icon-uncheck href=""></a></td>';
			$tr .= '<td headers="r'.$id.' c2" title="'.$item['detail'].'" class=tablet>'.$item['detail'].'</td>';
			$sender = trim("$item[fname] $item[lname]");
			$sender = $sender == '' ? $item['email'] : $sender;
			$tr .= '<td headers="r'.$id.' c3" class=mobile><a href="index.php?id='.$item['sender_id'].'&module=editprofile&src=edocument-setup" class="cuttext status'.$item['status'].'">'.$sender.'</a></td>';
			$tr .= '<td headers="r'.$id.' c4" class="'.($file_exists ? 'size' : 'notfound').' tablet center">'.gcms::formatFileSize($item['size']).'</td>';
			$tr .= '<td headers="r'.$id.' c5" class="date mobile">'.gcms::mktime2date($item['last_update']).'</td>';
			$tr .= '<td headers="r'.$id.' c6" class="visited mobile"><a class=count href="index.php?id='.$id.'&module=edocument-report&src=edocument-setup" title="{LNG_EDOCUMENT_DOWNLOAD_DETAILS}">'.$item['downloads'].'</a></td>';
			$tr .= '<td headers="r'.$id.' c7" class=menu><a href="{URLQUERY?module=edocument-write&id='.$id.'}" title="{LNG_EDIT}" class=icon-edit></a></td>';
			$tr .= '</tr>';
			$content[] = $tr;
		}
		$content[] = '</tbody>';
		$content[] = '<tfoot>';
		$content[] = '<tr>';
		$content[] = '<td headers=c0 colspan=2>&nbsp;</td>';
		$content[] = '<td headers=c1 class=check-column><a class="checkall icon-uncheck"></a></td>';
		$content[] = '<td headers=c2 colspan=5></td>';
		$content[] = '</tr>';
		$content[] = '</tfoot>';
		$content[] = '</table>';
		// แบ่งหน้า
		$maxlink = 9;
		$url = '<a href="{URLQUERY?page=%d}" title="{LNG_DISPLAY_PAGE} %d">%d</a>';
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
		$content[] = '<p class=splitpage>'.$splitpage.'</p>';
		$content[] = '<div class=table_nav>';
		// sel action
		$content[] = '<fieldset>';
		$content[] = '<select id=sel_action><option value=delete>{LNG_DELETE}</option></select>';
		$content[] = '<label accesskey=e for=sel_action class="button go" id=btn_action><span>{LNG_SELECT_ACTION}</span></label>';
		$content[] = '</fieldset>';
		// add
		$content[] = '<fieldset>';
		$content[] = '<a class="button add" href="{URLQUERY?module=edocument-write&src=edocument-setup}"><span class=icon-plus>{LNG_ADD_NEW} {LNG_EDOCUMENT_ITEM}</span></a>';
		$content[] = '</fieldset>';
		$content[] = '</div>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = 'inintCheck("tbl_edocument");';
		$content[] = 'inintTR("tbl_edocument", /M_[0-9]+/);';
		$content[] = 'callAction("btn_action", function(){return $E("sel_action").value}, "tbl_edocument", "{WEBURL}/modules/edocument/admin_action.php");';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'edocument-setup';
		$url_query['page'] = $page;
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
