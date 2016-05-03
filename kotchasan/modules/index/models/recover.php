<?php
/*
 * @filesource index/models/recover.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Recover;

use \Kotchasan\Http\Request;
use \Kotchasan\Text;
use \Kotchasan\Language;
use \Kotchasan\Email;

/**
 * ขอรหัสผ่านใหม่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	/**
	 * บันทึก
	 */
	public function save(Request $request)
	{
		// referer, session
		if ($request->isReferer() && $request->inintSession()) {
			$ret = array();
			// ค่าที่ส่งมา
			$email = $request->post('email')->url();
			if ($email === '') {
				$ret['ret_email'] = Language::get('Please fill out this form');
			} else {
				$or = $this->groupOr(array('email', $email), array('phone1', $email));
				$search = $this->db()->createQuery()
					->from('user')
					->where(array($or, array('fb', '0')))
					->toArray()
					->first('id', 'email');
				if ($search === false) {
					$ret['ret_email'] = Language::get('not a registered user');
				}
			}
			if (empty($ret)) {
				// รหัสผ่านใหม่
				$password = Text::rndname(6);
				// ข้อมูลอีเมล์
				$replace = array(
					'/%PASSWORD%/' => $password,
					'/%EMAIL%/' => $search['email']
				);
				// send mail
				$err = Email::send(3, 'member', $replace, $search['email']);
				if (empty($err)) {
					// อัปเดทรหัสผ่านใหม่
					$save = array('password' => md5($password.$search['email']));
					$this->db()->createQuery()->update('user')->set($save)->where($search['id'])->execute();
					// คืนค่า
					$ret['alert'] = Language::get('Your message was sent successfully');
					$ret['ret_email'] = '';
					$ret['location'] = $request->post('modal')->toString() === 'true' ? 'close' : WEB_URL.'index.php?module=dologin';
				} else {
					$ret['ret_email'] = $err;
				}
			} else {
				$ret['input'] = 'email';
			}
			// คืนค่าเป็น JSON
			echo json_encode($ret);
		}
	}
}