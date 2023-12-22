<?php
include_once '../../../tool/dbTool.inc.php';
$map=parse_ini_file('../../../database/mapping.ini',true);
if(isset($map['map'][$_POST['machinetype']])){
	if(file_exists('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini')){
		$timeini=parse_ini_file('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini',true);
	}
	else{
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
}
else{	
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
if(file_exists('../../syspram/clientlist-'.$initsetting['init']['firlan'].'.ini')){
	$clientname=parse_ini_file('../../syspram/clientlist-'.$initsetting['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/clientlist-zh-TW.ini')){
	$clientname=parse_ini_file('../../syspram/clientlist-zh-TW.ini',true);
}
else{
	$clientname=parse_ini_file('../../syspram/clientlist-1.ini',true);
}
if(file_exists('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini')){
	$buttons=parse_ini_file('../../syspram/buttons-'.$initsetting['init']['firlan'].'.ini',true);
}
else if(file_exists('../../syspram/buttons-zh-TW.ini')){
	$buttons=parse_ini_file('../../syspram/buttons-zh-TW.ini',true);
}
else{
	$buttons=parse_ini_file('../../syspram/buttons-1.ini',true);
}

$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT ("'.$buttons['name']['listtype2'].'"||salemap.saleno||"("||tempCST011.CONSECNUMBER||")") AS saleno FROM tempCST011 JOIN salemap ON salemap.consecnumber=tempCST011.CONSECNUMBER WHERE TABLENUMBER="'.$_POST['consecnumber'].'" AND SUBSTR(REMARKS,1,1)="2"';
$res=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
//print_r($res);
echo json_encode($res);
?>