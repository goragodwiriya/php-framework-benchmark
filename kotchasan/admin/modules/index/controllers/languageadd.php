<?php
/*
 * @filesource index/controllers/languageadd.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Languageadd;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Html;

/**
 * ฟอร์มเพิ่ม/แก้ไข ภาษาหลัก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * แสดงผล
   */
  public function render()
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // รายการที่ต้องการ
      $id = self::$request->get('id')->toString();
      $title = Language::get(empty($id) ? 'Create' : 'Edit');
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-settings">'.Language::get('Site settings').'</span></li>');
      $ul->appendChild('<li><a href="{BACKURL?module=languages&id=0}">'.Language::get('Language').'</a></li>');
      $ul->appendChild('<li><span>'.$title.'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-language">'.$title.' '.Language::get('Language').' '.$id.'</h1>'
      ));
      // แสดงฟอร์ม
      $section->appendChild(createClass('Index\Languageadd\View')->render($id));
      return $section->render();
    } else {
      // 404.html
      return \Index\Error\Controller::page404();
    }
  }

  /**
   * title bar
   */
  public function title()
  {
    return Language::get('Create or Edit').' '.Language::get('Language');
  }
}