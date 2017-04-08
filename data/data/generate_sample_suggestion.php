<?php

require_once('util.inc');
date_default_timezone_set('Europe/Berlin');

/**
 
 Create a list of suggestions and create a sample pipeline from the suggestions
 TBD: Attach the ruleset to generate the items properly
 
*/

function generate_propensity_dummy($line_id,$c)
{
	if ($line_id === 1)
	{
		print("CIF;NAME;EXPECTED_VALUE;EXPECTED_CVR;IMPORTANCE;URGENCY;NBA_DATE;NBA_TIME;CONTACT_CHANNEL;SUGGESTED_PRODUCTS;SUGGESTION_ID\n");
	}	
	$factor = get_value($c,'email_web') === '' ? 1 : 4;
	// PIck a random set of the 20%
	if (rand(0, 100)*$factor < 10)
	{
		print(get_value($c,'cif').';'.get_value($c,'nombre_empresa'));
		print(';');print(1500+rand(1,20)*500);//CVR
		print(';');print(5+rand(0,3)*10);//CVR
		print(';');print(rand(1,3));//IMPORTANCE
		print(';');print(rand(1,3));//URGENCY
		//Y-m-d H:i:s"
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
		print(';');print($dt);//NBA DATE
		print(';');print("10:00");//NBA TIME
		print(';');
		switch(rand(1,4))
		{
			case 1:print('E');break;
			case 2:print('S');break;
			case 3:print('P');break;
			case 4:print('C');break;
		}
		print(';');
		//for($i = )
		switch(rand(1,10))
		{
			case 1:print('1');break;
			case 2:print('2');break;
			case 3:print('4');break;
			case 4:print('5');break;
			case 5:print('1,2');break;
			case 6:print('3,6');break;
			case 7:print('1,4');break;
			case 8:print('2,5');break;
			case 9:print('3,6');break;
			case 10:print('8');break;
		}		
		print(';');print(1000+$line_id);
		print("\n");
		
	}
}

function generate_sample_pipeline($line_id,$c)
{

}


process_file('data/data_wop.txt',generate_propensity_dummy,"\t",true);


?>