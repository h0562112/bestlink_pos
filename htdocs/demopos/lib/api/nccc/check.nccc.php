<?php
include_once '../../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(!isset($_POST['type'])||$_POST['type']!='viewvoid'){
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
else{//2021/9/14 ¼Èµ²§@¼o
	$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if($data[0]['TAX3']!=''&&$data[0]['TAX3']!='0'&&isset($initsetting['init']['nccc'])&&$initsetting['init']['nccc']=='1'){
	$res=['result'=>'exists','asm'=>$data[0]['CREDITCARD'],'paymoney'=>($data[0]['TAX3']+$data[0]['TAX9'])];
	echo json_encode($res);
}
else{
	$res=['result'=>'not use nccc pay'];
	echo json_encode($res);
}
?>