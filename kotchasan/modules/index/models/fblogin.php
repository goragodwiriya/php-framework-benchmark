<?php
/*
 * @filesource index/models/fblogin.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Fblogin;

use \Kotchasan\Http\Request;
use \Kotchasan\Text;
use \Kotchasan\Language;

/**
 * Facebook Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	public function chklogin(Request $request)
	{
		$data = $request->post('data')->toString();
		if (!empty($data) && $request->inintSession()) {
			// สุ่มรหัสผ่านใหม่
			$login_password = Text::rndname(6);
			// ข้อมูลที่ส่งมา
			$facebook_data = array('email' => '');
			foreach (explode('&', $data) AS $item) {
				list($k, $v) = explode('=', $item);
				if ($k === 'gender') {
					$facebook_data['sex'] = $v === 'male' ? 'm' : 'f';
				} elseif ($k === 'link') {
					$facebook_data['website'] = str_replace(array('http://', 'https://', 'www.'), '', $v);
				} elseif ($k === 'first_name') {
					$facebook_data['fname'] = $v;
				} elseif ($k === 'last_name') {
					$facebook_data['lname'] = $v;
				} elseif ($k === 'name') {
					$facebook_data['displayname'] = $v;
				} elseif ($k === 'email') {
					$facebook_data['email'] = $v;
				} elseif ($k === 'birthday') {
					if (preg_match('/^([0-9]+)[\/\-]([0-9]+)[\/\-]([0-9]+)$/', $v, $match)) {
						$facebook_data['birthday'] = "$match[3]-$match[1]-$match[2]";
					}
				} elseif ($k === 'id') {
					$fb_id = $v;
				}
			}
			if (empty($facebook_data['email'])) {
				// อีเมล์ว่างเปล่า
				$save = false;
				$ret['alert'] = Language::get('Can not log in, because e-mail blank');
				$ret['isMember'] = 0;
			} else {
				// db
				$db = $this->db();
				// table
				$user_table = $this->tableWithPrefix('user');
				// ตรวจสอบสมาชิกกับ db
				$search = $db->createQuery()
					->from('user')
					->where(array('email', $facebook_data['email']))
					->toArray()
					->first('id', 'email', 'visited', 'fb');
				if ($search === false) {
					// ยังไม่เคยลงทะเบียน, ลงทะเบียนใหม่
					$save = $facebook_data;
					$save['id'] = 1 + $db->lastId($user_table);
					$save['fb'] = 1;
					$save['subscrib'] = 1;
					$save['visited'] = 1;
					$save['ip'] = $request->getClientIp();
					$save['password'] = md5($login_password.$save['email']);
					$save['lastvisited'] = time();
					$save['create_date'] = $save['lastvisited'];
					$save['icon'] = $save['id'].'.jpg';
					$save['country'] = 'TH';
					$db->insert($user_table, $save);
				} elseif ($search['fb'] == 1) {
					// facebook เคยเยี่ยมชมแล้ว อัปเดทการเยี่ยมชม
					$save = $search;
					$save['visited'] ++;
					$save['lastvisited'] = time();
					$save['ip'] = $request->getClientIp();
					$save['password'] = md5($login_password.$search['email']);
					$save['icon'] = $search['id'].'.jpg';
					$db->update($user_table, $save['id'], $save);
				} else {
					// ไม่สามารถ login ได้ เนื่องจากมี email อยู่ก่อนแล้ว
					$save = false;
					$ret['alert'] = str_replace(':name', Language::get('Email'), Language::get('This :name is already registered'));
					$ret['isMember'] = 0;
				}
				if (is_array($save)) {
					// อัปเดท icon สมาชิก
					$data = @file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture');
					if ($data) {
						$f = @fopen(ROOT_PATH.self::$cfg->usericon_folder.$save['icon'], 'wb');
						if ($f) {
							fwrite($f, $data);
							fclose($f);
						}
					}
					// login
					$save['password'] = $login_password;
					$_SESSION['login'] = $save;
					// reload
					$ret['isMember'] = 1;
					$u = $request->post('u')->toString();
					if (preg_match('/module=(do)?login/', $u) || preg_match('/(do)?login\.html/', $u)) {
						$ret['location'] = 'back';
					} else {
						$ret['location'] = 'reload';
					}
				}
			}
			// คืนค่าเป็น json
			echo json_encode($ret);
		}
	}
}