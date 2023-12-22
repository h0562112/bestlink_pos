<?php
include_once '../../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$otherpay=parse_ini_file('../../../../database/otherpay.ini',true);
$checkrow=array();
for($p=1;$p<sizeof($otherpay);$p++){
	if(isset($otherpay['item'.$p]['pxpayplus'])&&$otherpay['item'.$p]['pxpayplus']=='1'){//2022/11/4 串接全支付付款
		$checkrow[]=$otherpay['item'.$p]['dbname'];
	}
	else{
	}
}

$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
if(!isset($_POST['type'])||$_POST['type']!='viewvoid'){
	$sql='SELECT * FROM CST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
	$data=sqlquery($conn,$sql,'sqlite');

	//2022/11/7 產生產品明細json
	$sql='SELECT * FROM CST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC';
	$item=sqlquery($conn,$sql,'sqlite');
}
else{//2021/9/14 暫結作廢
	$sql='SELECT * FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
	$data=sqlquery($conn,$sql,'sqlite');

	//2022/11/7 產生產品明細json
	$sql='SELECT * FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" ORDER BY LINENUMBER ASC';
	$item=sqlquery($conn,$sql,'sqlite');
}
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

//2022/11/7 產生產品明細json
if(isset($item[0])){
	$itemjson=array();
	$itemindex='';
	for($i=0;$i<sizeof($item);$i++){
		if($item[$i]['ITEMCODE']!='item'&&$item[$i]['ITEMCODE']!='list'&&$item[$i]['ITEMCODE']!='autodis'&&$item[$i]['ITEMCODE']!='member'){
			$itemindex=sizeof($itemjson);
			$itemjson[$itemindex]['name']=$item[$i]['ITEMNAME'];
			$itemjson[$itemindex]['amount']=intval($item[$i]['AMT']);
			$itemjson[$itemindex]['qty']=intval($item[$i]['QTY']);
		}
		else if($item[$i]['ITEMCODE']=='item'&&$item[$i]['AMT']!='0'){
			$itemjson[$itemindex]['amount']=intval($itemjson[$itemindex]['amount'])+intval($item[$i]['AMT']);
		}
		else if($item[$i]['AMT']!='0'){
			$itemname=array_column($itemjson,'name');
			if(in_array('系統優惠',$itemname)){
				$itemjson[array_search('系統優惠',$itemname)]['amount']=intval($itemjson[array_search('系統優惠',$itemname)]['amount'])+intval($item[$i]['AMT']);
			}
			else{
				$itemindex=sizeof($itemjson);
				$itemjson[$itemindex]['name']='系統優惠';
				$itemjson[$itemindex]['amount']=intval($item[$i]['AMT']);
				$itemjson[$itemindex]['qty']=1;
			}
		}
	}
	$res['itemjson']=json_encode($itemjson);
}
else{
}

if(isset($res)){
	//$res=['result'=>'exists','asm'=>$data[0]['CREDITCARD'],'paymoney'=>($data[0]['TAX3']+$data[0]['TAX9'])];
	echo json_encode($res);
}
else{
	$res=['result'=>'not use pxpayplus'];
	echo json_encode($res);
}
?>