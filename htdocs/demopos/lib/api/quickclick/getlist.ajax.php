<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限

include_once '../../../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT quickclick FROM userlogin WHERE company="'.$_POST['company'].'" AND dept="'.$_POST['dep'].'"';
$result=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($result[0]['quickclick'])){
	$conn=sqlconnect('localhost','deliveryspace','orderuser','0424732003','utf-8','mysql');
	if(!isset($_POST['type'])||$_POST['type']!='delete'){//取新單//2022/1/26 加入判斷CLKNAME=QuickClick
		if(isset($_POST['getlisttype'])&&$_POST['getlisttype']=='1'){//2022/5/24 QuickClick訂單為手動接單，自動下載不需要包括QuickClick訂單，但需要下載串接的UberEat與FoodPanda訂單
			$sql='SELECT * FROM cst011 WHERE (CLKCODE LIKE "FP-%" || CLKCODE LIKE "UE-%") AND CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND NBCHKNUMBER IS NULL ORDER BY BIZDATE ASC';
		}
		else{
			$sql='SELECT * FROM cst011 WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND NBCHKNUMBER IS NULL ORDER BY BIZDATE ASC';
		}
	}
	else{//作廢單//2022/1/26 加入判斷CLKNAME=QuickClick
		$sql='SELECT * FROM cst011 WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND NBCHKNUMBER IS NOT NULL ORDER BY BIZDATE ASC';
	}
	$tempres011=sqlquery($conn,$sql,'mysql');
	if(sizeof($tempres011)>0){
		$bizdate=array_column($tempres011,'BIZDATE');

		if(isset($_POST['getlisttype'])&&$_POST['getlisttype']=='1'){//2022/5/24 QuickClick訂單為手動接單，自動下載不需要包括QuickClick訂單，但需要下載串接的UberEat與FoodPanda訂單
			//2022/1/26 加入判斷CLKNAME=QuickClick
			$sql='SELECT * FROM cst012 WHERE (CLKCODE LIKE "FP-%" || CLKCODE LIKE "UE-%") AND CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND BIZDATE IN ("'.implode('","',$bizdate).'") ORDER BY BIZDATE ASC,LINENUMBER DESC';
			$tempres012=sqlquery($conn,$sql,'mysql');
			
			//2022/1/26 加入判斷CLKNAME=QuickClick
			$sql='UPDATE cst011 SET ORDERTYPE="-1" WHERE (CLKCODE LIKE "FP-%" || CLKCODE LIKE "UE-%") AND CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;UPDATE cst012 SET ORDERTYPE="-1" WHERE (CLKCODE LIKE "FP-%" || CLKCODE LIKE "UE-%") AND CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;';
		}
		else{
			//2022/1/26 加入判斷CLKNAME=QuickClick
			$sql='SELECT * FROM cst012 WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND ORDERTYPE="1" AND BIZDATE IN ("'.implode('","',$bizdate).'") ORDER BY BIZDATE ASC,LINENUMBER DESC';
			$tempres012=sqlquery($conn,$sql,'mysql');
			
			//2022/1/26 加入判斷CLKNAME=QuickClick
			$sql='UPDATE cst011 SET ORDERTYPE="-1" WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;UPDATE cst012 SET ORDERTYPE="-1" WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$result[0]['quickclick'].'" AND CONSECNUMBER="'.$_POST['company'].'-'.$_POST['dep'].'" AND BIZDATE IN ("'.implode('","',$bizdate).'");;;';
		}
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
	echo json_encode(array($tempres011,$tempres012));
}
else{
	echo json_encode('empty');
}
?>