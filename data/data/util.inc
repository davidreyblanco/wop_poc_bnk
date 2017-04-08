<?php

date_default_timezone_set('Europe/Berlin');

function test()
{
	print('test');
}

function get_value($data,$field)
{
	$r = trim($data[$GLOBALS['dictionary'][$field]]);
	return $r;
}

function clean_fieldname($field)
{
	$field = str_replace(".","_",$field);
	return utf8_encode($field);
}

$GLOBALS['stop_l_c_p_f'] = false;

function process_file($filename,$function,$split_by = ';',$create_dictionary = true)
{
	$line_id = 0;
	$handle = fopen($filename, "r");
	if ($handle) {
		while (($line = fgets($handle)) !== false && !$GLOBALS['stop_l_c_p_f']) 
		{
			if ($split_by !== '')
			{
				$line = explode($split_by,trim($line));
			}
			if ($line_id === 0)
			{
				$indices = array();
				for($i = 0; $i < count($line);$i++)
				{
					$field_name = clean_fieldname(trim($line[$i]));
					$indices[$field_name] = $i;
				}
				$GLOBALS['dictionary'] = $indices;
			}
			else
			{
				$function($line_id,$line);
			}				
			// process the line read.
			$line_id++;
				
		}
	
		fclose($handle);
	} else {
		// error opening the file.
	}
}

/*
	Some WOP utilities
*/

/* LOOKUP COMPANY */ 
function process_cv_line($line_id,$content)
{	
	$item = array();
	$cif = get_value($content,'cif');
	//print_r($content);
	if ($cif === $GLOBALS['filter'])
	{
		foreach($GLOBALS['dictionary'] as $key => $index)
		{
			$item[$key] = utf8_encode($content[$index]);
		}
		if ($GLOBALS['multiline'])
		{
			//print('cif-multiline'.count($GLOBALS['result'])."\n");
			$GLOBALS['current_company'] = $cif;
		}
		else
		{			
			$GLOBALS['stop_l_c_p_f'] = true;
		}
		array_push($GLOBALS['result'],$item);
	}
	else
	{
		if (count($GLOBALS['result']) > 1)
		{
			// Already found
			$GLOBALS['stop_l_c_p_f'] = true;
		}
	}
}

function process_near_line($line_id,$content)
{
//	print_r($GLOBALS['dictionary']);
//	print_r($content);
	$item = array();
	$item_x = get_value($content,'x');
	$item_y = get_value($content,'y');
	
	$calculated_distance = distance($GLOBALS['x'],$GLOBALS['y'],$item_x,$item_y,$calculated_distance);
	
	if ($calculated_distance < $GLOBALS['distance'])
	{
	//	print($item_x.' '.$item_y.' Distance:'.$calculated_distance.' max:'.$GLOBALS['max'].'<br/>');
		foreach($GLOBALS['dictionary'] as $key => $index)
		{
			$item[$key] = utf8_encode($content[$index]);
		}
		array_push($GLOBALS['result'],$item);
		$GLOBALS['stop_l_c_p_f'] = count($GLOBALS['result']) >= $GLOBALS['max']; // Stop if maximum is reached
	}
}




function process_csv($filename,$cif,$split_by = ';')
{
	$GLOBALS['filter'] = trim($cif);
	$GLOBALS['result'] = array();
	process_file($filename,process_cv_line,$split_by,true);
	$res = $GLOBALS['multiline'] ? $GLOBALS['result'] : $GLOBALS['result'][0];
	return $res;	
}


function lookup_near($x,$y,$filename = "data/data_wop.txt",$max)
{
	$sep = "\t";
	$path = str_replace('util.inc','',__FILE__);
	$filename = $path . $filename;
	
	$GLOBALS['x'] = $x;
	$GLOBALS['y'] = $y;
	$GLOBALS['max'] = 10;
	$GLOBALS['distance'] = 1;
	$GLOBALS['result'] = array();
	process_file($filename,process_near_line,$sep,true);
	$data = $GLOBALS['result'];
	if ($data === null)
	{
		$r = "{}";
	}
	else
	{
		$r = json_encode($data,true);
	}
	return $r;
}

function lookup_company($cif,$filename = "data/data_wop.txt",$multiline = false)
{
	$GLOBALS['stop_l_c_p_f'] = false;
	$GLOBALS['multiline'] = $multiline; 
	$sep = "\t";
	$path = str_replace('util.inc','',__FILE__);
	$filename = $path . $filename;
	$data = process_csv($filename,$cif,"\t",true);
	if ($data === null)
	{
		$r = "{}";
	}
	else
	{
		$r = json_encode($data,true);
	}
	return $r;
}

// Transform CSV to JSON

/* LOOKUP COMPANY */
function process_cv_line_obj($line_id,$content)
{
	$item = array();
	foreach($GLOBALS['dictionary'] as $key => $index)
	{
		$item[$key] = utf8_encode($content[$index]);
	}
	array_push($GLOBALS['result'],$item);
}

function process_csv_obj($filename,$split_by = ';')
{
	$GLOBALS['result'] = array();
	process_file($filename,process_cv_line_obj,$split_by,true);
	$res = $GLOBALS['result'];
	return $res;
}

function distance($lon1,$lat1, $lon2,$lat2) {

	$unit = "K";
	
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}
/*	Date transformation functions	*/

function get_key_from_date($date)
{	
	$p1 = substr($date,0,2);
	$p2 = substr($date,3,2);
	$p3 = substr($date,6,4);
	$r = trim($p3.$p2.$p1);
	return $r;	
}
{

}
?>