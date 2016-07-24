<?php
/*
 * @filesource board/views/admin/categorywrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Board\Admin\Categorywrite;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\HtmlTable;
use \Kotchasan\Http\UploadedFile;
use \Kotchasan\ArrayTool;

/**
 * ฟอร์มสร้าง/แก้ไข หมวดหมู่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=board-categorywrite
   *
   * @param object $index
   * @return string
   */
  public function render($index)
  {
    // form
    $form = Html::create('form', array(
        'id' => 'setup_frm',
        'class' => 'setup_frm',
        'autocomplete' => 'off',
        'action' => 'index.php/board/model/admin/categorywrite/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Category')
    ));
    // category_id
    $fieldset->add('text', array(
      'id' => 'category_id',
      'labelClass' => 'g-input icon-category',
      'itemClass' => 'item',
      'label' => Language::get('ID'),
      'comment' => Language::get('The ID of the category, is unique to each category and must be greater than 0.'),
      'value' => $index->category_id
    ));
    // ภาษาปัจจุบัน
    $lng = Language::name();
    $multi_language = sizeof(Language::installedLanguage()) > 1;
    // topic,detail,icon
    $topic = ArrayTool::unserialize($index->topic);
    $detail = ArrayTool::unserialize($index->detail);
    $icon = ArrayTool::unserialize($index->icon);
    foreach (Language::installedLanguage() as $item) {
      $fieldset = $form->add('fieldset', array(
        'title' => Language::get('Details of').' '.Language::get('Category').' <img src="'.WEB_URL.'/language/'.$item.'.gif" alt="'.$item.'">'
      ));
      // topic
      $fieldset->add('text', array(
        'id' => 'topic_'.$item,
        'name' => 'topic['.$item.']',
        'labelClass' => 'g-input icon-edit',
        'itemClass' => 'item',
        'label' => Language::get('Category'),
        'comment' => Language::get('The name of the category, less than 50 characters'),
        'maxlength' => 50,
        'value' => isset($topic[$item]) ? $topic[$item] : (isset($topic['']) && (!$multi_language || ($item == $lng && !isset($topic[$lng]))) ? $topic[''] : '')
      ));
      // detail
      $fieldset->add('textarea', array(
        'id' => 'detail_'.$item,
        'name' => 'detail['.$item.']',
        'labelClass' => 'g-input icon-file',
        'itemClass' => 'item',
        'label' => Language::get('Description'),
        'comment' => Language::get('Description of this category, less than 255 characters'),
        'rows' => 3,
        'value' => isset($detail[$item]) ? $detail[$item] : (isset($detail['']) && (!$multi_language || ($item == $lng && !isset($detail[$lng]))) ? $detail[''] : '')
      ));
      // icon
      $img = isset($icon[$item]) ? $icon[$item] : (isset($icon['']) && (!$multi_language || ($item == $lng && !isset($icon[$lng]))) ? $icon[''] : '');
      if (is_file(ROOT_PATH.DATA_FOLDER.'board/'.$img)) {
        $img = WEB_URL.DATA_FOLDER.'board/'.$img;
      } else {
        $img = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/board/img/default_icon.png');
      }
      $fieldset->add('file', array(
        'id' => 'icon_'.$item,
        'name' => 'icon['.$item.']',
        'labelClass' => 'g-input icon-upload',
        'itemClass' => 'item',
        'label' => Language::get('Icon'),
        'comment' => str_replace('%s', 'jpg gif png', Language::get('Image upload types %s only, should be prepared to have the same size')),
        'dataPreview' => 'img'.$item,
        'previewSrc' => $img
      ));
    }
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Upload')
    ));
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Type of file uploads'),
      'comment' => Language::get('Type of files allowed to upload it, if not select any item can not be uploaded.')
    ));
    // img_upload_type
    foreach (array('jpg', 'gif', 'png') as $item) {
      $groups->add('checkbox', array(
        'id' => 'img_upload_type_'.$item,
        'name' => 'img_upload_type[]',
        'itemClass' => 'width',
        'label' => $item,
        'value' => $item,
        'checked' => isset($index->img_upload_type) && is_array($index->img_upload_type) ? in_array($item, $index->img_upload_type) : false
      ));
    }
    // img_upload_size
    $upload_max_filesize = UploadedFile::getUploadSize(true);
    $options = array();
    foreach (array(100, 200, 300, 400, 500, 600, 700, 800, 900, 1024, 2048) as $item) {
      if ($item * 1024 <= $upload_max_filesize) {
        $options[$item] = $item.' Kb.';
      }
    }
    $fieldset->add('select', array(
      'id' => 'img_upload_size',
      'labelClass' => 'g-input icon-config',
      'itemClass' => 'item',
      'label' => Language::get('Size of the file upload'),
      'comment' => Language::get('Size of file allowed to upload up (Kb.)'),
      'options' => $options,
      'value' => isset($index->img_upload_size) ? $index->img_upload_size : $upload_max_filesize / 1024
    ));
    // img_law
    $fieldset->add('select', array(
      'id' => 'img_law',
      'labelClass' => 'g-input icon-config',
      'itemClass' => 'item',
      'label' => Language::get('Upload rules'),
      'comment' => Language::get('The rules for uploading pictures for questions. (Choose the type of files. If is uploaded.)'),
      'options' => Language::get('IMG_LAW'),
      'value' => isset($index->img_law) ? $index->img_law : 0
    ));
    $fieldset = $form->add('fieldset', array(
      'title' => Language::get('Role of Members')
    ));
    // สถานะสมาชิก
    $status = array();
    $status[-1] = Language::get('Guest');
    foreach (self::$cfg->member_status AS $i => $item) {
      $status[$i] = $item;
    }
    $table = new HtmlTable(array(
      'class' => 'responsive config_table'
    ));
    $table->addHeader(array(
      array(),
      array('text' => Language::get('Posting')),
      array('text' => Language::get('Comment')),
      array('text' => Language::get('Viewing')),
      array('text' => Language::get('Moderator'))
    ));
    foreach ($status AS $i => $item) {
      if ($i != 1) {
        $row = array();
        $row[] = array(
          'scope' => 'col',
          'text' => $item
        );
        $check = isset($index->can_post) && is_array($index->can_post) && in_array($i, $index->can_post) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => '<label data-text="'.Language::get('Posting').'"><input type=checkbox name=can_post[] title="'.Language::get('Members of this group can post').'" value='.$i.$check.'></label>'
        );
        $check = in_array($i, $index->can_reply) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => '<label data-text="'.Language::get('Comment').'"><input type=checkbox name=can_reply[] title="'.Language::get('Members of this group can post comment').'" value='.$i.$check.'></label>'
        );
        $check = isset($index->can_view) && is_array($index->can_view) && in_array($i, $index->can_view) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => '<label data-text="'.Language::get('Viewing').'"><input type=checkbox name=can_view[] title="'.Language::get('Members of this group can see the content').'" value='.$i.$check.'></label>'
        );
        $check = isset($index->moderator) && is_array($index->moderator) && in_array($i, $index->moderator) ? ' checked' : '';
        $row[] = array(
          'class' => 'center',
          'text' => $i > 1 ? '<label data-text="'.Language::get('Moderator').'"><input type=checkbox name=moderator[] title="'.Language::get('Members of this group can edit content written by others').'" value='.$i.$check.'></label>' : ''
        );
        $table->addRow($row, array(
          'class' => 'status'.$i
        ));
      }
    }
    $div = $fieldset->add('div', array(
      'class' => 'item'
    ));
    $div->appendChild($table->render());
    $fieldset = $form->add('fieldset', array(
      'class' => 'submit'
    ));
    // submit
    $fieldset->add('submit', array(
      'class' => 'button ok large',
      'value' => Language::get('Save')
    ));
    // id
    $fieldset->add('hidden', array(
      'id' => 'id',
      'value' => $index->id
    ));
    // module_id
    $fieldset->add('hidden', array(
      'id' => 'module_id',
      'value' => $index->module_id
    ));
    return $form->render();
  }
}