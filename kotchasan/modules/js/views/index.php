<?php
/*
 * @filesource js/views/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Js\Index;

use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * Generate JS file
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

	/**
	 * สร้างไฟล์ js
	 */
	public function index()
	{
		// cache 1 month
		$expire = 2592000;
		$this->setHeaders(array(
			'Content-type' => 'text/javascript; charset: UTF-8',
			'Cache-Control' => 'max-age='.$expire.', must-revalidate, public',
			'Expires' => gmdate('D, d M Y H:i:s', time() + $expire).' GMT',
			'Last-Modified' => gmdate('D, d M Y H:i:s', time() - $expire).' GMT'
		));
		// default js
		$js = array();
		$js[] = file_get_contents(ROOT_PATH.'js/gajax.js');
		$js[] = file_get_contents(ROOT_PATH.'js/gddmenu.js');
		$js[] = file_get_contents(ROOT_PATH.'js/table.js');
		$js[] = file_get_contents(ROOT_PATH.'js/common.js');
		$js[] = file_get_contents(ROOT_PATH.'js/gtooltip.js');
		$js[] = file_get_contents(ROOT_PATH.'js/gcms.js');
		$lng = Language::name();
		$data_folder = Language::languageFolder();
		if (is_file($data_folder.$lng.'.js')) {
			$js[] = file_get_contents($data_folder.$lng.'.js');
		}
		// js ของโมดูล
		$dir = ROOT_PATH.'modules/';
		$f = @opendir($dir);
		if ($f) {
			while (false !== ($text = readdir($f))) {
				if ($text != "." && $text != "..") {
					if (is_dir($dir.$text)) {
						if (is_file($dir.$text.'/script.js')) {
							$js[] = file_get_contents($dir.$text.'/script.js');
						}
					}
				}
			}
			closedir($f);
		}
		// js ของ Widgets
		$dir = ROOT_PATH.'Widgets/';
		$f = @opendir($dir);
		if ($f) {
			while (false !== ($text = readdir($f))) {
				if ($text != "." && $text != "..") {
					if (is_dir($dir.$text)) {
						if (is_file($dir.$text.'/script.js')) {
							$js[] = file_get_contents($dir.$text.'/script.js');
						}
					}
				}
			}
			closedir($f);
		}
		$languages = Language::getItems(array(
				'MONTH_SHORT',
				'MONTH_LONG',
				'DATE_LONG',
				'DATE_SHORT',
				'YEAR_OFFSET'
		));
		$js[] = 'Date.monthNames = ["'.implode('", "', $languages['MONTH_SHORT']).'"];';
		$js[] = 'Date.longMonthNames = ["'.implode('", "', $languages['MONTH_LONG']).'"];';
		$js[] = 'Date.longDayNames = ["'.implode('", "', $languages['DATE_LONG']).'"];';
		$js[] = 'Date.dayNames = ["'.implode('", "', $languages['DATE_SHORT']).'"];';
		$js[] = 'Date.yearOffset = '.(int)$languages['YEAR_OFFSET'].';';
		// compress javascript
		$patt = array('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#u', '#[\r\t]#', '#\n//.*\n#', '#;//.*\n#', '#[\n]#', '#[\s]{2,}#');
		$replace = array('', '', '', ";\n", '', ' ');
		$this->output(preg_replace($patt, $replace, implode("\n", $js)));
	}
}