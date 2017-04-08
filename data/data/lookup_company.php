<?php

require_once('util.inc');

$cif = $argv[1];
$r = lookup_company($cif);
print($r);print("\n");
?>