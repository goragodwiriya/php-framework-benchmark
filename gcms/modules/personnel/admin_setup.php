<?php
// modules/personnel/admin_setup.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'personnel_can_write')) {
	unset($url_query['id']);
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='personnel' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// sql
		$sqls = array();
		$sqls[] = "`module_id`='$index[id]'";
		// ข้อความค้นหา
		$search = $db->sql_trim_str($_GET, 'search');
		if ($search != '') {
			$sqls[] = "(`name` LIKE '%$search%' OR `email` LIKE '%$search%')";
			$url_query['search'] = urlencode($search);
		}
		// สถานะของสมาชิกที่ต้องการ
		$cat = gcms::getVars($_GET, 'cat', 0);
		// ค้นหาสมาชิกจากสถานะ
		if ($cat > 0) {
			$sqls[] = "`category_id`='$cat'";
			$url_query['cat'] = $cat;
		}
		$where = sizeof($sqls) > 0 ? ' WHERE '.implode(' AND ', $sqls) : '';
		// จำนวนสมาชิกทั้งหมด
		$sql = "SELECT COUNT(*) AS `count` FROM `".DB_PERSONNEL."` $where";
		$count = $db->customQuery($sql);
		// รายการต่อหน้า
		$list_per_page = gcms::getVars('GET,COOKIE', 'count,personnel_listperpage', 30);
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
		setCookie('personnel_cat', $cat, time() + 3600 * 24 * 365);
		setCookie('personnel_listperpage', $list_per_page, time() + 3600 * 24 * 365);
		// title
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_PERSONNEL]";
		$a = array();
		$a[] = '<span class=icon-modules>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=personnel-config}">{LNG_PERSONNEL}</a>';
		$a[] = '{LNG_PERSONNEL}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-customer>'.$title.'</h1></header>';
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
		// หมวดหมู่
		$categories = array();
		$categories[0] = '{LNG_ALL} {LNG_PERSONNEL_CATEGORY}';
		$sql = "SELECT `category_id`,`topic` FROM `".DB_CATEGORY."` WHERE `module_id`='$index[id]' ORDER BY `category_id`";
		foreach ($db->customQuery($sql) AS $item) {
			$categories[$item['category_id']] = gcms::ser2Str($item, 'topic');
		}
		$content[] = '<fieldset>';
		$content[] = '<label>{LNG_PERSONNEL_CATEGORY} <select name=cat>';
		foreach ($categories AS $i => $item) {
			$sel = $i == $cat ? ' selected' : '';
			$content[] = '<option value='.$i.$sel.'>'.$item.'</option>';
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
		$content[] = '<input type=hidden name=module value=personnel-setup>';
		$content[] = '<input type=hidden name=page value=1>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		// ตารางข้อมูล
		$content[] = '<table id=personnel class="tbl_list fullwidth">';
		$content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_MEMBER']).'</caption>';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th id=c0 scope=col colspan=2>{LNG_FNAME} {LNG_LNAME}</th>';
		$content[] = '<th id=c1 scope=col class=check-column><a class="checkall icon-uncheck"></a></th>';
		$content[] = '<th id=c2 scope=col>{LNG_PERSONNEL_CATEGORY}</th>';
		$content[] = '<th id=c3 scope=col class="center tablet">{LNG_SORT}</th>';
		$content[] = '<th id=c4 scope=col class=mobile>{LNG_EMAIL}</th>';
		$content[] = '<th id=c5 scope=col class=mobile>{LNG_POSITION}</th>';
		$content[] = '<th id=c6 scope=col class="center mobile">{LNG_PHONE}</th>';
		$content[] = '<th id=c7 scope=col>&nbsp;</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		// เรียกสมาชิกทั้งหมด
		$sql = "SELECT * FROM `".DB_PERSONNEL."`";
		$sql .= " $where ORDER BY `order` ASC,`id` ASC";
		$sql .= " LIMIT $start, $list_per_page";
		foreach ($db->customQuery($sql) AS $item) {
			$id = $item['id'];
			$tr = '<tr id=L_'.$id.'>';
			$tr .= '<th headers=c0 id=r'.$id.' scope=row class=topic>'.$item['name'].'</th>';
			$icon = is_file(DATA_PATH."personnel/$item[picture]") ? DATA_URL."personnel/$item[picture]" : WEB_URL.'/modules/personnel/img/noicon.jpg';
			$tr .= '<td headers="r'.$id.' c0" class=thumb><img src='.$icon.' alt=personnel></td>';
			$tr .= '<td headers="r'.$id.'" class=check-column><a id=check_'.$id.' class=icon-uncheck></a></td>';
			$tr .= '<td headers="r'.$id.' c2"><a href="{URLQUERY?cat='.$item['category_id'].'}">'.$categories[$item['category_id']].'</a></td>';
			$tr .= '<td headers="r'.$id.' c3" class="center tablet"><label><input type=text size=5 id=order_'.$id.' value="'.$item['order'].'"></label></td>';
			$tr .= '<td headers="r'.$id.' c4" class="email mobile"><a href="{URLQUERY?module=sendmail&src=personnel-setup&spage='.$page.'&mail='.$item['email'].'}" title="{LNG_EMAIL_SEND} {LNG_TO} '.$item['email'].'">'.gcms::cutstring($item['email'], 20).'</a></td>';
			$tr .= '<td headers="r'.$id.' c5" class=mobile>'.$item['position'].'</td>';
			$tr .= '<td headers="r'.$id.' c6" class="center mobile">'.$item['phone'].'</td>';
			$tr .= '<td headers="r'.$id.' c7" class=menu><a title="{LNG_EDIT}" href="{URLQUERY?module=personnel-write&src=personnel-setup&spage='.$page.'&id='.$id.'}" class=icon-edit></a></td>';
			$tr .= '</tr>';
			$content[] = $tr;
		}
		$content[] = '</tbody>';
		$content[] = '<tfoot>';
		$content[] = '<tr>';
		$content[] = '<td headers=c0 colspan=2>&nbsp;</td>';
		$content[] = '<td headers=c1 class=check-column><a class="checkall icon-uncheck"></a></td>';
		$content[] = '<td headers=c2 colspan=6>&nbsp;</td>';
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
		$content[] = '<a class="button add" href="{URLQUERY?module=personnel-write}"><span class=icon-plus>{LNG_ADD_NEW} {LNG_PERSONNEL}</span></a>';
		$content[] = '</fieldset>';
		$content[] = '</div>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = "inintCheck('personnel');";
		$content[] = "inintTR('personnel', /L_[0-9]+/);";
		$content[] = 'callAction("btn_action", function(){return $E("sel_action").value}, "personnel", "'.WEB_URL.'/modules/personnel/admin_action.php");';
		$content[] = "inintPersonnel('personnel');";
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'personnel-setup';
		$url_query['page'] = $page;
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
