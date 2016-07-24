<?php
/*
 * @filesource Widgets/News/Models/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\News\Models;

/**
 * อ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลโมดูล
   *
   * @param array $query_string ข้อมูลที่เรียก query string
   * @return array
   */
  public function get($query_string)
  {
    // จำนวน
    $count = isset($query_string['count']) ? (int)$query_string['count'] : 10;
    // โมดูลและจำนวนบทความ
    $sql = "SELECT M.`id`,M.`module`,M.`owner`";
    $sql .= " FROM ".$this->getFullTableName('modules')." AS M";
    $sql .= " WHERE M.`module`=:module LIMIT 1";
    $where = array(
      ':module' => 'news'
    );
    $result = $this->db()->customQuery($sql, false, $where);
    if (empty($result)) {
      return null;
    } else {
      $result = $result[0];
      // query ข่าวล่าสุด
      $sql = "SELECT I.* FROM ".$this->getFullTableName('news')." AS I";
      $sql .= " WHERE I.`module_id`=:module_id";
      $sql .= " ORDER BY I.`create_date` DESC LIMIT $count";
      $where = array(
        ':module_id' => $result->id
      );
      $result->items = $this->db()->customQuery($sql, false, $where);
    }
    return $result;
  }
}