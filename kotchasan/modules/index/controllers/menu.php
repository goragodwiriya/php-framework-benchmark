<?php
/*
 * @filesource index/controllers/menu.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Menu;

/**
 * Controller สำหรับจัดการเมนู
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{
	/**
	 * Model
	 *
	 * @var \Index\Menu\Model
	 */
	private $menu;

	/**
	 * create Menus
	 *
	 * @return \static
	 */
	public static function create()
	{
		$obj = new static;
		$obj->menu = \Index\Menu\Model::create();
		return $obj;
	}

	/**
	 * อ่านรายการเมนูทั้งหมด
	 *
	 * @return object
	 */
	public function getMenus()
	{
		return $this->menu->getMenus();
	}

	/**
	 * อ่านเมนูรายการแรกสุด (หน้าหลัก)
	 *
	 * @return array|bool แอเรย์ของเมนูรายการแรก ถ้าไม่พบคืนค่า false
	 */
	public function homeMenu()
	{
		$menus = $this->menu->get('MAINMENU');
		if (isset($menus['toplevel'][0])) {
			$menu = $menus['toplevel'][0];
		} else {
			$menu = false;
		}
		return $menu;
	}

	/**
	 * อ่านเมนู (MAINMENU) ของโมดูล
	 *
	 * @param string $module ชื่อโมดูลที่ต้องการ
	 *
	 * @return array รายการเมนูของเมนูที่เลือก ถ้าไม่พบคืนค่าแอเรย์ว่าง
	 */
	public function moduleMenu($module)
	{
		$result = array();
		$menus = $this->menu->get('MAINMENU');
		if (isset($menus['toplevel'])) {
			foreach ($menus['toplevel'] as $item) {
				if ($item->module == $module) {
					$result = $item;
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * สร้างเมนูตามตำแหน่งของเมนู (parent)
	 *
	 * @return array รายการเมนูทั้งหมด
	 */
	public function render()
	{
		$view = new \Index\Menu\View;
		$result = array();
		foreach ($this->menu->getMenusByPos() AS $parent => $items) {
			if ($parent != '') {
				$result['/{'.$parent.'}/'] = $view->render($items);
			}
		}
		return $result;
	}
}