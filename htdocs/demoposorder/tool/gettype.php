<?php
include '../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT * FROM '.$_POST['type'].'typemap WHERE company="'.$_POST['company'].'"';
$frtype=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
echo json_encode($frtype);
?>