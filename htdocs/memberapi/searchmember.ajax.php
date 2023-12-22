<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
date_default_timezone_set($_POST['settime']);
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
$res=sqlquery($conn,$sql,'mysql');
if(isset($res[0])){//DB存在
	sqlclose($conn,'mysql');
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$sql='SELECT cardno FROM member WHERE memno="'.$_POST['memno'].'"';
	$cardno=sqlquery($conn,$sql,'mysql');
	$sql='SELECT giftpoint FROM memsalelist WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.intval($_POST['consecnumber']).'"';
	$giftpoint=sqlquery($conn,$sql,'mysql');
	echo json_encode(array('cardno'=>$cardno[0]['cardno'],'giftpoint'=>$giftpoint[0]['giftpoint']));
}
else{
}
sqlclose($conn,'mysql');


/*$conn1=sqlconnect('../management/menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
$sql='SELECT cardno FROM person WHERE memno="'.$_POST['memno'].'"';
$cardno=sqlquery($conn1,$sql,'sqlite');
sqlclose($conn1,'sqlite');
$conn1=sqlconnect('../ourpos/'.$_POST['company'].'/'.$_POST['story'],'memsalelist_'.date('Ym').'.db','','','','sqlite');
$sql='SELECT giftpoint FROM salemap WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.intval($_POST['consecnumber']).'"';
$giftpoint=sqlquery($conn1,$sql,'sqlite');
sqlclose($conn1,'sqlite');
echo json_encode(array('cardno'=>$cardno[0]['cardno'],'giftpoint'=>$giftpoint[0]['giftpoint']));*/
?>