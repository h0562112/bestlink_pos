<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/inilib.php';
$en=$_POST['en'];
$num=$_POST['num'];
if(intval($num)%250==249){
	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$enum=str_pad((intval($num)+1),8,'0',STR_PAD_LEFT);
	$machinedata['basic']['startoinv']=$en.$num;
	$machinedata['basic']['endoinv']=$en.$enum;
	write_ini_file($machinedata,'../../../database/machinedata.ini');
	echo $machinedata['basic']['startoinv'];
}
else{
	$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
	$enum=str_pad((intval($num)-intval($num%250)+250),8,'0',STR_PAD_LEFT);
	$machinedata['basic']['startoinv']=$en.$num;
	$machinedata['basic']['endoinv']=$en.$enum;
	write_ini_file($machinedata,'../../../database/machinedata.ini');
	echo $machinedata['basic']['startoinv'];
}
?>