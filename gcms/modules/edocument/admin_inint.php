<?php
// modules/edocument/admin_inint.php
if (MAIN_INIT == 'admin' && $isAdmin && (sizeof($install_owners['edocument']) == 0 || !defined('DB_EDOCUMENT'))) {
	// เมนูติดตั้ง
	$admin_menus['tools']['install']['edocument'] = '<a href="index.php?module=install&amp;modules=edocument"><span>E-Document</span></a>';
} else {
	// เมนูแอดมิน
	if (!gcms::canConfig($config, 'edocument_can_config')) {
		unset($admin_menus['modules']['edocument']['config']);
	}
	if (gcms::canConfig($config, 'edocument_moderator')) {
		$admin_menus['modules']['edocument']['setup'] = '<a href="index.php?module=edocument-setup"><span>{LNG_EDOCUMENT_LIST}</span></a>';
		foreach ($install_owners['edocument'] AS $items) {
			// menu ของโมดูล
			$module_menus['edocument']["write_$items[module]"] = array("$items[module], {LNG_ADD} {LNG_EDOCUMENT_ITEM}", WEB_URL."/index.php?module=$items[module]-write");
		}
	} else {
		unset($admin_menus['modules']['edocument']['setup']);
	}
}
