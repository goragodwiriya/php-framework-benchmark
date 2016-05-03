<?php
/*
 * @filesource index/models/sendmail.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Sendmail;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Validator;
use \Kotchasan\Email;
use \Kotchasan\Http\Request;

/**
 * อ่าน/บันทึก ข้อมูลหน้าเพจ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	/**
	 * ลิสต์รายชื่ออีเมล์ของแอดมิน
	 */
	public static function findAdmin(Request $request)
	{
		$model = new static($request);
		$result = array();
		foreach ($model->db()->select($model->tableWithPrefix('user'), array('status', 1), array('email')) as $item) {
			$result[] = $item['email'];
		}
		return $result;
	}

	/**
	 * ฟังก์ชั่นส่งอีเมล์
	 */
	public function save()
	{
		$ret = array();
		// referer, session, member
		if (self::$request->isReferer() && self::$request->inintSession() && $login = Login::isMember()) {
			if ($login['email'] == 'demo') {
				$ret['alert'] = Language::get('Unable to complete the transaction');
			} else {
				// รับค่าจากการ POST
				$save = array(
					'reciever' => self::$request->post('reciever')->toString(),
					'from' => self::$request->post('from')->toString(),
					'subject' => self::$request->post('subject')->topic(),
					'detail' => self::$request->post('detail')->toString()
				);
				// ตรวจสอบค่าที่ส่งมา
				$input = false;
				// reciever
				if (!empty($save['reciever'])) {
					foreach (explode(',', $save['reciever']) as $item) {
						if (!Validator::email($item)) {
							if (!$input) {
								$input = 'reciever';
								break;
							}
						}
					}
				} else {
					$ret['reciever'] = '';
				}
				// subject
				if (empty($save['subject'])) {
					$input = !$input ? 'subject' : $input;
				} else {
					$ret['ret_subject'] = '';
				}
				// from
				if (Login::isAdmin()) {
					if ($save['from'] == self::$cfg->noreply_email) {
						$save['from'] = self::$cfg->noreply_email.'<'.strip_tags(self::$cfg->web_title).'>';
					} else {
						$user = $this->db()->first($this->tableWithPrefix('user'), array('email', $save['from']));
						if (!$user) {
							// ไม่พบผู้ส่ง ให้ส่งโดยตัวเอง
							$save['from'] = $login['email'];
						} else {
							$save['from'] = $user->email.(empty($user->displayname) ? '' : '<'.$user->displayname.'>');
						}
					}
				} else {
					// ไม่ใช่แอดมิน ผู้ส่งเป็นตัวเองเท่านั้น
					$save['from'] = $login['email'];
				}
				// detail
				$patt = array(
					'/^(&nbsp;|\s){0,}<br[\s\/]+?>(&nbsp;|\s){0,}$/iu' => '',
					'/<\?(.*?)\?>/su' => '',
					'@<script[^>]*?>.*?</script>@siu' => ''
				);
				$save['detail'] = trim(preg_replace(array_keys($patt), array_values($patt), $save['detail']));
				if (!$input) {
					$err = Email::custom($save['reciever'], $save['from'], $save['subject'], $save['detail']);
					if (empty($err)) {
						// ส่งอีเมล์สำเร็จ
						$ret['alert'] = Language::get('Your message was sent successfully');
						$ret['location'] = self::$request->getUri()->postBack('index.php', array('id' => 0));
					} else {
						// ข้อผิดพลาดการส่งอีเมล์
						$ret['alert'] = $err;
					}
				} else {
					// คืนค่า input ตัวแรกที่ error
					$ret['input'] = $input;
				}
			}
		} else {
			$ret['alert'] = Language::get('Unable to complete the transaction');
		}
		// คืนค่าเป็น JSON
		echo json_encode($ret);
	}
}