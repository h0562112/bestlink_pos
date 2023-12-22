<?php
include '../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT * FROM salemap WHERE company="'.$_POST['company'].'" ORDER BY type,sale';
$frtype=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
echo json_encode($frtype);
?>