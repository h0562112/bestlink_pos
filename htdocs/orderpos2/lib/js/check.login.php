<?php
session_start();
if($_POST['dep']==substr($_POST['memno'],0,strlen($_POST['dep']))){
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	$sql='SELECT name,tel FROM member WHERE memno="'.$_POST['memno'].'"';
	$data=sqlquery($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
	echo 'islogin;-;'.$data[0]['tel'].';-;'.$_POST['memno'].';-;'.$data[0]['name'];
}
else{
	unset($_SESSION['tableplusmemno']);
	unset($_SESSION['tableplusphone']);
	unset($_SESSION['tableplusname']);
	echo 'islogout';
}
?>