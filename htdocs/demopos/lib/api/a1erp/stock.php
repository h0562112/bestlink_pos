<?php
include_once './a1_api.inc.php';
$setup=parse_ini_file('../../../../menudata/mummum/mummum0001/setup.ini',true);
$url=$setup['a1erp']['url'];
$id=$setup['a1erp']['id'];
$pw=$setup['a1erp']['pw'];
$login=Login($url,"post",$id,$pw);
$key=$login[1]['access_token'];
$res=Stock($url,'get',$key,'1');
print_r($res);
?>