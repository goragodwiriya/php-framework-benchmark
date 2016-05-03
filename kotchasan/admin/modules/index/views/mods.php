<?php
/*
 * @filesource index/views/mods.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Mods;

use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\Date;

/**
 * module=mods
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * ตารางโมดูลที่ติดตั้งแล้ว
	 *
	 * @return string
	 */
	public function render()
	{

		// Uri
		$uri = self::$request->getUri();
		$table = new DataTable(array(
			/* Model */
			'model' => 'Index\Mods\Model',
			'defaultFilters' => array(
				array('I.index', 1)
			),
			/* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
			'onRow' => array($this, 'onRow'),
			/* คอลัมน์ที่ไม่ต้องแสดงผล */
			'hideColumns' => array('module_id', 'id'),
			/* table action */
			'action' => 'index.php/index/model/mods/action',
			'actionCallback' => 'indexActionCallback',
			'actionConfirm' => 'confirmAction',
			/* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
			'headers' => array(
				'topic' => array(
					'text' => Language::get('Topic')
				),
				'published' => array(
					'text' => Language::get('Status'),
					'class' => 'center'
				),
				'language' => array(
					'text' => Language::get('Language'),
					'class' => 'center'
				),
				'module' => array(
					'text' => Language::get('module name')
				),
				'owner' => array(
					'text' => '&nbsp;'
				),
				'last_update' => array(
					'text' => Language::get('Last updated'),
					'class' => 'center'
				),
				'visited' => array(
					'text' => Language::get('Preview'),
					'class' => 'center'
				)
			),
			/* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
			'cols' => array(
				'published' => array(
					'class' => 'center'
				),
				'language' => array(
					'class' => 'center'
				),
				'last_update' => array(
					'class' => 'center'
				),
				'visited' => array(
					'class' => 'visited'
				)
			),
			/* ปุ่มแสดงในแต่ละแถว */
			'buttons' => array(
				'edit' => array(
					'class' => 'icon-edit button green',
					'href' => $uri->createBackUri(array('module' => 'pagewrite', 'id' => ':id')),
					'text' => Language::get('Edit')
				),
				'delete' => array(
					'class' => 'icon-delete button red',
					'id' => ':id',
					'text' => Language::get('Delete')
				)
			),
			/* ปุ่มเพิ่ม */
			'addNew' => array(
				'class' => 'button green icon-plus',
				'href' => $uri->createBackUri(array('module' => 'addmodule', 'id' => '0')),
				'text' => Language::get('Add New').' '.Language::get('Module')
			)
		));
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
		$publisheds = Language::get('PUBLISHEDS');
		$item['published'] = '<a id=published_'.$item['id'].' class="icon-published'.$item['published'].'" title="'.$publisheds[$item['published']].'"></a>';
		$item['last_update'] = Date::format($item['last_update'], 'd M Y H:i');
		$item['language'] = empty($item['language']) ? '' : '<img src="'.WEB_URL.'language/'.$item['language'].'.gif" alt="'.$item['language'].'">';
		return $item;
	}
}