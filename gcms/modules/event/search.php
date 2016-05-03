<?php
// modules/event/search.php
if (defined('MAIN_INIT') && !empty($install_modules['event']['owner']) && $install_modules['event']['owner'] == 'event') {
	$searchs = array();
	foreach ($words AS $item) {
		$searchs[] = "I.`topic` LIKE '%$item%' OR I.`detail` LIKE '%$item%'";
	}
	$sql = "SELECT I.`id`,I.`topic` AS `alias`,I.`topic`,I.`description`,I.`detail`,0 AS `index`,M.`module`,M.`owner`,3 AS `level`";
	$sql .= "FROM `".DB_EVENTCALENDAR."` AS I,`".DB_MODULES."` AS M ";
	$sql .= "WHERE (".implode(' OR ', $searchs).") AND M.`id`=I.`module_id` AND `published`='1' AND `published_date`<='$today'";
	$sqls[] = "($sql)";
}
