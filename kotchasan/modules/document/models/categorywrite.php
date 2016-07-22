<?php
/*
 * @filesource document/models/categorywrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Categorywrite;

use \Gcms\Login;
use \Kotchasan\ArrayTool;
use \Kotchasan\Language;
use \Kotchasan\Http\Request;

/**
 * อ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลหมวดหมู่
   * $_GET['id'] id ของ module (module_id)
   * $_GET['cat'] id ของ category (id) ถ้าไม่ระบุจะเป็นหมวดหมู่ใหม่
   *
   * @return Object ถ้าไม่พบคืนค่า null
   */
  public static function get(Request $request)
  {
    // ค่าที่ส่งมา
    $category_id = $request->get('cat')->toInt();
    $module_id = $request->get('id')->toInt();
    $model = new static($request);
    if ($category_id == 0) {
      // ใหม่, ตรวจสอบโมดูลที่เรียก
      $sql1 = " SELECT MAX(`category_id`) FROM `".$model->tableWithPrefix('category')."` WHERE `module_id`=M.`id`";
      $sql = "SELECT 0 AS `id`,M.`id` AS `module_id`,M.`module`,M.`config` AS `mconfig`,1+COALESCE(($sql1),0) AS `category_id`";
      $sql .= " FROM `".$model->tableWithPrefix('modules')."` AS M";
      $sql .= " WHERE M.`id`=:module_id AND M.`owner`='document' LIMIT 1";
      $where = array(
        ':module_id' => $module_id
      );
    } else {
      // แก้ไข ตรวจสอบโมดูลและหมวดที่เลือก
      $sql = "SELECT C.*,M.`module`,M.`config` AS `mconfig`";
      $sql .= " FROM `".$model->tableWithPrefix('category')."` AS C";
      $sql .= " INNER JOIN `".$model->tableWithPrefix('modules')."` AS M ON M.`id`=C.`module_id` AND M.`owner`='document'";
      $sql .= " WHERE C.`id`=:category_id AND C.`module_id`=:module_id LIMIT 1";
      $where = array(
        ':module_id' => $module_id,
        ':category_id' => $category_id
      );
    }
    $result = $model->db()->customQuery($sql, true, $where);
    if (empty($result)) {
      $result = null;
    } else {
      $result = (object)$result[0];
      if ($category_id == 0) {
        $result->topic = serialize(array('' => ''));
      } else {
        if (!empty($result->config)) {
          foreach (ArrayTool::unserialize($result->config)as $key => $value) {
            $result->$key = $value;
          }
        }
        unset($result->config);
      }
      if (!empty($result->mconfig)) {
        foreach (ArrayTool::unserialize($result->mconfig)as $key => $value) {
          $result->$key = $value;
        }
      }
      unset($result->mconfig);
    }
    return $result;
  }

  /**
   * บันทึกหมวดหมู่
   */
  public function save()
  {
    $ret = array();
    // referer, session, member
    if ($this->request->isReferer() && $this->request->initSession() && Login::isAdmin()) {
      if ($_SESSION['login']->email == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        $input = false;
        // รับค่าจากการ $_POST
        $save = $this->request->filter($_POST);
        print_r($save);
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}