<?php
/*
 * @filesource index/views/pagenotfound.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\PageNotFound;

use \Kotchasan\Language;
use \Kotchasan\Template;

/**
 * หน้าเพจ 404 (Page Not Found)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * แสดงผล
   *
   * @return string
   */
  public function render()
  {
    $template = Template::create('', '', '404');
    $message = Language::get('Sorry, cannot find a page called Please check the URL or try the call again.');
    $template->add(array(
      '/{TOPIC}/' => $message,
      '/{DETAIL}/' => $message
    ));
    return (object)array(
        'topic' => $message,
        'detail' => $template->render(),
        'description' => $message,
        'keywords' => $message,
        'module' => '404',
        'owner' => 'index'
    );
  }
}