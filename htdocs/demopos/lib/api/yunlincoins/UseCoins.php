<?php
include_once 'yunlincoins_api.php';

$yunlincoins=parse_ini_file('../../../../database/yunlincoins.ini',true);

print_r(UseCoins($yunlincoins['init']['url'],'post',$_POST['token'],$yunlincoins['init']['key'],$yunlincoins['init']['iv'],$_POST['account'],$_POST['consumeamount'],$_POST['consumeitems'],$_POST['commodityholder']));
?>