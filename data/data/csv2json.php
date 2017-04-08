<?php

require_once('util.inc');

date_default_timezone_set('Europe/Berlin');


//$GLOBALS['contents'] = array();
$filename = $argv[1];

$sep = ';';
if ($argc > 2)
{
	$sep = $argv[2];
	if ($sep='t')
	{
		$sep = "\t";
	}
}
$data = process_csv_obj($argv[1],$sep,true);
$r = json_encode($data);
print($r);
?>