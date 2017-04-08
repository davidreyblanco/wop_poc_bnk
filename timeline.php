<?php
require_once('data/data/util.inc');
error_reporting(E_ERROR | E_PARSE); // Remove warnings
$cif = trim($_GET['cif']);
$r = json_decode(lookup_company($cif,"data/data_wop.txt"),true);
$t = json_decode(lookup_company($cif,"data/company_events.txt"),true);
$r = array_merge($r,$t);

foreach($r as $k => $v)
{
	if ($v === '')
	{
		$r[$k] = 0;
	}
}
if (array_key_exists('debug',$_GET)) {print_r($r);}

$timeline = array();
$timeline[get_key_from_date($r['FECHA_PUBLICACION_ULTIMO_ACTO'])] = array('title' =>'Latest detrimental event','desc' =>'Latest recorded detrimental event');
$timeline[get_key_from_date($r['FECHA_PUBLICACION_DEPOSITO'])] = array('title' =>'P&L registration','desc' =>'P&L registration');
$timeline[get_key_from_date($r['FECHA_PUBLICACION_PRIMER_ACTO'])] = array('title' =>'Company incorporation','desc' =>'Company incorporation');
ksort($timeline);
/*
 
 FECHA_PUBLICACION_PRIMER_ACTO
 FECHA_PUBLICACION_DEPOSITO
 FECHA_PUBLICACION_ULTIMO_ACTO

*/
// Get company Data
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<link href="css/timeline.css" rel="stylesheet" type="text/css">

</head>
<body>

<section id="timeline">
<?php 
//print_r($timeline);
foreach($timeline as $k => $v)
{?>
  <article>
    <div class="inner">
      <span class="date">
        <span class="day"><?=substr($k,6,2)?><sup>th</sup></span>
        <span class="month"><?=substr($k,4,2)?></span>
        <span class="year"><?=substr($k,0,4)?></span>
      </span>
      <h2><?=$v['title']?></h2>
      <p><?=$v['desc']?> took place at <?=substr($k,4,2)?>/<?=substr($k,7,2)?>/<?=substr($k,0,4)?>.</p>
    </div>
  </article>
<?php 
}
?>

</section>
</body>
</html></body>
</html>