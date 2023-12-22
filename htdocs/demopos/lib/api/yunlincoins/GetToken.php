<?php
include_once 'yunlincoins_api.php';

$yunlincoins=parse_ini_file('../../../../database/yunlincoins.ini',true);

echo json_encode(Get_Token($yunlincoins['init']['url'],'post',$yunlincoins['init']['id'],$yunlincoins['init']['pw']));
?>