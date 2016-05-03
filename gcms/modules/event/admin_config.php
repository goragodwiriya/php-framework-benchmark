<?php
// modules/event/admin_config.php
if (MAIN_INIT == 'admin' && gcms::canConfig($config, 'event_can_config')) {
	// ตรวจสอบโมดูลที่เรียก
	$sql = "SELECT `module` FROM `".DB_MODULES."` WHERE `owner`='event' LIMIT 1";
	$index = $db->customQuery($sql);
	if (sizeof($index) == 0) {
		$title = $lng['LNG_DATA_NOT_FOUND'];
		$content[] = '<aside class=error>'.$title.'</aside>';
	} else {
		$index = $index[0];
		// title
		$m = ucwords($index['module']);
		$title = "$lng[LNG_CONFIG] $m";
		$a = array();
		$a[] = '<span class=icon-event>{LNG_MODULES}</span>';
		$a[] = $m;
		$a[] = '{LNG_CONFIG}';
		// แสดงผล
		$content[] = '<div class=breadcrumbs><ul><li>'.implode('</li><li>', $a).'</li></ul></div>';
		$content[] = '<section>';
		$content[] = '<header><h1 class=icon-config>'.$title.'</h1></header>';
		// form
		$content[] = '<form id=setup_frm class=setup_frm method=post action=index.php>';
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
				$tr .= '<td><label data-text="{LNG_CAN_WRITE}"><input type=checkbox name=config_can_write[]'.(isset($config['event_can_write']) && in_array($i, $config['event_can_write']) ? ' checked' : '').' value='.$i.' title="{LNG_CAN_WRITE_COMMENT}"></label></td>';
				// can_config
				$tr .= '<td><label data-text="{LNG_CAN_CONFIG}"><input type=checkbox name=config_can_config[]'.(isset($config['event_can_config']) && in_array($i, $config['event_can_config']) ? ' checked' : '').' value='.$i.' title="{LNG_CAN_CONFIG_COMMENT}"></label></td>';
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
		$content[] = 'new GForm("setup_frm", "'.WEB_URL.'/modules/event/admin_config_save.php").onsubmit(doFormSubmit);';
		$content[] = '});';
		$content[] = '</script>';
		// หน้านี้
		$url_query['module'] = 'event-config';
	}
} else {
	$title = $lng['LNG_DATA_NOT_FOUND'];
	$content[] = '<aside class=error>'.$title.'</aside>';
}
