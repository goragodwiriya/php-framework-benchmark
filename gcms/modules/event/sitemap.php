<?php
// modules/event/sitemap.php
if (is_array($owners['event'])) {
	$sql = "SELECT `id`,`begin_date`,`module_id`";
	$sql .= " FROM `".DB_EVENTCALENDAR."`";
	$sql .= " WHERE `module_id` IN(".implode(',', $owners['event']).") AND `published`='1' AND `published_date`<='$cdate'";
	$datas = $cache->get($sql);
	if (!$datas) {
		$datas = $db->customQuery($sql);
		$cache->save($sql, $datas);
	}
	foreach ($datas AS $item) {
		echo '<url>';
		echo '<loc>'.gcms::getURL($modules[$item['module_id']], '', 0, 0, "id=$item[id]").'</loc>';
		list($d, $t) = explode(' ', $item['begin_date']);
		echo '<lastmod>'.$d.'</lastmod>';
		echo '<changefreq>daily</changefreq>';
		echo '<priority>0.5</priority>';
		echo '</url>';
	}
}
