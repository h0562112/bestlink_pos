<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
include_once '../tool/inilib.php';
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$init=parse_ini_file('../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../database/machinedata.ini',true);
$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
write_ini_file($machinedata,'../database/machinedata.ini');
if(isset($_POST['tabnum'])&&preg_match('/-/',$_POST['tabnum'])){
	$temp=preg_split('/-/',$_POST['tabnum']);
	$_POST['tabnum']=$temp[0].'-'.(intval($temp[1])+1);
}
else{
	$_POST['tabnum']=$_POST['tabnum'].'-1';
}
$conn=sqlconnect('../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$createdatetime=date('YmdHis');
$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,INVOICEDATE,INVOICETIME,OPENCLKCODE,CLKCODE,CLKNAME,REGMODE,REGTYPE,REGFUNC,TABLENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME,UPDATEDATETIME) VALUES ("'.$_POST['machinetype'].'","'.$timeini['time']['bizdate'].'","'.str_pad($machinedata['basic']['consecnumber'],6,"0",STR_PAD_LEFT).'","","0","0","0","'.$_POST['usercode'].'","'.$_POST['username'].'","0","0","0","'.$_POST['tabnum'].'","'.$timeini['time']['zcounter'].'","1","'.$createdatetime.'","'.$createdatetime.'")';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo $timeini['time']['bizdate'].','.str_pad($machinedata['basic']['consecnumber'],6,"0",STR_PAD_LEFT).','.$_POST['tabnum'];
$ftable=fopen('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$_POST['tabnum']).'.ini','a');
fwrite($ftable,'['.$_POST['tabnum'].']'.PHP_EOL);
fwrite($ftable,'bizdate="'.$timeini['time']['bizdate'].'"'.PHP_EOL);
fwrite($ftable,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
fwrite($ftable,'consecnumber="'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'"'.PHP_EOL);
fwrite($ftable,'saleamt="0"'.PHP_EOL);
fwrite($ftable,'person="0"'.PHP_EOL);
fwrite($ftable,'createdatetime="'.$createdatetime.'"'.PHP_EOL);
fwrite($ftable,'table="'.$_POST['tabnum'].'"'.PHP_EOL);
if(strstr($_POST['tabnum'],',')){
	fwrite($ftable,'tablestate="1"'.PHP_EOL);
}
else{
	fwrite($ftable,'tablestate="0"'.PHP_EOL);
}
fwrite($ftable,'state="999"'.PHP_EOL);
fwrite($ftable,'machine="'.$_POST['machinetype'].'"'.PHP_EOL);
fclose($ftable);
?>