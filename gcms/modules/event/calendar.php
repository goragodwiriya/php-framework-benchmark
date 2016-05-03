<?php
// modules/event/calendar.php
if (defined('MAIN_INIT')) {
	// วันนี้
	$year = date('Y', $mmktime);
	$month = date('m', $mmktime);
	$today = date('d', $mmktime);
	if (isset($_POST['id']) && preg_match('/^(prev|next)_([0-9]+)_([0-9]+)$/', $_POST['id'], $match)) {
		// มาจาก Ajax
		$c_mkdate = mktime(0, 0, 0, (int)$match[2] + ($match[1] == 'prev' ? -1 : 1), 1, $match[3]);
		$c_year = date('Y', $c_mkdate);
		$c_month = date('m', $c_mkdate);
	} elseif (isset($_REQUEST['m']) && preg_match('/^([0-9]+)\-([0-9]+)$/', $_REQUEST['m'], $match)) {
		// มาจาก URL
		$c_year = (int)$match[1];
		$c_month = (int)$match[2];
	} else {
		// ไม่ได้เลือกวันที่มา แสดงวันที่วันนี้
		$c_year = $year;
		$c_month = $month;
	}
	// วันที่กำลังแสดงผล
	$d = $c_month.'_'.$c_year;
	// วันที่ 1 ของเดือนนี้
	$mkdate = mktime(0, 0, 0, $c_month, 1, $c_year);
	$weekday = date('w', $mkdate);
	$endday = date('t', $mkdate);
	$day = 1;
	// วันที่ 1 ของเดือนถัดไป
	$first_next_month = $c_month == 12 ? mktime(0, 0, 0, 1, 1, $c_year + 1) : mktime(0, 0, 0, $c_month + 1, 1, $c_year);
	// จำนวนวันของเดือนก่อนหน้า
	$days_of_last_month = $c_month == 1 ? date('t', mktime(0, 0, 0, 12, 1, $c_year - 1)) : date('t', mktime(0, 0, 0, $c_month - 1, 1, $c_year));
	// ตรวจสอบรายการที่เกี่ยวข้องจากฐานข้อมูล
	$events = array();
	$sql = "SELECT DAY(D.`begin_date`) AS `d`,D.`id`,D.`topic`,D.`color`,M.`module`";
	$sql .= " FROM `".DB_EVENTCALENDAR."` AS D";
	$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`id`=D.`module_id` AND M.`owner`='event'";
	$sql .= " WHERE MONTH(D.`begin_date`)='$c_month' AND YEAR(D.`begin_date`)='$c_year' AND D.`published`='1' AND D.`published_date`<='".date('Y-m-d', $mmktime)."'";
	$sql .= " ORDER BY D.`begin_date` DESC,D.`last_update` DESC";
	$datas = $cache->get($sql);
	if (!$datas) {
		$datas = $db->customQuery($sql);
		$cache->save($sql, $datas);
	}
	foreach ($datas AS $item) {
		$events[(int)$item['d']][] = $item;
	}
	// calendar
	$calendar = array();
	$calendar[] = '<div id=event-calendar>';
	$calendar[] = '<div class=header>';
	$calendar[] = '<a class="prev" id="prev_'.$d.'">'.$lng['LNG_PREV_MONTH'].'</a>';
	$calendar[] = '<p>'.$lng['MONTH_LONG'][$c_month - 1].' '.($c_year + $lng['YEAR_OFFSET']).'</p>';
	$calendar[] = '<a class="next" id="next_'.$d.'">'.$lng['LNG_NEXT_MONTH'].'</a>';
	$calendar[] = '</div>';
	$calendar[] = '<table id=event-details>';
	$calendar[] = '<thead>';
	$calendar[] = '<tr><th>'.implode('</th><th>', $lng['DATE_SHORT']).'</th></tr>';
	$calendar[] = '</thead>';
	$start = 1;
	$calendar[] = '<tbody>';
	$data = '<tr class=date>';
	while ($start <= $weekday) {
		$data .= '<td class="ex"><span class="d">'.($days_of_last_month - $weekday + $start).'</span></td>';
		$start++;
	}
	$weekday++;
	while ($day <= $endday) {
		if ($today == $day && $month == $c_month && $year == $c_year) {
			$c = ' class="current"';
		} elseif ($weekday == 1) {
			$c = ' class="su"';
		} else {
			$c = '';
		}
		$data .= '<td'.$c.'><a class="d">'.$day.'</a><p>';
		if (isset($events[$day])) {
			foreach ($events[$day] AS $item) {
				$data .= '<a href="'.gcms::getUrl($item['module'], '', 0, 0, "d=$c_year-$c_month-$day").'" class=cuttext style="background-color:'.$item['color'].'" title="'.$item['topic'].'">'.$item['topic'].'</a>';
			}
		}
		$data .= '</p></td>';
		if ($weekday == 7 && $day != $endday) {
			$calendar[] = $data.'</td>';
			$data = '<tr class="date row">';
			$weekday = 0;
		}
		$day++;
		$weekday++;
	}
	$n = 1;
	while ($weekday <= 7) {
		$data .= '<td class="ex"><span class="d">'.$n.'</span></td>';
		$weekday++;
		$n++;
	}
	$calendar[] = $data.'</tr>';
	$calendar[] = '</tbody>';
	$calendar[] = '</table>';
	$calendar[] = '</div>';
}
