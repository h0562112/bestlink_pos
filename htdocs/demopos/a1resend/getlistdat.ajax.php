<?php
include_once '../../tool/dbTool.inc.php';

$conn=sqlconnect('../../database/sale/','SALES_'.substr(preg_replace('/-/','',$_POST['date']),0,6).'.db','','','','sqlite');
$sql='SELECT CONSECNUMBER FROM CST011 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" ORDER BY CAST(CONSECNUMBER AS FLOAT) ASC LIMIT 1';
$consecnumber=sqlquery($conn,$sql,'sqlite');
$sql='SELECT CONSECNUMBER FROM CST011 WHERE BIZDATE="'.preg_replace('/-/','',$_POST['date']).'" ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1';
$lastconsecnumber=sqlquery($conn,$sql,'sqlite');
//2021/10/18 查詢網路訂單的編號
$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
$w=sqlquery($conn,$sql,'sqlite');
if($consecnumber[0]['CONSECNUMBER']==null){
	$consecnumber[0]['CONSECNUMBER']=$w[0]['one'];
}
else{
	if(floatval($consecnumber[0]['CONSECNUMBER'])<floatval($w[0]['one'])){
		$consecnumber[0]['CONSECNUMBER']=$w[0]['one'];
	}
	else{
	}
}
if($lastconsecnumber[0]['CONSECNUMBER']==null){
	$lastconsecnumber[0]['CONSECNUMBER']=$w[0]['two'];
}
else{
	if(floatval($lastconsecnumber[0]['CONSECNUMBER'])<floatval($w[0]['two'])){
		$lastconsecnumber[0]['CONSECNUMBER']=$w[0]['two'];
	}
	else{
	}
}

sqlclose($conn,'sqlite');

$res['date']=$_POST['date'];
$res['consecnumber']=$consecnumber[0]['CONSECNUMBER'];
$res['lastconsecnumber']=$lastconsecnumber[0]['CONSECNUMBER'];

echo json_encode($res);
?>