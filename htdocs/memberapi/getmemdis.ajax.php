<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../../../tool/dbTool.inc.php';
if(isset($_POST['type'])&&$_POST['type']=='online'){//網路會員
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$sql='SELECT discount,type,needbuy,cangift,times FROM powergroup WHERE pno=(SELECT power FROM member WHERE memno="'.$_POST['memno'].'")';
	$dis=sqlquery($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
}
else{
	$conn=sqlconnect('../database/person','member.db','','','','sqlite');
	$sql='select discount,type,needbuy,cangift,times from powergroup where pno=(select power from person where memno="'.$_POST['memno'].'")';
	$dis=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
echo json_encode($dis);
?>