<?php
/*
 * @filesource document/models/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Setup;

use \Kotchasan\Orm\Recordset;
use \Gcms\Login;
use \Kotchasan\Language;

/**
 * โมเดลสำหรับแสดงรายการบทความ (setup.php)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Orm\Field
{
  /**
   * ชื่อตาราง
   *
   * @var string
   */
  protected $table = 'index P';

  /**
   * query หน้าเพจ เรียงลำดับตาม module,language
   *
   * @return array
   */
  public function getConfig()
  {
    return array(
      'select' => array(
        'D.topic',
        'P.picture',
        'P.can_reply',
        'P.published',
        'P.show_news',
        'P.category_id',
        '(CASE WHEN ISNULL(U.`id`) THEN P.`email` WHEN U.`displayname`=\'\' THEN U.`email` ELSE U.`displayname` END) AS `writer`',
        'P.create_date',
        'P.last_update',
        'P.member_id',
        'P.visited',
        'U.status',
        'P.id'
      ),
      'join' => array(
        array(
          'INNER',
          'Document\Detail\Model',
          array(
            array('D.id', 'P.id'),
            array('D.module_id', 'P.module_id')
          )
        ),
        array(
          'LEFT',
          'Document\User\Model',
          array(
            array('U.id', 'P.member_id')
          )
        )
      ),
      'order' => array(
        'P.id DESC'
      )
    );
  }

  /**
   * รับค่าจาก action ของ table
   */
  public static function action()
  {
    $ret = array();
    // referer, session, admin
    if ($this->request->isReferer() && $this->request->initSession() && Login::isAdmin()) {
      if ($_SESSION['login']->email == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $save = $this->request->filter($_POST);
        // record set (Index)
        $rs = Recordset::create('Index\Index\Model');
        if ($save['action'] === 'published') {
          $index = $rs->find($save['id']);
          if ($index) {
            $index->published = $index->published == 1 ? 0 : 1;
            $index->save();
            // คืนค่า
            $ret['published'] = $index->id;
            $publisheds = Language::get('PUBLISHEDS');
            $ret['title'] = $publisheds[$index->published];
            $ret['value'] = $index->published;
          }
        } elseif ($save['action'] === 'delete') {
          $index = $rs->find($save['id']);
          if ($index) {
            Recordset::create('Index\Modules\Model')->delete((int)$index->module_id);
            Recordset::create('Index\Detail\Model')->delete((int)$index->id);
            $index->delete();
          }
          // คืนค่า
          $ret['delete_id'] = $save['src'].'_'.$save['id'];
          $ret['alert'] = Language::get('Deleted successfully');
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}