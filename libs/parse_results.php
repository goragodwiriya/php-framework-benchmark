<?php
$modules = array(
	'hello' => 'Loading Performance',
	'orm' => 'ORM Performance (select and update)',
	'select' => 'ORM Performance (select only)',
	'cms' => 'CMS compasion',
	'db' => 'Database compasion'
);

function parse_results($file)
{
	$lines = file($file);

	$results = array();
	$min_rps = INF;
	$min_memory = INF;
	$min_time = INF;

	foreach ($lines as $line) {
		$column = explode(':', $line);
		$fw = $column[0];
		$rps = (float)trim($column[1]);
		$memory = (float)trim($column[2]) / 1024 / 1024;
		$time = (float)trim($column[3]) * 1000;

		$min_rps = min($min_rps, $rps);
		$min_memory = min($min_memory, $memory);
		$min_time = min($min_time, $time);

		$results[$fw] = array(
			'rps' => $rps,
			'memory' => $memory,
			'time' => $time,
		);
	}

	foreach ($results as $fw => $data) {
		$results[$fw]['rps_relative'] = empty($min_rps) ? 0 : $data['rps'] / $min_rps;
		$results[$fw]['memory_relative'] = empty($min_memory) ? 0 : $data['memory'] / $min_memory;
		$results[$fw]['time_relative'] = empty($min_time) ? 0 : $data['time'] / $min_time;
	}
	//var_dump($results);

	return $results;
}
