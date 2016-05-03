<?php
/*
 * @filesource Widgets/Share/Controllers/Xhr.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Share\Controllers;

/**
 * get facebook share count (Ajax called)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Xhr extends \Kotchasan\Controller
{

	/**
	 * get facebook share count
	 *
	 * @param array $query_string
	 */
	public function get($query_string)
	{
		$url = 'https://api.facebook.com/method/links.getStats?format=json&urls='.rawurlencode($query_string['url']);
		if (function_exists('curl_init') && $curl = @curl_init()) {
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl, CURLOPT_TIMEOUT, 5);
			$result = curl_exec($curl);
			curl_close($curl);
		} else {
			$result = @file_get_contents($url);
		}
		$result = json_decode($result);
		if (empty($result)) {
			echo 0;
		} else {
			echo number_format($result[0]->total_count);
		}
	}
}