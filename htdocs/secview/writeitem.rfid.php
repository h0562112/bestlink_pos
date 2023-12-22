<?php
print_r($_POST);
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
$init=parse_ini_file('../database/initsetting.ini',true);
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinename']])){
		$invmachine=$dbmapping['map'][$_POST['machinename']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}

$itemname=parse_ini_file('../database/'.$_POST['company'].'-menu.ini',true);

if(file_exists("../database/sale/temp".$_POST['machinename'].".db")){
}
else{
	if(file_exists("../database/sale/EMtemp.DB")){
	}
	else{
		include_once './create.emptyDB.php';
		create('EMtemp');
	}
	copy("../database/sale/EMtemp.db","../database/sale/temp".$_POST['machinename'].".db");
}
$connM=sqlconnect("../database","menu.db","","","","sqlite");
$conn=sqlconnect('../database/sale','temp'.$_POST['machinename'].'.db','','','','sqlite');

$sql='';
for($i=0;$i<sizeof($_POST['linenumber']);$i++){
	
	$sqlt='SELECT * FROM itemsdata WHERE inumber="'.$_POST['no'][$i].'" AND fronttype="'.$_POST['itemtype'][$i].'"';
	$data=sqlquery($connM,$sqlt,'sqlite');
	
	$sql .= 'INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'","'.str_pad($_POST['linenumber'][$i],3,'0',STR_PAD_LEFT).'","'.$_POST['usercode'].'","'.$_POST['username'].'","1","1","01",substr(("0000000000000000"||"'.$data[0]['inumber'].'"),-16,16),"'.$itemname[$data[0]['inumber']]['name1'].'",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",substr(("000000"||"'.$data[0]['fronttype'].'"),-6,6),"",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"'.$_POST['mname'][$i].'",0,1,'.$_POST['money1'][$i].','.$_POST['money1'][$i].',NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";INSERT INTO list SELECT "'.$_POST['machinename'].'","'.$timeini['time']['bizdate'].'","'.$_POST['consecnumber'].'","'.str_pad(($_POST['linenumber'][$i]+1),3,'0',STR_PAD_LEFT).'","'.$_POST['usercode'].'","'.$_POST['username'].'","1","3","02","item","單品優惠","","","","",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,"",0,0,0,0,NULL,NULL,NULL,NULL,NULL,"'.$timeini['time']['zcounter'].'","'.$_POST['listtype'].'","'.date('YmdHis').'";';

}
sqlclose($connM,'sqlite');

sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
?>