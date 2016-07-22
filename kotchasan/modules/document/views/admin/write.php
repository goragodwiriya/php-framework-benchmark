<?php
/*
 * @filesource document/views/admin/write.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Write;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * ฟอร์มสร้าง/แก้ไข เนื้อหา
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * module=document-write
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
        'action' => 'index.php/document/model/admin/write/save',
        'onsubmit' => 'doFormSubmit',
        'ajax' => true
    ));
    // ภาษาที่ติดตั้ง
    $languages = Language::installedLanguage();
    foreach ($languages as $item) {
      $details = isset($index->details[$item]) ? $index->details[$item] : (object)array('topic' => '', 'keywords' => '', 'description' => '', 'detail' => '', 'relate' => '');
      // รายละเอียดแต่ละภาษา
      $fieldset = $form->add('fieldset', array(
        'id' => 'detail_'.$item,
        'title' => Language::get('Detail').'&nbsp;<img src='.WEB_URL.'language/'.$item.'.gif alt='.$item.'>'
      ));
      // topic
      $fieldset->add('text', array(
        'id' => 'topic_'.$item,
        'labelClass' => 'g-input icon-edit',
        'itemClass' => 'item',
        'label' => Language::get('Topic'),
        'comment' => Language::get('Title or topic of the article 3-255 characters'),
        'value' => $details->topic
      ));
      // keywords
      $fieldset->add('textarea', array(
        'id' => 'keywords_'.$item,
        'labelClass' => 'g-input icon-tags',
        'itemClass' => 'item',
        'label' => Language::get('Keywords'),
        'comment' => Language::get('Text keywords for SEO or Search Engine to search'),
        'value' => $details->keywords
      ));
      // relate
      $fieldset->add('text', array(
        'id' => 'relate_'.$item,
        'labelClass' => 'g-input icon-edit',
        'itemClass' => 'item',
        'label' => Language::get('Relate'),
        'comment' => Language::get('Title or topic of the article 3-255 characters'),
        'value' => $details->relate
      ));
      // description
      $fieldset->add('textarea', array(
        'id' => 'description_'.$item,
        'labelClass' => 'g-input icon-file',
        'itemClass' => 'item',
        'label' => Language::get('Description'),
        'comment' => Language::get('Text short summary of your story. Which can be used to show in your theme. (If not the program will fill in the contents of the first paragraph)'),
        'value' => $details->description
      ));
      // detail
      $fieldset->add('ckeditor', array(
        'id' => 'details_'.$item,
        'itemClass' => 'item',
        'height' => 300,
        'language' => Language::name(),
        'toolbar' => 'Document',
        'upload' => true,
        'label' => Language::get('Detail'),
        'value' => $details->detail
      ));
    }
    // รายละเอียดอื่นๆ
    $fieldset = $form->add('fieldset', array(
      'id' => 'options',
      'title' => Language::get('Set up or configure other details')
    ));
    // alias
    $fieldset->add('text', array(
      'id' => 'alias',
      'labelClass' => 'g-input icon-world',
      'itemClass' => 'item',
      'label' => Language::get('Alias'),
      'comment' => Language::get('Used for the URL of the web page (SEO) can use letters, numbers and _ only can not have duplicate names.'),
      'value' => $index->alias
    ));
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Article Date'),
      'comment' => Language::get('The date that the story was written')
    ));
    // create_date
    preg_match('/([0-9]{4,4}\-[0-9]{2,2}\-[0-9]{2,2})\s([0-9]+):([0-9]+)/', date('Y-m-d H:i', $index->create_date), $match);
    $groups->add('date', array(
      'id' => 'create_date',
      'labelClass' => 'g-input icon-calendar',
      'itemClass' => 'width',
      'value' => $match[1]
    ));
    // create_hour
    $datas = array();
    for ($i = 0; $i < 24; $i++) {
      $d = sprintf('%02d', $i);
      $datas[$d] = $d;
    }
    $groups->add('select', array(
      'id' => 'create_hour',
      'labelClass' => 'width',
      'label' => Language::get('Time'),
      'options' => $datas,
      'value' => $match[2]
    ));
    $groups->add('span', array(
      'class' => 'width',
      'innerHTML' => ':'
    ));
    // create_minute
    $datas = array();
    for ($i = 0; $i < 60; $i++) {
      $d = sprintf('%02d', $i);
      $datas[$d] = $d;
    }
    $groups->add('select', array(
      'id' => 'create_minute',
      'labelClass' => 'width',
      'options' => $datas,
      'value' => $match[3]
    ));
    // picture
    if (!empty($index->picture) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$index->picture)) {
      $img = WEB_URL.DATA_FOLDER.'document/'.$index->picture;
    } else {
      $img = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/document/img/document-icon.png');
    }
    $fieldset->add('file', array(
      'id' => 'picture',
      'labelClass' => 'g-input icon-upload',
      'itemClass' => 'item',
      'label' => Language::get('Thumbnail'),
      'comment' => str_replace(array(':type', ':width', ':height'), array(implode(', ', $index->img_typies), $index->icon_width, $index->icon_height), Language::get('Browse image uploaded, type :type size :width*:height pixel (automatic resize)')),
      'dataPreview' => 'imgPicture',
      'previewSrc' => $img
    ));
    // หมวดหมู่
    $categories = array(0 => Language::get('Uncategorized'));
    foreach (\Index\Category\Model::categories((int)$index->module_id) as $item) {
      $categories[$item['category_id']] = Gcms::ser2Str($item, 'topic');
    }
    // category_id
    $fieldset->add('select', array(
      'id' => 'category_'.$index->module_id,
      'name' => 'category_id',
      'labelClass' => 'g-input icon-category',
      'label' => Language::get('Category'),
      'comment' => Language::get('Select the category you want'),
      'itemClass' => 'item',
      'options' => $categories,
      'value' => $index->category_id
    ));
    // can_reply
    $fieldset->add('select', array(
      'id' => 'can_reply',
      'labelClass' => 'g-input icon-comments',
      'itemClass' => 'item',
      'label' => Language::get('Comment'),
      'comment' => Language::get('Comment the story'),
      'options' => Language::get('REPLIES'),
      'value' => $index->can_reply
    ));
    // show_news
    $groups = $fieldset->add('groups-table', array(
      'label' => Language::get('Display in the widget').' <a href="http://gcms.in.th/index.php?module=howto&id=311" target=_blank class=icon-help></a>',
      'comment' => Language::get('Use this option if you want a list that is presented in part by itself.')
    ));
    foreach (Language::get('SHOW_NEWS') as $key => $value) {
      $groups->add('checkbox', array(
        'id' => 'show_news_'.$key,
        'name' => 'show_news[]',
        'itemClass' => 'width',
        'label' => $value,
        'value' => $key,
        'checked' => strpos($index->show_news, "$key=1") === false ? false : true
      ));
    }
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
    // preview
    $fieldset->add('button', array(
      'id' => 'preview',
      'class' => 'button preview large',
      'value' => Language::get('Preview')
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
    // tab ที่เลือก
    $tab = self::$request->get('tab')->toString();
    $tab = empty($tab) ? 'detail_'.reset($languages) : $tab;
    $form->script('initWriteTab("accordient_menu", "'.$tab.'");');
    $form->script('checkSaved("preview", "'.WEB_URL.'/index.php?module='.$index->module.'", "id");');
    $form->script('new GValidator("alias", "keyup,change", checkAlias, "index.php/index/model/checker/alias", null, "setup_frm");');
    $form->script('selectChanged("category_'.$index->module_id.'","index.php/index/model/category/action",doFormSubmit);');
    // tab
    $fieldset->add('hidden', array(
      'id' => 'tab',
      'value' => $tab
    ));
    return $form->render();
  }
}