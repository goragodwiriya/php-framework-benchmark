<?php
/*
 * @filesource index/views/mailwrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Mailwrite;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\CKEditor;

/**
 * ฟอร์มเขียน/แก้ไข แม่แบบอีเมล์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * module=mailwrite
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
				'action' => 'index.php/index/model/mailwrite/save',
				'onsubmit' => 'doFormSubmit',
				'ajax' => true
		));
		$fieldset = $form->add('fieldset', array(
			'title' => Language::get('Details of').' '.Language::get('Email')
		));
		// from_email
		$fieldset->add('text', array(
			'id' => 'from_email',
			'labelClass' => 'g-input icon-email',
			'itemClass' => 'item',
			'label' => Language::get('Sender'),
			'comment' => Language::get('E-mail address for replies. If you do not want a response, please leave blank.'),
			'maxlength' => 255,
			'value' => $index->from_email
		));
		// copy_to
		$fieldset->add('text', array(
			'id' => 'copy_to',
			'labelClass' => 'g-input icon-cc',
			'itemClass' => 'item',
			'label' => Language::get('Copy to'),
			'comment' => Language::get('More email addresses to send a copy of the email. Separate each item with comma (,)'),
			'value' => $index->copy_to
		));
		// subject
		$fieldset->add('text', array(
			'id' => 'subject',
			'labelClass' => 'g-input icon-edit',
			'itemClass' => 'item',
			'label' => Language::get('Subject'),
			'title' => Language::get('Please fill in').' '.Language::get('Subject'),
			'maxlength' => 64,
			'value' => $index->subject
		));
		// language
		$fieldset->add('select', array(
			'id' => 'language',
			'labelClass' => 'g-input icon-language',
			'itemClass' => 'item',
			'label' => Language::get('Language'),
			'comment' => Language::get('The system will e-mail the selected language. If you do not use the default language.'),
			'options' => Language::installedLanguage(),
			'value' => $index->language
		));
		// detail
		$detail = new CKEditor('textarea', array(
			'id' => 'detail',
			'itemClass' => 'item',
			'height' => 300,
			'language' => Language::name(),
			'toolbar' => 'Document',
			'upload' => true,
			'label' => Language::get('Detail'),
			'value' => $index->detail
		));
		$fieldset->appendChild($detail->render());
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
		return $form->render();
	}
}