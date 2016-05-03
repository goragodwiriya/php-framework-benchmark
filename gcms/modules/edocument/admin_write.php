<?php
// modules/edocument/admin_write.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'edocument_moderator')) {
	// รายการที่แก้ไข
	$id = gcms::getVars($_GET, 'id', 0);
	if ($id > 0) {
		// แก้ไข
		$sql = "SELECT D.*,M.`module`";
		$sql .= " FROM `".DB_EDOCUMENT."` AS D";
		$sql .= " INNER JOIN `".DB_MODULES."` AS M ON M.`owner`='edocument' AND M.`id`=D.`module_id`";
		$sql .= " WHERE D.`id`='$id' LIMIT 1";
	} else {
		// ใหม่
		$sql = "SELECT M.`module`,(SELECT MAX(`id`) FROM `".DB_EDOCUMENT."` WHERE `module_id`=M.`id`) AS `document_no`";
		$sql .= " FROM `".DB_MODULES."` AS M";
		$sql .= " WHERE M.`owner`='edocument' LIMIT 1";
	}
	$index = $db->customQuery($sql);
	if (sizeof($index) == 1) {
		$index = $index[0];
		// title
		$title = "$lng[LNG_ADD]-$lng[LNG_EDIT] $lng[LNG_EDOCUMENT_ITEM]";
		$a = array();
		$a[] = '<span class=icon-edocument>{LNG_MODULES}</span>';
		$a[] = '<a href="{URLQUERY?module=edocument-config}">'.ucwords($index['module']).'</a>';
		$a[] = '<a href="{URLQUERY?module=edocument-setup}">{LNG_EDOCUMENT_ITEM}</a>';
		if ($id == 0) {
			$a[] = '{LNG_ADD}';
			$index['document_no'] = sprintf($config['edocument_format_no'], (int)$index['document_no'] + 1);
			$index['ext'] = '';
			$index['id'] = 0;
			$index['topic'] = '';
			$index['detail'] = '';
			$reciever = array();
		} else {
			$a[] = '{LNG_EDIT}';
			$reciever = explode(',', $index['reciever']);
		}
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-write>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>'.$a[3].'</span></legend>';
		// document_no
		$content[] = '<div class=item>';
		$content[] = '<label for=edocument_no>{LNG_EDOCUMENT_NO}</label>';
		$content[] = '<span class="g-input icon-edit"><input type=text id=edocument_no name=edocument_no maxlength=20 value="'.$index['document_no'].'" title="{LNG_EDOCUMENT_NO_COMMENT}"></span>';
		$content[] = '<div class=comment id=result_edocument_no>{LNG_EDOCUMENT_NO_COMMENT}</div>';
		$content[] = '</div>';
		// reciever
		$content[] = '<div class=item>';
		$content[] = '<label for=edocument_reciever>{LNG_EDOCUMENT_RECIVE_GROUPS}</label>';
		$content[] = '<span class="g-input icon-config"><select id=edocument_reciever name=edocument_reciever[] multiple size=3 title="{LNG_EDOCUMENT_RECIVE_GROUPS_COMMENT}">';
		// สถานะ (กลุ่ม) ของสมาชิกทั้งหมด
		$sel = in_array(-1, $reciever) ? ' selected' : '';
		$content[] = '<option value=-1'.$sel.'>{LNG_GUEST}</option>';
		foreach ($config['member_status'] AS $i => $item) {
			$sel = in_array($i, $reciever) ? ' selected' : '';
			$content[] = '<option value='.$i.$sel.'>'.$item.'</option>';
		}
		$content[] = '</select></span>';
		$content[] = '<div class=comment id=result_edocument_reciever>{LNG_EDOCUMENT_RECIVE_GROUPS_COMMENT}</div>';
		$content[] = '</div>';
		// topic
		$icon = "skin/ext/$index[ext].png";
		$icon = WEB_URL.(is_file(ROOT_PATH.$icon) ? "/$icon" : "/skin/ext/file.png");
		$content[] = '<div class=item>';
		$content[] = '<label for=edocument_topic>{LNG_EDOCUMENT_TOPIC}</label>';
		$content[] = '<div class=input-groups-table>';
		$content[] = '<span class=width><img src="'.$icon.'" id=imgIcon alt="'.$index['ext'].'"></span>';
		$content[] = '<span class="width g-input icon-edit"><input type=text id=edocument_topic name=edocument_topic maxlength=64 value="'.$index['topic'].'" title="{LNG_EDOCUMENT_TOPIC_COMMENT}"></span>';
		$content[] = '</div>';
		$content[] = '<div class=comment id=result_edocument_topic>{LNG_EDOCUMENT_TOPIC_COMMENT}</div>';
		$content[] = '</div>';
		// file
		$content[] = '<div class=item>';
		$t = str_replace(array('{TYPE}', '{SIZE}'), array(implode(', ', $config['edocument_file_typies']), gcms::formatFileSize($config['edocument_upload_size'])), $lng['LNG_EDOCUMENT_FILE_BROWSER_COMMENT']);
		$content[] = '<label for=edocument_file>{LNG_BROWSE_FILE}</label>';
		$content[] = '<span class="g-input icon-upload"><input type=file class=g-file id=edocument_file name=edocument_file title="'.$t.'"></span>';
		$content[] = '<div class=comment id=result_edocument_file>'.$t.'</div>';
		$content[] = '</div>';
		// detail
		$content[] = '<div class=item>';
		$content[] = '<label for=edocument_detail>{LNG_DESCRIPTION}</label>';
		$content[] = '<span class="g-input icon-file"><textarea id=edocument_detail name=edocument_detail rows=5 title="{LNG_EDOCUMENT_DESCRIPTION_COMMENT}">'.gcms::detail2TXT($index, 'detail').'</textarea></span>';
		$content[] = '<div class=comment id=result_edocument_detail>{LNG_EDOCUMENT_DESCRIPTION_COMMENT}</div>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		// submit
		$content[] = '<fieldset class=submit>';
		$content[] = '<input type=submit class="button large save" value="{LNG_SAVE}">';
		$content[] = '&nbsp;<label>{LNG_EDOCUMENT_SEND_EMAIL_TO}&nbsp;<input type=checkbox name=send_email value=1></label>';
		$content[] = gcms::get2Input($_GET);
		$content[] = '<input type=hidden name=write_id value='.(int)$index['id'].'>';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = 'new GForm("setup_frm","'.WEB_URL.'/modules/edocument/admin_write_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'edocument-write';
	} else {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
