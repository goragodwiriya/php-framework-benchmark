<?php
/*
 * @filesource index/views/member.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Member;

use \Kotchasan\Template;
use \Kotchasan\Text;
use \Kotchasan\Language;
use \Gcms\Gcms;
use \Kotchasan\Login;

/**
 * register, forgot page
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * หน้าสมัครสมาชิก
	 *
	 * @param bool $modal true แสดงแบบ modal, false (default) แสดงหน้าเว็บปกติ
	 * @return object
	 */
	public function register($modal = false)
	{
		$index = (object)array(
				'canonical' => WEB_URL.'index.php?module=register',
				'topic' => Language::get('Create new account'),
				'description' => self::$cfg->web_description
		);
		// antispam
		$antispamchar = Text::rndname(32);
		$_SESSION[$antispamchar] = Text::rndname(4);
		$template = Template::create('member', 'member', 'registerfrm');
		$template->add(array(
			'/<PHONE>(.*)<\/PHONE>/isu' => empty(self::$cfg->member_phone) ? '' : '\\1',
			'/<IDCARD>(.*)<\/IDCARD>/isu' => empty(self::$cfg->member_idcard) ? '' : '\\1',
			'/<INVITE>(.*)<\/INVITE>/isu' => empty(self::$cfg->member_invitation) ? '' : '\\1',
			'/{TOPIC}/' => $index->topic,
			'/{LNG_([\w\s\.\-\'\(\),%\/:&\#;]+)}/e' => '\Kotchasan\Language::get(array(1=>"$1"))',
			'/{ANTISPAM}/' => $antispamchar,
			'/{WEBURL}/' => WEB_URL,
			'/{MODAL}/' => $modal ? 'true' : 'false',
			'/{INVITE}/' => self::$request->cookie('invite')->topic()
		));
		$index->detail = $template->render();
		$index->keywords = $index->topic;
		if (isset(Gcms::$view)) {
			Gcms::$view->addBreadcrumb($index->canonical, $index->topic, $index->topic);
		}
		return $index;
	}

	/**
	 * หน้าขอรหัสผ่านใหม่
	 *
	 * @param bool $modal true แสดงแบบ modal, false (default) แสดงหน้าเว็บปกติ
	 * @return object
	 */
	public function forgot($modal = false)
	{
		$index = (object)array(
				'canonical' => WEB_URL.'index.php?module=forgot',
				'topic' => Language::get('Request new password'),
				'description' => self::$cfg->web_description
		);
		$template = Template::create('member', 'member', 'forgotfrm');
		$template->add(array(
			'/{TOPIC}/' => $index->topic,
			'/{EMAIL}/' => Login::$text_email,
			'/{LNG_([\w\s\.\-\'\(\),%\/:&\#;]+)}/e' => '\Kotchasan\Language::get(array(1=>"$1"))',
			'/{WEBURL}/' => WEB_URL,
			'/{MODAL}/' => $modal ? 'true' : 'false'
		));
		$index->detail = $template->render();
		$index->keywords = $index->topic;
		if (isset(Gcms::$view)) {
			Gcms::$view->addBreadcrumb($index->canonical, $index->topic, $index->topic);
		}
		return $index;
	}

	/**
	 * หน้า login
	 *
	 * @return object
	 */
	public function dologin()
	{
		$index = (object)array(
				'canonical' => WEB_URL.'index.php?module=dologin',
				'topic' => Language::get('Visitors please login'),
				'description' => self::$cfg->web_description
		);
		$template = Template::create('member', 'member', 'loginfrm');
		$template->add(array(
			'/{LNG_([\w\s\.\-\'\(\),%\/:&\#;]+)}/e' => '\Kotchasan\Language::get(array(1=>"$1"))',
			'/{EMAIL}/' => Login::$text_email,
			'/{PASSWORD}/' => Login::$text_password,
			'/{REMEMBER}/' => self::$request->cookie('login_remember')->toInt() == 1 ? 'checked' : '',
			'/{FACEBOOK}/' => empty(self::$cfg->facebook_appId) ? 'hidden' : 'facebook',
			'/{TOPIC}/' => $index->topic
		));
		$index->detail = $template->render();
		$index->keywords = $index->topic;
		if (isset(Gcms::$view)) {
			Gcms::$view->addBreadcrumb($index->canonical, $index->topic, $index->topic);
		}
		return $index;
	}
}