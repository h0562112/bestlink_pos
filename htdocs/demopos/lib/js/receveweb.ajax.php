<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','','tableplus','0424732003','utf-8','mysql');
$sql='SHOW DATABASES LIKE "'.$_POST['company'].'"';
$res=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($res[0])){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	$sql='UPDATE tempcst011 SET ORDERTYPE="-1" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1"';
	sqlnoresponse($conn,$sql,'mysql');
	$sql='UPDATE tempcst012 SET ORDERTYPE="-1" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="1"';
	sqlnoresponse($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
}
else{
}
?>