<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
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
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT ZCOUNTER FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(sizeof($data)>0&&isset($data[0]['ZCOUNTER'])){
	$zcounter=$data[0]['ZCOUNTER'];
}
else{
	$zcounter=$timeini['time']['zcounter'];
}

if(strstr($_POST['tabnum'],',')){
	$templist=preg_split('/,/',$_POST['tabnum']);
	foreach($templist as $tl){
		if(file_exists('../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$tl.'.ini')){
			$tabini=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$tl.'.ini',true);
			$tabini[$tl]['state']="1";
			$tabini[$tl]['machine']="";
			write_ini_file($tabini,'../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$tl.'.ini');
		}
		else{
		}
	}
}
else{
	if(file_exists('../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$_POST['tabnum'].'.ini')){
		$tabini=parse_ini_file('../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$_POST['tabnum'].'.ini',true);
		$tabini[$_POST['tabnum']]['state']="1";
		$tabini[$_POST['tabnum']]['machine']="";
		write_ini_file($tabini,'../../table/'.$_POST['bizdate'].';'.$zcounter.';'.$_POST['tabnum'].'.ini');
	}
	else{
	}
}
?>