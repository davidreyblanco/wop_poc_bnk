<?php 


function load_propensity_table()
{
	$GLOBALS['prop'] = array();
	process_file('data/propension.txt',function($line_id,$line)
	{
		array_push($GLOBALS['prop'],$line);
	});
	$table = array();
	foreach($GLOBALS['prop'] as $line)
	{
		$item = explode('.',$line[3]);
		$item = $item[0];
		$table[$line[0]][$line[1]][$line[2]] = $item; // Remove trailing .POSI...
	}
	unset($GLOBALS['prop']); // Release memory
	$GLOBALS['props'] = $table;
}

function get_propensity($cnae,$size,$month)
{
	$r = $GLOBALS['props'][$cnae][$size][$month];
	return $r;
}


// *******************************************************
//	 				HELPERS
// *******************************************************
function evaluate_rules($model,$threshold)
{
	$recommendations = array();
	// Evaluate the ruleset
	array_push($recommendations,evaluate_rule_1($model));
	array_push($recommendations,evaluate_rule_2($model));
	array_push($recommendations,evaluate_rule_3($model));
	array_push($recommendations,evaluate_rule_4($model));
	array_push($recommendations,evaluate_rule_5($model));
	array_push($recommendations,evaluate_rule_6($model));
	array_push($recommendations,evaluate_rule_6($model));
	array_push($recommendations,evaluate_rule_8($model));
	$result = array();
	foreach($recommendations as $r)
	{
		if ($r['score'] > $threshold)
		{
			array_push($result,$r);
		}
	}
	return $result;
}

function get_aggregated_metrics($recommendations)
{
	$result = array();
	$result['EXPECTED_CVR'] = 0;
	$result['EXPECTED_VALUE'] = 0;
	$result['IMPORTANCE'] = rand(1,3);
	$result['URGENCY'] = rand(1,3);
	$result['SCORE'] = 0;
	$result['PRODUCTS'] = '';
	// Select values

	foreach($recommendations as $r)
	{
		$result['SCORE'] = $result['SCORE'] > $r['score'] ? $result['SCORE'] : $r['score'];
		$result['EXPECTED_CVR'] = $result['EXPECTED_CVR'] > $r['cvr'] ? $result['EXPECTED_CVR'] : $r['cvr'];
		$result['EXPECTED_VALUE'] += $r['value'];
		$result['SCORE'] = $result['SCORE'] > $r['score'] ? $result['SCORE'] : $r['score'];
		$result['IMPORTANCE'] = $result['IMPORTANCE'] > $r['importance'] ? $result['IMPORTANCE'] : $r['importance'];
		$result['URGENCY'] = $result['URGENCY'] > $r['urgency'] ? $result['URGENCY'] : $r['urgency'];
		
		
		// if  $result['SCORE']
		if ($result['PRODUCTS'] !== '')
		{
			$result['PRODUCTS'] .= ',';
		}
		$result['PRODUCTS'] .= $r['product_id'];
	}
	return $result;		
}
?>