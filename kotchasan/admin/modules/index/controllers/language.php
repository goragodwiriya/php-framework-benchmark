<?php
/*
 * @filesource index/controllers/language.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Language;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Html;

/**
 * รายการภาษา
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

	/**
	 * แสดงผล
	 */
	public function render()
	{
		// แอดมิน
		if (Login::isAdmin()) {
			// แสดงผล
			$section = Html::create('section');
			// breadcrumbs
			$breadcrumbs = $section->add('div', array(
				'class' => 'breadcrumbs'
			));
			$ul = $breadcrumbs->add('ul');
			$ul->appendChild('<li><span class="icon-tools">'.Language::get('Tools').'</span></li>');
			$ul->appendChild('<li><span>'.Language::get('Language').'</span></li>');
			$section->add('header', array(
				'innerHTML' => '<h1 class="icon-language">'.$this->title().'</h1>'
			));
			// แสดงตาราง
			$section->appendChild(createClass('Index\Language\View')->render());
			return $section->render();
		} else {
			// 404.html
			return \Index\Error\Controller::page404();
		}
	}

	/**
	 * title bar
	 */
	public function title()
	{
		return Language::get('Add and manage the display language of the site');
	}
}