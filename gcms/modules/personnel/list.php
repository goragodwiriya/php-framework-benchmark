<?php
// modules/personnel/list.php
if (defined('MAIN_INIT')) {
	// ตรวจสอบโมดูลที่ติดตั้ง
	$sql = "SELECT I.`module_id`,M.`module`,D.`detail`,D.`topic`,D.`description`,D.`keywords`";
	$sql .= " FROM `".DB_INDEX_DETAIL."` AS D";
	$sql .= " INNER JOIN `".DB_INDEX."` AS I ON I.`id`=D.`id` AND I.`module_id`=D.`module_id` AND I.`index`='1' AND I.`language`=D.`language`";
	$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`id`=D.`module_id` AND M.`owner`='personnel'";
	$sql .= " WHERE D.`language` IN ('".LANGUAGE."','') LIMIT 1";
	$index = $cache->get($sql);
	if (!$index) {
		$index = $db->customQuery($sql);
		$cache->save($sql, $index);
	}
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content = '<div class=error>'.$title.'</div>';
	} else {
		$index = $index[0];
		// ค่าที่ส่งมา
		$id = isset($_REQUEST['cat']) ? (int)$_REQUEST['cat'] : 0;
		// หมวดทั้งหมด
		$categories = array();
		$sql = "SELECT `category_id`,`topic` FROM `".DB_CATEGORY."` WHERE `module_id`='$index[module_id]' ORDER BY `category_id`";
		$saved = $cache->get($sql);
		if (!$saved) {
			$saved = $db->customQuery($sql);
			$cache->save($sql, $saved);
		}
		foreach ($saved AS $category) {
			$categories[$category['category_id']] = gcms::ser2Str($category, 'topic');
		}
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
		// กลุ่ม
		if ($id > 0) {
			$breadcrumbs['CATEGORY'] = gcms::breadcrumb('', gcms::getURL($index['module'], '', 0, 0, "cat=$id"), $categories[$id], $categories[$id], $breadcrumb);
		}
		// default query
		$q = array();
		$q[] = "`module_id`='$index[module_id]'";
		if ($id > 0) {
			$q[] = "`category_id`='$id'";
		}
		$where = 'WHERE '.implode(' AND ', $q);
		// query บุคลากร
		$sql = "SELECT * FROM `".DB_PERSONNEL."` AS M";
		$sql .= " $where ORDER BY `category_id` ASC,`order` ASC,`id` ASC";
		$list = $cache->get($sql);
		if (!$list) {
			$list = $db->customQuery($sql);
			$cache->save($sql, $list);
		}
		$items = array();
		$patt = array('/{ID}/', '/{NAME}/', '/{POSITION}/', '/{DETAIL}/', '/{ADDRESS}/', '/{PHONE}/', '/{EMAIL}/', '/{ORDER}/', '/{PICTURE}/', '/{URL}/');
		$old_cat = 0;
		$old_order = 0;
		$skin = gcms::loadtemplate($index['module'], 'personnel', 'item');
		$n = 0;
		foreach ($list AS $i => $item) {
			if ($old_cat != $item['category_id']) {
				$old_cat = $item['category_id'];
				if ($i > 0) {
					$items[] = '</ul></article><article>';
				}
				$items[] = '<h3>'.$categories[$old_cat].'</h3><ul>';
			}
			if ($n > 0 && ($old_order != $item['order'] || ($item['order'] > 0 && $n % $item['order'] == 0))) {
				$items[] = '</ul><ul>';
				$old_order = $item['order'];
				$n = 0;
			}
			$replace = array();
			$replace[] = $item['id'];
			$replace[] = $item['name'];
			$replace[] = $item['position'];
			$replace[] = $item['detail'];
			$replace[] = $item['address'];
			$replace[] = $item['phone'];
			$replace[] = $item['email'];
			$replace[] = $item['order'];
			$replace[] = ($item['picture'] != '' && is_file(DATA_PATH."personnel/$item[picture]")) ? DATA_URL."personnel/$item[picture]" : WEB_URL.'/modules/personnel/img/noicon.jpg';
			$replace[] = gcms::getUrl($index['module'], 0, 0, '', "id=$item[id]");
			$items[] = preg_replace($patt, $replace, $skin);
			$n++;
		}
		// แสดงผล list รายการ
		$patt = array('/{BREADCRUMS}/', '/{LIST}/', '/{TOPIC}/', '/{DETAIL}/', '/{CATEGORY}/');
		$replace = array();
		$replace[] = implode("\n", $breadcrumbs);
		$replace[] = sizeof($items) == 0 ? '<div class=error>{LNG_LIST_EMPTY}</div>' : '<article>'.implode("\n", $items).'</ul></article>';
		$replace[] = $index['topic'];
		$replace[] = $index['detail'];
		$replace[] = gcms::getVars($categories, $id, '');
		$content = preg_replace($patt, $replace, gcms::loadtemplate($index['module'], 'personnel', 'main'));
		// title,keywords,description
		$title = $index['topic'];
		$keywords = $index['keywords'];
		$description = $index['description'];
		// facebook api
		$canonical = gcms::getURL($index['module']);
	}
	// เลือกเมนู
	$menu = empty($install_modules[$index['module']]['alias']) ? $index['module'] : $install_modules[$index['module']]['alias'];
}
