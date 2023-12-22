<?php
header('Access-Control-Allow-Origin: *');//╗╖║▌йIеs┼vнн
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','','orderuser','0424732003','utf-8','mysql');
$sql='SHOW DATABASES LIKE "'.$_POST['company'].'"';
$res=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($res[0])){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	$sql='SELECT COUNT(*) AS num FROM tempcst011 WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1"';
	$listnum=sqlquery($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
	if(isset($listnum[0])&&intval($listnum[0]['num'])>0){
		echo $listnum[0]['num'];
	}
	else{
		echo 'empty';
	}
}
else{
	echo 'empty';
}
?>