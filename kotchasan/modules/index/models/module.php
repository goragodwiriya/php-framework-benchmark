<?php
/*
 * @filesource index/models/module.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Module;

use \Kotchasan\ArrayTool;

/**
 * คลาสสำหรับโหลดรายการโมดูลที่ติดตั้งแล้วทั้งหมด จากฐานข้อมูลของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	/**
	 * อ่านรายชื่อโมดูลทั้งหมดที่ติดตั้งแล้ว
	 *
	 * @return array
	 */
	public static function getModules()
	{
		if (defined('MAIN_INIT')) {
			$result = array();
			$model = new static;
			$query = $model->db()->createQuery()
				->select('id module_id', 'module', 'owner', 'config')
				->from('modules')
				->cacheOn()
				->toArray();
			foreach ($query->execute() as $item) {
				if (!empty($item['config'])) {
					$config = @unserialize($item['config']);
					if (is_array($config)) {
						foreach ($config as $key => $value) {
							$item[$key] = $value;
						}
					}
				}
				unset($item['config']);
				$result[] = (object)$item;
			}
			return $result;
		} else {
			// เรียก method โดยตรง
			\Kotchasan\Error::send('Do not call method directly');
		}
	}

	/**
	 * ฟังก์ชั่นอ่านข้อมูลโมดูล
	 *
	 * @param int $id
	 * @return object|false คืนค่าข้อมูลโมดูล (Object) ไม่พบคืนค่า false
	 */
	public static function getModule($id)
	{
		if (is_int($id) && $id > 0) {
			$model = new static;
			$module = $model->db()->createQuery()
				->from('modules')
				->where($id)
				->toArray()
				->cacheOn()
				->first();
			if ($module) {
				$module = ArrayTool::unserialize($module['config'], $module);
				unset($module['config']);
			}
		}
		return empty($module) ? false : (object)$module;
	}
}