<?php
// modules/personnel/admin_inint.php
if (MAIN_INIT == 'admin' && $isAdmin && (sizeof($install_owners['personnel']) == 0 || !defined('DB_PERSONNEL'))) {
	// เมนูติดตั้ง
	$admin_menus['tools']['install']['personnel'] = '<a href="index.php?module=install&amp;modules=personnel"><span>Personnel</span></a>';
} else {
	// เมนูแอดมิน
	if (gcms::canConfig($config, 'personnel_can_config')) {
		$admin_menus['modules']['personnel']['category'] = '<a href="index.php?module=personnel-category"><span>{LNG_PERSONNEL_CATEGORY}</span></a>';
	} else {
		unset($admin_menus['modules']['personnel']['config']);
		unset($admin_menus['modules']['personnel']['category']);
	}
	if (gcms::canConfig($config, 'personnel_can_config')) {
		$admin_menus['modules']['personnel']['setup'] = '<a href="index.php?module=personnel-setup"><span>{LNG_PERSONNEL_LIST}</span></a>';
		$admin_menus['modules']['personnel']['write'] = '<a href="index.php?module=personnel-write"><span>{LNG_ADD_NEW} {LNG_PERSONNEL}</span></a>';
	} else {
		unset($admin_menus['modules']['personnel']['setup']);
	}
}
