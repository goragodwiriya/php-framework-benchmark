<?php
/*
 * @filesource board/views/replyedit.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Replyedit;

use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Text;

/**
 * แก้ไขความคิดเห็น
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

	public function index(Request $request, $index)
	{
		// login
		$login = $request->session('login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''))->all();
		// สมาชิก true
		$isMember = $login['status'] > -1;
		// antispam
		$antispamchar = Text::rndname(32);
		$_SESSION[$antispamchar] = Text::rndname(4);
		// template
		$template = Template::create($index->owner, $index->module, 'replyedit');
		$template->add(array(
			'/{TOPIC}/' => $index->topic,
			'/{DETAIL}/' => $index->detail,
			'/<UPLOAD>(.*)<\/UPLOAD>/s' => empty($index->img_upload_type) ? '' : '$1',
			'/{MODULEID}/' => $index->module_id,
			'/{ANTISPAM}/' => isset($antispamchar) ? $antispamchar : '',
			'/{ANTISPAMVAL}/' => isset($antispamchar) && Login::isAdmin() ? $_SESSION[$antispamchar] : '',
			'/{QID}/' => $index->index_id,
			'/{RID}/' => $index->id
		));
		Gcms::$view->setContents(array(
			'/:size/' => $index->img_upload_size,
			'/:type/' => implode(', ', $index->img_upload_type)
			), false);
		$topic = Language::get('Edit').' '.Language::get('comments');
		$result = (object)array(
				'topic' => $topic.' - '.$index->topic,
				'detail' => $template->render(),
				'description' => $index->description,
				'keywords' => $index->keywords
		);
		// breadcrumb ของโมดูล
		if (!Gcms::isHome($index->module)) {
			$menu = Gcms::$menu->moduleMenu($index->module);
			if ($menu) {
				Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module), $menu->menu_text, $menu->menu_tooltip);
			}
		}
		// breadcrumb ของหมวดหมู่
		if (!empty($index->category_id)) {
			$category = Gcms::ser2Str($index->category);
			Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', $index->category_id), $category, $category);
		}
		// breadcrumb ของกระทู้
		Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', 0, 0, 'wbid='.$index->id), $index->topic, $index->topic);
		// breadcrumb ของหน้า
		$index->canonical = Gcms::createUrl($index->module, 'write', 0, $index->id);
		Gcms::$view->addBreadcrumb($index->canonical, '{LNG_Edit}', '{LNG_Edit}');
		// คืนค่า
		return $result;
	}
}