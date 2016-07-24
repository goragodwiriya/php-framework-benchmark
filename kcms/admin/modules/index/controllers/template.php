<?php
/*
 * @filesource index/controllers/template.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Template;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Html;
use \Kotchasan\Config;
use \Kotchasan\File;

/**
 * รายการ template
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
    if ($login = Login::isAdmin()) {
      // โหลด config
      $config = Config::load(ROOT_PATH.'settings/config.php');
      // path ของ skin
      $dir = ROOT_PATH.'skin';
      // action
      $action = self::$request->get('action')->toString();
      if (!empty($action)) {
        if ($login['email'] == 'demo') {
          $message = '<aside class=error>'.Language::get('Unable to complete the transaction').'</aside>';
        } else {
          $theme = preg_replace('/[\/\\\\]/ui', '', self::$request->get('theme')->text());
          if (is_dir($dir."/$theme")) {
            if ($action == 'use') {
              // skin ที่กำหนด
              $config->skin = $theme;
              // บันทึก config.php
              if (Config::save($config, ROOT_PATH.'settings/config.php')) {
                self::$request->setSession('my_skin', $config->skin);
                $message = '<aside class=message>'.Language::get('Select a new template successfully').'</aside>';
              } else {
                $message = '<aside class=error>'.sprintf(Language::get('File %s cannot be created or is read-only.'), 'settings/config.php').'</aside>';
              }
            } elseif ($action == 'delete') {
              // ลบ skin
              File::removeDirectory($dir.'/'.$theme.'/');
              $message = '<aside class=message>'.Language::get('Successfully remove template files').'</aside>';
            }
          }
        }
      }
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-settings">'.Language::get('Site settings').'</span></li>');
      $ul->appendChild('<li><span>'.Language::get('Template').'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-template">'.$this->title().'</h1>'
      ));
      if (!empty($message)) {
        $section->appendChild($message);
      }
      // อ่าน theme ทั้งหมด
      $themes = array();
      $f = opendir($dir);
      while (false !== ($text = readdir($f))) {
        if ($text !== $config->skin && $text !== "." && $text !== "..") {
          if (is_dir($dir."/$text") && is_file($dir."/$text/style.css")) {
            $themes[] = $text;
          }
        }
      }
      closedir($f);
      // แสดงฟอร์ม
      $section->appendChild(createClass('Index\Template\View')->render($dir, $config, $themes));
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
    return Language::get('Select a template of the site');
  }
}