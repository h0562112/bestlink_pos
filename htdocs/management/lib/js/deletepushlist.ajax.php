<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$stock=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini',true);
$tt='SELECT listno FROM pushlist WHERE no IN (';
for($i=0;$i<sizeof($_POST['no']);$i++){
	if($i==0){
		$tt=$tt.$_POST['no'][$i];
	}
	else{
		$tt=$tt.','.$_POST['no'][$i];
	}
}
$tt=$tt.')';
$sql='SELECT listno,itemno,qty FROM pushlistdetail WHERE listno IN ('.$tt.')';
$list=sqlquery($conn,$sql,'sqlite');
foreach($list as $l){
	$stock[$l['itemno']]['stock']=floatval($stock[$l['itemno']]['stock'])-floatval($l['qty']);
}
write_ini_file($stock,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini');
$sql='DELETE FROM pushlistdetail WHERE listno IN ('.$tt.')';
sqlnoresponse($conn,$sql,'sqlite');
$sql='DELETE FROM pushlist WHERE listno IN ('.$tt.')';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>