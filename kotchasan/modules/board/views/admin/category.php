<?php
/*
 * @filesource board/views/admin/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Admin\Category;

use \Kotchasan\Language;
use \Kotchasan\DataTable;

/**
 * แสดงรายการหมวดหมู่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{
	/**
	 * ข้อมูลโมดูล
	 *
	 * @var object
	 */
	private $img_law;

	/**
	 * module=board-category
	 *
	 * @param object $index
	 * @return string
	 */
	public function render($index)
	{
		$this->img_law = Language::get('IMG_LAW');
		// Uri
		$uri = self::$request->getUri();
		// ตาราง
		$table = new DataTable(array(
			/* ข้อมูลใส่ลงในตาราง */
			'datas' => \Index\Category\Model::categories((int)$index->module_id),
			/* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
			'onRow' => array($this, 'onRow'),
			/* คอลัมน์ที่ไม่ต้องแสดงผล */
			'hideColumns' => array('module_id', 'id', 'group_id', 'icon', 'published', 'img_upload_type', 'can_post', 'can_view', 'moderator', 'img_law'),
			/* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
			'action' => 'index.php/index/model/category/action?mid='.$index->module_id,
			'actionCallback' => 'indexActionCallback',
			'actionConfirm' => 'confirmAction',
			'actions' => array(
				array(
					'id' => 'action',
					'class' => 'ok',
					'text' => Language::get('With selected'),
					'options' => array(
						'delete' => Language::get('Delete')
					)
				)
			),
			/* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
			'headers' => array(
				'topic' => array(
					'text' => Language::get('Category')
				),
				'category_id' => array(
					'text' => Language::get('ID')
				),
				'detail' => array(
					'text' => Language::get('Description')
				),
				'can_reply' => array(
					'text' => Language::get('Config'),
					'class' => 'center'
				),
				'img_upload_size' => array(
					'text' => Language::get('Upload'),
					'class' => 'center'
				),
				'c1' => array(
					'text' => Language::get('Posted'),
					'class' => 'center'
				),
				'c2' => array(
					'text' => Language::get('comments'),
					'class' => 'center'
				)
			),
			/* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
			'cols' => array(
				'published' => array(
					'class' => 'center'
				),
				'img_upload_size' => array(
					'class' => 'center'
				),
				'can_reply' => array(
					'class' => 'center'
				),
				'img_law' => array(
					'class' => 'center'
				),
				'c1' => array(
					'class' => 'visited center'
				),
				'c2' => array(
					'class' => 'comment center'
				)
			),
			/* ปุ่มแสดงในแต่ละแถว */
			'buttons' => array(
				'edit' => array(
					'class' => 'icon-edit button green',
					'href' => $uri->createBackUri(array('module' => 'board-categorywrite', 'mid' => $index->module_id, 'id' => ':id')),
					'text' => Language::get('Edit')
				)
			),
			/* ปุ่มเพิ่ม */
			'addNew' => array(
				'class' => 'button green icon-plus',
				'href' => $uri->createBackUri(array('module' => 'board-categorywrite', 'mid' => $index->module_id)),
				'text' => Language::get('Add New').' '.Language::get('Category')
			)
		));
		$table->script('inintListCategory("index");');
		return $table->render();
	}

	/**
	 * จัดรูปแบบการแสดงผลในแต่ละแถว
	 *
	 * @param array $item
	 * @return array
	 */
	public function onRow($item)
	{
		$item['topic'] = $this->unserialize($item['topic']);
		$item['detail'] = $this->unserialize($item['detail']);
		$item['category_id'] = '<label><input type=text class=number size=5 id=categoryid_'.$item['module_id'].'_'.$item['id'].' value="'.$item['category_id'].'" title="'.Language::get('Edit').'"></label>';
		$icons = array(
			'<span class="icon-newtopic" title="{LNG_Posting} '.$this->cfgToStr($item['can_post']).'"></span>',
			'<span class="icon-chat reply1" title="{LNG_Comment} '.$this->cfgToStr($item['can_reply']).'"></span>',
			'<span class="icon-visited color-red" title="{LNG_Viewing} '.$this->cfgToStr($item['can_view']).'"></span>',
			'<span class="icon-customer color-blue" title="{LNG_Moderator} '.$this->cfgToStr($item['moderator']).'"></span>'
		);
		$item['can_reply'] = '<span class=nowrap>'.implode(' ', $icons).'</span>';
		$item['img_upload_size'] = empty($item['img_upload_type']) ? '' : '{LNG_Type} <b>'.implode(', ', $item['img_upload_type']).'</b> {LNG_File size} <b>'.$item['img_upload_size'].' Kb.</b> '.(isset($this->img_law[$item['img_law']]) ? $this->img_law[$item['img_law']] : '');
		return $item;
	}

	private function cfgToStr($cfg)
	{
		$ret = array();
		foreach ($cfg as $item) {
			if ($item == -1) {
				$ret[] = '{LNG_Guest}';
			} else {
				$ret[] = self::$cfg->member_status[$item];
			}
		}
		return implode(', ', $ret);
	}

	/**
	 * เตรียมข้อมูล topic, detail สำหรับใส่ลงในตาราง
	 *
	 * @param array $item
	 * @return string
	 */
	private function unserialize($item)
	{
		$datas = array();
		foreach (unserialize($item) as $lng => $value) {
			$datas[$lng] = empty($lng) ? $value : '<p style="background:0 50% url('.WEB_URL.'language/'.$lng.'.gif) no-repeat;padding-left:21px;">'.$value.'</p>';
		}
		return implode('', $datas);
	}
}