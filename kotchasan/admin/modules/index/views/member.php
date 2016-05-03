<?php
/*
 * @filesource index/views/member.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Member;

use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\Date;

/**
 * module=member
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * ตารางรายชื่อสมาชิก
	 *
	 * @return string
	 */
	public function render()
	{
		// สถานะสมาชิก
		$member_status = array(-1 => Language::get('all items'));
		foreach (self::$cfg->member_status as $key => $value) {
			$member_status[$key] = $value;
		}
		// ตารางสมาชิก
		$table = new DataTable(array(
			'model' => 'Index\Member\Model',
			'perPage' => self::$request->cookie('member_perPage', 30)->toInt(),
			'sort' => self::$request->cookie('member_sort', 'id')->toString(),
			'sortType' => self::$request->cookie('member_sortType', 'desc')->toString(),
			'onRow' => array($this, 'onRow'),
			/* คอลัมน์ที่ไม่ต้องแสดงผล */
			'hideColumns' => array('visited', 'status', 'admin_access', 'activatecode', 'ban'),
			/* คอลัมน์ที่สามารถค้นหาได้ */
			'searchColumns' => array('fname', 'lname', 'displayname', 'email'),
			/* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
			'action' => 'index.php/index/model/member/action',
			'actions' => array(
				array(
					'id' => 'action',
					'class' => 'ok',
					'text' => Language::get('With selected'),
					'options' => array(
						'accept' => Language::get('Accept membership'),
						'activate' => Language::get('Send confirmation email'),
						'sendpassword' => Language::get('Get new password'),
						'ban' => Language::get('Suspended'),
						'unban' => Language::get('Cancel suspension'),
						'delete' => Language::get('Delete')
					)
				),
				array(
					'id' => 'status',
					'class' => 'ok',
					'text' => Language::get('Change member status'),
					'options' => self::$cfg->member_status
				)
			),
			/* ตัวเลือกด้านบนของตาราง ใช้จำกัดผลลัพท์การ query */
			'filters' => array(
				'status' => array(
					'name' => 'status',
					'default' => -1,
					'text' => Language::get('Member status'),
					'options' => $member_status,
					'value' => self::$request->get('status', -1)->toInt()
				)
			),
			/* รายชื่อฟิลด์ที่ query (ถ้าแตกต่างจาก Model) */
			'fields' => array(
				'id',
				'email',
				'displayname',
				'CONCAT_WS(" ", `pname`,`fname`,`lname`) name',
				'phone1',
				'sex',
				'website',
				'create_date',
				'lastvisited',
				'visited',
				'status',
				'ban',
				'admin_access',
				'activatecode'
			),
			/* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
			'headers' => array(
				'id' => array(
					'text' => Language::get('ID'),
					'sort' => 'id',
				),
				'email' => array(
					'text' => Language::get('Email'),
					'sort' => 'email'
				),
				'displayname' => array(
					'text' => Language::get('Displayname'),
					'sort' => 'displayname'
				),
				'name' => array(
					'text' => ''.Language::get('Name').' '.Language::get('Surname').'',
					'sort' => 'name'
				),
				'phone1' => array(
					'text' => Language::get('Phone')
				),
				'sex' => array(
					'text' => Language::get('Sex'),
					'class' => 'center'
				),
				'website' => array(
					'text' => Language::get('Website')
				),
				'create_date' => array(
					'text' => Language::get('Created'),
					'class' => 'center'
				),
				'lastvisited' => array(
					'text' => ''.Language::get('Last login').' ('.Language::get('times').')',
					'class' => 'center'
				)
			),
			/* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
			'cols' => array(
				'sex' => array(
					'class' => 'center'
				),
				'create_date' => array(
					'class' => 'center'
				),
				'lastvisited' => array(
					'class' => 'center'
				)
			),
			/* ปุ่มแสดงในแต่ละแถว */
			'buttons' => array(
				array(
					'class' => 'icon-edit button green',
					'href' => self::$request->getUri()->createBackUri(array('module' => 'editprofile', 'id' => ':id')),
					'text' => Language::get('Edit')
				)
			)
		));
		// save cookie
		setcookie('member_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
		setcookie('member_sort', $table->sort, time() + 3600 * 24 * 365, '/');
		setcookie('member_sortType', $table->sortType, time() + 3600 * 24 * 365, '/');
		return $table->render();
	}

	/**
	 * จัดรูปแบบการแสดงผลในแต่ละแถว
	 *
	 * @param array $item
	 * @return array
	 */
	public function onRow($item)
	{
		$item['email'] = '<a class="status'.$item['status'].(empty($item['ban']) ? '' : ' ban').'">'.$item['email'].'</a>';
		$item['create_date'] = Date::format($item['create_date'], 'd M Y');
		$item['lastvisited'] = Date::format($item['lastvisited'], 'd M Y H:i').' ('.$item['visited'].')';
		$sex = in_array($item['sex'], array_keys(Language::get('SEXES'))) ? $item['sex'] : 'u';
		$item['sex'] = '<span class=icon-sex-'.$sex.'></span>';
		$item['phone1'] = empty($item['phone1']) ? '' : '<a href="tel:'.$item['phone1'].'">'.$item['phone1'].'</a>';
		$item['website'] = empty($item['website']) ? '' : '<a href="http://'.$item['website'].'" target="_blank">'.$item['website'].'</a>';
		return $item;
	}
}