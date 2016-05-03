<?php
// modules/edocument/admin_config_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, admin
if (gcms::isReferer() && gcms::canConfig($config, 'edocument_can_config')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		$error = false;
		// ตรวจสอบชนิดของไฟล์
		$file_typies = strtolower(preg_replace('/[\s]/', '', $_POST['config_file_typies']));
		if ($file_typies != '') {
			foreach (explode(',', $file_typies) AS $item) {
				if (!preg_match('/^[a-z0-9]{2,6}$/u', $item)) {
					$error = true;
				}
			}
		}
		$edocument_format_no = $db->sql_trim_str($_POST, 'config_format_no');
		// ตรวจสอบค่าที่ส่งมา
		$ret['ret_config_format_no'] = '';
		$ret['ret_config_file_typies'] = '';
		if ($edocument_format_no == '') {
			$ret['error'] = 'EDOCUMENT_FORMAT_NO_EMPTY';
			$ret['input'] = 'config_format_no';
			$ret['ret_config_format_no'] = 'EDOCUMENT_FORMAT_NO_EMPTY';
		} elseif ($file_typies == '') {
			$ret['error'] = 'DOWNLOAD_FILE_TYPIES_EMPTY';
			$ret['input'] = 'config_file_typies';
			$ret['ret_config_file_typies'] = 'DOWNLOAD_FILE_TYPIES_EMPTY';
		} elseif ($error) {
			$ret['error'] = 'DOWNLOAD_FILE_TYPIES_INVALID';
			$ret['input'] = 'config_file_typies';
			$ret['ret_config_file_typies'] = 'DOWNLOAD_FILE_TYPIES_INVALID';
		} else {
			// โหลด config ใหม่
			$config = array();
			if (is_file(CONFIG)) {
				include CONFIG;
			}
			// ตรวจสอบความถูกต้องของ mimetype
			$typies = gcms::getMimeTypies(explode(',', $file_typies));
			if (sizeof($typies) == 0) {
				$ret['error'] = 'GET_FILE_MIMETYPE_ERROR';
				$ret['input'] = 'config_file_typies';
				$ret['ret_config_file_typies'] = 'GET_FILE_MIMETYPE_ERROR';
			} elseif (is_array($config['mimeTypes'])) {
				$config['mimeTypes'] = array_merge($config['mimeTypes'], $typies);
			} else {
				$config['mimeTypes'] = $typies;
			}
			if (!isset($ret['error'])) {
				$config['edocument_format_no'] = $edocument_format_no;
				$config['edocument_send_mail'] = gcms::getVars($_POST, 'config_send_mail', 0);
				$config['edocument_file_typies'] = array_keys($typies);
				$config['edocument_listperpage'] = gcms::getVars($_POST, 'config_listperpage', 0);
				$config['edocument_upload_size'] = gcms::getVars($_POST, 'config_upload_size', 0);
				$config['edocument_can_upload'] = gcms::getVars($_POST, 'config_can_upload', array());
				$config['edocument_can_upload'][] = 1;
				$config['edocument_moderator'] = gcms::getVars($_POST, 'config_moderator', array());
				$config['edocument_moderator'][] = 1;
				$config['edocument_can_config'] = gcms::getVars($_POST, 'config_can_config', array());
				$config['edocument_can_config'][] = 1;
				// บันทึก config.php
				if (gcms::saveconfig(CONFIG, $config)) {
					$ret['error'] = 'SAVE_COMPLETE';
					$ret['location'] = 'reload';
				} else {
					$ret['error'] = 'DO_NOT_SAVE';
				}
			}
		}
	}
} else {
	$ret['error'] = 'ACTION_ERROR';
}
// คืนค่าเป็น JSON
echo gcms::array2json($ret);
