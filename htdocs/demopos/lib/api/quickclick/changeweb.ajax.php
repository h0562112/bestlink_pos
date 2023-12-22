<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','deliveryspace','orderuser','0424732003','utf-8','mysql');
if(isset($_POST['data'])){
	foreach($_POST['data'] as $d){
		//2022/1/26 加入判斷CLKNAME=QuickClick
		$sql1='UPDATE cst011 SET ORDERTYPE="2",listbizdate="'.$d[0].'" WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$d[4].'" AND ORDERTYPE="-1" AND CONSECNUMBER="'.$d[3].'" AND BIZDATE="'.$d[5].'"';
		sqlnoresponse($conn,$sql1,'mysql');

		//2022/1/26 加入判斷CLKNAME=QuickClick
		$sql2='UPDATE cst012 SET ORDERTYPE="2" WHERE CLKNAME="QuickClick" AND TERMINALNUMBER="'.$d[4].'" AND ORDERTYPE="-1" AND CONSECNUMBER="'.$d[3].'" AND BIZDATE="'.$d[5].'"';
		sqlnoresponse($conn,$sql2,'mysql');
	}
}
else{
	/*$sql1='UPDATE cst011 SET ORDERTYPE="2" WHERE TERMINALNUMBER="'.$_POST['dep'].'"';
	sqlnoresponse($conn,$sql1,'mysql');
	$sql2='UPDATE cst012 SET ORDERTYPE="2" WHERE TERMINALNUMBER="'.$_POST['dep'].'"';
	sqlnoresponse($conn,$sql2,'mysql');*/
}
sqlclose($conn,'mysql');
?>