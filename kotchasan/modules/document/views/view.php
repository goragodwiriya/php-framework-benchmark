<?php
/*
 * @filesource document/views/view.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\View;

use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Http\Request;
use \Gcms\Gcms;
use \Kotchasan\Login;
use \Kotchasan\Text;
use \Kotchasan\Date;
use \Document\Index\Controller;
use \Kotchasan\Grid;

/**
 * แสดงบทความ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{

	/**
	 *
	 * @param Request $request
	 * @param type $index
	 * @return \stdClass
	 */
	public function index(Request $request, $index)
	{
		// ค่าที่ส่งมา
		$id = $request->get('id')->toInt();
		$alias = $request->get('alias')->text();
		$search = preg_replace('/[+\s]+/u', ' ', $request->get('q')->text());
		// อ่านรายการที่เลือก
		$story = \Document\View\Model::get((int)$index->module_id, $id, $alias);
		if (empty($story)) {
			// 404
			$result = createClass('Index\PageNotFound\View')->render();
		} else {
			// login
			$login = $request->session('login', array('id' => 0, 'status' => -1, 'email' => '', 'password' => ''))->all();
			// สมาชิก true
			$isMember = $login['status'] > -1;
			// แสดงความคิดเห็นได้
			$canReply = !empty($story->can_reply);
			// ผู้ดูแล
			$moderator = Gcms::canConfig($login, $index, 'moderator');
			// สถานะสมาชิกที่สามารถเปิดดูกระทู้ได้
			$canView = Gcms::canConfig($login, $index, 'can_view');
			// dir ของรูปภาพอัปโหลด
			$imagedir = ROOT_PATH.DATA_FOLDER.'document/';
			$imageurl = WEB_URL.DATA_FOLDER.'document/';
			// รูปภาพ
			if (!empty($story->picture) && is_file($imagedir.$story->picture)) {
				$image_src = $imageurl.$story->picture;
			} else {
				$image_src = '';
			}
			if ($canView || $index->viewing == 1) {
				if ($canReply) {
					// antispam
					$antispamchar = Text::rndname(32);
					$_SESSION[$antispamchar] = Text::rndname(4);
					// รายการแสดงความคิดเห็น
					$listitem = Grid::create($index->owner, $index->module, 'commentitem');
					foreach (\Document\Comment\Model::get($story) as $no => $item) {
						// moderator และ เจ้าของ สามารถแก้ไขความคิดเห็นได้
						$canEdit = $moderator || ($isMember && $login['id'] == $item->member_id);
						$listitem->add(array(
							'/(edit-{QID}-{RID}-{NO}-{MODULE})/' => $canEdit ? '\\1' : 'hidden',
							'/(delete-{QID}-{RID}-{NO}-{MODULE})/' => $moderator ? '\\1' : 'hidden',
							'/{DETAIL}/' => Gcms::highlightSearch(Gcms::showDetail(str_replace(array('{', '}'), array('&#x007B;', '&#x007D;'), nl2br($item->detail)), $canView, true, true), $search),
							'/{UID}/' => $item->member_id,
							'/{DISPLAYNAME}/' => $item->displayname,
							'/{STATUS}/' => $item->status,
							'/{DATE}/' => Date::format($item->last_update),
							'/{DATEISO}/' => date(DATE_ISO8601, $item->last_update),
							'/{IP}/' => Gcms::showip($item->ip),
							'/{NO}/' => $no + 1,
							'/{RID}/' => $item->id
						));
					}
				}
				$tags = array();
				foreach (explode(',', $story->relate) as $tag) {
					$tags[] = '<a href="'.Gcms::createUrl('tag', rawurlencode($tag)).'">'.$tag.'</a>';
				}
				$detail = Gcms::showDetail(str_replace(array('{', '}'), array('&#x007B;', '&#x007D;'), $story->detail), $canView, true, true);
				$replace = array(
					'/{COMMENTLIST}/' => isset($listitem) ? $listitem->render() : '',
					'/{REPLYFORM}/' => $canReply ? Template::load($index->owner, $index->module, 'reply') : '',
					'/<MEMBER>(.*)<\/MEMBER>/s' => $isMember ? '' : '$1',
					'/{TOPIC}/' => $story->topic,
					'/<IMAGE>(.*)<\/IMAGE>/s' => $image_src == '' ? '' : '$1',
					'/{IMG}/' => $image_src,
					'/{DETAIL}/' => Gcms::HighlightSearch($detail, $search),
					'/{DATE}/' => Date::format($story->create_date),
					'/{DATEISO}/' => date(DATE_ISO8601, $story->create_date),
					'/{COMMENTS}/' => number_format($story->comments),
					'/{VISITED}/' => number_format($story->visited),
					'/{DISPLAYNAME}/' => empty($story->displayname) ? $story->email : $story->displayname,
					'/{STATUS}/' => $story->status,
					'/{UID}/' => (int)$story->member_id,
					'/{LOGIN_PASSWORD}/' => $login['password'],
					'/{LOGIN_EMAIL}/' => $login['email'],
					'/{QID}/' => $story->id,
					'/{MODULE}/' => $index->module,
					'/{MODULEID}/' => $story->module_id,
					'/{ANTISPAM}/' => isset($antispamchar) ? $antispamchar : '',
					'/{ANTISPAMVAL}/' => isset($antispamchar) && Login::isAdmin() ? $_SESSION[$antispamchar] : '',
					'/{DELETE}/' => $moderator ? '{LNG_Delete}' : '{LNG_Removal request}',
					'/{TAGS}/' => implode(', ', $tags)
				);
				$detail = Template::create($index->owner, $index->module, 'view')->add($replace)->render();
			} else {
				// not login
				$replace = array(
					'/{TOPIC}/' => $story->topic,
					'/{DETAIL}/' => '<div class=error>'.Language::get('Members Only').'</div>'
				);
				$detail = Template::create($index->owner, $index->module, 'error')->add($replace)->render();
			}
			// แทนที่ลงใน template
			$result = (object)array(
					'canonical' => Controller::url($index->module, $story->alias, $story->id),
					'detail' => $detail,
					'topic' => $story->topic,
					'description' => $story->description,
					'keywords' => $story->keywords,
					'image_src' => $image_src
			);
			// breadcrumb ของหมวดหมู่
			if (!empty($story->category)) {
				Gcms::$view->addBreadcrumb(Gcms::createUrl($index->module, '', $story->category_id), Gcms::ser2Str($story->category), Gcms::ser2Str($story->cat_tooltip));
			}
			// breadcrumb ของหน้า
			Gcms::$view->addBreadcrumb($result->canonical, $result->topic, $result->description);
		}
		// คืนค่า
		return $result;
	}
}