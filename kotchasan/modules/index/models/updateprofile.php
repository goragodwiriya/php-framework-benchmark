<?php
/*
 * @filesource index/models/updateprofile.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Updateprofile;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\File;
use \Kotchasan\Http\Request;

/**
 * บันทึกข้อมูลสมาชิก
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
		$ret = array();
		// referer, session, member
		if ($request->isReferer() && $request->inintSession() && $login = Login::isMember()) {
			if ($login['email'] == 'demo') {
				$ret['alert'] = Language::get('Unable to complete the transaction');
			} else {
				// รับค่าจากการ POST
				$save = array();
				foreach ($request->getParsedBody() as $key => $value) {
					switch ($key) {
						case 'phone1':
						case 'phone2':
						case 'provinceID':
						case 'zipcode':
							$save[$key] = $request->post($key)->number();
							break;
						case 'displayname':
						case 'sex':
						case 'fname':
						case 'lname':
						case 'address1':
						case 'address2':
						case 'province':
						case 'country':
							$save[$key] = $request->post($key)->text();
							break;
						case 'website':
							$save['website'] = str_replace(array('http://', 'https://', 'ftp://'), array('', '', ''), $request->post($key)->url());
							break;
						case 'subscrib':
							$save[$key] = $request->post($key)->toBoolean();
							break;
						case 'birthday':
							$save[$key] = $request->post($key)->date();
							break;
						case 'password':
						case 'repassword':
							$$key = $request->post($key)->text();
							break;
					}
				}
				// ชื่อตาราง user
				$user_table = $this->tableWithPrefix('user');
				// database connection
				$db = $this->db();
				// ตรวจสอบค่าที่ส่งมา
				$user = $db->first($user_table, $request->post('id')->toInt());
				if (!$user) {
					// ไม่พบสมาชิกที่แก้ไข
					$ret['alert'] = Language::get('not a registered user');
				} else {
					$input = false;
					// ชื่อเรียก
					if (!empty($save['displayname'])) {
						if (mb_strlen($save['displayname']) < 2) {
							$ret['ret_displayname'] = Language::get('Name for the show on the site at least 2 characters');
							$input = !$input ? 'displayname' : $input;
						} elseif (in_array($save['displayname'], self::$cfg->member_reserv)) {
							$ret['ret_displayname'] = Language::get('Invalid name');
							$input = !$input ? 'displayname' : $input;
						} else {
							// ตรวจสอบ ชื่อเรียก
							$search = $db->first($user_table, array('displayname', $save['displayname']));
							if ($search !== false && $user->id != $search->id) {
								$ret['ret_displayname'] = str_replace(':name', Language::get('Name'), Language::get('This :name is already registered'));
								$input = !$input ? 'displayname' : $input;
							} else {
								$ret['ret_displayname'] = '';
							}
						}
						$save['subscrib'] = $request->post('subscrib')->toBoolean();
					}
					// โทรศัพท์
					if (!empty($save['phone1'])) {
						if (!preg_match('/[0-9]{10,10}/', $save['phone1'])) {
							$ret['ret_phone1'] = str_replace(':name', Language::get('phone number'), Language::get('Invalid :name'));
							$input = !$input ? 'phone1' : $input;
						} else {
							// ตรวจสอบโทรศัพท์
							$search = $db->first($user_table, array('phone1', $save['phone1']));
							if ($search !== false && $user->id != $search->id) {
								$ret['ret_phone1'] = str_replace(':name', Language::get('phone number'), Language::get('This :name is already registered'));
								$input = !$input ? 'phone1' : $input;
							} else {
								$ret['ret_phone1'] = '';
							}
						}
					}
					// แก้ไขรหัสผ่าน
					if ($user->fb == 0 && (!empty($password) || !empty($repassword))) {
						if (mb_strlen($password) < 4) {
							// รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
							$ret['ret_password'] = Language::get('Passwords must be at least four characters');
							$input = !$input ? 'password' : $input;
						} elseif ($repassword != $password) {
							// ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน
							$ret['ret_repassword'] = Language::get('To change your password, enter your password to match the two inputs');
							$input = !$input ? 'repassword' : $input;
						} else {
							// password ใหม่ถูกต้อง
							$save['password'] = md5($password.$user->email);
							// เปลี่ยน password ที่ login ใหม่
							$_SESSION['login']['password'] = $password;
							$ret['ret_password'] = '';
							$ret['ret_repassword'] = '';
						}
					}
					// อัปโหลดไฟล์
					foreach ($request->getUploadedFiles() as $item => $file) {
						if ($file->hasUploadFile()) {
							if (!File::makeDirectory(ROOT_PATH.self::$cfg->usericon_folder)) {
								// ไดเรคทอรี่ไม่สามารถสร้างได้
								$ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), self::$cfg->usericon_folder);
								$input = !$input ? $item : $input;
							} else {
								if (!empty($user->icon)) {
									// ลบไฟล์เดิม
									@unlink(ROOT_PATH.self::$cfg->usericon_folder.$user->icon);
								}
								try {
									// อัปโหลด thumbnail
									$save['icon'] = $user->id.'.jpg';
									$file->cropImage(self::$cfg->user_icon_typies, ROOT_PATH.self::$cfg->usericon_folder.$save['icon'], self::$cfg->user_icon_w, self::$cfg->user_icon_h);
								} catch (\Exception $exc) {
									// ไม่สามารถอัปโหลดได้
									$ret['ret_'.$item] = Language::get($exc->getMessage());
									$input = !$input ? $item : $input;
								}
							}
						}
					}
					if (!empty($save)) {
						if (!$input) {
							// save
							$db->update($user_table, $user->id, $save);
							// คืนค่า
							$ret['alert'] = Language::get('Saved successfully');
							$ret['location'] = 'reload';
						} else {
							// error
							$ret['input'] = $input;
						}
					}
				}
			}
		} else {
			$ret['alert'] = Language::get('Unable to complete the transaction');
		}
		// คืนค่าเป็น JSON
		if (!empty($ret)) {
			echo json_encode($ret);
		}
	}
}