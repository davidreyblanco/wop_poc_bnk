<?php

require_once('util.inc');
date_default_timezone_set('Europe/Berlin');
define('CONST_MAX_PIPE_ITEMS',50);

$data = file_get_contents('./payload/suggestions.json');
$items = json_decode($data);
$count = 0;
$pipeline = array();
foreach($items as $item)
{
	if (rand(1,100) < 30)
	{
		$item->NBA_time_end = '10:00';
		$value = rand(1,5);
		switch($value)
		{
			case 1:$item->current_stage='U';break;
			case 2:$item->current_stage='C';break;
			case 3:$item->current_stage='Q';break;
			case 4:$item->current_stage='O';break;
			case 5:$item->current_stage='X';break;
		}
		$item->untouched_date = '2016-10-28';
		$item->contated_date = '2016-10-30';
		$item->qualified_date = '2016-11-01';
		$item->offered_date = '2016-11-03';
		$item->closed_date = '2016-11-07';
		array_push($pipeline,$item);
		$count++;
	}
	if ($count > CONST_MAX_PIPE_ITEMS)
	{
		break;
	}
}
//print_r($pipeline);
$r = json_encode($pipeline);
print($r);

?>