<?php
/**
 * bin/inint.php
 * load with session and cookie
 *
 * @author Goragod Wiriya <admin@goragod.com>
 * @link http://gcms.in.th/
 * @copyright 2015 Goragod.com
 * @license http://gcms.in.th/license/
 */
/**
 *  เวลาเริ่มต้นในการประมวลผลเว็บไซต์
 */
define('BEGIN_TIME', microtime(true));
session_start();
if (!ob_get_status()) {
	if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
		// เปิดใช้งานการบีบอัดหน้าเว็บไซต์
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}
// load
include dirname(__FILE__).'/load.php';
