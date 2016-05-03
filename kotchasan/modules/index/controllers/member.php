<?php
/*
 * @filesource index/controllers/member.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Member;

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
	 * แสดงผลฟอร์ม ที่เรียกมาจาก GModal
	 */
	public function modal($query_string)
	{
		if ($query_string['action'] == 'register') {
			$page = createClass('Index\Member\View')->register(true);
		} elseif ($query_string['action'] == 'forgot') {
			$page = createClass('Index\Member\View')->forgot(true);
		}
		echo json_encode($page);
	}

	public function editprofile(Request $request)
	{
		return createClass('Index\Editprofile\View')->render($request);
	}

	public function sendmail(Request $request)
	{
		return createClass('Index\Sendmail\View')->render($request);
	}

	public function register()
	{
		return createClass('Index\Member\View')->register(false);
	}

	public function forgot()
	{
		return createClass('Index\Member\View')->forgot();
	}

	public function dologin()
	{
		return createClass('Index\Member\View')->dologin();
	}
}