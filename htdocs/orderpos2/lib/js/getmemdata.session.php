<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
$sql='SELECT tel,name,address FROM member WHERE memno="'.$_POST['memno'].'" AND state=1';
$memdata=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($memdata[0]['tel'])){
	if(isset($_SESSION['tableplusphone'])){
		unset($_SESSION['tableplusphone']);
		unset($_SESSION['tableplusmemno']);
		unset($_SESSION['tableplusname']);
		unset($_SESSION['tableplusaddress']);
		$_SESSION['tableplusphone']=$memdata[0]['tel'];
		$_SESSION['tableplusmemno']=$_POST['memno'];
		$_SESSION['tableplusname']=$memdata[0]['name'];
		$_SESSION['tableplusaddress']=$memdata[0]['address'];
	}
	else{
		$_SESSION['tableplusphone']=$memdata[0]['tel'];
		$_SESSION['tableplusmemno']=$_POST['memno'];
		$_SESSION['tableplusname']=$memdata[0]['name'];
		$_SESSION['tableplusaddress']=$memdata[0]['address'];
	}
	echo 'success;-;'.$_SESSION['tableplusphone'].';-;'.$_SESSION['tableplusmemno'].';-;'.$_SESSION['tableplusname'].';-;'.$_SESSION['tableplusaddress'];
}
else{
	echo 'error';
}
?>