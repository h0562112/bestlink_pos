<?php
include_once '../../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(!isset($_POST['type'])||$_POST['type']!='viewvoid'){
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
else{//2021/9/14 �ȵ��@�o
	$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(isset($data[0]['intella'])&&$data[0]['intella']!=''&&$data[0]['intella']!='0'){
	$temp=preg_split('/:/',$data[0]['intella']);
	$res=['result'=>'exists','intellaconsecnumber'=>$temp[0],'paycode'=>$temp[1],'paymoney'=>$temp[2]];
	echo json_encode($res);
}
else{
	$res=['result'=>'not intella pay'];
	echo json_encode($res);
}
?>