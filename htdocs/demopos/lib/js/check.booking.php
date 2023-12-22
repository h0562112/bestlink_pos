<?php
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
	$invmachine='';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$conn=sqlconnect('../../../database/sale/','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT COUNT(*) AS num FROM tempCST011 WHERE REMARKS LIKE "%-'.$timeini['time']['bizdate'].'%"';
//echo $sql;
$res=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
//print_r($res);
if(isset($res[0])&&intval($res[0]['num'])>0){
	echo 'success';
}
else{
	echo 'error';
}
?>