<?php
/*
 * @filesource index/models/usericon.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Usericon;

use \Kotchasan\Http\Request;

/**
 * คลาสสำหรับแสดงรูปภาพสมาชิกจาก id
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	public function index(Request $request)
	{
		if ($request->isReferer()) {
			$user = $this->db()->createQuery()
				->from('user')
				->where($request->get('id')->toInt())
				->cacheOn()
				->toArray()
				->first('icon');
			if ($user) {
				if (!empty($user['icon']) && is_file(ROOT_PATH.self::$cfg->usericon_folder.$user['icon'])) {
					$icon = ROOT_PATH.self::$cfg->usericon_folder.$user['icon'];
				}
			}
			if (empty($icon)) {
				$icon = ROOT_PATH.'skin/img/noicon.jpg';
			}
			// ตรวจสอบรูป
			$info = getImageSize($icon);
			if (empty($info['error'])) {
				// cache 1 day
				header('Pragma: public');
				header('Cache-Control: max-age=86400');
				header('Expires: '.gmdate('D, d M Y H:i:s GMT', time() + 86400));
				// image header
				header("Content-type: $info[mime]");
				echo file_get_contents($icon);
			}
		}
	}
}