<?php
include_once '../../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$otherpay=parse_ini_file('../../../../database/otherpay.ini',true);

for($p=1;$p<sizeof($otherpay);$p++){
	if(isset($otherpay['item'.$p]['jkos'])&&$otherpay['item'.$p]['jkos']=='1'){//2022/5/5 串接街口支付付款
		$checkrow[]=$otherpay['item'.$p]['dbname'];
	}
	else{
	}
}

$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(!isset($_POST['type'])||$_POST['type']!='viewvoid'){
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
else{//2021/9/14 暫結作廢
	$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

foreach($checkrow as $dbname){
	if($data[0][$dbname]!=''&&$data[0][$dbname]!='0'){
		$temp=preg_split('/\=/',$data[0][$dbname]);
		if(isset($temp[1])){
		}
		else{
			$temp[1]=0;
		}
		if(isset($temp[2])){
		}
		else{
			$temp[2]='';
		}
		if(isset($temp[3])){
		}
		else{
			$temp[3]='';
		}
		$res=['result'=>'exists','paymoney'=>$temp[1],'saleno'=>$temp[2],'tradeno'=>$temp[3]];
		break;
	}
	else{
	}
}

if(isset($res)){
	//$res=['result'=>'exists','asm'=>$data[0]['CREDITCARD'],'paymoney'=>($data[0]['TAX3']+$data[0]['TAX9'])];
	echo json_encode($res);
}
else{
	$res=['result'=>'not use jkos'];
	echo json_encode($res);
}
?>