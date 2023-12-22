<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限

include_once '../../../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT ubereats FROM userlogin WHERE company="'.$_POST['company'].'" AND dept="'.$_POST['dep'].'"';
$result=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($result[0]['ubereats'])){
	$conn=sqlconnect('localhost','deliveryspace','orderuser','0424732003','utf-8','mysql');
	if(!isset($_POST['type'])||$_POST['type']!='delete'){//取新單//2022/1/26 加入判斷CLKNAME=UberEats
		$sql='SELECT * FROM cst011 WHERE CLKNAME="UberEats" AND TERMINALNUMBER="'.$result[0]['ubereats'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND NBCHKNUMBER IS NULL ORDER BY BIZDATE ASC';
	}
	else{//作廢單//2022/1/26 加入判斷CLKNAME=UberEats
		$sql='SELECT * FROM cst011 WHERE CLKNAME="UberEats" AND TERMINALNUMBER="'.$result[0]['ubereats'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND NBCHKNUMBER IS NOT NULL ORDER BY BIZDATE ASC';
	}
	$tempres011=sqlquery($conn,$sql,'mysql');
	if(sizeof($tempres011)>0){
		$bizdate=array_column($tempres011,'BIZDATE');
		//2022/1/26 加入判斷CLKNAME=UberEats
		$sql='SELECT * FROM cst012 WHERE CLKNAME="UberEats" AND TERMINALNUMBER="'.$result[0]['ubereats'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND BIZDATE IN ("'.implode('","',$bizdate).'") ORDER BY BIZDATE ASC,LINENUMBER DESC';
		$tempres012=sqlquery($conn,$sql,'mysql');
		
		//2022/1/26 加入判斷CLKNAME=UberEats
		$sql='UPDATE cst011 SET ORDERTYPE="-1" WHERE CLKNAME="UberEats" AND TERMINALNUMBER="'.$result[0]['ubereats'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;UPDATE cst012 SET ORDERTYPE="-1" WHERE CLKNAME="UberEats" AND TERMINALNUMBER="'.$result[0]['ubereats'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;';
		sqlnoresponse($conn,$sql,'mysqlexec');
	}
	else{
	}
	
	/*include_once '../../../../tool/socket.io.php';
	$socketio = new SocketIO();
	if(!isset($_POST['type'])||$_POST['type']!='delete'){
		$socketio->send('api.tableplus.com.tw', 3700, 'getlist',$_POST['dep']);
	}
	else{
		$socketio->send('api.tableplus.com.tw', 3700, 'getdelete',$_POST['dep']);
	}*/

	sqlclose($conn,'mysql');
}
else{
}
if(sizeof($tempres011)>0){
	//2022/2/25 後續要使用自動結帳(開發票)存統編與載具，目前沒有轉換該欄位
	/*if(isset($tempres011[0]['nidin'])){
		for($i=0;sizeof($tempres011);$i++){
			unset($tempres011[$i]['nidin']);
		}
	}
	else{
	}*/
	echo json_encode(array($tempres011,$tempres012));
}
else{
	echo json_encode('empty');
}
?>