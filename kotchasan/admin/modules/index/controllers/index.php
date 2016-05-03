<?php
/*
 * @filesource index/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Index;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Template;
use \Kotchasan\Language;

/**
 * Controller หลัก สำหรับแสดง backend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

	/**
	 * แสดงผลหน้าหลักเว็บไซต์
	 *
	 * @param Request $request
	 */
	public function index(Request $request)
	{
		// ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
		define('MAIN_INIT', __FILE__);
		// session cookie
		self::$request->inintSession();
		// ตรวจสอบการ login
		Login::create();
		// กำหนด skin ให้กับ template
		Template::init(self::$request->get('skin', self::$cfg->skin)->toString());
		// backend
		$view = new \Kotchasan\View;
		if ($login = Login::adminAccess()) {
			// โหลดโมดูลที่ติดตั้งแล้ว
			\Index\Index\Model::installedmodules();
			// Controller หลัก
			$main = new \Index\Main\Controller;
		} else {
			// forgot or login
			if (self::$request->request('action')->toString() === 'forgot') {
				$main = new \Index\Forgot\Controller;
			} else {
				$main = new \Index\Login\Controller;
			}
		}
		$languages = array();
		$uri = self::$request->getUri();
		foreach (array_merge(self::$cfg->languages, Language::installedLanguage()) AS $i => $item) {
			$languages[$item] = '<a id=lang_'.$item.' href="'.$uri->withParams(array('lang' => $item), true).'" title="'.Language::get('Language').' '.strtoupper($item).'" style="background-image:url('.WEB_URL.'language/'.$item.'.gif)" tabindex=1>&nbsp;</a>';
		}
		// เนื้อหา
		$view->setContents(array(
			// main template
			'/{MAIN}/' => $main->execute(),
			// Framework Version
			'/{VERSION}/' => VERSION,
			// language menu
			'/{LANGUAGES}/' => implode('', $languages),
			// title
			'/{TITLE}/' => $main->title().' (Admin)',
			// url สำหรับกลับไปหน้าก่อนหน้า
			'/{BACKURL(\?([a-zA-Z0-9=&\-_@\.]+))?}/e' => '\Kotchasan\View::back'
		));
		if ($login) {
			$view->setContents(array(
				// ID สมาชิก
				'/{LOGINID}/' => $login['id'],
				// ชื่อ นามสกุล
				'/{LOGINNAME}/' => empty($login['displayname']) ? $login['email'] : $login['displayname'],
				// สถานะสมาชิก
				'/{STATUS}/' => $login['status'],
				// เมนู
				'/{MENUS}/' => \Index\Menu\View::render()
			));
		}
		// ส่งออก เป็น HTML
		$view->renderHTML();
	}
}