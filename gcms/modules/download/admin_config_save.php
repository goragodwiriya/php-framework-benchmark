<?php
// modules/download/admin_config_save.php
header("content-type: text/html; charset=UTF-8");
// inint
include '../../bin/inint.php';
$ret = array();
// referer, member
if (gcms::isReferer() && gcms::canConfig($config, 'download_can_config')) {
	if (isset($_SESSION['login']['account']) && $_SESSION['login']['account'] == 'demo') {
		$ret['error'] = 'EX_MODE_ERROR';
	} else {
		// ตรวจสอบชนิดของไฟล์
		$error = false;
		$file_typies = strtolower(preg_replace('/[\s]/', '', $_POST['config_file_typies']));
		if ($file_typies != '') {
			foreach (explode(',', $file_typies) AS $item) {
				if (!preg_match('/^[a-z0-9]{2,6}$/u', $item)) {
					$error = true;
				}
			}
		}
		// ตรวจสอบค่าที่ส่งมา
		$ret['ret_config_file_typies'] = '';
		if ($file_typies == '') {
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
				if (isset($_POST['config_can_download'])) {
					$config['download_can_download'] = gcms::getVars($_POST, 'config_can_download', '');
				} else {
					unset($config['download_can_download']);
				}
				$config['download_can_upload'] = gcms::getVars($_POST, 'config_can_upload', array());
				$config['download_can_upload'][] = 1;
				$config['download_can_config'] = gcms::getVars($_POST, 'config_can_config', array());
				$config['download_can_config'][] = 1;
				$config['download_list_per_page'] = gcms::getVars($_POST, 'config_list_per_page', 0);
				$config['download_upload_size'] = gcms::getVars($_POST, 'config_upload_size', 0);
				$config['download_file_typies'] = implode(',', array_keys($typies));
				$config['download_news_count'] = gcms::getVars($_POST, 'config_news_count', 0);
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
