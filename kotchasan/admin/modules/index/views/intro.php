<?php
/*
 * @filesource index/views/intro.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Intro;

use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * ฟอร์มตั้งค่าหน้า intro
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=intro
   *
   * @param string $language
   * @param string $template
   * @return string
   */
  public function render($language, $template)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/index/model/intro/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Web page displayed prior to entering the home page of the website')
    ));
    // show_intro
    $fieldset->add('select', array(
      'id' => 'show_intro',
      'labelClass' => 'g-input icon-config',
      'itemClass' => 'item',
      'label' => Language::get('Settings'),
      'options' => Language::get('BOOLEANS'),
      'value' => isset(self::$cfg->show_intro) ? self::$cfg->show_intro : 0
    ));
    $div = $fieldset->add('groups-table', array(
      'label' => Language::get('Language')
    ));
    // language
    $div->add('select', array(
      'id' => 'language',
      'labelClass' => 'g-input icon-language',
      'itemClass' => 'width',
      'options' => Language::installedLanguage(),
      'value' => $language
    ));
    $div->add('button', array(
      'id' => 'btn_go',
      'itemClass' => 'width',
      'class' => 'button go',
      'value' => Language::get('Go')
    ));
    // detail
    $fieldset->add('ckeditor', array(
      'id' => 'detail',
      'itemClass' => 'item',
      'height' => 300,
      'language' => Language::name(),
      'toolbar' => 'Document',
      'label' => Language::get('Detail'),
      'value' => $template,
      'upload' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => Language::get('Save')
    ));
    $form->script('doChangeLanguage("btn_go", "index.php?module=intro");');
    return $form->render();
  }
}