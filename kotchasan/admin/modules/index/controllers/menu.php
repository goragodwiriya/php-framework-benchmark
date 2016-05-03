<?php
/*
 * @filesource index/controllers/menu.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Menu;

use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Language;

/**
 * รายการเมนูทั้งหมด.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{
	/**
	 * แอเรย์เก็บรายการเมนู
	 *
	 * @var array
	 */
	public static $menus = array();

	/**
	 * โหลดรายการเมนูทั้งหมด.
	 *
	 * @return array รายการเมนูทั้งหมด
	 */
	public static function getMenus()
	{
		// menu section
		self::$menus['sections']['home'] = array('h', '<a href="index.php?module=dashboard" accesskey=h title="'.Language::get('Home').'"><span>'.Language::get('Home').'</span></a>');
		self::$menus['sections']['settings'] = array('1', ''.Language::get('Site settings').'');
		self::$menus['sections']['index'] = array('2', ''.Language::get('Menus').' &amp; '.Language::get('Web pages').'');
		self::$menus['sections']['modules'] = array('3', ''.Language::get('Modules').'');
		self::$menus['sections']['widgets'] = array('4', ''.Language::get('Widgets').'');
		self::$menus['sections']['users'] = array('5', ''.Language::get('Users').'');
		self::$menus['sections']['email'] = array('6', ''.Language::get('Mailbox').'');
		self::$menus['sections']['tools'] = array('7', ''.Language::get('Tools').'');
		// settings
		self::$menus['settings']['system'] = '<a href="index.php?module=system"><span>'.Language::get('General').'</span></a>';
		self::$menus['settings']['mailserver'] = '<a href="index.php?module=mailserver"><span>'.Language::get('Email settings').'</span></a>';
		self::$menus['settings']['mailtemplate'] = '<a href="index.php?module=mailtemplate"><span>'.Language::get('Email template').'</span></a>';
		self::$menus['settings']['template'] = '<a href="index.php?module=template"><span>'.Language::get('Template').'</span></a>';
		self::$menus['settings']['skin'] = '<a href="index.php?module=skin"><span>'.Language::get('Template settings').'</span></a>';
		self::$menus['settings']['maintenance'] = '<a href="index.php?module=maintenance"><span>'.Language::get('Maintenance Mode').'</span></a>';
		self::$menus['settings']['intro'] = '<a href="index.php?module=intro"><span>'.Language::get('Intro Page').'</span></a>';
		self::$menus['settings']['languages'] = '<a href="index.php?module=languages"><span>'.Language::get('Language').'</span></a>';
		self::$menus['settings']['other'] = '<a href="index.php?module=other"><span>'.Language::get('Other').'</span></a>';
		self::$menus['settings']['meta'] = '<a href="index.php?module=meta"><span>'.Language::get('SEO &amp; Social').'</span></a>';
		// email
		self::$menus['email']['sendmail'] = '<a href="index.php?module=sendmail"><span>'.Language::get('Email send').'</span></a>';
		// เมนู
		self::$menus['index']['pages'] = '<a href="index.php?module=pages"><span>'.Language::get('Web pages').'</span></a>';
		self::$menus['index']['insmod'] = '<a href="index.php?module=mods"><span>'.Language::get('installed module').'</span></a>';
		self::$menus['index']['menu'] = '<a href="index.php?module=menus"><span>'.Language::get('Menus').'</span></a>';
		// เมนูสมาชิก
		self::$menus['users']['memberstatus'] = '<a href="index.php?module=memberstatus"><span>'.Language::get('Member status').'</span></a>';
		self::$menus['users']['member'] = '<a href="index.php?module=member"><span>'.Language::get('Member List').'</span></a>';
		self::$menus['users']['register'] = '<a href="index.php?module=register"><span>'.Language::get('Register').'</span></a>';
		// tools
		self::$menus['tools']['install'] = array();
		self::$menus['tools']['database'] = '<a href="index.php?module=database"><span>'.Language::get('Database').'</span></a>';
		self::$menus['tools']['language'] = '<a href="index.php?module=language"><span>'.Language::get('Language').'</span></a>';
		self::$menus['tools']['debug'] = '<a href="index.php?module=debug"><span>'.Language::get('Debug tool').'</span></a>';
		self::$menus['modules'] = array();
		// โมดูลที่ติดตั้งแล้ว
		foreach (Gcms::$install_modules as $item) {
			// ตรวจสอบไฟล์ config
			if (is_file(ROOT_PATH."modules/$item[owner]/controllers/settings.php")) {
				self::$menus['modules'][$item['module']]['config'] = '<a href="index.php?module='.$item['owner'].'-settings&amp;id='.$item['id'].'"><span>'.Language::get('Config').'</span></a>';
			}
			// ตรวจสอบไฟล์ category
			if (is_file(ROOT_PATH."modules/$item[owner]/controllers/category.php")) {
				self::$menus['modules'][$item['module']]['category'] = '<a href="index.php?module='.$item['owner'].'-category&amp;id='.$item['id'].'"><span>'.Language::get('Category').'</span></a>';
			}
			// ตรวจสอบไฟล์ setup
			if (is_file(ROOT_PATH."modules/$item[owner]/controllers/setup.php")) {
				self::$menus['modules'][$item['module']]['setup'] = '<a href="index.php?module='.$item['owner'].'-setup&amp;id='.$item['id'].'"><span>'.Language::get('Contents').'</span></a>';
			}
		}
		if (!Login::isAdmin()) {
			unset(self::$menus['sections']['settings']);
			unset(self::$menus['sections']['index']);
			unset(self::$menus['sections']['menus']);
			unset(self::$menus['sections']['widgets']);
			unset(self::$menus['sections']['users']);
			unset(self::$menus['sections']['tools']);
		}
		if (sizeof(self::$menus['modules']) == 0) {
			unset(self::$menus['sections']['modules']);
		}
		if (isset(self::$menus['widgets']) && sizeof(self::$menus['widgets']) == 0) {
			unset(self::$menus['sections']['widgets']);
		}
		if (sizeof(self::$menus['tools']['install']) == 0) {
			unset(self::$menus['tools']['install']);
		}
	}

	/**
	 * สร้างเมนูตามตำแหน่งของเมนู (parent)
	 *
	 * @return array รายการเมนูทั้งหมด
	 */
	public static function render()
	{
		$controller = new static;
		return $controller->createView('Index\Menu\View')->render(self::$menus);
	}

	/**
	 * อ่านเมนู (MAINMENU) ของโมดูล
	 *
	 * @param string $module ชื่อโมดูลที่ต้องการ
	 * @return array รายการเมนูของเมนูที่เลือก ถ้าไม่พบคืนค่าแอเรย์ว่าง
	 */
	public function moduleMenu($module)
	{
		$result = array();
		if (isset($this->menus->MAINMENU['toplevel'])) {
			foreach ($this->menus->MAINMENU['toplevel'] as $item) {
				if ($item->module == $module) {
					$result = $item;
					break;
				}
			}
		}
		return $result;
	}
}