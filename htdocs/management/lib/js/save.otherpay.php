<?php
//print_r($_POST);
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/inilib.php';
if(file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini')){
}
else{
	$f=fopen('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini','w');
	fwrite($f,'[pay]'.PHP_EOL);
	fwrite($f,'openpay=0'.PHP_EOL);
	fclose($f);
}
$otherpay=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini',true);
if(isset($_POST['number'])&&$_POST['number']!=''){
	if($_POST['location']=='CST011'){
		$otherpay[$_POST['number']]['fromdb']='';
		$otherpay[$_POST['number']]['should']='1';
		$otherpay[$_POST['number']]['pay']='1';
	}
	else{
		$otherpay[$_POST['number']]['fromdb']='member';
		$otherpay[$_POST['number']]['should']=$_POST['should'];
		$otherpay[$_POST['number']]['pay']=$_POST['pay'];
	}
	$otherpay[$_POST['number']]['location']=$_POST['location'];
	$otherpay[$_POST['number']]['dbname']=$_POST['dbname'];
	$otherpay[$_POST['number']]['name']=$_POST['name'];
	$otherpay[$_POST['number']]['inv']=$_POST['inv'];
	$otherpay[$_POST['number']]['type']=$_POST['type'];
	$otherpay[$_POST['number']]['price']=$_POST['price'];
}
else{
	$row='item'.sizeof($otherpay);
	if($_POST['location']=='CST011'){
		$otherpay[$row]['fromdb']='';
		$otherpay[$row]['should']='1';
		$otherpay[$row]['pay']='1';
	}
	else{
		$otherpay[$row]['fromdb']='member';
		$otherpay[$row]['should']=$_POST['should'];
		$otherpay[$row]['pay']=$_POST['pay'];
	}
	$otherpay[$row]['location']=$_POST['location'];
	$otherpay[$row]['dbname']=$_POST['dbname'];
	$otherpay[$row]['name']=$_POST['name'];
	$otherpay[$row]['inv']=$_POST['inv'];
	$otherpay[$row]['type']=$_POST['type'];
	$otherpay[$row]['price']=$_POST['price'];
}
write_ini_file($otherpay,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini');
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>