<?php
include 'dbTool.inc.php';
$conn=sqlconnect("localhost","basic","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql="INSERT INTO DeviceList (seqno,bypass,createdate) VALUES ('222',1,".date("Y-m-d").")";
$table=sqlquery($conn,$sql,"mysql");
sqlclose($conn,"mysql");
?>