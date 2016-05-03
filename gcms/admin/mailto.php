<?php
// admin/mailto.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../bin/inint.php';
$ret = array();
// ตรวจสอบ referer และ สมาชิก
if (gcms::isReferer() && gcms::isMember()) {
    if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
        $ret['error'] = 'EX_MODE_ERROR';
    } else {
        // ค่าที่ส่งมา
        $topic = htmlspecialchars(trim($_POST['email_subject']));
        $detail = gcms::ckClean($_POST['email_detail']);
        $reciever = htmlspecialchars(trim($_POST['email_reciever']));
        if (gcms::isAdmin()) {
            $sender = $db->getRec(DB_USER, $_POST['email_from']);
        } else {
            $sender = $_SESSION['login'];
        }
        // ตรวจสอบค่าที่ส่งมา
        if ($sender['email'] == '') {
            $ret['error'] = 'ACTION_ERROR';
        } elseif ($reciever == '') {
            $ret['error'] = 'RECIEVER_EMPTY';
            $ret['input'] = 'email_reciever';
        } elseif ($sender == $reciever) {
            $ret['error'] = 'ACTION_ERROR';
        } elseif ($topic == '') {
            $ret['error'] = 'TOPIC_EMPTY';
            $ret['input'] = 'email_subject';
        } elseif ($detail == '') {
            $ret['error'] = 'DETAIL_EMPTY';
        } else {
            $error = gcms::customMail($reciever, "$sender[email]<$sender[displayname]>", $topic, $detail);
            if ($error == '') {
                $ret['error'] = 'EMAIL_SEND_SUCCESS';
                $ret['location'] = 'back';
            } else {
                $ret['alert'] = rawurlencode($error);
            }
        }
    }
    // คืนค่าเป็น JSON
    echo gcms::array2json($ret);
}
