<?php
// modules/personnel/admin_write.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'personnel_can_write')) {
	// อัลบัมที่แก้ไข
	$id = gcms::getVars($_GET, 'id', 0);
	// ตรวจสอบโมดูลที่เรียก
	if ($id > 0) {
		$sql = "SELECT C.*,M.`module` FROM `".DB_MODULES."` AS M";
		$sql .= " INNER JOIN `".DB_PERSONNEL."` AS C ON C.`module_id`=M.`id` AND C.`id`=$id";
	} else {
		$sql = "SELECT M.`id` AS `module_id`,M.`module` FROM `".DB_MODULES."` AS M";
	}
	$sql .= " WHERE M.`owner`='personnel' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 1) {
		$index = $index[0];
		if ($id == 0) {
			$index['name'] = '';
			$index['category_id'] = 0;
			$index['id'] = 0;
			$index['order'] = 1;
			$index['position'] = '';
			$index['detail'] = '';
			$index['address'] = '';
			$index['phone'] = '';
			$index['email'] = '';
			$index['picture'] = '';
		}
		// title
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_PERSONNEL]";
		$a = array();
		$a[] = '<span class=icon-modules>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=personnel-config}">{LNG_PERSONNEL}</a>';
		$a[] = '<a href="{URLQUERY?module=personnel-setup}">{LNG_PERSONNEL}</a>';
		$a[] = $id == 0 ? '{LNG_ADD}' : '{LNG_EDIT}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-customer>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_PERSONNEL_DETAILS}</span></legend>';
		// name
		$content[] = '<div class=item>';
		$content[] = '<label for=write_name>{LNG_FNAME} {LNG_LNAME}</label>';
		$content[] = '<span class="g-input icon-customer"><input type=text id=write_name name=write_name maxlength=50 title="{LNG_PLEASE_FILL}" value="'.$index['name'].'"></span>';
		$content[] = '</div>';
		// category
		$content[] = '<div class=item>';
		$sql = "SELECT `category_id`,`topic` FROM `".DB_CATEGORY."` WHERE `module_id`='$index[module_id]' ORDER BY `category_id`";
		$content[] = '<label for=write_category>{LNG_PERSONNEL_CATEGORY}</label>';
		$content[] = '<span class="g-input icon-category"><select name=write_category id=write_category title="{LNG_PLEASE_SELECT}">';
		foreach ($db->customQuery($sql) AS $item) {
			$sel = $item['category_id'] == $index['category_id'] ? ' selected' : '';
			$content[] = '<option value='.$item['category_id'].$sel.'>'.gcms::ser2Str($item, 'topic').'</option>';
		}
		$content[] = '</select></span>';
		$content[] = '</div>';
		// order
		$content[] = '<div class=item>';
		$content[] = '<label for=write_order>{LNG_SORT}</label>';
		$content[] = '<span class="g-input icon-edit"><input type=number id=write_order name=write_order title="{LNG_PERSONNEL_ORDER_COMMENT}" value="'.$index['order'].'"></span>';
		$content[] = '<div class=comment>{LNG_PERSONNEL_ORDER_COMMENT}</div>';
		$content[] = '</div>';
		// position
		$content[] = '<div class=item>';
		$content[] = '<label for=write_position>{LNG_POSITION}</label>';
		$content[] = '<span class="g-input icon-edit"><input type=text id=write_position name=write_position maxlength=100 title="{LNG_PLEASE_FILL}" value="'.$index['position'].'"></span>';
		$content[] = '</div>';
		// detail
		$content[] = '<div class=item>';
		$content[] = '<label for=write_detail>{LNG_DETAIL}</label>';
		$content[] = '<span class="g-input icon-file"><input type=text id=write_detail name=write_detail maxlength=255 title="{LNG_PLEASE_FILL}" value="'.$index['detail'].'"></span>';
		$content[] = '</div>';
		// address
		$content[] = '<div class=item>';
		$content[] = '<label for=write_address>{LNG_ADDRESS}</label>';
		$content[] = '<span class="g-input icon-location"><input type=text id=write_address name=write_address maxlength=255 title="{LNG_PLEASE_FILL}" value="'.$index['address'].'"></span>';
		$content[] = '</div>';
		// phone
		$content[] = '<div class=item>';
		$content[] = '<label for=write_phone>{LNG_PHONE}</label>';
		$content[] = '<span class="g-input icon-phone"><input type=text pattern="[0-9]+" id=write_phone name=write_phone maxlength=20 title="{LNG_PLEASE_FILL}" value="'.$index['phone'].'"></span>';
		$content[] = '</div>';
		// email
		$content[] = '<div class=item>';
		$content[] = '<label for=write_email>{LNG_EMAIL}</label>';
		$content[] = '<span class="g-input icon-email"><input type=email id=write_email name=write_email maxlength=255 title="{LNG_PLEASE_FILL}" value="'.$index['email'].'"></span>';
		$content[] = '</div>';
		// picture
		$content[] = '<div class=item>';
		$t = str_replace(array('{T}', '{W}', '{H}'), array('jpg', $config['personnel_image_w'], $config['personnel_image_h']), $lng['LNG_IMAGE_UPLOAD_COMMENT']);
		$picture = ($index['picture'] != '' && is_file(DATA_PATH."personnel/$index[picture]")) ? DATA_URL."personnel/$index[picture]" : WEB_URL.'/modules/personnel/img/noicon.jpg';
		$content[] = '<div class=usericon><span><img src="'.$picture.'" id=imgIcon alt=UserIcon></span></div>';
		$content[] = '<label for=write_picture>{LNG_IMAGE}</label>';
		$content[] = '<span class="g-input icon-upload"><input type=file class=g-file id=write_picture name=write_picture title="'.$t.'" accept="'.gcms::getEccept(array('jpg', 'png', 'gif')).'" data-preview=imgIcon></span>';
		$content[] = '<div class=comment id=result_write_picture>'.$t.'</div>';
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
		$content[] = 'new GForm("setup_frm","'.WEB_URL.'/modules/personnel/admin_write_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'personnel-write';
	} else {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
