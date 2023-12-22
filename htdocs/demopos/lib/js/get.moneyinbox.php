<?php
include_once '../../../tool/dbTool.inc.php';

$initsetting=parse_ini_file('../../../database/initsetting.ini',true);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($machinedata['basic']['change'])){
}
else{
	$machinedata['basic']['change']='0';
}
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machine']])){
		$invmachine=$dbmapping['map'][$_POST['machine']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
$filename='SALES_'.substr($_POST['bizdate'],0,6).'.db';
if(file_exists('../../../database/sale/cover.db')){
	$coverconn=sqlconnect('../../../database/sale','Cover.db','','','','sqlite');
	$sql='SELECT * FROM list WHERE bizdate="'.$_POST['bizdate'].'"';
	$cores=sqlquery($coverconn,$sql,'sqlite');
	if(isset($cores[0])){
		$cono=array_column($cores,'consecnumber');
	}
	else{
		$cono=[];
	}
	sqlclose($coverconn,'sqlite');
}
else{
	$cono=[];
}

$conn=sqlconnect('../../../database/sale',$filename,'','','','sqlite');
$sql='SELECT CONSECNUMBER,TAX2 FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND ZCOUNTER="'.$_POST['zcounter'].'" AND NBCHKNUMBER IS NULL AND TERMINALNUMBER="'.$invmachine.'"';
$res=sqlquery($conn,$sql,'sqlite');
//$tax=sqlquery($conn,$sql,'sqlite');
//echo $sql;
//其他收/支
$sql='SELECT SUM(AMT) as AMT FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND ZCOUNTER="'.$_POST['zcounter'].'" AND TERMINALNUMBER="'.$invmachine.'" AND (DTLMODE="4" OR DTLMODE="3") AND DTLTYPE="1" AND DTLFUNC="01"';
$amt=sqlquery($conn,$sql,'sqlite');
//echo $sql;
sqlclose($conn,'sqlite');

$tax[0]['TAX2']=0;

for($i=0;$i<sizeof($res);$i++){
	if(in_array($res[$i]['CONSECNUMBER'],$cono)){
		$tax[0]['TAX2'] += floatval($cores[array_search($res[$i]['CONSECNUMBER'],$cono)]['tax2']);
	}
	else{
		$tax[0]['TAX2'] += floatval($res[$i]['TAX2']);
	}
}

if(isset($tax[0]['TAX2'])){
	$tax[0]['initTAX2']=$tax[0]['TAX2'];
}
else{
	$tax[0]['TAX2']=0;
	$tax[0]['initTAX2']=0;
}
if(isset($amt[0]['AMT'])){
}
else{
	$amt[0]['AMT']=0;
}
$tax[0]['TAX2'] += $amt[0]['AMT'];
$tax[0]['AMT']=$amt[0]['AMT'];
$tax[0]['change']=$machinedata['basic']['change'];
$tax[0]['TAX2'] += $machinedata['basic']['change'];

echo json_encode($tax);
?>