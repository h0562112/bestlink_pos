<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT COUNT(*) AS num FROM userlogin WHERE id="'.$_SESSION['ID'].'" AND password="'.$_POST['oldpw'].'"';
$res=sqlquery($conn,$sql,'mysql');
if(isset($res[0]['num'])&&floatval($res[0]['num'])>0){
	//echo 'w';
	$sql='UPDATE userlogin SET password="'.$_POST['newpw'].'" WHERE id="'.$_SESSION['ID'].'" AND password="'.$_POST['oldpw'].'"';
	sqlnoresponse($conn,$sql,'mysql');
	echo 'success';
}
else{
	echo 'oldpwerror';
}
sqlclose($conn,'mysql');
?>