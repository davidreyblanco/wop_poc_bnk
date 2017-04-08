<?php

require_once('util.inc');

date_default_timezone_set('Europe/Berlin');

function process_cv_line($line_id,$content)
{	
	$item = array();
	foreach($GLOBALS['dictionary'] as $key => $index)
	{
	$item[$key] = $content[$index];
	}
	array_push($GLOBALS['contents'],$item);
}

function process_csv($filename,$split_by = ';')
{
	$GLOBALS['contents'] = array();	
	process_file($filename,process_cv_line,$split_by,true);
	$r = $GLOBALS['contents'];
	$GLOBALS['contents'] = array();
	return $r;	
}

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
//print('sep:'.$sep.'#'."\n");
$data = process_csv($argv[1],$sep,true);
//print_r($data);
$r = json_encode($data);
print($r);
?>