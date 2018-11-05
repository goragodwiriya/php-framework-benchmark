<?php
// js/js.php
header('Content-type: text/javascript; charset: UTF-8');
// inint
include '../bin/inint.php';
// cache 1 month
$expire = 2592000;
header("Cache-Control: max-age=$expire, must-revalidate, public");
header('Expires: '.gmdate("D, d M Y H:i:s", time() + $expire)." GMT");
header('Last-Modified:'.gmdate("D, d M Y H:i:s", time() - $expire)." GMT");
// default js
$js = array();
$js[] = file_get_contents('gajax.js');
$js[] = file_get_contents('common.js');
$js[] = file_get_contents('gcms.js');
$js[] = file_get_contents('media.js');
$js[] = file_get_contents('editinplace.js');
$js[] = file_get_contents('gddmenu.js');
if (is_file(DATA_PATH.'language/'.LANGUAGE.'.js')) {
    $js[] = file_get_contents(DATA_PATH.'language/'.LANGUAGE.'.js');
}
// js ของโมดูล
$dir = ROOT_PATH.'modules/';
$f = @opendir($dir);
if ($f) {
    while (false !== ($text = readdir($f))) {
        if ($text != "." && $text != "..") {
            if (is_dir($dir.$text)) {
                if (is_file(ROOT_PATH."modules/$text/script.js")) {
                    $js[] = file_get_contents(ROOT_PATH."modules/$text/script.js");
                }
            }
        }
    }
    closedir($f);
}
// js ของ widgets
$dir = ROOT_PATH.'widgets/';
$f = @opendir($dir);
if ($f) {
    while (false !== ($text = readdir($f))) {
        if ($text != "." && $text != "..") {
            if (is_dir($dir.$text)) {
                if (is_file(ROOT_PATH."widgets/$text/script.js")) {
                    $js[] = file_get_contents(ROOT_PATH."widgets/$text/script.js");
                }
            }
        }
    }
    closedir($f);
}
if ((int)$config['counter_digit'] > 0) {
    $js[] = "var counter_digit = $config[counter_digit];";
}
if ($config['use_ajax'] == 1) {
    $js[] = "var use_ajax = $config[use_ajax];";
}
// web url ใช้ตาม addressbar
preg_match('/^(http(s)?:\/\/)(.*)(\/(.*))?$/U', WEB_URL, $match);
$js[] = "window.WEB_URL = '$match[1]' + getWebURL() + '".(isset($match[4]) ? $match[4] : '')."/';";
// skin ที่เรียกใช้
$js[] = "window.SKIN='".SKIN."';";
// module url
$js[] = "window.MODULE_URL='$config[module_url]';";
// compress javascript
$regex = array(
  "`^([\t\s]+)`ism" => '',
  "`^\/\*(.+?)\*\/`ism" => "",
  "`([\n\A;]+)\/\*(.+?)\*\/`ism" => "$1",
  "`([\n\A;\s]+)//(.+?)[\n\r]`ism" => "$1\n",
  "`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism" => "\n",
  '/[\r\n]+/sm' => ''
);
echo preg_replace(array_keys($regex), $regex, implode("\n", $js));


