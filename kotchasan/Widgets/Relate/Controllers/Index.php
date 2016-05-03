<?php
/*
 * @filesource Widgets/Relate/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Relate\Controllers;

use \Gcms\Gcms;
use \Kotchasan\Template;
use \Kotchasan\Grid;
use \Document\Index\Controller;
use \Kotchasan\Date;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{

	/**
	 * แสดงผล Widget
	 *
	 * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
	 * @return string
	 */
	public function get($query_string)
	{
		$id = empty($query_string['id']) ? 0 : (int)$query_string['id'];
		if ($id > 0 && !empty($query_string['module']) && isset(Gcms::$install_modules[$query_string['module']])) {
			$index = Gcms::$install_modules[$query_string['module']];
			$cols = isset($query_string['cols']) ? (int)$query_string['cols'] : 1;
			$rows = isset($query_string['rows']) ? (int)$query_string['rows'] : 1;
			$style = isset($query_string['style']) && in_array($query_string['style'], array('list', 'icon', 'thumb')) ? $query_string['style'] : 'list';
			// template
			$template = Template::create('document', $index->module, 'relate');
			$template->add(array(
				'/{DETAIL}/' => '<script>getWidgetNews("{ID}", "Relate", 0)</script>',
				'/{ID}/' => $index->module.'_'.$id.'_'.$cols.'_'.$rows.'_0_'.$style,
				'/{MODULE}/' => $index->module,
				'/{STYLE}/' => $style.'view'
			));
			return $template->render();
		}
	}

	/**
	 * อ่านข้อมูล relate
	 *
	 * @param array $query_string
	 * @return object|null คืนค่าข้อมูล Object รายการเดียว ไม่พบคืนค่า null
	 */
	public function getWidgetNews($query_string)
	{
		// module_id_cols_rows_sort_style
		if (self::$request->isReferer() && preg_match('/^([a-z]+)_([0-9]+)_([0-9]+)_([0-9]+)_([0-9]+)_(list|icon|thumb)$/', $query_string['id'], $match)) {
			$cols = (int)$match[3];
			$rows = (int)$match[4];
			// query
			$index = createClass('Widgets\Relate\Models\Index')->getModule((int)$match[2], $rows * $cols);
			if ($index) {
				// รายการ
				$listitem = Grid::create('document', $index->module, 'relateitem');
				$listitem->setCols($cols);
				foreach ($index->items as $item) {
					if (!empty($item->picture) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$item->picture)) {
						$thumb = WEB_URL.DATA_FOLDER.'document/'.$item->picture;
					} elseif (!empty($index->icon) && is_file(ROOT_PATH.DATA_FOLDER.'document/'.$index->icon)) {
						$thumb = WEB_URL.DATA_FOLDER.'document/'.$index->icon;
					} else {
						$thumb = WEB_URL.(isset($index->default_icon) ? $index->default_icon : 'modules/document/img/document-icon.png');
					}
					$listitem->add(array(
						'/{URL}/' => Controller::url($match[1], $item->alias, $item->id),
						'/{TOPIC}/' => $item->topic,
						'/{DATE}/' => Date::format($item->create_date),
						'/{COMMENTS}/' => number_format($item->comments),
						'/{VISITED}/' => number_format($item->visited),
						'/{DETAIL}/' => $item->description,
						'/{PICTURE}/' => $thumb,
						'/{COLS}/' => $cols
					));
				}
				echo $listitem->render();
			}
		}
	}
}