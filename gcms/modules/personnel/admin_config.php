<?php
// modules/personnel/admin_config.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'personnel_can_config')) {
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `id` FROM `".DB_MODULES."` WHERE `owner`='personnel' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// title
		$title = "$lng[LNG_CONFIG] $lng[LNG_PERSONNEL]";
		$a = array();
		$a[] = '<span class=icon-modules>{LNG_MODULES}</span>';
		$a[] = '{LNG_PERSONNEL}';
		$a[] = '{LNG_CONFIG}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-customer>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
		$content[] = '<fieldset>';
		$content[] = '<legend><span>{LNG_PERSONNEL_CONFIG}</span></legend>';
		// personnel_image_w,personnel_image_h
		$content[] = '<div class=item>';
		$content[] = '<label for=config_image_w>{LNG_IMAGE} ({LNG_PX})</label>';
		$content[] = '<div class="table collapse">';
		$content[] = '<label class=td for=config_image_w>{LNG_WIDTH}&nbsp;</label>';
		$content[] = '<div class=td><span class="g-input icon-width"><input type=number min=50 name=config_image_w id=config_image_w value="'.$config['personnel_image_w'].'" title="{LNG_WIDTH} {LNG_PX}"></span></div>';
		$content[] = '<label class=td for=config_image_h>&nbsp;{LNG_HEIGHT}&nbsp;</label>';
		$content[] = '<div class=td><span class="g-input icon-height"><input type=number min=50 name=config_image_h id=config_image_h value="'.$config['personnel_image_h'].'" title="{LNG_HEIGHT} {LNG_PX}"></span></div>';
		$content[] = '</div>';
		$content[] = '<div class=comment>{LNG_PERSONNEL_IMAGE_COMMENT}</div>';
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
		$content[] = '<th scope=col class=col2>{LNG_CAN_WRITE}</th>';
		$content[] = '<th scope=col>{LNG_CAN_CONFIG}</th>';
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
				// can_write
				$tr .= '<td><label data-text="{LNG_CAN_WRITE}"><input type=checkbox name=config_can_write[]'.(is_array($config['personnel_can_write']) && in_array($i, $config['personnel_can_write']) ? ' checked' : '').' value='.$i.' title="{LNG_PERSONNEL_CAN_WRITE_COMMENT}"></label></td>';
				// can_config
				$tr .= '<td><label data-text="{LNG_CAN_CONFIG}"><input type=checkbox name=config_can_config[]'.(is_array($config['personnel_can_config']) && in_array($i, $config['personnel_can_config']) ? ' checked' : '').' value='.$i.' title="{LNG_CAN_CONFIG_COMMENT}"></label></td>';
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
		$content[] = 'new GForm("setup_frm", "'.WEB_URL.'/modules/personnel/admin_config_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'personnel-config';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
