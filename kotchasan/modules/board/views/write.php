<?php
/*
 * @filesource board/views/view.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Write;

use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Text;

/**
 * ตั้งกระทู้
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

	public function index(Request $request, $index)
	{
		// หมวดที่เลือก
		$cat = $request->request('cat')->toInt();
		// login
		$login = $request->session('login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''))->all();
		// สมาชิก true
		$isMember = $login['status'] > -1;
		// หมวดหมู่
		$category_options = array();
		foreach (\Index\Category\Model::all($index->module_id) as $item) {
			if ($cat == $item->category_id) {
				$category_options[] = '<option value='.$item->category_id.'>'.$item->topic.'</option>';
			}
		}
		if (empty($category_options)) {
			$category_options[] = '<option value=0>{LNG_Uncategorized}</option>';
		}
		// antispam
		$antispamchar = Text::rndname(32);
		$_SESSION[$antispamchar] = Text::rndname(4);
		// template
		$template = Template::create($index->owner, $index->module, 'write');
		$template->add(array(
			'/{TOPIC}/' => $index->topic,
			'/{CATEGORIES}/' => implode('', $category_options),
			'/<MEMBER>(.*)<\/MEMBER>/s' => $isMember ? '' : '$1',
			'/<UPLOAD>(.*)<\/UPLOAD>/s' => empty($index->img_upload_type) ? '' : '$1',
			'/{MODULEID}/' => $index->module_id,
			'/{ANTISPAM}/' => isset($antispamchar) ? $antispamchar : '',
			'/{ANTISPAMVAL}/' => isset($antispamchar) && Login::isAdmin() ? $_SESSION[$antispamchar] : '',
			'/{LOGIN_PASSWORD}/' => $login['password'],
			'/{LOGIN_EMAIL}/' => $login['email']
		));
		Gcms::$view->setContents(array(
			'/:size/' => $index->img_upload_size,
			'/:type/' => implode(', ', $index->img_upload_type)
			), false);
		$topic = Language::get('Create topic');
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
			Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', $index->category_id), $index->topic, $index->topic);
		}
		// breadcrumb ของหน้า
		$index->canonical = Gcms::createUrl($index->module, 'write', $index->category_id);
		Gcms::$view->addBreadcrumb($index->canonical, $topic, $topic);
		// คืนค่า
		return $result;
	}
}