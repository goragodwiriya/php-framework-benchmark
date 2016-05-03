<?php
/*
 * @filesource document/controllers/admin/categorywrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Categorywrite;

use \Kotchasan\Login;
use \Gcms\Gcms;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * ฟอร์มสร้าง/แก้ไข หมวดหมู่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

	/**
	 * แสดงผล
	 */
	public function render()
	{
		// อ่านรายการที่เลือก
		$index = \Document\Admin\Categorywrite\Model::get(self::$request->get('mid')->toInt(), self::$request->get('id')->toInt());
		// login
		$login = Login::isMember();
		// สมาชิกและสามารถตั้งค่าได้
		if ($index && Gcms::canConfig($login, $index, 'can_config')) {
			// แสดงผล
			$section = Html::create('section');
			// breadcrumbs
			$breadcrumbs = $section->add('div', array(
				'class' => 'breadcrumbs'
			));
			$ul = $breadcrumbs->add('ul');
			$ul->appendChild('<li><span class="icon-documents">'.Language::get('Module').'</span></li>');
			$ul->appendChild('<li><a href="{BACKURL?module=document-settings&mid='.$index->module_id.'}">'.ucfirst($index->module).'</a></li>');
			$ul->appendChild('<li><a href="{BACKURL?module=document-category&mid='.$index->module_id.'}">'.Language::get('Category').'</a></li>');
			$ul->appendChild('<li><span>'.Language::get(empty($index->id) ? 'Create' : 'Edit').'</span></li>');
			$section->add('header', array(
				'innerHTML' => '<h1 class="icon-write">'.$this->title().'</h1>'
			));
			// แสดงฟอร์ม
			$section->appendChild(createClass('Document\Admin\Categorywrite\View')->render($index));
			return $section->render();
		} else {
			// 404.html
			return \Index\Error\Controller::page404();
		}
	}

	/**
	 * title bar
	 */
	public function title()
	{
		return Language::get('Create or Edit').' '.Language::get('Category');
	}
}