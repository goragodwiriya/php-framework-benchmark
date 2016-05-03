<?php
// modules/video/admin_setup.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'video_can_write')) {
	unset($url_query['id']);
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='video' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// default query
		$q = array();
		$q[] = "`module_id`='$index[id]'";
		// ข้อความค้นหา
		$search = preg_replace('/[\+\s]+/u', ' ', $db->sql_trim_str($_GET, 'search', ''));
		$searchs = array();
		foreach (explode(' ', $search) AS $item) {
			// แยกข้อความค้นหาออกเป็นคำๆ ค้นหาข้อความที่มีความยาวมากกว่า 2 ตัวอักษร
			if (mb_strlen($item) > 2) {
				$item = addslashes($item);
				$searchs[] = "`topic` LIKE '%$item%' OR `detail` LIKE '%$item%'";
			}
		}
		// ค้นหา สำหรับส่งไปหน้าถัดไป
		if (sizeof($searchs) > 0) {
			$q[] = '('.implode(' OR ', $searchs).')';
			$url_query['search'] = urlencode($search);
		}
		$where = " WHERE ".implode(' AND ', $q);
		// จำนวนรายการทั้งหมด
		$sql = "SELECT COUNT(*) AS `count` FROM `".DB_VIDEO."` $where";
		$count = $db->customQuery($sql);
		// รายการต่อหน้า
		$list_per_page = gcms::getVars('GET,COOKIE', 'count,video_listperpage', 30);
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
		setCookie('video_listperpage', $list_per_page, time() + 3600 * 24 * 365);
		// title
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_VIDEO]";
		$a = array();
		$a[] = '<span class=icon-video>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=video-config}">{LNG_VIDEO}</a>';
		$a[] = '{LNG_VIDEO_LIST}';
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
		$content[] = '<input name=module type=hidden value=video-setup>';
		$content[] = '<input type=hidden name=page value=1>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		// ตารางข้อมูล
		$content[] = '<table id=list class="tbl_list fullwidth">';
		$content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_ITEMS']).'</caption>';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th id=c0 scope=col class=center>{LNG_ID}</th>';
		$content[] = '<th id=c1 scope=col class=mobile>{LNG_THUMBNAIL}</th>';
		$content[] = '<th id=c2 scope=col>{LNG_TOPIC}</th>';
		$content[] = '<th id=c3 scope=col class=check-column><a class="checkall icon-uncheck"></a></th>';
		$content[] = '<th id=c4 scope=col>{LNG_VIDEO_ID}</th>';
		$content[] = '<th id=c5 scope=col class="center mobile">{LNG_LAST_UPDATE}</th>';
		$content[] = '<th id=c6 scope=col class="center tablet">{LNG_VIEWS}</th>';
		$content[] = '<th id=c7 scope=col>&nbsp;</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		// รายการทั้งหมด
		$sql = "SELECT * FROM `".DB_VIDEO."` $where ORDER BY `id` DESC LIMIT $start,$list_per_page";
		foreach ($db->customQuery($sql) AS $item) {
			$id = $item['id'];
			$thumb = is_file(DATA_PATH."video/$item[youtube].jpg") ? DATA_URL."video/$item[youtube].jpg" : WEB_URL.'/modules/video/img/nopicture.jpg';
			$tr = '<tr id=M_'.$id.'>';
			$tr .= '<td headers="r'.$id.' c0" class=no>'.$id.'</td>';
			$tr .= '<td headers="r'.$id.' c1" class=mobile><img src="'.$thumb.'" alt=thumb width=32></td>';
			$tr .= '<th headers=c2 id=r'.$id.' scope=row>'.$item['topic'].'</th>';
			$tr .= '<td headers="r'.$id.' c3" class=check-column><a id=check_'.$id.' class=icon-uncheck></a></td>';
			$tr .= '<td headers="r'.$id.' c4"><a href="http://www.youtube.com/watch?v='.$item['youtube'].'" target=_blank>'.$item['youtube'].'</a></td>';
			$tr .= '<td headers="r'.$id.' c5" class="date mobile">'.gcms::mktime2date($item['last_update']).'</td>';
			$tr .= '<td headers="r'.$id.' c6" class="visited tablet">'.$item['views'].'</td>';
			$tr .= '<td headers="r'.$id.' c7" class=menu><a href="{URLQUERY?module=video-write&src=video-setup&spage='.$page.'&id='.$id.'}" title="{LNG_EDIT}" class=icon-edit></a></td>';
			$tr .= '</tr>';
			$content[] = $tr;
		}
		$content[] = '</tbody>';
		$content[] = '<tfoot>';
		$content[] = '<tr>';
		$content[] = '<td headers=c0>&nbsp;</td>';
		$content[] = '<td headers=c1 class=mobile></td>';
		$content[] = '<td headers=c2></td>';
		$content[] = '<td headers=c3 class=check-column><a class="checkall icon-uncheck"></a></td>';
		$content[] = '<td headers=c4 colspan=4></td>';
		$content[] = '</tr>';
		$content[] = '</tfoot>';
		$content[] = '</table>';
		// แบ่งหน้า
		$url = '<a href="{URLQUERY?page=%d}" title="{LNG_DISPLAY_PAGE} %d">%d</a>';
		$content[] = '<div class=splitpage>'.gcms::pagination($totalpage, $page, $url).'</div>';
		$content[] = '<div class=table_nav>';
		// sel action
		$content[] = '<fieldset>';
		$content[] = '<select id=sel_action><option value=delete>{LNG_DELETE}</option></select>';
		$content[] = '<label accesskey=e for=sel_action class="button go" id=btn_action><span>{LNG_SELECT_ACTION}</span></label>';
		$content[] = '</fieldset>';
		// add
		$content[] = '<fieldset>';
		$content[] = '<a class="button add" href="{URLQUERY?module=video-write&src=video-setup}"><span class=icon-plus>{LNG_ADD_NEW} {LNG_VIDEO}</span></a>';
		$content[] = '</fieldset>';
		$content[] = '</div>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = "inintCheck('list');";
		$content[] = "inintTR('list', /M_[0-9]+/);";
		$content[] = 'callAction("btn_action", function(){return $E("sel_action").value}, "list", "'.WEB_URL.'/modules/video/admin_action.php");';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = "video-setup";
		$url_query['page'] = $page;
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
