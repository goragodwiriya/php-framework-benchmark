<?php
/*
 * @filesource board/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Index;

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
		// รายการที่เลือก (wbid หรือ id)
		$id = $request->request('wbid', $request->request('id')->toInt())->toInt();
		// ตรวจสอบโมดูลและอ่านข้อมูลโมดูล
		$index = \Board\Module\Model::get($request, $module);
		if (empty($index)) {
			// 404
			$list = createClass('Index\PageNotFound\View')->render();
		} elseif (!empty($id)) {
			// หน้าแสดงกระทู้
			$index->id = $id;
			$list = createClass('Board\View\View')->index($request, $index);
		} elseif (!empty($index->category_id) || empty($index->categories) || empty($index->category_display)) {
			// เลือกหมวดมา หรือไม่มีหมวด หรือปิดการแสดงผลหมวดหมู่ แสดงรายการกระทู้
			$list = createClass('Board\Stories\View')->index($request, $index);
		} else {
			// หน้าแสดงรายการหมวดหมู่
			$list = createClass('Board\Categories\View')->index($request, $index);
		}
		return $list;
	}

	/**
	 * ฟังก์ชั่นสร้าง URL ของบทความ
	 *
	 * @param string $module
	 * @param int $cat
	 * @param int $id
	 * @return string
	 */
	public static function url($module, $cat, $id)
	{
		return Gcms::createUrl($module, '', $cat, 0, 'wbid='.$id);
	}
}