<?php
require_once('data/data/util.inc');
require_once('data/wooda.util.inc');
require_once('data/wooda.db.conf.inc');

error_reporting(E_ERROR | E_PARSE); // Remove warnings
$cif = $_GET['cif'];
$r = db_get_company_data($cif);
// Remove all dots
foreach($r as $k => $v)
{
	$r[$k] = trim(str_replace(".","",$r[$k]));
	$r[$k] = trim(str_replace(",",".",$r[$k]));
	
}
//print_r($r['RESULTADO_FINANCIERO_2009']);
//print_r($r);
//$r = json_decode(lookup_company($cif,"data/data_wop.txt"),true);
$financial_data = array($r['RESULTADO_FINANCIERO_2009'],$r['RESULTADO_FINANCIERO_2010'],$r['RESULTADO_FINANCIERO_2011'],$r['RESULTADO_FINANCIERO_2012'],$r['RESULTADO_FINANCIERO_2013'],$r['RESULTADO_FINANCIERO_2014']);

setlocale(LC_MONETARY, 'de_DE');
$rt = array();
foreach($r as $k => $v)
{
	$v = trim($v);
	if ($v === '' || $v === '-')
	{
		$r[$k] = 0;
	}
	$rt[$k] =number_format($r[$k]/100, 0, ',', '.');
//	$rt[$k] = money_format('%=*(#10.2n', $r[$k]);
}
if (array_key_exists('debug',$_GET)) {print_r($r);}

$v_2009 = $r['ACTIVO_NO_CORRIENTE_2009'] === 0 ? 'none' : 'block';
$v_2010 = $r['ACTIVO_NO_CORRIENTE_2010'] === 0 ? 'none' : 'block';
$v_2011 = $r['ACTIVO_NO_CORRIENTE_2011'] === 0 ? 'none' : 'block';
$v_2012 = $r['ACTIVO_NO_CORRIENTE_2012'] === 0 ? 'none' : 'block';
$v_2013 = $r['ACTIVO_NO_CORRIENTE_2013'] === 0 ? 'none' : 'block';
$v_2014 = $r['ACTIVO_NO_CORRIENTE_2014'] === 0 ? 'none' : 'block';
$v_2015 = $r['ACTIVO_NO_CORRIENTE_2015'] === 0 ? 'none' : 'block';

// Get company Data
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Load c3.css -->
 <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
  
<link href="mocks/c3-master/c3.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/charts.css"/>
<!-- Load d3.js and c3.js -->
<script src="mocks/c3-master/c3.js"></script>

<title>Graficos Financieros</title>
<style>
	.tabla-charts
	{
		width: 90%;
		font-size: 1em;
     	
	}
	.data-cell
	{
		text-align: right;
	}
	.tabla-charts-header-2
	{
		color: white;
		background-color: #959595;	
	}
	.tabla-charts-header
	{
		color: white;
		background-color: #555555;
	}
</style>
</head>
<body>

<h5> <b>An&aacute;lisis Financiero de la Empresa</b> </h5>


<table class='tabla-charts'>
  <tr style='width:500px;' class='tabla-charts-header'> <!-- FILA -->
    <th>ANALISIS DE BALANCE</th> <!-- CELDA CABECERA -->
    <th >2013</th>
    <th >2014</th>
    <th >2015</th>
  </tr>
  <tr>
    <td colspan='7' class='tabla-charts-header-2'>ACTIVO</td>
  </tr>
  <tr>
     <td>ACTIVO NO CORRIENTE</td> 
    <td class='datacell'><?=$rt['ACTIVO_NO_CORRIENTE_2013']?></td>
    <td class='datacell'><?=$rt['ACTIVO_NO_CORRIENTE_2014']?></td>
    <td class='datacell'><?=$rt['ACTIVO_NO_CORRIENTE_2015']?></td>
  </tr>
  <tr>
     <td>ACTIVO CORRIENTE</td> 
    <td class='datacell'><?=$rt['ActivoCorriente2013']?></td>
    <td class='datacell'><?=$rt['ActivoCorriente2014']?></td>
    <td class='datacell'><?=$rt['ActivoCorriente2015']?></td>
  </tr>
  <tr>
    <td colspan='7' class='tabla-charts-header-2'>PASIVO</td>
  </tr>
  <tr>
     <td>PASIVO NO CORRIENTE</td> 
    <td class='datacell'><?=$rt['PASIVO_NO_CORRIENTE_2013']?></td>
    <td class='datacell'><?=$rt['PASIVO_NO_CORRIENTE_2014']?></td>
    <td class='datacell'><?=$rt['PASIVO_NO_CORRIENTE_2015']?></td>
  </tr>
  <tr>
     <td>PASIVO CORRIENTE</td> 
    <td class='datacell'><?=$rt['PASIVO_CORRIENTE_2013']?></td>
    <td class='datacell'><?=$rt['PASIVO_CORRIENTE_2014']?></td>
    <td class='datacell'><?=$rt['PASIVO_CORRIENTE_2015']?></td>
  </tr>
   <tr class='tabla-charts-header'> <!-- FILA -->
    <th>CUENTA DE PERDIDAS Y GANANCIAS</th> 
    <th >2013</th>
    <th >2014</th>
    <th >2015</th>
  </tr>
    <tr>
     <td>VENTAS</td> 
    <td class='datacell'><?=$rt['ventas_2013']?></td>
    <td class='datacell'><?=$rt['ventas_2014']?></td>
    <td class='datacell'><?=$rt['ventas_2015']?></td>
  </tr>
   <tr>
     <td>MARGEN BRUTO</td> 
    <td class='datacell'><?=$rt['Margen_Bruto_2013']?></td>
    <td class='datacell'><?=$rt['Margen_Bruto_2014']?></td>
    <td class='datacell'><?=$rt['Margen_Bruto_2015']?></td>
  </tr>
   <tr>
     <td>EBITDA</td> 
    <td class='datacell'><?=$rt['EBITDA_2013']?></td>
    <td class='datacell'><?=$rt['EBITDA_2014']?></td>
    <td class='datacell'><?=$rt['EBITDA_2015']?></td>
  </tr>
  <tr>
     <td>EBIT</td> 
    <td class='datacell'><?=$rt['EBIT_2013']?></td>
    <td class='datacell'><?=$rt['EBIT_2014']?></td>
    <td class='datacell'><?=$rt['EBIT_2015']?></td>
  </tr>
</table>

<span style='padding-top: 30px'></span>
<!--
<div class='row' style='padding-top: 30px'>
	<div id="chart_revenues" class='chart_wooda col-sm-8'></div>
</div>
-->
<div class='row'>
	<div id="chart_indedebtness" class='chart_wooda col-sm-6'></div>
</div>
<!--
<div class='row'>
	<div id="chart_assets" class='chart_wooda col-sm-6'></div>
</div>
<div class='row'>
	<div id="chart_ratios_1" class='chart_wooda_small col-sm-6'></div>
	<div id="chart_ratios_2" class='chart_wooda_small col-sm-6'></div>
</div>
-->
<script>
$( document ).ready(function() {
// Setup all charts
	var chart1 = c3.generate({
	    bindto: '#chart_revenues',
	    data: {
		    x : 'x',

	      columns: [
	        ['x','2009','2010','2011','2012','2013','2014'],        	
	        ['Beneficio/Perdida', <?=$r['RESULTADO_FINANCIERO_2009']?>, <?=$r['RESULTADO_FINANCIERO_2010']?>
	        , <?=$r['RESULTADO_FINANCIERO_2011']?>, <?=$r['RESULTADO_FINANCIERO_2012']?>
	        , <?=$r['RESULTADO_FINANCIERO_2013']?>],
	        ['Ingresos', <?=$r['Clientesxventas2009']?>, <?=$r['Clientesxventas2010']?>
	        , <?=$r['Clientesxventas2011']?>, <?=$r['Clientesxventas2012']?>
	        , <?=$r['Clientesxventas2013']?>, <?=$r['Clientesxventas2014']?>
         ]
	      ],
	      keys: {
		      
		      value: ['revenues'],
		    },
	      type : 'spline'
	    },
	    axis:
	    {
	     	x: {
	    	label: {text: 'Periodos', position: 'outer-middle'}	
	    	},
	    	y: {
		    	label: {text: 'Ingresos EUR', position: 'outer-middle'},
		    	tick: {count: 3,format: d3.format(',.0f')}
		    	}
	    }
	});
	     
	var chart2 = c3.generate({
	    bindto: '#chart_indedebtness',
	    data: {
		    x : 'x',

	      columns: [
	        ['x','2009','2010','2011','2012','2013','2014','2015'],        	
	        ['Deuda a largo', <?=$r['deuda_largo_2009']?>,<?=$r['deuda_largo_2010']?>,<?=$r['deuda_largo_2011']?>,<?=$r['deuda_largo_2012']?>, <?=$r['deuda_largo_2013']?>, <?=$r['deuda_largo_2014']?>, <?=$r['deuda_largo_2015']?>]
	        ,['Deuda a corto', <?=$r['deuda_corto_2009']?>,<?=$r['deuda_corto_2010']?>,<?=$r['deuda_corto_2011']?>,<?=$r['deuda_corto_2012']?>, <?=$r['deuda_corto_2013']?>, <?=$r['deuda_corto_2014']?>, <?=$r['deuda_corto_2015']?>]

	        ],
	      keys: {
		      
		      value: ['revenues'],
		    },
	      type : 'spline'
	    },
	    axis:
	    {
	     	x: {
	    	label: {text: 'Periodos', position: 'outer-middle'}	
	    	},
	    	y: {
		    	label: {text: 'Deuda en EUR', position: 'outer-middle'},
		    	tick: {count: 3,format: d3.format(',.0f')}
		    	}
	    }
	});
	
	var chart3 = c3.generate({
	    bindto: '#chart_assets',
	    data: {
		    x : 'x',

	      columns: [
	        ['x','2009','2010','2011','2012','2013','2014'],        	
	        ['Valor Activos', <?=$r['Inmovilizado_material2009']?>, <?=$r['Inmovilizado_material2010']?>
	        , <?=$r['Inmovilizado_material2011']?>, <?=$r['Inmovilizado_material2012']?>
	        , <?=$r['Inmovilizado_material2013']?>, <?=$r['Inmovilizado_material2014']?>],
	        ['Capital', <?=$r['Capital20091']?>, <?=$r['Capital20101']?>
	        , <?=$r['Capital20111']?>, <?=$r['Capital20121']?>
	        , <?=$r['Capital20131']?>, <?=$r['Capital20141']?>]],
	      keys: {
		      
		      value: ['revenues'],
		    },
	      type : 'spline'
	    },
	    axis:
	    {
	     	x: {
	    	label: {text: 'Periodos', position: 'outer-middle'}	
	    	},
	    	y: {
		    	label: {text: 'Valor Activos EUR', position: 'outer-middle'},
		    	tick: {count: 3,format: d3.format(',.0f')}
		    	}
	    }
	});
	var chart4 = c3.generate({
	    bindto: '#chart_ratios_1',
	    data: {
		    x : 'x',

	      columns: [
	        ['x','2009','2010','2011','2012','2013','2014','2015'],        	
	        ['Activos/Deuda', <?=$r['Inmovilizado_material2009']/($r['DeudasCON_entidadesCREDITO2009']+0.001)?> 
	        ,<?=$r['Inmovilizado_material2010']/($r['DeudasCON_entidadesCREDITO2010'] + 0.0001)?>
	        ,<?=$r['Inmovilizado_material2011']/($r['DeudasCON_entidadesCREDITO2011'] + 0.0001)?>
	        ,<?=$r['Inmovilizado_material2012']/($r['DeudasCON_entidadesCREDITO2012'] + 0.0001)?>
	        ,<?=$r['Inmovilizado_material2013']/($r['DeudasCON_entidadesCREDITO2013'] + 0.0001)?>
	        ,<?=$r['Inmovilizado_material2014']/($r['DeudasCON_entidadesCREDITO2014'] + 0.0001)?>
         ,<?=$r['Inmovilizado_material2015']/($r['DeudasCON_entidadesCREDITO2015'] + 0.0001)?>
			]
	        
	      ],
	      keys: {
		      
		      value: ['revenues'],
		    },
	      type : 'bar'
	    },
	    axis:
	    {
	     	x: {
	    	label: {text: 'Periodos', position: 'outer-middle'}	
	    	},
	    	y: {
		    	label: {text: 'Ratio %', position: 'outer-middle'},
		    	tick: {count: 3,format: d3.format('.2f%')}
		    	}
	    }
	});
	var chart4 = c3.generate({
	    bindto: '#chart_ratios_2',
	    data: {
		    x : 'x',

	      columns: [
	        ['x','2009','2010','2011','2012','2013','2014','2015'],        	
	       
	        ['Beneficio/Ingresos', <?=$r['RESULTADO_FINANCIERO_2009']/($r['Clientesxventas2009']+0001)?> 
	        ,<?=$r['RESULTADO_FINANCIERO_2010']/($r['Clientesxventas2010'] + 0.001)?>
	        ,<?=$r['RESULTADO_FINANCIERO_2011']/($r['Clientesxventas2011'] + 0.001)?>
	        ,<?=$r['RESULTADO_FINANCIERO_2012']/($r['Clientesxventas2012'] + 0.001)?>
	        ,<?=$r['RESULTADO_FINANCIERO_2013']/($r['Clientesxventas2013'] + 0.001)?>
	        ,<?=$r['RESULTADO_FINANCIERO_2014']/($r['Clientesxventas2014'] + 0.001)?>
         ,<?=$r['RESULTADO_FINANCIERO_2015']/($r['Clientesxventas2015'] + 0.001)?>
	        ]
	      ],
	      keys: {
		      
		      value: ['revenues'],
		    },
	      type : 'bar'
	    },
	    axis:
	    {
	     	x: {
	    	label: {text: 'Periodos', position: 'outer-middle'}	
	    	},
	    	y: {
		    	label: {text: 'Ratio %', position: 'outer-middle'},
		    	tick: {count: 3,format: d3.format('.2f%')}
		    	}
	    }
	});
});
</script>
</body>
</html>