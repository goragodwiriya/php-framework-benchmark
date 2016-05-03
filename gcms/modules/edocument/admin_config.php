<?php
// modules/edocument/admin_config.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'edocument_can_config')) {
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='edocument' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// title
		$title = "$lng[LNG_CONFIG] $lng[LNG_EDOCUMENT]";
		$a = array();
		$a[] = '<span class=icon-edocument>{LNG_MODULES}</span>';
		$a[] = '{LNG_EDOCUMENT}';
		$a[] = '{LNG_CONFIG}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-config>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php autocomplete=off>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_MAIN_CONFIG}</span></legend>';
		// edocument_format_no
		$content[] = '<div class=item>';
		$content[] = '<label for=config_format_no>{LNG_EDOCUMENT_NO} :</label>';
		$content[] = '<span class="g-input icon-config"><input type=text id=config_format_no name=config_format_no value="'.$config['edocument_format_no'].'" size=100 title="{LNG_EDOCUMENT_FORMAT_NO_COMMENT}"></span>';
		$content[] = '<div class=comment id=result_config_format_no>{LNG_EDOCUMENT_FORMAT_NO_COMMENT}</div>';
		$content[] = '</div>';
		// edocument_send_mail
		$content[] = '<div class=item>';
		$content[] = '<label for=config_send_mail>{LNG_SENDMAIL} :</label>';
		$content[] = '<span class="g-input icon-email"><select name=config_send_mail id=config_send_mail title="{LNG_EDOCUMENT_SEND_EMAIL_COMMENT}">';
		foreach ($lng['OPEN_CLOSE'] AS $i => $item) {
			$sel = $i == $config['edocument_send_mail'] ? ' selected' : '';
			$content[] = '<option value='.$i.$sel.'>'.$item.'</option>';
		}
		$content[] = '</select></span>';
		$content[] = '<p class=comment>{LNG_EDOCUMENT_SEND_EMAIL_COMMENT}</p>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_UPLOADING}</span></legend>';
		// edocument_file_typies
		$content[] = '<div class=item>';
		$content[] = '<label for=config_file_typies>{LNG_UPLOAD_FILE_TYPIES} :</label>';
		$content[] = '<span class="g-input icon-config"><input type=text id=config_file_typies name=config_file_typies value="'.(is_array($config['edocument_file_typies']) ? implode(',', $config['edocument_file_typies']) : '').'" title="{LNG_DOWNLOAD_FILE_TYPIES_COMMENT}"></span>';
		$content[] = '<div class=comment id=result_config_file_typies>{LNG_DOWNLOAD_FILE_TYPIES_COMMENT}</div>';
		$content[] = '</div>';
		//config_upload_size
		$content[] = '<div class=item>';
		$t = str_replace('{SIZE}', ini_get('upload_max_filesize'), $lng['LNG_DOWNLOAD_UPLOAD_SIZE_COMMENT']);
		$content[] = '<label for=config_upload_size>{LNG_UPLOAD_FILE_SIZE}</label>';
		$content[] = '<span class="g-input icon-config"><select name=config_upload_size id=config_upload_size title="'.$t.'">';
		$list = array(2, 4, 6, 8, 16, 32, 64, 128, 256, 512, 1024, 2048);
		foreach ($list AS $i) {
			$a = $i * 1048576;
			$sel = $a == $config['edocument_upload_size'] ? ' selected' : '';
			$content[] = '<option value='.$a.$sel.'>'.gcms::formatFileSize($a).'</option>';
		}
		$content[] = '</select></span>';
		$content[] = '<div class=comment>'.$t.'</div>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_DISPLAY}</span></legend>';
		// list_per_page
		$content[] = '<div class=item>';
		$content[] = '<label for=config_listperpage>{LNG_QUANTITY}</label>';
		$content[] = '<span class="g-input icon-published1"><select name=config_listperpage id=config_listperpage title="{LNG_LIST_PER_PAGE_COMMENT}">';
		foreach (array(10, 20, 30, 40, 50) AS $item) {
			$sel = $item == $config['edocument_listperpage'] ? ' selected' : '';
			$content[] = '<option value='.$item.$sel.'>'.$item.'</option>';
		}
		$content[] = '</select></span>';
		$content[] = '<div class=comment id=result_config_listperpage>{LNG_LIST_PER_PAGE_COMMENT}</div>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		// กำหนดความสามารถของสมาชิกแต่ละระดับ
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_MEMBER_ROLE_SETTINGS}</span></legend>';
		$content[] = '<div class=item>';
		$content[] = '<table class="responsive config_table">';
		$content[] = '<thead>';
		$content[] = '<tr>';
		$content[] = '<th>&nbsp;</th>';
		$content[] = '<th scope=col class=col2>{LNG_UPLOAD}</th>';
		$content[] = '<th scope=col>{LNG_MODERATOR}</th>';
		$content[] = '<th scope=col class=col2>{LNG_CAN_CONFIG}</th>';
		$content[] = '</tr>';
		$content[] = '</thead>';
		$content[] = '<tbody>';
		// สถานะสมาชิก
		$bg = 'bg2';
		foreach ($config['member_status'] AS $i => $item) {
			if ($i > 1) {
				$bg = $bg == 'bg1' ? 'bg2' : 'bg1';
				$tr = '<tr class="'.$bg.' status'.$i.'">';
				$tr .= '<th>'.$item.'</th>';
				// can_upload
				$tr .= '<td><label data-text="{LNG_UPLOAD}" ><input type=checkbox name=config_can_upload[]'.(isset($config['edocument_can_upload']) && in_array($i, $config['edocument_can_upload']) ? ' checked' : '').' value='.$i.' title="{LNG_CAN_UPLOAD_COMMENT}"></label></td>';
				// moderator
				$tr .= '<td><label data-text="{LNG_MODERATOR}" ><input type=checkbox name=config_moderator[]'.(isset($config['edocument_moderator']) && in_array($i, $config['edocument_moderator']) ? ' checked' : '').' value='.$i.' title="{LNG_EDOCUMENT_MODERATOR_COMMENT}"></label></td>';
				// can_config
				$tr .= '<td><label data-text="{LNG_CAN_CONFIG}" ><input type=checkbox name=config_can_config[]'.(isset($config['edocument_can_config']) && in_array($i, $config['edocument_can_config']) ? ' checked' : '').' value='.$i.' title="{LNG_CAN_CONFIG_COMMENT}"></label></td>';
				$tr .= '</tr>';
				$content[] = $tr;
			}
		}
		$content[] = '</tbody>';
		$content[] = '</table>';
		$content[] = '</div>';
		$content[] = '</fieldset>';
		// submit
		$content[] = '<fieldset class=submit>';
		$content[] = '<input type=submit class="button large save" value="{LNG_SAVE}">';
		$content[] = '</fieldset>';
		$content[] = '</form>';
		$content[] = '</section>';
		$content[] = '<script>';
		$content[] = '$G(window).Ready(function(){';
		$content[] = 'new GForm("setup_frm", "'.WEB_URL.'/modules/edocument/admin_config_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'edocument-config';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
