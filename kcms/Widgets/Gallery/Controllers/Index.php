<?php
/*
 * @filesource Widgets/Gallery/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Gallery\Controllers;

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
    $query_string['rows'] = empty($query_string['rows']) ? 3 : (int)$query_string['rows'];
    $query_string['cols'] = empty($query_string['cols']) ? 2 : (int)$query_string['cols'];
    $dir = DATA_FOLDER.'gallery/';
    $widget = array();
    $model = new \Widgets\Gallery\Models\Index;
    foreach ($model->get($query_string) AS $item) {
      $img = str_replace('image', 'thumb', $item->image);
      $img = is_file(ROOT_PATH.$dir.$img) ? WEB_URL.$dir.$img : WEB_URL.'modules/gallery/img/noimage.jpg';
      $url = WEB_URL.'index.php?module='.$item->module.'&amp;id='.$item->id;
      $widget[] = '<div class=col'.$query_string['cols'].'><div class=figure>';
      $widget[] = '<a href="'.$url.'"><img src="'.$img.'" class=nozoom alt="'.$item->topic.'"></a>';
      $widget[] = '<a class=figcaption href="'.$url.'" title="'.$item->topic.'"><span class=cuttext>'.$item->topic.'</span></a>';
      $widget[] = '</div></div>';
    }
    if (sizeof($widget) > 0) {
      return '<div class="widget-album gbox">'.implode('', $widget).'</div>';
    }
    return '';
  }
}