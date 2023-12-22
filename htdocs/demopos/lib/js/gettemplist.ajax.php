<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
if(file_exists('../../../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db')){
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
	$sql='SELECT COUNT(*) AS num FROM tempCST011 WHERE NBCHKNUMBER IS NULL AND BIZDATE="'.$timeini['time']['bizdate'].'" AND ZCOUNTER="'.$timeini['time']['zcounter'].'"';
	$num=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(sizeof($num)==0||(isset($num[0]['num'])&&intval($num[0]['num'])==0)){
		echo 'close';
	}
	else{
		echo 'open';
	}
}
else{
	echo 'close';
}
?>