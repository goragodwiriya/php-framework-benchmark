<?php
// useronline.php
if (defined('MAIN_INIT')) {
	// บอกว่ายังไม่มีคนเปลี่ยนแปลงไว้ก่อน
	$validtime = $mmktime - COUNTER_GAP;
	// แอเรย์เก็บ id ที่ต้องการลบ
	$session_id = session_id();
	$login = gcms::getVars($_SESSION, 'login', array('id' => 0, 'status' => -1, 'displayname' => '', 'email' => '', 'password' => ''));
	// ลบคนที่หมดเวลาและตัวเอง
	$db->query("DELETE FROM `".DB_USERONLINE."` WHERE `time`<$validtime OR `session`='$session_id'");
	// เพิ่มตัวเอง
	$save = array();
	$save['member_id'] = (int)$login['id'];
	$save['displayname'] = trim(gcms::cutstring(empty($login['displayname']) ? $login['email'] : $login['displayname'], 10));
	$save['time'] = $mmktime;
	$save['session'] = $session_id;
	$db->add(DB_USERONLINE, $save);
}
