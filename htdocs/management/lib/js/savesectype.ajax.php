<?php
include_once '../../../tool/inilib.php';
$rear=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-rear.ini',true);
date_default_timezone_set($timeout['time']['name']);
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
if(strlen($_POST['number'])>0){
	$rear[$_POST['number']]['name']=$_POST['name'];
}
else{
	$row=sizeof($rear);
	$rear[$row]['name']=$_POST['name'];
	$rear[$row]['state']='1';
}
write_ini_file($rear,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-rear.ini');
?>