<?php
/*
 * @filesource index/controllers/pagewrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Pagewrite;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Html;

/**
 * ฟอร์มสร้าง/แก้ไข หน้าเว็บไซต์
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
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // รายการที่ต้องการ
      $index = \Index\Pagewrite\Model::getIndex($request->get('id')->toInt(), $request->get('owner', 'index')->topic());
      if ($index) {
        // สร้างหรือแก้ไข
        $title = empty($index->id) ? 'Create' : 'Edit';
        // แสดงผล
        $section = Html::create('section');
        // breadcrumbs
        $breadcrumbs = $section->add('div', array(
          'class' => 'breadcrumbs'
        ));
        $ul = $breadcrumbs->add('ul');
        $ul->appendChild('<li><span class="icon-modules">{LNG_Menus} &amp; {LNG_Web pages}</span></li>');
        $ul->appendChild('<li><a href="{BACKURL?module=pages&id=0}">{LNG_Web pages}</a></li>');
        $ul->appendChild('<li><span>{LNG_'.$title.'}</span></li>');
        $section->add('header', array(
          'innerHTML' => '<h1 class="icon-write">{LNG_'.$title.'} {LNG_'.($index->owner === 'index' ? 'Page' : 'Module').'} '.$index->module.' ('.$index->owner.')'.'</h1>'
        ));
        if (!$index) {
          $section->appendChild('<aside class=error>{LNG_Can not be performed this request. Because they do not find the information you need or you are not allowed}</aside>');
        } else {
          // แสดงฟอร์ม
          $section->appendChild(createClass('Index\Pagewrite\View')->render($index));
        }
        return $section->render();
      } else {
        // 404.html
        return \Index\Error\Controller::page404();
      }
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
    return Language::get('Create or Edit').' '.Language::get('Webpage');
  }
}