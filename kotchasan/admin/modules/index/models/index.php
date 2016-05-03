<?php
/*
 * @filesource index/models/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Index;

use \Gcms\Gcms;
use \Kotchasan\Language;
use \Kotchasan\Login;

/**
 * Model สำหรับโหลดโมดูลและเมนูของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Orm\Field
{
	/**
	 * ชื่อตาราง
	 *
	 * @var string
	 */
	protected $table = 'index I';
	/**
	 * รายการเมนู (Backend)
	 *
	 * @var array
	 */
	public static $menus;

	/**
	 * โหลดโมดูลที่ติดตั้ง
	 */
	public static function installedmodules()
	{
		if (defined('MAIN_INIT')) {
			// ตรวจสอบโมดูลที่ติดตั้ง ตามโฟลเดอร์
			$dir = ROOT_PATH.'modules/';
			$f = @opendir($dir);
			if ($f) {
				while (false !== ($text = readdir($f))) {
					if ($text !== '.' && $text !== '..' && $text !== 'css' && $text !== 'js') {
						Gcms::$install_owners[$text] = array();
					}
				}
				closedir($f);
			}
			// model
			$model = new \Kotchasan\Model;
			// โหลดโมดูลที่ติดตั้ง เรียงตามลำดับโฟลเดอร์
			$query = $model->db()->createQuery()
				->select('id', 'module', 'owner')
				->from('modules')
				->where(array('owner', '!=', 'index'))
				->order('owner');
			foreach ($query->execute() as $item) {
				Gcms::$install_modules[$item->module] = $item;
				Gcms::$install_owners[$item->owner][] = $item;
			}
			// โหลดเมนู
			self::$menus = self::loadMenus();
			// called Initial
			foreach (Gcms::$install_owners as $owner => $items) {
				if (is_file(ROOT_PATH.'modules/'.$owner.'/models/admin/init.php')) {
					include ROOT_PATH.'modules/'.$owner.'/models/admin/init.php';
					$class = ucfirst($owner).'\Admin\Init\Model';
					if (method_exists($class, 'init')) {
						// module Initial
						$class::init($items);
					}
				}
			}
		} else {
			// เรียก method โดยตรง
			\Kotchasan\Error::send('Do not call method directly');
		}
	}

	/**
	 * โหลดรายการเมนูทั้งหมด.
	 *
	 * @return array รายการเมนูทั้งหมด
	 */
	public static function loadMenus()
	{
		$menus = array();
		// menu section
		$menus['sections']['home'] = array('h', '<a href="index.php?module=dashboard" accesskey=h title="'.Language::get('Home').'"><span>'.Language::get('Home').'</span></a>');
		$menus['sections']['settings'] = array('1', ''.Language::get('Site settings').'');
		$menus['sections']['index'] = array('2', ''.Language::get('Menus').' &amp; '.Language::get('Web pages').'');
		$menus['sections']['modules'] = array('3', ''.Language::get('Modules').'');
		$menus['sections']['widgets'] = array('4', ''.Language::get('Widgets').'');
		$menus['sections']['users'] = array('5', ''.Language::get('Users').'');
		$menus['sections']['email'] = array('6', ''.Language::get('Mailbox').'');
		$menus['sections']['tools'] = array('7', ''.Language::get('Tools').'');
		// settings
		$menus['settings']['system'] = '<a href="index.php?module=system"><span>'.Language::get('General').'</span></a>';
		$menus['settings']['mailserver'] = '<a href="index.php?module=mailserver"><span>'.Language::get('Email settings').'</span></a>';
		$menus['settings']['mailtemplate'] = '<a href="index.php?module=mailtemplate"><span>'.Language::get('Email template').'</span></a>';
		$menus['settings']['template'] = '<a href="index.php?module=template"><span>'.Language::get('Template').'</span></a>';
		$menus['settings']['skin'] = '<a href="index.php?module=skin"><span>'.Language::get('Template settings').'</span></a>';
		$menus['settings']['maintenance'] = '<a href="index.php?module=maintenance"><span>'.Language::get('Maintenance Mode').'</span></a>';
		$menus['settings']['intro'] = '<a href="index.php?module=intro"><span>'.Language::get('Intro Page').'</span></a>';
		$menus['settings']['languages'] = '<a href="index.php?module=languages"><span>'.Language::get('Language').'</span></a>';
		$menus['settings']['other'] = '<a href="index.php?module=other"><span>'.Language::get('Other').'</span></a>';
		$menus['settings']['meta'] = '<a href="index.php?module=meta"><span>'.Language::get('SEO &amp; Social').'</span></a>';
		// email
		$menus['email']['sendmail'] = '<a href="index.php?module=sendmail"><span>'.Language::get('Email send').'</span></a>';
		// เมนู
		$menus['index']['pages'] = '<a href="index.php?module=pages"><span>'.Language::get('Web pages').'</span></a>';
		$menus['index']['insmod'] = '<a href="index.php?module=mods"><span>'.Language::get('installed module').'</span></a>';
		$menus['index']['menu'] = '<a href="index.php?module=menus"><span>'.Language::get('Menus').'</span></a>';
		// เมนูสมาชิก
		$menus['users']['memberstatus'] = '<a href="index.php?module=memberstatus"><span>'.Language::get('Member status').'</span></a>';
		$menus['users']['member'] = '<a href="index.php?module=member"><span>'.Language::get('Member List').'</span></a>';
		$menus['users']['register'] = '<a href="index.php?module=register"><span>'.Language::get('Register').'</span></a>';
		// tools
		$menus['tools']['install'] = array();
		$menus['tools']['database'] = '<a href="index.php?module=database"><span>'.Language::get('Database').'</span></a>';
		$menus['tools']['language'] = '<a href="index.php?module=language"><span>'.Language::get('Language').'</span></a>';
		$menus['tools']['debug'] = '<a href="index.php?module=debug"><span>'.Language::get('Debug tool').'</span></a>';
		$menus['modules'] = array();
		// โมดูลที่ติดตั้งแล้ว
		foreach (Gcms::$install_modules as $item) {
			// ตรวจสอบไฟล์ config
			if (is_file(ROOT_PATH.'modules/'.$item->owner.'/controllers/admin/settings.php')) {
				$menus['modules'][$item->module]['config'] = '<a href="index.php?module='.$item->owner.'-settings&amp;mid='.$item->id.'"><span>'.Language::get('Config').'</span></a>';
			}
			// ตรวจสอบไฟล์ category
			if (is_file(ROOT_PATH.'modules/'.$item->owner.'/controllers/admin/category.php')) {
				$menus['modules'][$item->module]['category'] = '<a href="index.php?module='.$item->owner.'-category&amp;mid='.$item->id.'"><span>'.Language::get('Category').'</span></a>';
			}
			// ตรวจสอบไฟล์ setup
			if (is_file(ROOT_PATH.'modules/'.$item->owner.'/controllers/admin/setup.php')) {
				$menus['modules'][$item->module]['setup'] = '<a href="index.php?module='.$item->owner.'-setup&amp;mid='.$item->id.'"><span>'.Language::get('Contents').'</span></a>';
			}
		}
		if (!Login::isAdmin()) {
			unset($menus['sections']['settings']);
			unset($menus['sections']['index']);
			unset($menus['sections']['menus']);
			unset($menus['sections']['widgets']);
			unset($menus['sections']['users']);
			unset($menus['sections']['tools']);
		}
		if (empty($menus['modules'])) {
			unset($menus['sections']['modules']);
		}
		if (isset($menus['widgets']) && sizeof($menus['widgets']) == 0) {
			unset($menus['sections']['widgets']);
		}
		if (empty($menus['tools']['install'])) {
			unset($menus['tools']['install']);
		}
		return $menus;
	}
}