<?php
/*
 * @filesource Widgets/News/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\News\Controllers;

use \Kotchasan\Template;
use \Kotchasan\Date;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{

  /**
   * แสดงผล Widget
   *
   * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
   * @return string
   */
  public function get($query_string)
  {
    $model = new \Widgets\News\Models\Index;
    $index = $model->get($query_string);
    // template ของ Widget (widgetitem.html)
    $listitem = Template::create($index->owner, $index->module, 'widgetitem');
    // โฟลเดอร์เก็บรูป
    $dir = DATA_FOLDER.'news/';
    foreach ($index->items AS $item) {
      $listitem->add(array(
        '/{PICTURE}/' => is_file(ROOT_PATH.$dir.$item->picture) ? WEB_URL.$dir.$item->picture : WEB_URL.'/modules/document/img/nopicture.png',
        '/{URL}/' => WEB_URL.'index.php?module='.$index->module.'&amp;id='.$item->id,
        '/{TOPIC}/' => $item->topic,
        '/{DETAIL}/' => $item->description,
        '/{DATE}/' => Date::format($item->create_date),
        '/{VISITED}/' => number_format($item->visited),
        '/{COLS}/' => 1
      ));
    }
    // template ของ Widget (widget.html)
    $template = Template::create($index->owner, $index->module, 'widget');
    $template->add(array(
      '/{LIST}/' => $listitem->render(),
      '/{MODULE}/' => $index->module
    ));
    return $template->render();
  }
}