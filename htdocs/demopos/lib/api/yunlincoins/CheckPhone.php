<?php
include_once 'yunlincoins_api.php';

$yunlincoins=parse_ini_file('../../../../database/yunlincoins.ini',true);

print_r(CheckPhone($yunlincoins['init']['url'],'get',$_POST['token'],$_POST['account']));
?>