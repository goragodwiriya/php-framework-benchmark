<?php
// modules/personnel/view.php
if (defined('MAIN_INIT')) {
	// อัลบัมที่เลือก
	$id = gcms::getVars($_REQUEST, 'id', 0);
	// ตรวจสอบโมดูล
	$sql = "SELECT P.*,M.`module`,D.`topic`,D.`description`,D.`keywords`,C.`category_id`,C.`topic` AS `category`";
	$sql .= " FROM `".DB_INDEX_DETAIL."` AS D";
	$sql .= " INNER JOIN `".DB_INDEX."` AS I ON I.`id`=D.`id` AND I.`module_id`=D.`module_id` AND I.`index`='1' AND I.`language`=D.`language`";
	$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`id`=D.`module_id` AND M.`owner`='personnel'";
	$sql .= " INNER JOIN `".DB_PERSONNEL."` AS P ON P.`id`=$id AND P.`module_id`=M.`id`";
	$sql .= " INNER JOIN `".DB_CATEGORY."` AS C ON C.`module_id`=M.`id` AND C.`category_id`=P.`category_id`";
	$sql .= " WHERE D.`language` IN ('".LANGUAGE."','') LIMIT 1";
	$index = $cache->get($sql);
	if (!$index) {
		$index = $db->customQuery($sql);
		if (sizeof($index) == 1) {
			$index = $index[0];
			$cache->save($sql, $index);
		} else {
			$index = false;
		}
	}
	if (!$index) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content = '<div class=error>'.$title.'</div>';
	} else {
		// breadcrumbs
		$breadcrumb = gcms::loadtemplate($index['module'], '', 'breadcrumb');
		$breadcrumbs = array();
		// หน้าหลัก
		$breadcrumbs['HOME'] = gcms::breadcrumb('icon-home', WEB_URL.'/index.php', $install_modules[$module_list[0]]['menu_tooltip'], $install_modules[$module_list[0]]['menu_text'], $breadcrumb);
		// โมดูล
		if (isset($install_modules[$index['module']]['menu_text'])) {
			$m = $install_modules[$index['module']]['menu_text'];
			$t = $install_modules[$index['module']]['menu_tooltip'];
		} else {
			$m = ucwords($index['module']);
			$t = $m;
		}
		$canonical = gcms::getURL($index['module']);
		$breadcrumbs['MODULE'] = gcms::breadcrumb('', $canonical, $t, $m, $breadcrumb);
		// อัลบัม
		$canonical = gcms::getURL($index['module'], '', 0, 0, "id=$index[category_id]");
		$index['category'] = gcms::ser2Str($index, 'category');
		$breadcrumbs['CATEGORY'] = gcms::breadcrumb('', $canonical, $index['category'], $index['category'], $breadcrumb);
		// แสดงผล list รายการ
		$patt = array('/{BREADCRUMS}/', '/{NAME}/', '/{POSITION}/', '/{CATEGORY}/',
			'/{DETAIL}/', '/{ADDRESS}/', '/{PHONE}/', '/{EMAIL}/', '/{PICTURE}/');
		$replace = array();
		$replace[] = implode("\n", $breadcrumbs);
		$replace[] = $index['name'];
		$replace[] = $index['position'];
		$replace[] = $index['category'];
		$replace[] = $index['detail'];
		$replace[] = $index['address'];
		$replace[] = $index['phone'];
		$replace[] = $index['email'];
		$replace[] = ($index['picture'] != '' && is_file(DATA_PATH."personnel/$index[picture]")) ? DATA_URL."personnel/$index[picture]" : WEB_URL.'/modules/personnel/img/noicon.jpg';
		$content = preg_replace($patt, $replace, gcms::loadtemplate($index['module'], 'personnel', 'view'));
		// ตัวแปรหลังจากแสดงผลแล้ว
		$custom_patt['/{W}/'] = $config['personnel_image_w'];
		// title,keywords,description
		$title = $index['topic'];
		$keywords = $index['keywords'];
		$description = $index['description'];
	}
	// เลือกเมนู
	$menu = empty($install_modules[$index['module']]['alias']) ? $index['module'] : $install_modules[$index['module']]['alias'];
}
