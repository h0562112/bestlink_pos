<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT COUNT(*) AS num FROM userlogin WHERE id="'.$_SESSION['ID'].'"';
$res=sqlquery($conn,$sql,'mysql');
if(isset($res[0]['num'])&&floatval($res[0]['num'])>0){
	$sql='UPDATE userlogin SET password="'.$_SESSION['ID'].'" WHERE id="'.$_SESSION['ID'].'"';
	sqlnoresponse($conn,$sql,'mysql');
	echo 'success';
}
else{
	echo 'oldpwerror';
}
sqlclose($conn,'mysql');
?>