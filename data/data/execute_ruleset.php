<?php

require_once('util.inc');
require_once('ruleset/rule_helpers.inc');
require_once('ruleset/ruleset.inc');
date_default_timezone_set('Europe/Berlin');

/**
 
 Execute ruleset
 
*/
define('MINIMUM_SCORE',10);
$GLOBALS['MAX_RECOMMENDATIONS'] = -1;
$GLOBALS['PERFORMED_RECOMMENDATIONS'] = 0;

load_propensity_table();
/*
 	print(get_propensity('9609',1,10)."\n");
	print(get_propensity('9609',0,12)."\n");
	print(get_propensity('9609',0,11)."\n");
 */
$GLOBALS['recommendations'] = array();

function calculate_nba_for_lead($line_id,$c)
{
	// Evaluate the ruleset for the company
	//"CIF;NAME;EXPECTED_VALUE;EXPECTED_CVR;IMPORTANCE;URGENCY;NBA_DATE;NBA_TIME;CONTACT_CHANNEL;
	//SUGGESTED_PRODUCTS;SUGGESTION_ID\n");
	$item = array();
	if ($GLOBALS['MAX_RECOMMENDATIONS'] < 0 || ($GLOBALS['PERFORMED_RECOMMENDATIONS'] <= $GLOBALS['MAX_RECOMMENDATIONS']))
	{
		$recommendations = evaluate_rules($c,23);
		if (count($recommendations) > 0) // At least one product recommended then
		{
			$metrics = get_aggregated_metrics($recommendations);
			$item['CIF'] = get_value($c,'cif');
			$item['NAME'] = get_value($c,'nombre_empresa');
			$item['EXPECTED_VALUE'] = $metrics['EXPECTED_VALUE'];
			$item['EXPECTED_CVR'] = $metrics['EXPECTED_CVR'];
			$item['IMPORTANCE'] = $metrics['IMPORTANCE'];
			$item['URGENCY'] = $metrics['URGENCY'];
			$item['SCORE'] = $metrics['SCORE'];
			$timestamp = time()+rand(1,15)*3600*24;
			if (date( "w", $timestamp) == 0) // Sunday
			{
				$timestamp = $timestamp + 3600*24; // To Monday
			}
			else if (date( "w", $timestamp) == 6) // Saturday
			{
				$timestamp = $timestamp + 2*3600*24; // To Monday
			}
			$dt = date('Y-m-d',$timestamp); // 1 ..30 days
			$item['NBA_DATE'] = $dt;
			$item['NBA_TIME'] = "10:00";
			switch(rand(1,4))
			{
				case 1:$channel = 'E';break;
				case 2:$channel = 'S';break;
				case 3:$channel = 'P';break;
				case 4:$channel = 'C';break;
			}
			
			$item['CONTACT_CHANNEL'] = $channel;
			$item['SUGGESTED_PRODUCTS'] = $metrics['PRODUCTS'];
			$item['SUGGESTION_ID'] = 1000+$line_id;
			
			array_push($GLOBALS['recommendations'],$item);
			$GLOBALS['PERFORMED_RECOMMENDATIONS']++;
		}
	}
}


$GLOBALS['MAX_RECOMMENDATIONS'] = $argc == 2 ? $argv[1] : -1;
process_file('data/data_wop.txt',calculate_nba_for_lead,"\t",true);
$r = json_encode($GLOBALS['recommendations']);
print($r);
//print_r($GLOBALS['recommendations']);

?>