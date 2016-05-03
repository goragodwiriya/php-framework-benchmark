<?php
/*
 * @filesource board/models/admin/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Admin\Index;

use \Kotchasan\ArrayTool;

/**
 *  Model สำหรับอ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	/**
	 * อ่านข้อมูลโมดูล
	 *
	 * @param int $module_id
	 * @return object|null ข้อมูลโมดูล (Object) หรือ null หากไม่พบบ
	 */
	public static function module($module_id)
	{
		$model = new static;
		// ตรวจสอบโมดูลที่เรียก
		$index = $model->db()->createQuery()
			->select('id module_id', 'module', 'owner', 'config')
			->from('modules')
			->where(array(
				array('id', $module_id),
				array('owner', 'board')
			))
			->limit(1)
			->toArray()
			->execute();
		if (empty($index)) {
			return null;
		} else {
			// ค่าติดตั้งเริ่มต้น
			$default = array(
				'icon_width' => 600,
				'icon_height' => 400,
				'img_typies' => array('jpg'),
				'default_icon' => 'modules/board/img/default_icon.png',
				'list_per_page' => 20,
				'new_date' => 604800,
				'viewing' => 0,
				'category_display' => 1,
				'news_count' => 10,
				'img_upload_type' => array('jpg'),
				'img_upload_size' => 1024,
				'img_law' => 0,
				'can_post' => array(1),
				'can_reply' => array(1),
				'can_view' => array(1),
				'moderator' => array(1),
				'can_config' => array(1)
			);
			$default = ArrayTool::unserialize($index[0]['config'], $default);
			unset($index[0]['config']);
			$index = ArrayTool::merge($default, $index[0]);
			return (object)$index;
		}
	}
}