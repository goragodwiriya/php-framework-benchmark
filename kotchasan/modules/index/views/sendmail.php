<?php
/*
 * @filesource index/views/sendmail.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Sendmail;

use \Kotchasan\Http\Request;
use \Kotchasan\Template;
use \Kotchasan\Text;

/**
 * หน้าส่งอีเมล์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * แสดงผล
	 *
	 * @param Request $request
	 * @return string
	 */
	public function render(Request $request)
	{
		// antispam
		$antispamchar = Text::rndname(32);
		$_SESSION[$antispamchar] = Text::rndname(4);
		$index = (object)array('description' => self::$cfg->web_description);
		$template = Template::create('member', 'member', 'sendmail');
		$template->add(array(
			'/{TOPIC}/' => $index->topic,
			'/{LNG_([\w\s\.\-\'\(\),%\/:&\#;]+)}/e' => '\Kotchasan\Language::get(array(1=>"$1"))',
			'/{ANTISPAM}/' => $antispamchar,
			'/{WEBURL}/' => WEB_URL
		));
		$index->detail = $template->render();
		$index->keywords = self::$cfg->web_title;
		return $index;
	}
}