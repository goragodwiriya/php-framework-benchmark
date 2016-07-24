<?php
/*
 * @filesource document/views/admin/category.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Category;

use \Kotchasan\Language;
use \Kotchasan\DataTable;

/**
 * แสดงรายการหมวดหมู่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{
  /**
   * ข้อมูลโมดูล
   *
   * @var object
   */
  private $publisheds;
  private $replies;

  /**
   * module=document-category
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    $this->publisheds = Language::get('PUBLISHEDS');
    $this->replies = Language::get('REPLIES');
    // Uri
    $uri = self::$request->getUri();
    // ตาราง
    $table = new DataTable(array(
      /* ข้อมูลใส่ลงในตาราง */
      'datas' => \Index\Category\Model::categories((int)$index->module_id),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('module_id', 'id', 'group_id', 'c2', 'icon'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/index/model/category/action?mid='.$index->module_id,
      'actionCallback' => 'indexActionCallback',
      'actionConfirm' => 'confirmAction',
      'actions' => array(
        array(
          'id' => 'action',
          'class' => 'ok',
          'text' => Language::get('With selected'),
          'options' => array(
            'delete' => Language::get('Delete')
          )
        )
      ),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'topic' => array(
          'text' => Language::get('Category')
        ),
        'icon' => array(
          'text' => Language::get('Icon'),
          'class' => 'center'
        ),
        'category_id' => array(
          'text' => Language::get('ID')
        ),
        'published' => array(
          'text' => Language::get('Status'),
          'class' => 'center',
          'colspan' => 2
        ),
        'detail' => array(
          'text' => Language::get('Description')
        ),
        'c1' => array(
          'text' => Language::get('Contents'),
          'class' => 'center'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'published' => array(
          'class' => 'center'
        ),
        'img_upload_size' => array(
          'class' => 'center'
        ),
        'can_reply' => array(
          'class' => 'center'
        ),
        'c1' => array(
          'class' => 'visited center'
        )
      ),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'edit' => array(
          'class' => 'icon-edit button green',
          'href' => $uri->createBackUri(array('module' => 'document-categorywrite', 'mid' => $index->module_id, 'id' => ':id')),
          'text' => Language::get('Edit')
        )
      ),
      /* ปุ่มเพิ่ม */
      'addNew' => array(
        'class' => 'button green icon-plus',
        'href' => $uri->createBackUri(array('module' => 'document-categorywrite', 'mid' => $index->module_id)),
        'text' => Language::get('Add New').' '.Language::get('Category')
      )
    ));
    $table->script('initListCategory("index");');
    return $table->render();
  }

  /**
   * จัดรูปแบบการแสดงผลในแต่ละแถว
   *
   * @param array $item
   * @return array
   */
  public function onRow($item)
  {
    $item['topic'] = $this->unserialize($item['topic']);
    $item['detail'] = $this->unserialize($item['detail']);
    $item['category_id'] = '<label><input type=text class=number size=5 id=categoryid_'.$item['module_id'].'_'.$item['id'].' value="'.$item['category_id'].'" title="'.Language::get('Edit').'"></label>';
    $item['can_reply'] = '<a id=can_reply_'.$item['id'].' class="icon-reply reply'.$item['can_reply'].'" title="'.$this->replies[$item['can_reply']].'"></a>';
    $item['published'] = '<a id=published_'.$item['id'].' class="icon-published'.$item['published'].'" title="'.$this->publisheds[$item['published']].'"></a>';
    return $item;
  }

  /**
   * เตรียมข้อมูล topic, detail สำหรับใส่ลงในตาราง
   *
   * @param array $item
   * @return string
   */
  private function unserialize($item)
  {
    $datas = array();
    foreach (unserialize($item) as $lng => $value) {
      $datas[$lng] = empty($lng) ? $value : '<p style="background:0 50% url('.WEB_URL.'language/'.$lng.'.gif) no-repeat;padding-left:21px;">'.$value.'</p>';
    }
    return implode('', $datas);
  }
}