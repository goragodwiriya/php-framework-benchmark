<?php
// modules/event/admin_setup.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'event_can_write')) {
	unset($url_query['id']);
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id`,`module` FROM `".DB_MODULES."` WHERE `owner`='event' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// sql
		$q = array("D.`module_id`='$index[id]'");
		// user
		$u = gcms::getVars($_GET, 'u', 0);
		if ($u > 0) {
			$q[] = "D.`member_id`='$u'";
		}
		// ค้นหา จาก topic, detail
		$search = $db->sql_trim_str($_GET, 'search');
		if ($search != '') {
			$q[] = "(D.`topic`LIKE'%$search%' OR D.`detail`LIKE'%$search%')";
			$url_query['search'] = urlencode($search);
		}
		$where = 'WHERE '.implode(' AND ', $q);
		// ทั้งหมด
		$sql = "SELECT COUNT(*) AS `count` FROM `".DB_EVENTCALENDAR."` AS D $where";
		$count = $db->customQuery($sql);
		// รายการต่อหน้า
		$list_per_page = gcms::getVars('GET,COOKIE', 'count,event_listperpage', 30);
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
		setCookie('event_listperpage', $list_per_page, time() + 3600 * 24 * 365);
		// title
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_ALL_ITEMS]";
		$a = array();
		$a[] = '<span class=icon-event>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=event-config}">{LNG_EVENT}</a>';
		$a[] = '{LNG_ALL_ITEMS}';
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
		$content[] = '<input type=hidden name=module value=event-setup>';
		$content[] = '<input type=hidden name=page value=1>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		// ตารางข้อมูล
		$content[] = '<table id=event class="tbl_list fullwidth">';
		$content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_ITEMS']).'</caption>';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th id=c0 scope=col>{LNG_TOPIC}</th>';
		$content[] = '<th id=c1 scope=col class=check-column><a class="checkall icon-uncheck"></a></th>';
		$content[] = '<th id=c2 scope=col class=center><span class=mobile>{LNG_EVENT_COLOR}</span></th>';
		$content[] = '<th id=c3 scope=col class="center mobile">{LNG_PUBLISHED}</th>';
		$content[] = '<th id=c4 scope=col class="center tablet">{LNG_WRITER}</th>';
		$content[] = '<th id=c5 scope=col class="center tablet">{LNG_CREATED}</th>';
		$content[] = '<th id=c6 scope=col>&nbsp;</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		if ($count[0]['count'] > 0) {
			// เรียกเรื่องทั้งหมด
			$sql = "SELECT D.*,U.`status`,U.`displayname`,U.`email`";
			$sql .= " FROM `".DB_EVENTCALENDAR."` AS D";
			$sql .= " LEFT JOIN `".DB_USER."` AS U ON U.`id`=D.`member_id`";
			$sql .= " $where ORDER BY D.`begin_date` DESC,D.`last_update` DESC";
			$sql .= " LIMIT $start, ".$list_per_page;
			foreach ($db->customQuery($sql) AS $item) {
				$id = $item['id'];
				$tr = '<tr id=M_'.$id.'>';
				$tr .= '<th headers=c0 id=r'.$id.' scope=row class=topic><a href="../index.php?module='.$index['module'].'&amp;id='.$id.'" title="{LNG_PREVIEW}" target=_blank>'.$item['topic'].'</a></th>';
				$tr .= '<td headers="r'.$id.' c1" class=check-column><a id=check_'.$id.' class=icon-uncheck></a></td>';
				$tr .= '<td headers="r'.$id.' c2" class=center><span class=event_color style="background-color:'.$item['color'].'"></span></td>';
				$tr .= '<td headers="r'.$id.' c3" class="menu mobile"><span class=icon-published'.$item['published'].' title="'.$lng['LNG_PUBLISHEDS'][$item['published']].'"></span></td>';
				$tr .= '<td headers="r'.$id.' c4" class="center tablet"><a href="{URLQUERY?u='.$item['member_id'].'}" class="cuttext status'.$item['status'].'" title="{LNG_SELECT_ITEM}">'.($item['displayname'] == '' ? $item['email'] : $item['displayname']).'</a></td>';
				$tr .= '<td headers="r'.$id.' c5" class="date tablet">'.$db->sql_date2date($item['begin_date']).'</td>';
				$tr .= '<td headers="r'.$id.' c6" class=menu><a href="{URLQUERY?module=event-write&src=event-setup&spage='.$page.'&id='.$id.'}" title="{LNG_EDIT}" class=icon-edit></a></td>';
				$tr .= '</tr>';
				$content[] = $tr;
			}
		}
		$content[] = '</tbody>';
		$content[] = '<tfoot>';
		$content[] = '<tr>';
		$content[] = '<td headers=c0>&nbsp;</td>';
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
		$sel = array();
		$sel[] = '<select id=sel_action>';
		// delete
		$sel[] = '<option value=delete_event>{LNG_DELETE}</option>';
		// published
		foreach ($lng['LNG_PUBLISHEDS'] AS $i => $value) {
			$sel[] = '<option value=published_event_'.$i.'>'.$value.'</option>';
		}
		$sel[] = '</select>';
		$content[] = str_replace('value='.$action.'>', 'value='.$action.' selected>', implode('', $sel));
		$content[] = '<label accesskey=e for=sel_action class="button go" id=btn_action><span>{LNG_SELECT_ACTION}</span></label>';
		$content[] = '</fieldset>';
		// add
		$content[] = '<fieldset>';
		$content[] = '<a class="button add" href="{URLQUERY?module=event-write&src=event-setup}"><span class=icon-plus>{LNG_ADD_NEW} {LNG_EVENT}</span></a>';
		$content[] = '</fieldset>';
		$content[] = '</div>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = "inintCheck('event');";
		$content[] = "inintTR('event', /M_[0-9]+/);";
		$content[] = 'callAction("btn_action", function(){return $E("sel_action").value}, "event", "'.WEB_URL.'/modules/event/admin_action.php");';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'event-setup';
		$url_query['page'] = $page;
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
