<?php
/*
 * @filesource document/controllers/tag.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Tag;

use \Gcms\Gcms;
use \Kotchasan\Http\Request;

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
	 * Controller หลักของโมดูล ใช้เพื่อตรวจสอบว่าจะเรียกหน้าไหนมาแสดงผล
	 *
	 * @param Object $module ข้อมูลโมดูลจาก database
	 * @return Object
	 */
	public function init(Request $request, $module)
	{
		// รายการที่เลือก
		$id = $request->get('id')->toInt();
		$document = $request->get('alias')->text();
		// ตรวจสอบโมดูลและอ่านข้อมูลโมดูล
		$index = \Document\Module\Model::get($request, $module);
		if (empty($index)) {
			// 404
			$list = createClass('Index\PageNotFound\View')->render();
		} elseif (!empty($document) || !empty($id)) {
			// หน้าแสดงบทความ
			$list = createClass('Document\View\View')->index($request, $index);
		} elseif (!empty($index->category_id) || empty($index->categories) || empty($index->category_display)) {
			// เลือกหมวดมา หรือไม่มีหมวด หรือปิดการแสดงผลหมวดหมู่ แสดงรายการบทความ
			$list = createClass('Document\Stories\View')->index($request, $index);
		} else {
			// หน้าแสดงรายการหมวดหมู่
			$list = createClass('Document\Categories\View')->index($request, $index);
		}
		return $list;
	}

	/**
	 * ฟังก์ชั่นสร้าง URL ของบทความ
	 *
	 * @param string $module
	 * @param string $alias
	 * @param int $id
	 * @return string
	 */
	public static function url($module, $alias, $id)
	{
		if (self::$cfg->module_url == 1) {
			return Gcms::createUrl($module, $alias);
		} else {
			return Gcms::createUrl($module, '', 0, $id);
		}
	}
}