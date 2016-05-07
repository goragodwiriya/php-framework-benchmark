<?php
$module = isset($_GET['module']) ? $_GET['module'] : '';
require __DIR__.'/libs/parse_results.php';
$m = array_keys($modules);
if (!isset($modules[$module])) {
	$module = $m[0];
}
$results = parse_results(__DIR__.'/output/'.$module.'/results.hello_world.log');
$content = array();
$content[] = '<!DOCTYPE html>';
$content[] = '<html lang=th dir=ltr>';
$content[] = '<head>';
$content[] = '<meta charset=utf-8>';
$content[] = '<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
$content[] = '<title>PHP Framework Benchmark</title>';
$content[] = '<link rel=stylesheet href=http://www.goragod.com/skin/gcss.css>';
$content[] = '<link rel=stylesheet href=style.css>';
$content[] = '<script src=http://www.goragod.com/js/gajax.js></script>';
$content[] = '<script src=http://www.goragod.com/js/ggraph.js></script>';
$content[] = '<script src=http://www.goragod.com/js/gddmenu.js></script>';
$content[] = '</head>';
$content[] = '<body class="responsive">';
$content[] = '<div class=gcss-wrapper>';
$content[] = '<h1>PHP Framework Benchmark</h1>';
$content[] = '<h2>'.$modules[$module].'</h2>';
$content[] = '<p>';
foreach ($modules as $key => $value) {
	$content[] = '<a href="?module='.$key.'"><span>'.$value.'</span></a>';
}
$content[] = '</p>';
$thead = array();
$rps = array();
$memory = array();
$time = array();
$file = array();
$datas = array();
foreach ($results as $key => $values) {
	foreach ($values as $k => $v) {
		$datas[$k][$key] = $v;
	}
}
$headers = array(
	'rps' => 'จำนวน Request ต่อหนึ่งวินาที ค่านี้ยิ่งมากยิ่งดี',
	'memory' => 'หน่วยความจำเริ่มต้น ค่านี้ยิ่งน้อยยิ่งดี',
	'time' => 'เวลาที่ใช้ในการประมวลผลสคริปต์ ค่านี้ยิ่งน้อยยิ่งดี',
	'file' => 'จำนวนไฟล์ที่ถูกเรียก (include) ค่านี้ยิ่งน้อยยิ่งดี',
);
foreach ($headers as $key => $comment) {
	$content[] = '<section id='.$key.' class=ggraphs>';
	$content[] = '<header><h3>'.$key.'</h3></header>';
	$content[] = '<canvas></canvas>';
	$content[] = '<table>';
	$content[] = '<thead><tr><th></th><th>'.$key.'</th></tr></thead>';
	$content[] = '<tbody>';
	foreach ($datas[$key] as $k => $v) {
		$content[] = '<tr><th>'.$k.'</th><td>'.$v.'</td></tr>';
	}
	$content[] = '</tbody>';
	$content[] = '</table>';
	$content[] = '<p>'.$comment.'</p>';
	$content[] = '</section>';
	$script[] = 'new gGraphs("'.$key.'", {type:"bar",colors:["#7E57C2", "#FF5722", "#E91E63", "#259B24", "#607D8B", "#2CB6D5", "#FD971F", "#26A694", "#FF5722", "#00BCD4", "#8BC34A", "#616161", "#FFD54F", "#03A9F4", "#795548"]});';
}

$content[] = '<script>';
$content[] = implode("\n", $script);
$content[] = '</script>';
$content[] = '</div>';
$content[] = '</body>';
$content[] = '</html>';
echo implode("\n", $content);
