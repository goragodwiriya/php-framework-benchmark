<?php
/*
 * @filesource document/models/inint.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Inint;

use \Gcms\Gcms;

/**
 *  เริ่มต้นใช้งานโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{

  /**
   * Inint Module
   */
  public function inint()
  {
    $rss = array();
    foreach (Gcms::$install_owners['document'] as $item) {
      $module = Gcms::$install_modules[$item];
      // RSS Menu
      $topic = empty($module->menu_text) ? ucwords($module->module) : $module->menu_text;
      $rss[$module->module] = '<link rel=alternate type="application/rss+xml" title="'.$topic.'" href="'.WEB_URL.$module->module.'.rss">';
    }
    if (!empty($rss)) {
      Gcms::$view->setMetas($rss);
    }
  }
}