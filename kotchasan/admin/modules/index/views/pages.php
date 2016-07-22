<?php
/*
 * @filesource index/views/pages.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Pages;

use \Kotchasan\DataTable;
use \Kotchasan\Language;
use \Kotchasan\Date;

/**
 * แสดงรายการหน้าเว็บที่สร้างจากโมดูล index
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

  /**
   * module=pages
   *
   * @return string
   */
  public function render()
  {
    $this->publisheds = Language::get('PUBLISHEDS');
    // Uri
    $uri = self::$request->getUri();
    // ตาราง
    $table = new DataTable(array(
      /* Model */
      'model' => 'Index\Pages\Model',
      /* รายการต่อหน้า */
      'perPage' => self::$request->cookie('pages_perPage', 30)->toInt(),
      /* query where */
      'defaultFilters' => array(
        array('M.owner', 'index')
      ),
      /* ฟังก์ชั่นจัดรูปแบบการแสดงผลแถวของตาราง */
      'onRow' => array($this, 'onRow'),
      /* คอลัมน์ที่ไม่ต้องแสดงผล */
      'hideColumns' => array('module_id', 'id', 'owner'),
      /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
      'action' => 'index.php/index/model/pages/action',
      'actionCallback' => 'indexActionCallback',
      'actionConfirm' => 'confirmAction',
      /* คอลัมน์ที่สามารถค้นหาได้ */
      'searchColumns' => array('topic', 'module', 'detail'),
      /* ส่วนหัวของตาราง และการเรียงลำดับ (thead) */
      'headers' => array(
        'topic' => array(
          'text' => Language::get('Topic')
        ),
        'published' => array(
          'text' => Language::get('Status'),
          'class' => 'center'
        ),
        'language' => array(
          'text' => Language::get('Language'),
          'class' => 'center'
        ),
        'module' => array(
          'text' => Language::get('module name'),
          'class' => 'center'
        ),
        'last_update' => array(
          'text' => Language::get('Last updated'),
          'class' => 'center'
        ),
        'visited' => array(
          'text' => Language::get('Preview'),
          'class' => 'center'
        )
      ),
      /* รูปแบบการแสดงผลของคอลัมน์ (tbody) */
      'cols' => array(
        'published' => array(
          'class' => 'center'
        ),
        'language' => array(
          'class' => 'center'
        ),
        'module' => array(
          'class' => 'center'
        ),
        'last_update' => array(
          'class' => 'center'
        ),
        'visited' => array(
          'class' => 'visited'
        )
      ),
      /* ปุ่มแสดงในแต่ละแถว */
      'buttons' => array(
        'edit' => array(
          'class' => 'icon-edit button green',
          'href' => $uri->createBackUri(array('module' => 'pagewrite', 'id' => ':id')),
          'text' => Language::get('Edit')
        ),
        'delete' => array(
          'class' => 'icon-delete button red',
          'id' => ':id',
          'text' => Language::get('Delete')
        )
      ),
      /* ปุ่มเพิ่ม */
      'addNew' => array(
        'class' => 'button green icon-plus',
        'href' => $uri->createBackUri(array('module' => 'pagewrite', 'id' => '0')),
        'text' => Language::get('Add New').' '.Language::get('Page')
      )
    ));
    // save cookie
    setcookie('pages_perPage', $table->perPage, time() + 3600 * 24 * 365, '/');
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
    $item['topic'] = '<a href="../index.php?module=index&amp;id='.$item['id'].'" target="preview">'.$item['topic'].'</a>';
    $item['last_update'] = Date::format($item['last_update'], 'd M Y H:i');
    $item['language'] = empty($item['language']) ? '' : '<img src="'.WEB_URL.'language/'.$item['language'].'.gif" alt="'.$item['language'].'">';
    $item['published'] = '<a id=published_'.$item['id'].' class="icon-published'.$item['published'].'" title="'.$this->publisheds[$item['published']].'"></a>';
    return $item;
  }
}