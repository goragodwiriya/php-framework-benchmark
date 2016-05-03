<?php
/*
 * @filesource index/views/mailserver.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Mailserver;

use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * ตั้งค่าระบบอีเมล์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * module=mailserver
	 *
	 * @param object $config
	 * @return string
	 */
	public function render($config)
	{
		// form
		$form = Html::create('form', array(
				'id' => 'setup_frm',
				'class' => 'setup_frm',
				'autocomplete' => 'off',
				'action' => 'index.php/index/model/mailserver/save',
				'onsubmit' => 'doFormSubmit',
				'ajax' => true
		));
		$fieldset = $form->add('fieldset', array(
			'title' => Language::get('General')
		));
		// noreply_email
		$fieldset->add('text', array(
			'id' => 'noreply_email',
			'labelClass' => 'g-input icon-email',
			'itemClass' => 'item',
			'label' => Language::get('noreply email'),
			'comment' => Language::get('Email addresses for sender and do not reply such as no-reply@domain.tld'),
			'maxlength' => 255,
			'value' => isset($config->noreply_email) ? $config->noreply_email : self::$cfg->noreply_email
		));
		// email_charset
		$fieldset->add('text', array(
			'id' => 'email_charset',
			'labelClass' => 'g-input icon-language',
			'itemClass' => 'item',
			'label' => Language::get('Email encoding'),
			'comment' => Language::get('Specify the language code of the email, as utf-8'),
			'value' => isset($config->email_charset) ? $config->email_charset : self::$cfg->email_charset
		));
		// email_use_phpMailer
		$fieldset->add('select', array(
			'id' => 'email_use_phpMailer',
			'labelClass' => 'g-input icon-clock',
			'itemClass' => 'item',
			'label' => Language::get('Mail program'),
			'comment' => Language::get('Set the application for send an email'),
			'options' => Language::get('MAIL_PROGRAMS'),
			'value' => isset($config->email_use_phpMailer) ? $config->email_use_phpMailer : self::$cfg->email_use_phpMailer
		));
		// ตั้งค่า mail server
		$fieldset = $form->add('fieldset', array(
			'title' => Language::get('Mail Server Settings')
		));
		// email_Host
		$fieldset->add('text', array(
			'id' => 'email_Host',
			'labelClass' => 'g-input icon-world',
			'itemClass' => 'item',
			'label' => Language::get('Mail server'),
			'comment' => Language::get('The name of the mail server as localhost or smtp.gmail.com (To change the settings of your email is the default. To remove this box entirely.)'),
			'value' => isset($config->email_Host) ? $config->email_Host : self::$cfg->email_Host
		));
		// email_Port
		$fieldset->add('number', array(
			'id' => 'email_Port',
			'labelClass' => 'g-input icon-config',
			'itemClass' => 'item',
			'label' => Language::get('Port'),
			'comment' => Language::get('Mail server port number (default is 25, for GMail used 465, 587 for DirectAdmin).'),
			'value' => isset($config->email_Port) ? $config->email_Port : self::$cfg->email_Port
		));
		// email_SMTPAuth
		$fieldset->add('select', array(
			'id' => 'email_SMTPAuth',
			'labelClass' => 'g-input icon-config',
			'itemClass' => 'item',
			'label' => Language::get('Authentication require'),
			'comment' => Language::get('How to define user authentication for mail servers. If you enable it, you must configure below correctly.'),
			'options' => Language::get('BOOLEANS'),
			'value' => isset($config->email_SMTPAuth) ? $config->email_SMTPAuth : self::$cfg->email_SMTPAuth
		));
		// email_SMTPSecure
		$fieldset->add('select', array(
			'id' => 'email_SMTPSecure',
			'labelClass' => 'g-input icon-config',
			'itemClass' => 'item',
			'label' => Language::get('SSL support'),
			'comment' => Language::get('Enable SSL encryption for sending email'),
			'options' => Language::get('SMTPSECURIES'),
			'value' => isset($config->email_SMTPSecure) ? $config->email_SMTPSecure : self::$cfg->email_SMTPSecure
		));
		// email_Username
		$fieldset->add('text', array(
			'id' => 'email_Username',
			'labelClass' => 'g-input icon-user',
			'itemClass' => 'item',
			'label' => Language::get('Username'),
			'comment' => Language::get('Username for the mail server. (To change, enter a new value.)'),
			'value' => isset($config->email_Username) ? $config->email_Username : self::$cfg->email_Username
		));
		// email_Password
		$fieldset->add('text', array(
			'id' => 'email_Password',
			'labelClass' => 'g-input icon-password',
			'itemClass' => 'item',
			'label' => Language::get('Password'),
			'comment' => Language::get('Password of the mail server. (To change the fill.)')
		));
		$fieldset = $form->add('fieldset', array(
			'class' => 'submit'
		));
		// submit
		$fieldset->add('submit', array(
			'class' => 'button ok large',
			'value' => Language::get('Save')
		));
		$form->script('inintMailserver();');
		return $form->render();
	}
}