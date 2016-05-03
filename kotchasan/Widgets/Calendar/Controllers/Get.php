<?php
/*
 * @filesource Widgets/Calendar/Controllers/Get.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Calendar\Controllers;

/**
 * แสดงปฎิทิน (Ajax called)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Get extends \Kotchasan\Controller
{

	/**
	 * แสดงปฎิทิน
	 */
	public function render($query_string)
	{
		// settings
		$settings = include ROOT_PATH.'Widgets/Calendar/settings.php';
		// render calendar
		if (method_exists('Widgets\Calendar\Controllers\Calendar', 'render')) {
			echo createClass('Widgets\Calendar\Controllers\Calendar')->render($query_string, $settings);
		}
	}
}