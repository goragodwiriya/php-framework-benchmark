<?php
/*
 * @filesource index/views/pagewrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Pagewrite;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\ArrayTool;

/**
 * ฟอร์มสร้าง/แก้ไข หน้าเว็บไซต์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=pagewrite
   *
   * @param string $id
   * @return string
   */
  public function render($index)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/index/model/pagewrite/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true,
        'upload' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Details of').' '.Language::get($index->owner === 'index' ? 'Page' : 'Module')
    ));
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Language'),
      'id' => 'language',
      'comment' => Language::get('Select the language of this item (Select the first Is present in every language)'),
    ));
    // language
    $groups->add('select', array(
      'id' => 'language',
      'labelClass' => 'g-input icon-language',
      'itemClass' => 'width',
      'options' => ArrayTool::replace(array('' => Language::get('all languages')), Language::installedLanguage()),
      'value' => $index->language
    ));
    $groups->add('a', array(
      'id' => 'btn_copy',
      'class' => 'button icon-copy copy',
      'title' => Language::get('Copy this item to the selected language')
    ));
    // module
    $fieldset->add('text', array(
      'id' => 'module',
      'labelClass' => 'g-input icon-modules',
      'itemClass' => 'item',
      'label' => Language::get('Module'),
      'comment' => Language::get('Name of this module. English lowercase and number only, short. (Can not use a reserve or a duplicate)'),
      'maxlength' => 64,
      'value' => $index->module
    ));
    // topic
    $fieldset->add('text', array(
      'id' => 'topic',
      'labelClass' => 'g-input icon-edit',
      'itemClass' => 'item',
      'label' => Language::get('Topic'),
      'comment' => Language::get('Text displayed on the Title Bar of the browser (3 - 255 characters)'),
      'maxlength' => 255,
      'value' => $index->topic
    ));
    // keywords
    $fieldset->add('textarea', array(
      'id' => 'keywords',
      'labelClass' => 'g-input icon-tags',
      'itemClass' => 'item',
      'label' => Language::get('Keywords'),
      'comment' => Language::get('Text keywords for SEO or Search Engine to search'),
      'value' => $index->keywords
    ));
    // description
    $fieldset->add('textarea', array(
      'id' => 'description',
      'labelClass' => 'g-input icon-file',
      'itemClass' => 'item',
      'label' => Language::get('Description'),
      'comment' => Language::get('Text short summary of your story. Which can be used to show in your theme. (If not the program will fill in the contents of the first paragraph)'),
      'value' => $index->description
    ));
    // detail
    $fieldset->add('ckeditor', array(
      'id' => 'detail',
      'itemClass' => 'item',
      'height' => 300,
      'language' => Language::name(),
      'toolbar' => 'Document',
      'upload' => true,
      'label' => Language::get('Detail'),
      'value' => $index->detail
    ));
    // published_date
    $fieldset->add('date', array(
      'id' => 'published_date',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'item',
      'label' => Language::get('Published date'),
      'comment' => Language::get('The date of publication of this information. The publisher will start automatically when you log on due date'),
      'value' => $index->published_date
    ));
    // published
    $fieldset->add('select', array(
      'id' => 'published',
      'labelClass' => 'g-input icon-published1',
      'itemClass' => 'item',
      'label' => Language::get('Published'),
      'comment' => Language::get('Publish this item'),
      'options' => Language::get('PUBLISHEDS'),
      'value' => $index->published
    ));
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => Language::get('Save')
    ));
    // preview button
    if ($index->owner == 'index') {
      $fieldset->add('button', array(
        'id' => 'btn_preview',
        'class' => 'button preview large',
        'value' => Language::get('Preview')
      ));
    }
    // owner
    $fieldset->add('hidden', array(
      'id' => 'owner',
      'value' => $index->owner
    ));
    // id
    $fieldset->add('hidden', array(
      'id' => 'id',
      'value' => $index->id
    ));
    $form->script('initIndexWrite();');
    return $form->render();
  }
}