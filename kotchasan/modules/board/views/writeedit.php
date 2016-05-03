<?php
/*
 * @filesource board/views/writeedit.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Writeedit;

use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Text;

/**
 * แก้ไขกระทู้
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
		// หมวดหมู่
		$categories = array();
		$category_options = array();
		foreach (\Index\Category\Model::all($index->module_id) as $item) {
			$categories[$item->category_id] = $item->topic;
			if (Login::isAdmin() || $index->category_id == $item->category_id) {
				$sel = $index->category_id == $item->category_id ? ' selected' : '';
				$category_options[] = '<option value='.$item->category_id.$sel.'>'.$item->topic.'</option>';
			}
		}
		if (empty($category_options)) {
			$category_options[] = '<option value=0>{LNG_Uncategorized}</option>';
		}
		// antispam
		$antispamchar = Text::rndname(32);
		$_SESSION[$antispamchar] = Text::rndname(4);
		// วันที่ของบอร์ด
		preg_match('/([0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2})\s([0-9]+):([0-9]+)/', date('Y-m-d H:i', $index->create_date), $match);
		// hour
		$hour = array();
		for ($i = 0; $i < 24; $i++) {
			$d = sprintf('%02d', $i);
			$sel = $d == $match[2] ? ' selected' : '';
			$hour[] = '<option value='.$d.$sel.'>'.$d.'</option>';
		}
		// minute
		$minute = array();
		for ($i = 0; $i < 60; $i++) {
			$d = sprintf('%02d', $i);
			$sel = $d == $match[3] ? ' selected' : '';
			$minute[] = '<option value='.$d.$sel.'>'.$d.'</option>';
		}
		// template
		$template = Template::create($index->owner, $index->module, 'writeedit');
		$template->add(array(
			'/{TOPIC}/' => $index->topic,
			'/{DETAIL}/' => $index->detail,
			'/{CATEGORIES}/' => implode('', $category_options),
			'/<MODERATOR>(.*)<\/MODERATOR>/s' => Gcms::canConfig($login, $index, 'moderator') ? '$1' : '',
			'/<UPLOAD>(.*)<\/UPLOAD>/s' => empty($index->img_upload_type) ? '' : '$1',
			'/{DATE}/' => $match[1],
			'/{HOUR}/' => implode('', $hour),
			'/{MINUTE}/' => implode('', $minute),
			'/{MODULEID}/' => $index->module_id,
			'/{ANTISPAM}/' => isset($antispamchar) ? $antispamchar : '',
			'/{ANTISPAMVAL}/' => isset($antispamchar) && Login::isAdmin() ? $_SESSION[$antispamchar] : '',
			'/{QID}/' => $index->id
		));
		Gcms::$view->setContents(array(
			'/:size/' => $index->img_upload_size,
			'/:type/' => implode(', ', $index->img_upload_type)
			), false);
		$topic = Language::get('Edit').' '.Language::get('Posted');
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
			Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', $index->category_id), $categories[$index->category_id], $categories[$index->category_id]);
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