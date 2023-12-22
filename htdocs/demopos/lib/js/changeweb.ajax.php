<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','','orderuser','0424732003','utf-8','mysql');
$sql='SHOW DATABASES LIKE "'.$_POST['company'].'"';
$res=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
if(isset($res[0])){
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf-8','mysql');
	if(isset($_POST['data'])){
		foreach($_POST['data'] as $d){
			$sql1='UPDATE tempcst011 SET ORDERTYPE="2",realsaleno='.$d[2].' WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="-1" AND CONSECNUMBER="'.$d[3].'"';
			sqlnoresponse($conn,$sql1,'mysql');
			$sql2='UPDATE tempcst012 SET ORDERTYPE="2" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="-1" AND CONSECNUMBER="'.$d[3].'"';
			sqlnoresponse($conn,$sql2,'mysql');
		}
	}
	else{
		$sql1='UPDATE tempcst011 SET ORDERTYPE="2" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="-1"';
		sqlnoresponse($conn,$sql1,'mysql');
		$sql2='UPDATE tempcst012 SET ORDERTYPE="2" WHERE TERMINALNUMBER="'.$_POST['dep'].'" AND ORDERTYPE="-1"';
		sqlnoresponse($conn,$sql2,'mysql');
	}
	sqlclose($conn,'mysql');
}
else{
}
?>