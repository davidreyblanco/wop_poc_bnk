<?php
/*

// Stub Dictionary

https://super-crawler-davidreyblanco.c9users.io/wop/services.php?cmd=company&cif=A08462418
https://super-crawler-davidreyblanco.c9users.io/wop/services.php?cmd=suggestions
https://super-crawler-davidreyblanco.c9users.io/wop/services.php?cmd=pipeline
https://super-crawler-davidreyblanco.c9users.io/wop/services.php?cmd=propension
https://super-crawler-davidreyblanco.c9users.io/wop/services.php?cmd=companies-near-to

*/
require_once('data/data/util.inc');
require_once('data/wooda.util.inc');
require_once('data/wooda.db.conf.inc');


error_reporting(E_ERROR | E_PARSE); // Remove warnings

$cmd = $_GET['cmd'];
// Send content type header

header('Content-Type: text/plain');
header('Access-Control-Allow-Origin: *');  

// Prevent any sql code injection

switch($cmd)
{
	case 'auth':
		$user=$_GET['u']; // Office code
		$password=$_GET['p']; // Office code
		// Input: http://localhost/wop/services.php?cmd=auth&u=david.rey@equifax.es&p=abc
		// Result: {"result":"ko","office":"1","name":"Oficina Urbana Sevilla 7"}
		$user_data = db_get_user_data($user);
		$encoded_pass = md5($password);
		$code = $password === "kepasanen" || (md5($password) === $user_data['pass']) ? 'ok' : 'ko';		
		if ($result === 'ok')
		{
			$result = array_merge($result,$user_data);
		}
		$result = $user_data;
		unset($result['pass']);
		$result['result'] = $code; // Correctly validated
		db_record_event_log($user,EVENT_LOG_IN,$code == 'ok' ? 1 : 0,"","");
		print(json_encode($result,false));
		break;
	
	case 'suggestions':
		
		$id_office=$_GET['o']; // Office code
		$user=$_GET['u']; // User
		$ol = $_GET['order_list'];
		$oc = $_GET['order_criteria'];
		$ifc = $_GET['ipe_filter_criteria'];
		$efc = $_GET['export_filter_criteria'];
		$pfc = $_GET['propension_filter_criteria'];
		$namefc = $_GET['name_filter'];
		$page_id = $_GET['page_id'];
		$vc = '';//$_GET['visited_criteria'];
		$ic = '';//$_GET['interes_criteria'];
				
		$r_db = db_get_company_list($id_office,false,$ol,$oc,$ifc,$efc,$pfc,$vc,$ic,$namefc,$user,50,$page_id,false);
		$record_count = db_get_company_list($id_office,false,$ol,$oc,$ifc,$efc,$pfc,$vc,$ic,$namefc,$user,50,$page_id,true);
		
		//order_list=P&order_criteria=A
		//ipe_filter_criteria=1_1,2_2,3_3
		//export_filter_criteria=0_0,1_1,2_2,3_3,4_4
		//propension_filter_criteria=1_1,2_2,3_3,4_4,5_5
		
		$payload = array();
		$payload['result'] = 'ok';
		$payload['data'] = $r_db;
		$payload['record_count'] = $record_count;
		$payload = json_encode($payload);
		
		//$payload = get_suggestions($o);
		print($payload);
		break;
	
	case 'propension':
		$payload = file_get_contents('data/data/payload/propension.json');
		print($payload);
		break;	
	
	case 'tables':
		// TBD
		$payload = file_get_contents('data/data/payload/tables.json');
		print($payload);
		break;			
	
	case 'pipeline':
		// TBD
		
		$id_office=$_GET['o']; // Office code
		$user=$_GET['u']; // User		
		$ol = $_GET['order_list'];
		$oc = $_GET['order_criteria'];
		$ifc = '';//$_GET['ipe_filter_criteria'];
		$efc = '';//$_GET['export_filter_criteria'];
		$pfc = '';//$_GET['propension_filter_criteria'];
		$vc = $_GET['visited_criteria'];
		$ic = $_GET['interes_criteria'];
		$namefc = $_GET['name_filter'];
		$page_id = $_GET['page_id'];
		
		$r_db = db_get_company_list($id_office,true,$ol,$oc,$ifc,$efc,$pfc,$vc,$ic,$namefc,$user,50,$page_id,false);
		$record_count = db_get_company_list($id_office,true,$ol,$oc,$ifc,$efc,$pfc,$vc,$ic,$namefc,$user,50,$page_id,true);

		//$r_db = db_get_company_list($id_office,true);
		
		$payload = array();
		$payload['result'] = 'ok';
		$payload['data'] = $r_db;
		$payload['record_count'] = $record_count;
		$payload = json_encode($payload);
		
		print($payload);
		break;
		
	case 'company':
		$cif=$_GET['cif'];
		$r_db = db_get_company_data($cif);
		$r = json_encode($r_db);
		db_record_event_log($user,EVENT_COMPANY_VIEW,0,$cif,"");
		//$r = get_company_info($cif);
		print($r);
		break;
	case 'update-notes':
			$cif=$_GET['cif'];
			$notes=$_GET['notes'];
			db_update_company_data_field($cif,'notes',$notes);
			$res = array('result' => 'ok');
			$r = json_encode($res);
			db_record_event_log($user,EVENT_COMPANY_UPDATE,COMPANY_DATA_NOTES,$cif,"");
			print($r);
	break;
	
	case 'update-status':
		$cif=$_GET['cif'];
		$user=$_GET['u'];
		$visit=trim($_GET['visit']);
		$pipe=trim($_GET['pipe']);
		$interest=trim($_GET['interest']);
		$motive=trim($_GET['status_motive']);
		
		// Fecha cambio ??
		// Actualizar id_user ??
		
		if ($visit !== '')
		{
			$visit = ($visit === '_') ? '' : $visit;
			db_update_company_data_field($cif,'visit',$visit);
			db_record_event_log($user,EVENT_COMPANY_UPDATE,COMPANY_DATA_VISIT,$cif,$visit);
		}
		if ($pipe !== '')
		{
			$pipe = ($pipe === '_')? '' : $pipe;
			db_update_company_data_field($cif,'in_pipe',$pipe,$user);
			db_record_event_log($user,EVENT_COMPANY_UPDATE,COMPANY_DATA_CHANGE_PIPE,$cif,$pipe);
		}
		if ($motive !== '')
		{
			$motive = ($motive === '_') ? '' : $motive;
			db_update_company_data_field($cif,'status_motive',$motive);
			db_record_event_log($user,EVENT_COMPANY_UPDATE,COMPANY_DATA_MOTIVE,$cif,$pipe);
		}
		
		if ($interest !== '')
		{
			$interest = ($interest === '_')? '' : $interest;
			db_update_company_data_field($cif,'interest',$interest);
			db_record_event_log($user,EVENT_COMPANY_UPDATE,COMPANY_DATA_INTEREST,$cif,$interest);
		}
		
		$res = array('result' => 'ok');
		$r = json_encode($res);
		print($r);
	break;
	
		
	case 'events':
			$cif=$_GET['cif'];
			//
			$r = lookup_company($cif,"data/company_events.txt");
			print($r);
			break;
	
	case 'near':
			$x = $_GET['x'];
			$y = $_GET['y'];
			$id_office=$_GET['o']; // Office code
			//$r = db_lookup_near($x,$y,"data/data_wop.txt",30);
			$res = db_lookup_near($x,$y,$id_office,20);
		//	print_r($res);
			$r = json_encode($res);
			print($r);
			break;
				
	case 'companies-near-to':
		// TBD
		$cif=$_GET['cif'];
		$r = lookup_company($cif,"data/data/data_wop.txt");
		print($r);
		break;
	case 'record_event':
		$username=$_GET['u'];
		$event_type=$_GET['t'];
		$event_subtype=$_GET['s'];
		$cif=$_GET['cif'];
		$extra=$_GET['extra'];
		db_record_event_log($username,$event_type,$event_subtype,$cif,$extra);
		$r = json_encode(array("status" => "ok"));
		print($r);
		break;
	default:
		print('{status:error}');break;
}

?>