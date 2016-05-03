<?php
// admin/country.php
if (MAIN_INIT == 'admin' && $isAdmin) {
    // กำหนดเมนูเรียงลำดับ
    $orders = array();
    $orders[] = array('{LNG_COUNTRY_ISO} {LNG_SORT_ASC}', '`iso` ASC');
    $orders[] = array('{LNG_COUNTRY_ISO} {LNG_SORT_DESC}', '`iso` DESC');
    $orders[] = array('{LNG_COUNTRY_NAME} {LNG_SORT_ASC}', '`printable_name` ASC');
    $orders[] = array('{LNG_COUNTRY_NAME} {LNG_SORT_DESC}', '`printable_name` DESC');
    // รายการเรียงลำดับที่เลือก
    $order = gcms::getVars('GET,COOKIE', 'order,country_order', 0);
    $order = min(sizeof($orders), max(0, $order));
    // เมนูเรียงลำดับ
    $orderoptions = array();
    foreach ($orders AS $i => $item) {
        $sel = $i == $order ? ' selected' : '';
        $orderoptions[] = '<option value='.$i.$sel.'>'.$item[0].'</option>';
    }
    // query
    $qs = array();
    // เลือกจาก zone
    $zone = gcms::getVars('GET,COOKIE', 'zone,country_zone', -1);
    if ($zone > 0) {
        $qs[] = "`zone`='$zone'";
        $url_query['zone'] = $zone;
    }
    // ค้นหาจาก printable_name และ iso
    $search = $db->sql_trim_str($_GET, 'search');
    if ($search != '') {
        $qs[] = "(`printable_name` LIKE '%$search%' OR `iso` LIKE '%$search%')";
        $url_query['search'] = urlencode($search);
    }
    $where = sizeof($qs) == 0 ? '' : ' WHERE '.implode(' AND ', $qs);
    // จำนวนสมาชิกทั้งหมด
    $sql = "SELECT COUNT(*) AS `count` FROM `".DB_COUNTRY."`$where";
    $count = $db->customQuery($sql);
    // รายการต่อหน้า
    $list_per_page = gcms::getVars('GET,COOKIE', 'count,country_listperpage', 30);
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
    // save ฟิลเตอร์ลง cookie
    setCookie('country_order', $order, time() + 3600 * 24 * 365);
    setCookie('country_zone', $zone, time() + 3600 * 24 * 365);
    setCookie('country_listperpage', $list_per_page, time() + 3600 * 24 * 365);
    // title
    $title = $lng['LNG_COUNTRY_LIST'];
    $a = array();
    $a[] = '<span class=icon-settings>{LNG_SITE_SETTINGS}</span>';
    $a[] = '{LNG_COUNTRY_LIST}';
    // แสดงผล
    $content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
    $content[] = '<section>';
    $content[] = '<header><h1 class=icon-world>'.$title.'</h1></header>';
    $content[] = '<form class=table_nav method=get action=index.php>';
    // เรียงลำดับ
    $content[] = '<fieldset>';
    $content[] = '<label>{LNG_SORT_ORDER} <select name=order>';
    $content[] = implode("\n", $orderoptions);
    $content[] = '</select></label>';
    $content[] = '</fieldset>';
    // รายการต่อหน้า
    $content[] = '<fieldset>';
    $content[] = '<label>{LNG_LIST_PER_PAGE} <select name=count>';
    foreach (array(10, 20, 30, 40, 50, 100) AS $item) {
        $sel = $item == $list_per_page ? ' selected' : '';
        $content[] = '<option value='.$item.$sel.'>'.$item.' {LNG_ITEMS}</option>';
    }
    $content[] = '</select></label>';
    $content[] = '</fieldset>';
    // zone
    $content[] = '<fieldset>';
    $content[] = '<label>{LNG_COUNTRY_ZONE} <select name=zone>';
    $content[] = '<option value=0>{LNG_VIEW_ALL}</option>';
    if (isset($lng['COUNTRIES_ZONE'])) {
        foreach ($lng['COUNTRIES_ZONE'] AS $i => $item) {
            $sel = $i == $zone ? 'selected' : '';
            $content[] = '<option value='.$i.' '.$sel.'>'.$item.'</option>';
        }
    }
    $content[] = '</select></label>';
    $content[] = '</fieldset>';
    // submit
    $content[] = '<fieldset>';
    $content[] = '<label><input type=submit class="button go" value="{LNG_GO}"></label>';
    $content[] = '<input name=module type=hidden value=country>';
    $content[] = '</fieldset>';
    // search
    $content[] = '<fieldset class=search>';
    $content[] = '<label accesskey=f><input type=text name=search value="'.$search.'" placeholder="{LNG_SEARCH_TITLE}" title="{LNG_SEARCH_TITLE}"></label>';
    $content[] = '<input type=submit value="&#xE607;" title="{LNG_SEARCH}">';
    $content[] = '</fieldset>';
    $content[] = '</form>';
    // ตารางข้อมูล
    $content[] = '<table id=country class="tbl_list fullwidth">';
    $content[] = '<caption>'.preg_replace($patt2, $replace2, $search != '' ? $lng['SEARCH_RESULT'] : $lng['ALL_ITEMS']).'</caption>';
    $content[] = '<thead>';
    $content[] = '<tr>';
    $content[] = '<th id=c0 scope=col>{LNG_COUNTRY_NAME}</th>';
    $content[] = '<th id=c1 scope=col class=check-column><a class="checkall icon-uncheck"></a></th>';
    $content[] = '<th id=c2 scope=col>{LNG_COUNTRY_ISO}</th>';
    $content[] = '<th id=c3 scope=col class=mobile>{LNG_COUNTRY_ZONE}</th>';
    $content[] = '<th id=c4>&nbsp;</th>';
    $content[] = '</tr>';
    $content[] = '</thead>';
    $content[] = '<tbody>';
    // country
    $sql = "SELECT * FROM `".DB_COUNTRY."` $where";
    $sql .= " ORDER BY ".$orders[$order][1];
    $sql .= " LIMIT $start, $list_per_page";
    foreach ($db->customQuery($sql) AS $item) {
        $id = $item['id'];
        $tr = '<tr id=L_'.$id.'>';
        $tr .= '<th headers=c0 scope=row>'.$item['printable_name'].'</th>';
        $tr .= '<td headers="r'.$id.'" class=check-column><a id=check_'.$id.' class=icon-uncheck></a></td>';
        $tr .= '<td headers="r'.$id.' c2">'.$item['iso'].'</td>';
        $tr .= '<td headers="r'.$id.' c3" class=mobile>'.(isset($lng['COUNTRIES_ZONE'][$item['zone']]) ? $lng['COUNTRIES_ZONE'][$item['zone']] : '').'</td>';
        $tr .= '<td headers="r'.$id.' c4" class=menu><a href="{URLQUERY?id='.$id.'&module=countrywrite&src=country&spage='.$page.'}" title="{LNG_MEMBER_EDIT_TITLE}" class=icon-edit></a></td>';
        $tr .= '</tr>';
        $content[] = $tr;
    }
    $content[] = '</tbody>';
    $content[] = '<tfoot>';
    $content[] = '<tr>';
    $content[] = '<td headers=c0>&nbsp;</td>';
    $content[] = '<td headers=c1 class=check-column><a class="checkall icon-uncheck"></a></td>';
    $content[] = '<td headers=c2 colspan=3></td>';
    $content[] = '</tr>';
    $content[] = '</tfoot>';
    $content[] = '</table>';
    // แบ่งหน้า
    $url = '<a href="{URLQUERY?module=country&page=%d}" title="{LNG_DISPLAY_PAGE} %d">%d</a>';
    $content[] = '<div class=splitpage>'.gcms::pagination($totalpage, $page, $url).'</div>';
    $content[] = '<div class=table_nav>';
    $content[] = '<fieldset>';
    // sel action
    $sel = array();
    $sel[] = '<select id=sel_action>';
    // delete
    $sel[] = '<option value=delete_country>{LNG_DELETE}</option>';
    // country zone
    $sel[] = '<option value=zone_0>{LNG_COUNTRY_NO_ZONE}</option>';
    if (isset($lng['COUNTRIES_ZONE'])) {
        foreach ($lng['COUNTRIES_ZONE'] AS $i => $item) {
            $sel[] = '<option value=zone_'.$i.'>'.$item.'</option>';
        }
    }
    $sel[] = '</select>';
    $content[] = str_replace('value='.$action.'>', 'value='.$action.' selected>', implode('', $sel));
    $content[] = '<label accesskey=e for=sel_action id=member_action class="button ok"><span>{LNG_SELECT_ACTION}</span></label>';
    $content[] = '</fieldset>';
    // add
    $content[] = '<a class="button add" href="{URLQUERY?module=countrywrite&src=country}"><span class=icon-plus>{LNG_ADD_NEW} {LNG_COUNTRY}</span></a>';
    $content[] = '</div>';
    $content[] = '</section>';
    $content[] = '<script>';
    $content[] = '$G(window).Ready(function(){';
    $content[] = "inintCheck('country');";
    $content[] = "inintTR('country', /L_[0-9]+/);";
    $content[] = 'callAction("member_action", function(){return $E("sel_action").value}, "country", "action.php");';
    $content[] = '});';
    $content[] = '</script>';
    // หน้านี้
    $url_query['module'] = 'country';
    $url_query['page'] = $page;
} else {
    $title = $lng['LNG_DATA_NOT_FOUND'];
    $content[] = '<aside class=error>'.$title.'</aside>';
}
