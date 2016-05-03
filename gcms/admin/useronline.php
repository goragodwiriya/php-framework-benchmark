<?php
// useronline.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../bin/inint.php';
// referer
if (gcms::isReferer()) {
    $ret = array();
    // useronline, pages_view
    $sql = "SELECT COUNT(*) FROM `".DB_USERONLINE."`";
    $sql = "SELECT C.`pages_view`,($sql) AS `useronline` FROM `".DB_COUNTER."` AS C ORDER BY C.`id` DESC LIMIT 1";
    $datas = $db->customQuery($sql);
    $ret['useronline'] = number_format($datas[0]['useronline']);
    $ret['pages_view'] = number_format($datas[0]['pages_view']);
    // include ไฟล์อื่นๆที่ต้องการประมวลผล
    if (isset($config['useronline_include'])) {
        foreach ($config['useronline_include'] AS $item) {
            include ROOT_PATH.$item;
        }
    }
    // คืนค่า JSON
    echo json_encode($ret);
}
