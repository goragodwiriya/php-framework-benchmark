<?php
/**
 * bin/drivers/class.driver.php
 * สงวนลิขสิทธ์ ห้ามซื้อขาย ให้นำไปใช้ได้ฟรีเท่านั้น
 *
 * @package GCMS
 * @copyright http://www.goragod.com
 * @author กรกฎ วิริยะ
 * @version 09-06-58
 */
if (!defined('ROOT_PATH')) {
	exit('No direct script access allowed');
}

/**
 * Database Driver Class
 *
 * @package GCMS
 * @subpackage Database\Drivers
 * @category Database
 * @author กรกฎ วิริยะ
 */
class DB_driver
{
	var $dbdriver;
	var $hostname;
	var $username;
	var $password;
	var $dbname;
	var $char_set = 'utf8';
	var $time = 0;
	var $connection = null;
	var $port = '';
	var $error_message = '';

	/**
	 * @param array $params
	 */
	function __construct($params)
	{
		if (is_array($params)) {
			foreach ($params AS $key => $val) {
				$this->$key = $val;
			}
		}
	}

	/**
	 * จบ class
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * ฟังก์ชั่น อ่านค่า resource ID ของการเชื่อมต่อปัจจุบัน
	 *
	 * @return resource
	 */
	function connection()
	{
		return $this->connection;
	}

	/**
	 * ฟังก์ชั่น ตรวจสอบว่ามีตาราง $table หรือไม่
	 *
	 * @param string $table ชื่อตาราง
	 * @return boolean คืนค่า true หากมีตารางนี้อยู่ ไม่พบคืนค่า false
	 */
	function tableExists($table)
	{
		return $this->_query("SELECT 1 FROM `$table` LIMIT 1") === false ? false : true;
	}

	/**
	 * ฟังก์ชั่น ตรวจสอบว่ามีฟิลด์ $field ในตาราง $table หรือไม่
	 *
	 * @param string $table ชื่อตาราง
	 * @param string $field ชื่อฟิลด์
	 * @return boolean คืนค่า true หากมีฟิลด์นี้อยู่ ไม่พบคืนค่า false
	 */
	function fieldExists($table, $field)
	{
		if ($table != '' && $field != '') {
			$field = strtolower($field);
			// query table fields
			$result = $this->_customQuery("SHOW COLUMNS FROM `$table`");
			if ($result === false) {
				$this->debug("fieldExists($table, $field)", $this->error_message);
			} else {
				foreach ($result AS $item) {
					if (strtolower($item['Field']) == $field) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * สอบถามข้อมูลที่ $id เพียงรายการเดียว
	 *
	 * @param string $table ชื่อตาราง
	 * @param int $id id ที่ต้องการอ่าน
	 * @return array|boolean พบคืนค่ารายการที่พบเพียงรายการเดียว ไม่พบคืนค่า false
	 */
	function getRec($table, $id)
	{
		$sql = "SELECT * FROM `$table` WHERE `id`=".(int)$id." LIMIT 1";
		$result = $this->customQuery($sql);
		return sizeof($result) == 1 ? $result[0] : false;
	}

	/**
	 * ค้นหา $values ที่ $fields บนตาราง $table
	 *
	 * @param string $table ชื่อตาราง
	 * @param array|string $fields ชื่อฟิลด์
	 * @param array|string $values ข้อความค้นหาในฟิลด์ที่กำหนด ประเภทเดียวกันกับ $fields
	 * @return array|boolean พบคืนค่ารายการที่พบเพียงรายการเดียว ไม่พบหรือมีข้อผิดพลาดคืนค่า false
	 */
	function basicSearch($table, $fields, $values)
	{

	}

	/**
	 * ฟังก์ชั่นเพิ่มข้อมูลใหม่ลงในตาราง
	 *
	 * @param string $table ชื่อตาราง
	 * @param array $recArr ข้อมูลที่ต้องการบันทึก
	 * @return int|boolean สำเร็จ คืนค่า id ที่เพิ่ม ผิดพลาด คืนค่า false
	 */
	function add($table, $recArr)
	{

	}

	/**
	 * ฟังก์ชั่นแก้ไขข้อมูล
	 *
	 * @param string $table ชื่อตาราง
	 * @param array|string $idArr id ที่ต้องการแก้ไข หรือข้อความค้นหารูปแอเรย์ [filed=>value]
	 * @param array $recArr ข้อมูลที่ต้องการบันทึก
	 * @return boolean สำเร็จ คืนค่า true
	 */
	function edit($table, $idArr, $recArr)
	{

	}

	/**
	 * ฟังก์ชั่นลบข้อมูล
	 *
	 * @param string $table ชื่อตาราง
	 * @param int $id id ที่ต้องการลบ
	 * @return string  สำเร็จ คืนค่าว่าง ไม่สำเร็จคืนค่าข้อความผิดพลาด
	 */
	function delete($table, $id)
	{
		$sql = "DELETE FROM `$table` WHERE `id`=".(int)$id." LIMIT 1";
		$result = $this->query($sql);
		return $result === false ? $this->error_message : '';
	}

	/**
	 * ประมวลผลคำสั่ง SQL ที่ไม่ต้องการผลลัพท์ เช่น CREATE INSERT UPDATE
	 *
	 * @param string $sql
	 * @return int|boolean สำเร็จ คืนค่าจำนวนแถวที่ทำรายการ มีข้อผิดพลาดคืนค่า false
	 */
	function query($sql)
	{
		$result = $this->_query($sql);
		if ($result === false) {
			$this->debug($sql, $this->error_message);
		}
		return $result;
	}

	/**
	 * ประมวลผลคำสั่ง SQL สำหรับสอบถามข้อมูล คืนค่าผลลัพท์เป็นแอเรย์ของข้อมูลที่ตรงตามเงื่อนไข
	 *
	 * @param string $sql query string
	 * @return array คืนค่าผลการทำงานเป็น record ของข้อมูลทั้งหมดที่ตรงตามเงื่อนไข ไม่พบข้อมูลคืนค่าเป็น array ว่างๆ
	 */
	function customQuery($sql)
	{
		$result = $this->_customQuery($sql);
		if ($result === false) {
			$this->debug($sql, $this->error_message);
			return array();
		} else {
			return $result;
		}
	}

	/**
	 * อ่าน ID ล่าสุดของตาราง สำหรับตารางที่มีการกำหนด Auto_increment ไว้
	 *
	 * @param string $table ชื่อตาราง
	 * @return int คืนค่า id ล่าสุดของตาราง
	 */
	function lastId($table)
	{
		$sql = "SHOW TABLE STATUS LIKE '$table'";
		$result = $this->_customQuery($sql);
		return sizeof($result) == 1 ? (int)$result[0]['Auto_increment'] : 0;
	}

	/**
	 * ยกเลิกการ Lock ตารางทั้งหมดที่ได้ปิดกันไว้
	 *
	 * @return boolean สำเร็จ คืนค่า true
	 */
	function unLock()
	{
		return $this->query('UNLOCK TABLES') === false ? false : true;
	}

	/**
	 * Lock ตาราง
	 *
	 * @param string $table ชื่อตาราง
	 * @return boolean สำเร็จ คืนค่า true
	 */
	function _lock($table)
	{
		return $this->query("LOCK TABLES $table") === false ? false : true;
	}

	/**
	 * Lock ตาราง สำหรับการอ่าน
	 *
	 * @param string $table ชื่อตาราง
	 * @return boolean คืนค่า true ถ้าสำเร็จ
	 */
	function setReadLock($table)
	{
		return $this->_lock("`$table` READ");
	}

	/**
	 * Lock ตาราง สำหรับการเขียน
	 *
	 * @param string $table ชื่อตาราง
	 * @return boolean คืนค่า true ถ้าสำเร็จ
	 */
	function setWriteLock($table)
	{
		return $this->_lock("`$table` WRITE");
	}

	/**
	 * กรองอักขระพิเศษ ที่รับมาจาก INPUT และแปลง \ เป็น &#92;
	 *
	 * @param string $value ข้อความ
	 * @return string คืนค่าข้อความ
	 */
	function sql_clean($value)
	{
		if ((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || ini_get('magic_quotes_sybase')) {
			$value = stripslashes($value);
		}
		return $value;
	}

	/**
	 * กรองอักขระพิเศษ ที่รับมาจาก INPUT
	 *
	 * @param string $value ข้อความ
	 * @return string คืนค่าข้อความ
	 */
	function sql_quote($value)
	{
		return $this->sql_clean(str_replace('\\\\', '&#92;', $value));
	}

	/**
	 * ลบช่องว่างด้านหัวและท้ายของความ กรองอักขระพิเศษ ที่รับมาจาก INPUT และแปลง \ เป็น &#92;
	 *
	 * @param mixed $array ตัวแปรเก็บข้อความ
	 * @param string $key key ของ $array เช่น $array[$key]
	 * @return string คืนค่าข้อความ
	 */
	function sql_trim($array, $key = '')
	{
		if (is_array($array)) {
			if (!isset($array[$key])) {
				return '';
			} else {
				return $this->sql_quote(trim($array[$key]));
			}
		} else {
			return $this->sql_quote(trim($array));
		}
	}

	/**
	 * ลบช่องว่างด้านหัวและท้ายของความ กรองอักขระพิเศษ ที่รับมาจาก INPUT และแปลงอักขระพิเศษต่างๆเป็นรหัส HTML เช่น & แปลงเป็น &amp;
	 *
	 * @param mixed $array ตัวแปรเก็บข้อความ
	 * @param string $key key ของ $array เช่น $array[$key]
	 * @return string คืนค่าข้อความ
	 */
	function sql_trim_str($array, $key = '')
	{
		if (is_array($array)) {
			if (!isset($array[$key])) {
				return '';
			} else {
				return $this->sql_quote(htmlspecialchars(trim($array[$key])));
			}
		} else {
			return $this->sql_quote(htmlspecialchars(trim($array)));
		}
	}

	/**
	 * แปลงวันที่ ในรูป mktime เป็นวันที่ของ mysql ในรูป Y-m-d
	 *
	 * @param int $mktime วันที่ในรูป mktime
	 * @return string คืนค่าวันที่รูป Y-m-d
	 */
	function sql_mktimetodate($mktime)
	{
		return date("Y-m-d", $mktime);
	}

	/**
	 * แปลงวันที่ในรูป Y-m-d หรือ Y-m-d H:i:s เป็นวันที่และเวลา เช่น 1 มค. 2555 12:00:00
	 *
	 * @param int $mktime วันที่ในรูป mktime
	 * @return string คืนค่า วันที่และเวลาของ mysql เช่น Y-m-d H:i:s
	 */
	function sql_mktimetodatetime($mktime)
	{
		return date("Y-m-d H:i:s", $mktime);
	}

	/**
	 * แปลงวันที่ในรูป Y-m-d เป็นวันที่และเวลา เช่น 1 มค. 2555 12:00:00
	 *
	 * @global array $lng ตัวแปรภาษา
	 * @param string $date วันที่ในรูป Y-m-d หรือ Y-m-d h:i:s
	 * @param boolean $short (optional) true=เดือนแบบสั้น, false=เดือนแบบยาว (default true)
	 * @param boolean $time (optional) true=คืนค่าเวลาด้วยถ้ามี, false=ไม่ต้องคืนค่าเวลา (default true)
	 * @return string คืนค่า วันที่และเวลา
	 */
	function sql_date2date($date, $short = true, $time = true)
	{
		global $lng;
		if (preg_match('/([0-9]+){0,4}-([0-9]+){0,2}-([0-9]+){0,2}(\s([0-9]+){0,2}:([0-9]+){0,2}:([0-9]+){0,2})?/', $date, $match)) {
			$match[1] = (int)$match[1];
			$match[2] = (int)$match[2];
			if ($match[1] == 0 || $match[2] == 0) {
				return '';
			} else {
				$month = $short ? $lng['MONTH_SHORT'] : $lng['MONTH_LONG'];
				return $match[3].' '.$month[$match[2] - 1].' '.((int)$match[1] + $lng['YEAR_OFFSET']).($time && isset($match[4]) ? $match[4] : '');
			}
		} else {
			return '';
		}
	}

	/**
	 * ฟังก์ชั่น แปลงวันที่และเวลาของ sql เป็น mktime
	 *
	 * @param string $date วันที่ในรูป Y-m-d H:i:s
	 * @return int คืนค่าเวลาในรูป mktime
	 */
	function sql_datetime2mktime($date)
	{
		preg_match('/([0-9]+){0,4}-([0-9]+){0,2}-([0-9]+){0,2}\s([0-9]+){0,2}:([0-9]+){0,2}:([0-9]+){0,2}/', $date, $match);
		return mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]);
	}

	/**
	 * ฟังก์ชั่น อ่านจำนวน query ทั้งหมดที่ทำงาน
	 *
	 * @return int
	 */
	function query_count()
	{
		return $this->time;
	}

	/**
	 * ฟังก์ชั่น แสดงผล error
	 *
	 * @param string $source
	 * @param string $message
	 */
	function debug($source, $message = '')
	{
		$msg = "Error in <em>$source</em> Message : $message";
		if (class_exists('gcms')) {
			gcms::writeDebug($msg);
		} else {
			echo $msg;
		}
		return $message;
	}

	/**
	 * close database
	 */
	function close()
	{
		$this->_close();
		$this->connection = null;
	}
}