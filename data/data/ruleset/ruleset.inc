<?php 

define('KILLER_RULES_SCORE',-1);

/**

	8 Rules one for each product, some utilities
	-------------------------------------------------
	CNAE,Size,Month -> Propensity to consume credit
					   A, B = Positive , Positive+
					   C, D = Negative , Negative-
	Examples:
	print(get_propensity('9609',1,10)."\n");
	print(get_propensity('9609',0,12)."\n");
	print(get_propensity('9609',0,11)."\n");

	-------------------------------------------------
	
	Get Values from the model:

		get_value($c,'cif');

*/

function evaluate_rule_1($model)
{
	$r = array();
	$r['product_id'] = 1; 	// Product ID

	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

function evaluate_rule_2($model)
{
	$r = array();
	$r['product_id'] = 2; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	
	return $r;
}

function evaluate_rule_3($model)
{
	$r = array();
	$r['product_id'] = 3; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	
	return $r;
}

function evaluate_rule_4($model)
{
	$r = array();
	$r['product_id'] = 4; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

function evaluate_rule_5($model)
{
	$r = array();
	$r['product_id'] = 5; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

function evaluate_rule_6($model)
{
	$r = array();
	$r['product_id'] = 6; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

function evaluate_rule_7($model)
{
	$r = array();
	$r['product_id'] = 7; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

function evaluate_rule_8($model)
{
	$r = array();
	$r['product_id'] = 8; 	// Product ID
	$r['cvr'] = 10+5*rand(1,5); 		// Expected Conversion Rate 0 - 100
	$r['value'] = 100*rand(1,10); 		// Expected Value in Euros
	$r['score'] = 5 + rand(1,20); 		// Rule Score - Killer rules set this score to -1
	$r['importance'] = rand(1,3); 		// Imortance 1-3
	$r['urgency'] = rand(1,3); 			// Urgency 1-3
	
	return $r;
}

?>