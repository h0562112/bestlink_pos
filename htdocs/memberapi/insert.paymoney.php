<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
date_default_timezone_set($_POST['settime']);
$conn=sqlconnect('localhost',$_POST['company'],'tableplus','0424732003','utf8','mysql');
$sql='SHOW TABLES LIKE "memsalelist'.substr($_POST['bizdate'],0,6).'";';
$tbexists=sqlquery($conn,$sql,'mysql');
if(isset($tbexists[0])){//table存在
}
else{
	$sql='CREATE TABLE `memsalelist'.substr($_POST['bizdate'],0,6).'` (
			  `bizdate`		varchar(8) NOT NULL,
			  `company`		varchar(32) NOT NULL,
			  `dep`			varchar(32) NOT NULL,
			  `consecnumber`	varchar(32) NOT NULL,
			  `memno`		varchar(32) NOT NULL,
			  `cardno`		varchar(32) NOT NULL,
			  `initpoint`		int(11),
			  `initmoney`		int(11),
			  `money`		float NOT NULL,
			  `giftpoint`		int(11),
			  `memberpoint`		int(11),
			  `membermoney`		int(11),
			  `paymoeny`		int(11) DEFAULT "0",
			  `getmemmoney`		int(11) DEFAULT "0",
			  `remainingpoint`	int(11),
			  `remainingmoney`	int(11),
			  `requery`		varchar(64),
			  `datetime`		varchar(32),
			  `share`		INTEGER,
			  `state`		INTEGER DEFAULT "1",
			  PRIMARY KEY (`bizdate`,`company`,`dep`,`consecnumber`,`memno`,`datetime`)
			) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
	sqlnoresponse($conn,$sql,'mysql');
}
sqlclose($conn,'mysql');

$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');

if(isset($_POST['deletetime'])){
	$sql='INSERT INTO memsalelist'.substr($_POST['bizdate'],0,6).' (bizdate,company,dep,consecnumber,memno,cardno,initpoint,initmoney,money,giftpoint,memberpoint,membermoney,remainingpoint,remainingmoney,requery,datetime,state) SELECT "'.$_POST['bizdate'].'","'.$_POST['company'].'","'.$_POST['dep'].'","paymemmoney",memno,cardno,point,money,"'.$_POST['paymoney'].'",0,0,"'.$_POST['getmemmoney'].'",point,(money+'.$_POST['getmemmoney'].'),"success","'.date('YmdHis').'",0 FROM member WHERE memno="'.$_POST['memno'].'"';
	sqlnoresponse($conn,$sql,'mysql');
	$sql='UPDATE memsalelist'.substr($_POST['bizdate'],0,6).' SET state=0 WHERE datetime="'.$_POST['deletetime'].'" AND company="'.$_POST['company'].'" AND dep="'.$_POST['dep'].'"';
	sqlnoresponse($conn,$sql,'mysql');
}
else{
	$sql='INSERT INTO memsalelist'.substr($_POST['bizdate'],0,6).' (bizdate,company,dep,consecnumber,memno,cardno,initpoint,initmoney,money,giftpoint,memberpoint,membermoney,remainingpoint,remainingmoney,requery,datetime) SELECT "'.$_POST['bizdate'].'","'.$_POST['company'].'","'.$_POST['dep'].'","paymemmoney",memno,cardno,point,money,"'.$_POST['paymoney'].'",0,0,"'.$_POST['getmemmoney'].'",point,(money+'.$_POST['getmemmoney'].'),"success","'.date('YmdHis').'" FROM member WHERE memno="'.$_POST['memno'].'"';
	sqlnoresponse($conn,$sql,'mysql');
}

$sql='UPDATE member SET money=(money+'.$_POST['getmemmoney'].') WHERE memno="'.$_POST['memno'].'"';
sqlnoresponse($conn,$sql,'mysql');

$sql='SELECT * FROM member WHERE memno="'.$_POST['memno'].'"';
$res=sqlquery($conn,$sql,'mysql');

sqlclose($conn,'mysql');
echo json_encode($res);
?>