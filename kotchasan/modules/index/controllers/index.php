<?php
/*
 * @filesource index/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Index;

use \Gcms\Gcms;
use \Gcms\Login;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Kotchasan\Language;

/**
 * Controller หลัก สำหรับแสดง frontend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

	/**
	 * แสดงผล index.html
	 */
	public function index(Request $request)
	{
		// ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
		define('MAIN_INIT', __FILE__);
		// session cookie
		$request->inintSession();
		// ตรวจสอบการ login
		Login::create();
		// กำหนด skin ให้กับ template
		Template::init($request->get('skin', self::$cfg->skin)->toString());
		// ตรวจสอบหน้าที่จะแสดง
		if (!empty(self::$cfg->maintenance_mode) && !Login::isAdmin()) {
			Gcms::$view = new \Index\Maintenance\View;
		} elseif (!empty(self::$cfg->show_intro) && str_replace(array(BASE_PATH, '/'), '', $request->getUri()->getPath()) == '') {
			Gcms::$view = new \Index\Intro\View;
		} else {
			// counter และ useronline
			\Index\Counter\Model::init();
			// front end
			Gcms::$view = new \Gcms\View;
			// รายการเมนูทั้งหมด
			Gcms::$menu = \Index\Menu\Controller::create();
			Gcms::$view->setContents(Gcms::$menu->render());
			// โมดูลที่ติดตั้งแล้วจากเมนู
			foreach (Gcms::$menu->getMenus() as $item) {
				$module = $item->module;
				if (!empty($module) && !isset(Gcms::$install_modules[$module])) {
					Gcms::$install_modules[$module] = $item;
					Gcms::$install_owners[$item->owner][] = $module;
				}
			}
			// โหลดโมดูลทั้งหมด
			foreach (\Index\Module\Model::getModules() AS $item) {
				$module = $item->module;
				if (!isset(Gcms::$install_modules[$module])) {
					Gcms::$install_modules[$module] = $item;
					Gcms::$install_owners[$item->owner][] = $module;
				}
			}
			// รายชื่อโมดูลทั้งหมด
			$module_list = array_keys(Gcms::$install_modules);
			// หน้า home มาจากเมนูรายการแรก
			$home = Gcms::$menu->homeMenu();
			if ($home) {
				$home->canonical = WEB_URL.'index.php';
				// breadcrumb หน้า home
				Gcms::$view->addBreadcrumb($home->canonical, $home->menu_text, $home->menu_tooltip, 'icon-home');
			}
			// query string
			$modules = $request->getQueryParams();
			if (isset($modules['module']) && preg_match('/^(tag)[\/\-](.*)$/', $modules['module'], $match)) {
				// โมดูล document
				$modules['module'] = 'document';
				$modules['page'] = ucfirst($match[1]);
				$modules['document'] = $match[2];
			} elseif (isset($modules['module']) && preg_match('/^([a-z]+)[\/\-]([a-z]+)$/', $modules['module'], $match)) {
				// โมดูลที่ติดตั้ง
				$modules['module'] = $match[1];
				$modules['page'] = ucfirst($match[2]);
			} else {
				// โมดูล index
				$modules['page'] = 'Index';
			}
			// ตรวจสอบโมดูลที่เลือกกับโมดูลที่ติดตั้งแล้ว
			$module = null;
			if (!empty($module_list)) {
				if (empty($modules['module'])) {
					// ไม่ได้กำหนดโมดูลมา ใช้โมดูลแรกสุด
					$module = Gcms::$install_modules[reset($module_list)];
				} elseif ($modules['module'] == 'index' && isset($modules['id'])) {
					// เรียกโมดูล index จาก id
					$module = (object)array(
							'owner' => 'index',
							'id' => $modules['id']
					);
				} elseif (in_array($modules['module'], $module_list)) {
					// โมดูลที่เลือก
					$module = Gcms::$install_modules[$modules['module']];
				} elseif (in_array($modules['module'], Gcms::$install_owners)) {

				} else {
					// ไม่พบโมดูล (404)
				}
			}
			if ($module) {
				if ($module->owner == 'index') {
					// เรียกจากโมดูล index
					$index = null;
					if (!empty($module->module_id)) {
						$index = \Index\Index\Model::getIndex((int)$module->module_id);
					} elseif (!empty($module->id)) {
						$index = \Index\Index\Model::getIndexById((int)$module->id);
					}
					if ($index) {
						// view (index)
						$page = createClass('Index\Index\View')->render($index);
					} else {
						// ไม่พบหน้าที่เรียก (index)
						$page = createClass('Index\PageNotFound\View')->render($module);
					}
				} else {
					// เรียกจากโมดูลที่ติดตั้ง
					$className = ucfirst($module->owner).'\\'.$modules['page'].'\Controller';
					if (class_exists($className)) {
						$page = createClass($className)->init($request, $module);
					} else {
						// ไม่พบหน้าที่เรียก (page)
						$page = createClass('Index\PageNotFound\View')->render($module);
					}
				}
			} elseif (!empty($modules['module']) && method_exists('Index\Member\Controller', $modules['module'])) {
				// หน้าสมาชิก
				$method = $modules['module'];
				$page = createClass('Index\Member\Controller')->$method($request);
			} else {
				// ไม่พบโมดูล (404)
				$page = createClass('Index\PageNotFound\View')->render($module);
			}
			$web_title = strip_tags($page->topic);
			// meta tag
			$meta = array(
				'og:title' => '<meta property="og:title" content="'.$web_title.'">',
				'description' => '<meta name=description content="'.$page->description.'">',
				'keywords' => '<meta name=keywords content="'.$page->keywords.'">',
				'og:site_name' => '<meta property="og:site_name" content="'.$web_title.'">',
				'og:type' => '<meta property="og:type" content="article">'
			);
			if (empty($page->image_src)) {
				if (is_file(ROOT_PATH.DATA_FOLDER.'image/facebook_photo.jpg')) {
					$page->image_src = WEB_URL.DATA_FOLDER.'image/facebook_photo.jpg';
				}
			}
			if (!empty($page->image_src)) {
				$meta['image_src'] = '<link rel=image_src href="'.$page->image_src.'">';
				$meta['og:image'] = '<meta property="og:image" content="'.$page->image_src.'">';
			}
			$js = array(
				'<script>',
				'var MODULE_URL = '.(int)self::$cfg->module_url.';',
				'var FIRST_MODULE = "'.reset($module_list).'";',
				'var WEB_URL = "'.WEB_URL.'";'
			);
			if (!empty(self::$cfg->facebook_appId)) {
				$meta['og:app_id'] = '<meta property="fb:app_id" content="'.self::$cfg->facebook_appId.'">';
				$js[] = 'inintFacebook("'.self::$cfg->facebook_appId.'", "'.Language::name().'");';
			}
			if (isset($page->canonical)) {
				$meta['canonical'] = '<meta name=canonical content="'.$page->canonical.'">';
				$meta['og:url'] = '<meta property="og:url" content="'.$page->canonical.'">';
			}
			$js[] = '</script>';
			$meta['script'] = implode("\n", $js);
			Gcms::$view->setMetas($meta);
			// ภาษาที่ติดตั้ง
			$languages = Template::create('', '', 'language');
			foreach (self::$cfg->languages as $lng) {
				$languages->add(array(
					'/{LNG}/' => $lng
				));
			}
			// เนื้อหา
			Gcms::$view->setContents(array(
				// content
				'/{CONTENT}/' => $page->detail,
				// title
				'/{TITLE}/' => $web_title,
				// ภาษาที่ติดตั้ง
				'/{LANGUAGES}/' => $languages->render()
			));
		}
		// output เป็น HTML
		Gcms::$view->renderHTML();
	}
}