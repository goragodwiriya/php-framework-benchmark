<?php
/*
 * @filesource index/models/database.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Database;

use \Kotchasan\Login;
use \Kotchasan\Language;

/**
 * ตรวจสอบข้อมูลสมาชิกด้วย Ajax
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * get all table
   *
   * @return array
   */
  public function showTables()
  {
    if (defined('MAIN_INIT')) {
      return $this->db()->customQuery('SHOW TABLE STATUS', true);
    } else {
      // เรียก method โดยตรง
      new \Kotchasan\Http\NotFound('Do not call method directly');
    }
  }

  /**
   * export database to file
   */
  public function export()
  {
    // UTF-8
    header("content-type: text/html; charset=UTF-8");
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isAdmin()) {
      if ($login['email'] == 'demo') {
        // ไม่สามารถดาวน์โหลดได้
        header("HTTP/1.0 404 Not Found");
      } else {
        $sqls = array();
        $rows = array();
        $database = array();
        $datas = array();
        foreach ($_POST AS $table => $values) {
          foreach ($values AS $k => $v) {
            if (isset($datas[$table][$v])) {
              $datas[$table][$v] ++;
            } else {
              $datas[$table][$v] = 1;
            }
          }
        }
        $web_url = str_replace(array('http://', 'https://', 'www.'), '', WEB_URL);
        $web_url = '/http(s)?:\/\/(www\.)?'.preg_quote($web_url, '/').'/';
        // database
        $model = new static;
        // ชื่อฐานข้อมูล
        $fname = $model->getSetting('dbname').'.sql';
        // memory limit
        ini_set('memory_limit', '1024M');
        // ส่งออกเป็นไฟล์
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=$fname");
        // prefix
        $prefix = $model->getSetting('prefix');
        // ตารางทั้งหมด
        $tables = $model->showTables();
        // ตารางทั้งหมด
        foreach ($tables as $table) {
          if (preg_match('/^'.$prefix.'(.*?)$/', $table['Name']) && isset($datas[$table['Name']])) {
            $fields = $model->db()->customQuery('SHOW FULL FIELDS FROM '.$table['Name'], true);
            $primarykey = array();
            $rows = array();
            foreach ($fields AS $field) {
              if ($field['Key'] == 'PRI') {
                $primarykey[] = '`'.$field['Field'].'`';
              }
              $database[$table['Name']]['Field'][] = $field['Field'];
              $rows[] = '`'.$field['Field'].'` '.$field['Type'].($field['Collation'] != '' ? ' collate '.$field['Collation'] : '').($field['Null'] == 'NO' ? ' NOT NULL' : '').($field['Default'] != '' ? " DEFAULT '".$field['Default']."'" : '').($field['Extra'] != '' ? ' '.$field['Extra'] : '');
            }
            if (sizeof($primarykey) > 0) {
              $rows[] = 'PRIMARY KEY ('.implode(',', $primarykey).')';
            }
            if (isset($datas[$table['Name']]['sturcture'])) {
              $sqls[] = 'DROP TABLE IF EXISTS `'.preg_replace('/^'.$prefix.'/', '{prefix}', $table['Name']).'`;';
              $q = 'CREATE TABLE `'.preg_replace('/^'.$prefix.'/', '{prefix}', $table['Name']).'` ('.implode(',', $rows).') ENGINE='.$table['Engine'];
              $q .= ' DEFAULT CHARSET='.preg_replace('/([a-zA-Z0-9]+)_.*?/Uu', '\\1', $table['Collation']).' COLLATE='.$table['Collation'];
              $q .= ($table['Create_options'] != '' ? ' '.strtoupper($table['Create_options']) : '').';';
              $sqls[] = $q;
            }
          }
        }
        // ข้อมูลในตาราง
        foreach ($tables AS $table) {
          if (preg_match('/^'.$prefix.'(.*?)$/', $table['Name'], $match)) {
            if ($match[1] == '_emailtemplate') {
              if (isset($datas[$table['Name']]['datas'])) {
                if (($key = array_search('id', $database[$table['Name']]['Field'])) !== false) {
                  unset($database[$table['Name']]['Field'][$key]);
                }
                $data = "INSERT INTO `".preg_replace('/^'.$prefix.'/', '{prefix}', $table['Name'])."` (`".implode('`, `', $database[$table['Name']]['Field'])."`) VALUES ('%s');";
                $records = $model->db()->customQuery('SELECT * FROM '.$table['Name'], true);
                foreach ($records AS $record) {
                  foreach ($record AS $field => $value) {
                    if ($field === 'copy_to' || $field === 'from_email') {
                      $record[$field] = $value == $login['email'] ? '{WEBMASTER}' : '';
                    } elseif ($field == 'id') {
                      unset($record['id']);
                    } else {
                      $record[$field] = addslashes(preg_replace($web_url, '{WEBURL}', $value));
                    }
                  }
                  $sqls[] = preg_replace(array('/[\r]/u', '/[\n]/u'), array('\r', '\n'), sprintf($data, implode("','", $record)));
                }
              }
            } elseif (isset($datas[$table['Name']]['datas'])) {
              $data = "INSERT INTO `".preg_replace('/^'.$prefix.'/', '{prefix}', $table['Name'])."` (`".implode('`, `', $database[$table['Name']]['Field'])."`) VALUES ('%s');";
              $records = $model->db()->customQuery('SELECT * FROM '.$table['Name'], true);
              foreach ($records AS $record) {
                foreach ($record AS $field => $value) {
                  $record[$field] = addslashes(preg_replace($web_url, '{WEBURL}', $value));
                }
                $sqls[] = preg_replace(array('/[\r]/u', '/[\n]/u'), array('\r', '\n'), sprintf($data, implode("','", $record)));
              }
            }
          }
        }
        // คืนต่าข้อมูล
        echo preg_replace(array('/[\\\\]+/', '/\\\"/'), array('\\', '"'), implode("\r\n", $sqls));
      }
    } else {
      // ไม่สามารถดาวน์โหลดได้
      header("HTTP/1.0 404 Not Found");
    }
  }

  /**
   * import database
   */
  public function import()
  {
    $ret = array();
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isAdmin()) {
      // ไฟล์ที่ส่งมา
      $file = $_FILES['import_file'];
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } elseif ($file['tmp_name'] != '') {
        // long time
        set_time_limit(0);
        // database
        $model = new static;
        // prefix
        $prefix = $model->getSetting('prefix');
        // อัปโหลด
        $fr = file($file['tmp_name']);
        // query ทีละบรรทัด
        foreach ($fr as $value) {
          $sql = str_replace(array('\r', '\n', '{prefix}', '/{WEBMASTER}/', '/{WEBURL}/'), array("\r", "\n", $prefix, $login['email'], WEB_URL), trim($value));
          if ($sql != '') {
            $model->db()->query($sql);
          }
        }
        // คืนค่า
        $ret['alert'] = Language::get('Data import completed Please reload the page to see the changes');
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}