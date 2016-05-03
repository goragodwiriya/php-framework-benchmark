<?php
/*
 * @filesource index/views/languages.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Languages;

use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * รายการภาษาที่ติดตั้งแล้ว
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * module=languages
	 *
	 * @return string
	 */
	public function render()
	{
		$section = Html::create('div', array(
				'class' => 'subtitle',
				'innerHTML' => Language::get('Add, edit, and reorder the language of the site. The first item is the default language of the site.')
		));
		$list = $section->add('ol', array(
			'class' => 'editinplace_list',
			'id' => 'languages'
		));
		$languages = array();
		foreach (array_merge(self::$cfg->languages, Language::installedLanguage()) as $item) {
			if (empty($languages[$item])) {
				$languages[$item] = $item;
				$row = $list->add('li', array(
					'id' => 'L_'.$item,
					'class' => 'sort'
				));
				$row->add('span', array(
					'class' => 'icon-move'
				));
				$row->add('span', array(
					'id' => 'delete_'.$item,
					'class' => 'icon-delete',
					'title' => Language::get('Delete')
				));
				$row->add('a', array(
					'class' => 'icon-edit',
					'href' => '?module=languageadd&amp;id='.$item,
					'title' => Language::get('Edit')
				));
				$chk = in_array($item, self::$cfg->languages) ? 'check' : 'uncheck';
				$row->add('span', array(
					'id' => 'check_'.$item,
					'class' => 'icon-'.$chk
				));
				$row->add('span', array(
					'style' => 'background-image:url('.WEB_URL.'language/'.$item.'.gif)'
				));
				$row->add('span', array(
					'innerHTML' => $item
				));
			}
		}
		$div = $section->add('div', array(
			'class' => 'submit'
		));
		$a = $div->add('a', array(
			'class' => 'button add large',
			'href' => '?module=languageadd'
		));
		$a->add('span', array(
			'class' => 'icon-plus',
			'innerHTML' => Language::get('Add New').' '.Language::get('Language')
		));
		$section->script('inintLanguages("languages");');
		return $section->render();
	}
}