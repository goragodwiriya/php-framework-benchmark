<?php
/*
 * @filesource document/views/admin/categorywrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Categorywrite;

use \Kotchasan\Html;
use \Kotchasan\Language;
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
	 * module=document-categorywrite
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
				'action' => 'index.php/document/model/admin/categorywrite/save',
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
		$groups = $fieldset->add('groups', array(
			'comment' => Language::get('The configuration of the newly added entry only. You can configure it once each.'),
		));
		// published
		$groups->add('select', array(
			'id' => 'published',
			'labelClass' => 'g-input icon-published1',
			'itemClass' => 'width50',
			'label' => Language::get('Published'),
			'options' => Language::get('PUBLISHEDS'),
			'value' => $index->published
		));
		// can_reply
		$groups->add('select', array(
			'id' => 'can_reply',
			'labelClass' => 'g-input icon-comments',
			'itemClass' => 'width50',
			'label' => Language::get('Comment'),
			'options' => Language::get('REPLIES'),
			'value' => $index->can_reply
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
			if (is_file(ROOT_PATH.DATA_FOLDER.'document/'.$img)) {
				$img = WEB_URL.DATA_FOLDER.'document/'.$img;
			} else {
				$img = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/document/img/default_icon.png');
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