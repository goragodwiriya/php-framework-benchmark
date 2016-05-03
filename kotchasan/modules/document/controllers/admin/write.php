<?php
/*
 * @filesource document/controllers/admin/write.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Write;

use \Kotchasan\Login;
use \Gcms\Gcms;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * ฟอร์มสร้าง/แก้ไข เนื้อหา
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
		// ค่าที่ส่งมา
		$id = self::$request->get('id')->toInt();
		$module_id = self::$request->get('mid')->toInt();
		$category_id = self::$request->get('cat')->toInt();
		// ตรวจสอบรายการที่เลือก
		$index = \Document\Admin\Write\Model::get($module_id, $id, $category_id);
		// login
		$login = Login::isMember();
		// สมาชิกและสามารถตั้งค่าได้
		if ($index && Gcms::canConfig($login, $index, 'can_write')) {
			// ภาษาที่ติดตั้ง
			$languages = Language::installedLanguage();
			if (!empty($index->id)) {
				$index->details = \Document\Admin\Write\Model::details((int)$index->module_id, (int)$index->id, reset($languages));
			}
			// แสดงผล
			$section = Html::create('section');
			// breadcrumbs
			$breadcrumbs = $section->add('div', array(
				'class' => 'breadcrumbs'
			));
			$ul = $breadcrumbs->add('ul');
			$ul->appendChild('<li><span class="icon-documents">'.Language::get('Module').'</span></li>');
			$ul->appendChild('<li><a href="{BACKURL?module=document-settings&mid='.$index->module_id.'}">'.ucfirst($index->module).'</a></li>');
			$ul->appendChild('<li><a href="{BACKURL?module=document-setup&mid='.$index->module_id.'}">'.Language::get('Contents').'</a></li>');
			$ul->appendChild('<li><span>'.Language::get(empty($index->id) ? 'Create' : 'Edit').'</span></li>');
			$header = $section->add('header', array(
				'innerHTML' => '<h1 class="icon-write">'.$this->title().'</h1>'
			));
			$inline = $header->add('div', array(
				'class' => 'inline'
			));
			$writetab = $inline->add('div', array(
				'class' => 'writetab'
			));
			$ul = $writetab->add('ul', array(
				'id' => 'accordient_menu'
			));
			foreach ($languages as $item) {
				$ul->add('li', array(
					'innerHTML' => '<a id=tab_detail_'.$item.' href="{BACKURL?module=document-write&qid='.$index->id.'&tab=detail_'.$item.'}">'.Language::get('Detail').'&nbsp;<img src='.WEB_URL.'language/'.$item.'.gif alt='.$item.'></a>'
				));
			}
			$ul->add('li', array(
				'innerHTML' => '<a id=tab_options href="{BACKURL?module=document-write&qid='.$index->id.'&tab=options}">'.Language::get('Other details').'</a>'
			));
			if (!$index) {
				$section->appendChild('<aside class=error>'.Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed').'</aside>');
			} else {
				$section->appendChild(createClass('Document\Admin\Write\View')->render($index));
			}
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
		return Language::get('Create or Edit').' '.Language::get('Document');
	}
}