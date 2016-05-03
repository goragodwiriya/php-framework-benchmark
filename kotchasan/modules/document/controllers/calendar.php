<?php
/*
 * @filesource document/controllers/calendar.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Calendar;

use \Kotchasan\Date;
use \Gcms\Gcms;

/**
 *  Model สำหรับอ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

	/**
	 * ฟังก์ชั่นอ่านข้อมูลสำหรับการแสดงบนปฏิทิน
	 *
	 * @param array $settings ค่ากำหนดของปฎิทิน
	 * @param int $first_date วันที่ 1 (mktime)
	 * @param int $first_next_month วันที่ 1 ของเดือนถัดไป (mktime)
	 * @return array
	 */
	public function calendar($settings, $first_date, $first_next_month)
	{
		return createClass('Document\Calendar\Model')->calendar($settings, $first_date, $first_next_month);
	}

	/**
	 * ฟังก์ชั่นเรียมาจากปฏิทินเพื่อแสดงทูลทิป
	 *
	 * @param array $query_string ค่าที่ส่งมาจาก tooltip
	 * @param array $settings ค่ากำหนด
	 */
	public function tooltip($query_string, $settings)
	{
		if (preg_match('/^calendar\-([0-9]+){0,2}\-([0-9]+){0,2}\-([0-9]+){0,4}\-([0-9_]+)$/', $query_string['id'], $match)) {
			$ids = array();
			foreach (explode('_', $match[4]) as $id) {
				$ids[] = (int)$id;
			}
			if (!empty($ids)) {
				echo '<div id=calendar-tooltip>';
				echo '<h5>'.Date::format("$match[3]-$match[2]-$match[1]", 'd M Y').'</h5>';
				foreach (createClass('Document\Calendar\Model')->tooltip($ids, $settings) as $item) {
					echo '<a href="'.WEB_URL.'/index.php?module='.$item['module'].'&amp;id='.$item['id'].'" title="'.$item['description'].'">'.$item['topic'].'</a>';
				}
				echo '</div>';
			}
		}
	}

	/**
	 * สร้าง URL สำหรับการแสดงรายการภสยในวันที่เลือก
	 *
	 * @param string $d วันที่
	 * @return string
	 */
	public function url($d)
	{
		return Gcms::createUrl('document', '', 0, 0, 'd='.$d);
	}
}