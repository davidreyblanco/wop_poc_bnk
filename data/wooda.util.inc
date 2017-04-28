<?php

function get_suggestions($office)
{
	$payload = file_get_contents('data/data/payload/suggestions.json');
	return $payload;
}

function get_pipeline($office)
{
	$payload = file_get_contents('data/data/payload/pipeline.json');
	return $payload;
}

function get_company_info($cif,$office='')
{
	$r = lookup_company($cif,"data/data_wop.txt");
	return $r;
}

// --------------------------------------------------
// DATABASE BINDING  - DAO - Version 1.0 Beta - To be refactor
// Needed some environment variables
// DB_NAME  DB_SERVER DB_USER DB_PASS
// --------------------------------------------------


function db_get_user_data($username)
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link) 
	{
			$query = 'SELECT * from users where name="'.$username.'" LIMIT 1';
			$res_db = $link->query($query);
			$data_result = $res_db->fetch_assoc();	
			$result = $data_result;			
	}
	mysqli_close($link);
	return $result;
}

// --------------------------------------------------
// GET Company Data
// --------------------------------------------------
function db_get_company_data($cif)
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link)
	{
			$query = 'SELECT * from companies where cif="'.$cif.'" LIMIT 1;';
			$res_db = $link->query($query);
			$data_result = $res_db->fetch_assoc();
			
			foreach($data_result as $k => $v)
			{
				$data_result[$k] = trim(mb_detect_encoding($v,'UTF-8') ? utf8_encode($v) : $v);
			}
			$result = $data_result;
			mysqli_close($link);
	}
	return $result;
}
//
//$r_db = db_get_company_list($id_office,false,$ol,$oc,$ifc,$efc,$pfc);

//order_list=P&order_criteria=A
//ipe_filter_criteria=1_1,2_2,3_3
//export_filter_criteria=0_0,1_1,2_2,3_3,4_4
//propension_filter_criteria=1_1,2_2,3_3,4_4,5_5
// db_get_company_list($id_office,false,$ol,$oc,$ifc,$efc,$pfc);
function db_get_company_list($id_office,$in_pipeline = false,$ol = '',$oc= '',$ifc= '',$efc = '',$pfc = '',$vc = '',$ic = '',$namefc = '',$user = '',$page_size = 100, $page_count = 0,$is_count = false)
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link)
	{
		if ($is_count)
		{
			$query = 'SELECT count(*) as number from companies where id_office="'.$id_office.'"';
		}
		else
		{
			$query = 'SELECT *,if(volumen_negocio="<700k",1,if(volumen_negocio="700k-1.200K",2,if(volumen_negocio="1.200k-3.000k",3,4))) as volumen_negocio_clases from companies where id_office="'.$id_office.'"';
		}
		
		if ($in_pipeline)
		{
			$query .= " AND in_pipe='SI' ";
		}
		else
		{
			$query .= " AND in_pipe='NO' ";
			$query .= " AND (length(Resultado_neto_2015) > 0) ";
		}
			
			$filter = '';	
			if ($ifc !== '')
			{
				$subfilter = '';
				$items = explode(',',$ifc);
				for($i = 0; $i < count($items); $i++)
				{
					$item = explode('_',$items[$i]);
					if ($item[1] === "1")
					{
						if ($subfilter !== '')
						{
							$subfilter .= ' OR ';
						}
						$subfilter .= "left(IPE,1) = '".$item[0]."'";
					}
				}
				if ($subfilter !== '')
				{
					$query .= ' AND ('.$subfilter.')';
				}
			}
			
			if ($efc !== '')
			{
				$subfilter = '';
				$items = explode(',',$efc);
				for($i = 0; $i < count($items); $i++)
				{
					$item = explode('_',$items[$i]);
					if ($item[1] === "1")
					{
						if ($subfilter !== '')
						{
							$subfilter .= ' OR ';
						}
						$subfilter .= "ScoreExport = '".$item[0]."'";
					}
				}
				if ($subfilter !== '')
				{
					$query .= ' AND ('.$subfilter.')';
				}
			}
			
			if ($pfc !== '')
			{
				$subfilter = '';
				$items = explode(',',$pfc);
				for($i = 0; $i < count($items); $i++)
				{
					$item = explode('_',$items[$i]);
					if ($item[1] === "1")
					{
						if ($subfilter !== '')
						{
							$subfilter .= ' OR ';
						}
						$subfilter .= "left(Propension,1) = '".$item[0]."'";
					}
				}
				if ($subfilter !== '')
				{
					$query .= ' AND ('.$subfilter.')';
				}
			}

			if ($vc !== '')
			{
				$subfilter = '';
				$items = explode(',',$vc);
				for($i = 0; $i < count($items); $i++)
				{
				$item = explode('_',$items[$i]);
				if ($item[1] === "1")
				{
				if ($subfilter !== '')
				{
				$subfilter .= ' OR ';
				}
					$subfilter .= "visit = '".$item[0]."'";
				}
				}
				if ($subfilter !== '')
				{
						$query .= ' AND ('.$subfilter.')';
				}
			}
			
			if ($ic !== '')
			{
				$subfilter = '';
				$items = explode(',',$ic);
				for($i = 0; $i < count($items); $i++)
				{
				$item = explode('_',$items[$i]);
				if ($item[1] === "1")
				{
				if ($subfilter !== '')
				{
					$subfilter .= ' OR ';
				}
				$item[0] = ($item[0] === 'VACIO') ? '' : $item[0];
				$subfilter .= "interest = '".$item[0]."'";
				}
				}
						if ($subfilter !== '')
						{
						$query .= ' AND ('.$subfilter.')';
						}
			}
			// Name filter
			$namefc = trim($namefc);
			if ($namefc !== '')
			{
				$query .= ' AND ( nombre_empresa like "%'.$namefc.'%" OR cif ="'.$namefc.'")';
			}
			
			if ($is_count !== true)
			{
				switch ($ol)
				{
						case 'R': $query .= " ORDER by IPE " . (($oc == "A") ? "ASC":"DESC"); break;
						case 'M': $query .= " ORDER by Municipio  " . (($oc == "A") ? "ASC":"DESC"); break;
						case 'V': 
									//$query .= " ORDER by ventas_2015,ventas_2014,ventas_2013,ventas_2012 " . (($oc == "A") ? "ASC":"DESC"); break;
									$query .= " ORDER by volumen_negocio_clases " . (($oc == "A") ? "ASC":"DESC"); break;
				    
						case 'P': $query .= " ORDER by Propension " . (($oc == "A") ? "ASC":"DESC"); break;
						case 'N':
						default: $query .= " ORDER by Propension DESC"; break;
				}
				$query .= ' LIMIT 1000;';
			}
			
			$query .= ';';
	
//			print($query);exit;
			$res_db = $link->query($query);

			if ($is_count === true)
			{
				$data_result = $res_db->fetch_assoc();
				$result = $data_result['number'];
				
			}
			else
			{
				$i = 0;
				while($data_result = $res_db->fetch_assoc())
				{
					$page = floor($i / $page_size);
					//print('#'.$page.'-'.$page_count.' -'.$i.'-'.$page_size);
					if ($page == $page_count)
					{
						foreach($data_result as $k => $v)
						{
							if ($k === 'notes')
							{
		//						$v = '';
							}
							$data_result[$k] = trim(utf8_encode($v));
						}
						array_push($result,$data_result);
						//print(" OK ");
					}
					//	print("\n");
					$i++;
				//	print('*');
				}
			}

			
			//print_r(count($result));

		mysqli_close($link);
	}
	return $result;
}

function db_lookup_near($x,$y,$id_office,$distance)
{
		$result = array();
		$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		if ($link)
		{
				//$query = 'SELECT CIF,nombre_empresa,tel_fono,x,y from companies where id_office="'.$id_office.'" LIMIT 300;';
				$query = 'SELECT CIF,nombre_empresa,tel_fono,x,y,ScoreExport,in_pipe from companies';
				if ($id_office <> '')
				{
					$query .= ' WHERE id_office = '.$id_office;
				}
				$query .= ';';
				//print($query);
				$res_db = $link->query($query);
				while($data_result = $res_db->fetch_assoc())
				{
					foreach($data_result as $k => $v)
					{
						$data_result[$k] = trim(utf8_decode($v));
					}
					$item = array();
					$item_x = $data_result['x'];
					$item_y = $data_result['y'];
					
					$calculated_distance = distance($x,$y,$item_x,$item_y,$calculated_distance);
					//print($distance.' '.$calculated_distance."\n");
					if ($calculated_distance < $distance)
					{
						$data_result['cif'] = $data_result['CIF'];
						$data_result['nombre_empresa'] = utf8_decode($data_result['nombre_empresa']);
						unset($data_result['CIF']);
						$item = $data_result; // Better if clean the data up
						array_push($result,$item);
					}
					
				}
				mysqli_close($link);
	}
	
	return $result;
}

function db_get_borme_events($cif)
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link)
	{
			$query = 'SELECT * from borme_events where cif="'.$cif.'"';
//			$query .= " ORDER by NUMERO_DE_PUBLICACION desc;";
			//print($query);
			
			$res_db = $link->query($query);
			while($data_result = $res_db->fetch_assoc())
			{
				array_push($result,$data_result);
			}
		mysqli_close($link);
	}
	return $result;
}
// --------------------------------------------------
// UPDATE Company Data
// --------------------------------------------------
function db_update_company_data_field($cif,$field_name,$field_value,$id_user = '')
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link)
	{
		if ($field_name === 'notes')
		{
			$field_value = trim(utf8_decode($field_value));
		}
		$query = 'UPDATE companies SET '.$field_name.'="'.$field_value.'"';
		
		if ($id_user !== '')
		{
			$query .= ',id_user="'.$id_user.'" ';
		}
		
		$query .= ' WHERE cif="'.$cif.'";';
	//	print($query);
		$res_db = $link->query($query);
		mysqli_close($link);
	}
	return $result;
}

// ------------------------------------------------------
// 	LOG TABLE - Record event
//	Timestamp - User - Event Type - Event Subtype - CIF - EXTRA
// ------------------------------------------------------
function db_record_event_log($username,$event_type,$event_subtype,$cif,$extra = '')
{
	$result = array();
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if ($link)
	{
			$timestamp = date('Y-m-d H:i:s');
			$query = 'INSERT into event_log values ("'.$timestamp.'","'.$username.'",'.$event_type.','.$event_subtype.',"'.$cif.'","'.$extra.'");';
			//print($query);
			$res_db = $link->query($query);
			mysqli_close($link);
	}
	return $result;
}

?>