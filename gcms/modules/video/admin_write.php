<?php
// modules/video/admin_write.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'video_can_write')) {
	// รายการที่แก้ไข
	$id = gcms::getVars($_GET, 'id', 0);
	// ตรวจสอบโมดูลที่เรียก
	if ($id > 0) {
		// แก้ไข
		$sql = "SELECT C.*,M.`module` FROM `".DB_MODULES."` AS M";
		$sql .= " INNER JOIN `".DB_VIDEO."` AS C ON C.`module_id`=M.`id` AND C.`id`=$id";
	} else {
		// ใหม่
		$sql = "SELECT 0 AS `id`,M.`id` AS `module_id`,M.`module` FROM `".DB_MODULES."` AS M";
	}
	$sql .= " WHERE M.`owner`='video' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 1) {
		$index = $index[0];
		if ($id == 0) {
			$index['youtube'] = '';
			$index['topic'] = '';
			$index['description'] = '';
			$index['id'] = 0;
		}
		// title
		$title = "$lng[LNG_CREATE] - $lng[LNG_EDIT] $lng[LNG_VIDEO]";
		$a = array();
		$a[] = '<span class=icon-video>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=video-config}">{LNG_VIDEO}</a>';
		$a[] = '<a href="{URLQUERY?module=video-setup}">{LNG_VIDEO_LIST}</a>';
		$a[] = $id == 0 ? '{LNG_CREATE}' : '{LNG_EDIT}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-write>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_VIDEO_DETAIL}</span></legend>';
		// youtube
		$content[] = '<div class=item>';
		$content[] = '<label for=write_youtube>{LNG_VIDEO_ID}</label>';
		$content[] = '<div class="table collapse">';
		$content[] = '<div class="td mobile">http://www.youtube.com/watch?v=&nbsp;</div>';
		$content[] = '<div class=td><span class=g-input><input type=text id=write_youtube name=write_youtube maxlength=11 title="{LNG_VIDEO_ID_COMMENT}" value="'.$index['youtube'].'"></span></div>';
		$content[] = '</div>';
		$content[] = '<div class=comment id=result_write_youtube>{LNG_VIDEO_ID_COMMENT}</div>';
		$content[] = '</div>';
		// thumb
		$content[] = '<div class=item>';
		$thumb = is_file(DATA_PATH."video/$index[youtube].jpg") ? DATA_URL."video/$index[youtube].jpg" : WEB_URL.'/modules/video/img/nopicture.jpg';
		$content[] = '<div class=usericon><img src="'.$thumb.'" id=imgIcon></div>';
		$content[] = '</div>';
		// topic
		$content[] = '<div class=item>';
		$content[] = '<label for=write_topic>{LNG_TOPIC}</label>';
		$content[] = '<span class="g-input icon-edit"><input type=text id=write_topic name=write_topic maxlength=64 title="{LNG_VIDEO_TOPIC_COMMENT}" value="'.$index['topic'].'"></span>';
		$content[] = '<div class=comment id=result_write_topic>{LNG_VIDEO_TOPIC_COMMENT}</div>';
		$content[] = '</div>';
		// description
		$content[] = '<div class=item>';
		$content[] = '<label for=write_description>{LNG_DESCRIPTION}</label>';
		$content[] = '<span class="g-input icon-file"><textarea id=write_description name=write_description rows=3 title="{LNG_VIDEO_DESCRIPTION_COMMENT}">'.gcms::detail2TXT($index, 'description').'</textarea></span>';
		$content[] = '<div class=comment id=result_write_description>{LNG_VIDEO_DESCRIPTION_COMMENT}</div>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		// submit
		$content[] = '<fieldset class=submit>';
		$content[] = '<input type=submit class="button large save" value="{LNG_SAVE}">';
		$content[] = '<input type=hidden id=write_id name=write_id value='.(int)$index['id'].'>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = 'new GForm("setup_frm","'.WEB_URL.'/modules/video/admin_write_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'video-write';
	} else {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
