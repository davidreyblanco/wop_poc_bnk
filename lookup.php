<?php

require_once('data/data/util.inc');

error_reporting(E_ERROR | E_PARSE); // Remove warnings

$cif = "A28876365";
$r = lookup_company($cif,"data/data_wop.txt");
print($r);

?>