<?php
// admin/countrywrite_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../bin/inint.php';
$ret = array();
// ตรวจสอบ referer และ แอดมิน
if (gcms::isReferer() && gcms::isAdmin()) {
    if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
        $ret['error'] = 'EX_MODE_ERROR';
    } else {
        // ค่าที่ส่งมา
        $save['iso'] = strtoupper($_POST['write_iso']);
        $save['printable_name'] = $db->sql_trim_str($_POST, 'write_name');
        $save['zone'] = gcms::getVars($_POST, 'write_zone', 0);
        $id = gcms::getVars($_POST, 'write_id', 0);
        // ตรวจสอบค่าที่ส่งมา
        if ($id > 0) {
            $country = $db->getRec(DB_COUNTRY, $id);
        }
        if ($id > 0 && !$country) {
            $ret['error'] = 'ACTION_ERROR';
            $ret['location'] = 'back';
        } elseif ($save['iso'] == '' || $save['printable_name'] == '') {
            $input = $save['iso'] == '' ? 'write_iso' : 'write_name';
            $ret['error'] = 'DO_NOT_EMPTY';
            $ret['input'] = $input;
            $ret['ret_'.$input] = 'DO_NOT_EMPTY';
        } elseif (!preg_match('/[A-Z]{2,2}/', $save['iso'])) {
            $ret['error'] = 'COUNTRY_ISO_INVALID';
            $ret['input'] = 'write_iso';
            $ret['ret_write_iso'] = 'COUNTRY_ISO_INVALID';
        } else {
            // ตรวจสอบ iso ซ้ำ
            $sql = "SELECT `id` FROM `".DB_COUNTRY."`";
            $sql .= " WHERE `iso`='$save[iso]' AND `id`!='$id'";
            $sql .= " LIMIT 1";
            $search = $db->customQuery($sql);
            if (sizeof($search) == 1) {
                $ret['error'] = 'TOPIC_EXISTS';
                $ret['input'] = 'write_iso';
                $ret['ret_write_iso'] = 'TOPIC_EXISTS';
            } else {
                // save
                if ($id == 0) {
                    $id = $db->add(DB_COUNTRY, $save);
                    $ret['error'] = 'SAVE_COMPLETE';
                } else {
                    $db->edit(DB_COUNTRY, $id, $save);
                    $ret['error'] = 'EDIT_SUCCESS';
                }
                $ret['location'] = 'back';
            }
        }
    }
} else {
    $ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
