<?php
/*
 * @filesource index/models/counter.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Counter;

use \Kotchasan\File;

/**
 * ข้อมูล Counter และ Useronline
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

	/**
	 * Initial Counter & Useronline
	 */
	public static function init()
	{
		if (defined('MAIN_INIT')) {
			// วันนี้
			$y = (int)date('Y');
			$m = (int)date('m');
			$d = (int)date('d');
			// ตรวจสอบ ว่าเคยเยี่ยมชมหรือไม่
			$old_counter = self::$request->cookie('counter_date')->toInt();
			if ($old_counter != $d) {
				// เข้ามาครั้งแรกในวันนี้
				$old_counter = $d;
				$counter_visited = false;
			} else {
				$counter_visited = true;
			}
			// บันทึก counter 1 วัน
			setCookie('counter_date', $old_counter, time() + 3600 * 24, '/');
			// โฟลเดอร์ของ counter
			$counter_dir = ROOT_PATH.DATA_FOLDER.'counter';
			// ตรวจสอบโฟลเดอร์
			File::makeDirectory($counter_dir);
			// ตรวจสอบวันใหม่
			$c = (int)@file_get_contents($counter_dir.'/index.php');
			if ($d != $c) {
				$f = @fopen($counter_dir.'/index.php', 'wb');
				if ($f) {
					fwrite($f, $d);
					fclose($f);
				}
				$f = @opendir($counter_dir);
				if ($f) {
					while (false !== ($text = readdir($f))) {
						if ($text != '.' && $text != '..') {
							if ($text != $y) {
								File::removeDirectory($counter_dir."/$text");
							}
						}
					}
					closedir($f);
				}
			}
			// ตรวจสอบ + สร้าง โฟลเดอร์
			File::makeDirectory("$counter_dir/$y");
			File::makeDirectory("$counter_dir/$y/$m");
			// ip ปัจจุบัน
			$counter_ip = self::$request->getClientIp();
			// session ปัจจุบัน
			$counter_ssid = session_id();
			// วันนี้
			$counter_day = date('Y-m-d');
			// Model
			$model = new static;
			$my_counter = $model->db()->createQuery()
			->from('counter')
			->order('id DESC')
			->toArray()
			->cacheOn()
			->first();
			if (!$my_counter) {
				$my_counter = array('date' => '', 'counter' => 0);
			}
			if ($my_counter['date'] != $counter_day) {
				// วันใหม่
				$my_counter['visited'] = 0;
				$my_counter['pages_view'] = 0;
				$my_counter['date'] = $counter_day;
				$counter_add = true;
				// clear useronline
				$model->db()->emptyTable($model->tableWithPrefix('useronline'));
				// clear visited_today
				$model->db()->updateAll($model->tableWithPrefix('index'), array('visited_today' => 0));
			} else {
				$counter_add = false;
			}
			// บันทึกลง log
			$counter_log = "$counter_dir/$y/$m/$d.dat";
			if (is_file($counter_log)) {
				// เปิดไฟล์เพื่อเขียนต่อ
				$f = @fopen($counter_log, 'ab');
			} else {
				// สร้างไฟล์ log ใหม่
				$f = @fopen($counter_log, 'wb');
			}
			if ($f) {
				$data = $counter_ssid.chr(1).$counter_ip.chr(1).self::$request->server('HTTP_REFERER', '').chr(1).self::$request->server('HTTP_USER_AGENT', '').chr(1).date('H:i:s')."\n";
				fwrite($f, $data);
				fclose($f);
			}
			if (!$counter_visited) {
				// ยังไม่เคยเยี่ยมชมในวันนี้
				$my_counter['visited'] ++;
				$my_counter['counter'] ++;
			}
			$my_counter['pages_view'] ++;
			$my_counter['time'] = time();
			if ($counter_add) {
				unset($my_counter['id']);
				$model->db()->insert($model->tableWithPrefix('counter'), $my_counter);
			} else {
				$model->db()->update($model->tableWithPrefix('counter'), $my_counter['id'], $my_counter);
			}
		} else {
			// เรียก method โดยตรง
			\Kotchasan\Error::send('Do not call method directly');
		}
	}
}