<?php
/*
 * @filesource index/views/login.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Login;

use \Kotchasan\Html;
use \Kotchasan\Language;
use \Kotchasan\Login;
use \Kotchasan\Template;

/**
 * ฟอร์ม login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	public function render()
	{
		// form
		$form = Html::create('form', array(
				'id' => 'login_frm',
				'class' => 'login',
				'autocomplete' => 'off',
				'ajax' => false,
				'action' => '?action=login'
		));
		// h1
		$form->add('h1', array(
			'class' => 'icon-customer',
			'innerHTML' => Language::get('Administrator Area')
		));
		// message
		if (!empty(Login::$login_message)) {
			$form->add('p', array(
				'class' => empty(Login::$login_input) ? 'message' : 'error',
				'innerHTML' => Login::$login_message
			));
			if (isset(Login::$login_input)) {
				$a = array();
				$a[] = 'var input = $E("'.Login::$login_input.'");';
				$a[] = 'input.focus();';
				$a[] = 'input.select();';
				$form->script(implode("\n", $a));
			}
		}
		// fieldset
		$fieldset = $form->add('fieldset');
		// email or phone
		$fieldset->add('text', array(
			'id' => 'login_email',
			'labelClass' => 'g-input icon-email',
			'placeholder' => Language::get('Email'),
			'value' => isset(Login::$text_email) ? Login::$text_email : '',
			'autofocus',
			'required',
			'accesskey' => 'e',
			'maxlength' => 255
		));
		// password
		$fieldset->add('password', array(
			'id' => 'login_password',
			'labelClass' => 'g-input icon-password',
			'placeholder' => Language::get('Password'),
			'value' => isset(Login::$text_password) ? Login::$text_password : ''
		));
		// input-groups (div สำหรับจัดกลุ่ม input)
		$group = $fieldset->add('groups');
		// a
		$group->add('a', array(
			'href' => self::$request->getUri()->withParams(array('action' => 'forgot'), true),
			'class' => 'td',
			'title' => Language::get('Request new password'),
			'innerHTML' => ''.Language::get('Forgot').' ?'
		));
		// checkbox
		$group->add('checkbox', array(
			'id' => 'login_remember',
			'checked' => self::$request->cookie('login_remember')->toInt(),
			'value' => 1,
			'label' => Language::get('Remember me'),
			'labelClass' => 'td right'
		));
		// submit
		$fieldset->add('submit', array(
			'class' => 'button ok large wide',
			'value' => Language::get('Sign In')
		));
		// template
		$template = Template::create('', '', 'login');
		$template->add(array(
			'/{FORM}/' => $form->render()
		));
		return $template->render();
	}
}