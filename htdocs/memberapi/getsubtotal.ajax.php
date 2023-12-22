<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('localhost',$_POST['company'],'tableplus','0424732003','utf8','mysql');
$sql='SELECT COUNT(*) AS totaltime,IF(SUM(membermoney) IS NULL,0,SUM(membermoney)) AS totalmoney FROM memsalelist'.substr($_POST['bizdate'],0,6).' WHERE memno="'.$_POST['memno'].'" AND company="'.$_POST['company'].'" AND dep="'.$_POST['dep'].'" AND consecnumber="paymemmoney" AND state=1 ORDER BY datetime DESC';
$res=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
echo json_encode($res);
?>