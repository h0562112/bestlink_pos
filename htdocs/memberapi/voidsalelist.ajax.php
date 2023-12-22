<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
$sql='UPDATE salemap SET state=0 WHERE bizdate="'.$_POST['date'].'" AND consecnumber="'.$_POST['listno'].'"';
sqlnoresponse($conn,$sql,'mysql');
sqlclose($conn,'mysql');
?>