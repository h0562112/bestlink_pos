<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
//$_POST=json_decode($_POST);
include_once '../tool/dbTool.inc.php';
$conn1=sqlconnect('localhost',$_POST['company'],'orderpos','0424732003','utf8','mysql');
//$conn1=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
$sql='SELECT cardno FROM member WHERE memno="'.$_POST['memno'].'"';
//$sql='SELECT cardno FROM person WHERE memno="'.$_POST['memno'].'"';
$cardno=sqlquery($conn1,$sql,'mysql');
sqlclose($conn1,'sqlite');
echo json_encode($cardno);
?>