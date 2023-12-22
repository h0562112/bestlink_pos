<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
date_default_timezone_set($_POST['settime']);
$date=date('Ym');
//echo $_POST['type'];
if(isset($_POST['type'])&&$_POST['type']=='online'){//網路會員
	/*if(file_exists('../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/memsalelist_'.$date.'.db')){
	}
	else{
		if(file_exists('../database/sale/memsalelist.db')){
		}
		else{
			include_once '../demopos/lib/js/create.emptyDB.php';
			create('memsalelist','../demopos/lib/sql/','../database/sale/','../tool/');
		}
		copy("../database/sale/memsalelist.DB",'../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/memsalelist_'.$date.'.DB');
	}*/

	$conn=sqlconnect('localhost',$_POST['company'],'tableplus','0424732003','utf8','mysql');
	$sql='SHOW TABLES LIKE "memsalelist'.substr($_POST['senddata'][0]['bizdate'],0,6).'";';
	$tbexists=sqlquery($conn,$sql,'mysql');
	//echo $sql;
	if(isset($tbexists[0])){//table存在
	}
	else{
		$sql='CREATE TABLE `memsalelist'.substr($_POST['senddata'][0]['bizdate'],0,6).'` (
				  `bizdate`		varchar(8) NOT NULL,
				  `company`		varchar(32) NOT NULL,
				  `dep`			varchar(32) NOT NULL,
				  `consecnumber`	varchar(32) NOT NULL,
				  `memno`		varchar(32) NOT NULL,
				  `cardno`		varchar(32) NOT NULL,
				  `initpoint`		int(11),
				  `initmoney`		float,
				  `money`		float NOT NULL,
				  `giftpoint`		int(11),
				  `memberpoint`		int(11),
				  `membermoney`		float,
				  `paymoeny`		float DEFAULT "0",
				  `getmemmoney`		float DEFAULT "0",
				  `remainingpoint`	int(11),
				  `remainingmoney`	float,
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

	/*$sql='PRAGMA table_info(salemap)';
	$res=sqlquery($conn,$sql,'sqlite');
	if(sizeof($res)<15){
		$sql='ALTER TABLE salemap ADD initpoint INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
		$sql='ALTER TABLE salemap ADD initmoney INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
		$sql='ALTER TABLE salemap ADD giftpoint INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}*/

	if($_POST['data'][0]['state']=='success'){
		$sql='INSERT INTO memsalelist'.substr($_POST['senddata'][0]['bizdate'],0,6).' (bizdate,company,dep,consecnumber,memno,cardno,initpoint,initmoney,money,giftpoint,memberpoint,membermoney,remainingpoint,remainingmoney,requery,datetime,share) VALUES ("'.$_POST['senddata'][0]['bizdate'].'","'.$_POST['senddata'][0]['company'].'","'.$_POST['senddata'][0]['story'].'","'.intval($_POST['senddata'][0]['consecnumber']).'","'.$_POST['data'][0]['memno'].'","'.$_POST['data'][0]['cardno'].'","'.$_POST['data'][0]['initpoint'].'","'.$_POST['data'][0]['initmoney'].'","'.$_POST['senddata'][0]['paymoney'].'","'.$_POST['data'][0]['giftpoint'].'","'.$_POST['data'][0]['paypoint'].'","'.$_POST['data'][0]['paymoney'].'","'.$_POST['data'][0]['remainingpoint'].'","'.$_POST['data'][0]['remainingmoney'].'","'.$_POST['data'][0]['state'].'","'.$_POST['senddata'][0]['datetime'].'"';
		if(file_exists('../management/menudata/'.$_POST['senddata'][0]['company'].'/'.$_POST['senddata'][0]['story'].'/initsetting.ini')){
			$init=parse_ini_file('../management/menudata/'.$_POST['senddata'][0]['company'].'/'.$_POST['senddata'][0]['story'].'/initsetting.ini',true);
			if(isset($init['init']['membertype'])){
				$sql=$sql.','.$init['init']['membertype'];
			}
			else{
				$sql=$sql.',0';
			}
		}
		else{
			$sql=$sql.',0';
		}
		$sql=$sql.')';
	}
	else{
		$sql='INSERT INTO memsalelist'.substr($_POST['senddata'][0]['bizdate'],0,6).' (bizdate,company,dep,consecnumber,memno,cardno,money,giftpoint,memberpoint,membermoney,requery,datetime,share) VALUES ("'.$_POST['senddata'][0]['bizdate'].'","'.$_POST['senddata'][0]['company'].'","'.$_POST['senddata'][0]['story'].'","'.intval($_POST['senddata'][0]['consecnumber']).'","'.$_POST['senddata'][0]['memno'].'","'.$_POST['senddata'][0]['cardno'].'","'.$_POST['senddata'][0]['paymoney'].'","'.$_POST['senddata'][0]['giftpoint'].'","'.$_POST['senddata'][0]['memberpoint'].'","'.$_POST['senddata'][0]['membermoney'].'","'.$_POST['data'][0]['message'].'","'.$_POST['senddata'][0]['datetime'].'"';
		if(file_exists('../management/menudata/'.$_POST['senddata'][0]['company'].'/'.$_POST['senddata'][0]['story'].'/initsetting.ini')){
			$init=parse_ini_file('../management/menudata/'.$_POST['senddata'][0]['company'].'/'.$_POST['senddata'][0]['story'].'/initsetting.ini',true);
			if(isset($init['init']['membertype'])){
				$sql=$sql.','.$init['init']['membertype'];
			}
			else{
				$sql=$sql.',0';
			}
		}
		else{
			$sql=$sql.',0';
		}
		$sql=$sql.')';
	}
	echo $sql;
	sqlnoresponse($conn,$sql,'mysql');
	sqlclose($conn,'mysql');
}
else{
	if(file_exists('../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/memsalelist_'.$date.'.db')){
	}
	else{
		if(file_exists('../database/sale/memsalelist.db')){
		}
		else{
			include_once '../demopos/lib/js/create.emptyDB.php';
			create('memsalelist','../demopos/lib/sql/','../database/sale/','../tool/');
		}
		copy("../database/sale/memsalelist.DB",'../ourpos/'.$_POST['company'].'/'.$_POST['story'].'/memsalelist_'.$date.'.DB');
	}
	$conn=sqlconnect('../ourpos/'.$_POST['company'].'/'.$_POST['story'],'memsalelist_'.$date.'.db','','','','sqlite');

	$sql='PRAGMA table_info(salemap)';
	$res=sqlquery($conn,$sql,'sqlite');
	if(sizeof($res)<15){
		$sql='ALTER TABLE salemap ADD initpoint INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
		$sql='ALTER TABLE salemap ADD initmoney INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
		$sql='ALTER TABLE salemap ADD giftpoint INTEGER';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}

	if($_POST['data'][0]['state']=='success'){
		$sql='INSERT INTO salemap (bizdate,company,dep,consecnumber,memno,cardno,initpoint,initmoney,money,giftpoint,memberpoint,membermoney,remainingpoint,remainingmoney,requery,datetime) VALUES ("'.$_POST['senddata'][0]['bizdate'].'","'.$_POST['senddata'][0]['company'].'","'.$_POST['senddata'][0]['story'].'","'.intval($_POST['senddata'][0]['consecnumber']).'","'.$_POST['data'][0]['memno'].'","'.$_POST['data'][0]['cardno'].'","'.$_POST['data'][0]['initpoint'].'","'.$_POST['data'][0]['initmoney'].'","'.$_POST['senddata'][0]['paymoney'].'","'.$_POST['data'][0]['giftpoint'].'","'.$_POST['data'][0]['paypoint'].'","'.$_POST['data'][0]['paymoney'].'","'.$_POST['data'][0]['remainingpoint'].'","'.$_POST['data'][0]['remainingmoney'].'","'.$_POST['data'][0]['state'].'","'.$_POST['senddata'][0]['datetime'].'")';
	}
	else{
		$sql='INSERT INTO salemap (bizdate,company,dep,consecnumber,memno,cardno,money,giftpoint,memberpoint,membermoney,requery,datetime) VALUES ("'.$_POST['senddata'][0]['bizdate'].'","'.$_POST['senddata'][0]['company'].'","'.$_POST['senddata'][0]['story'].'","'.intval($_POST['senddata'][0]['consecnumber']).'","'.$_POST['senddata'][0]['memno'].'","'.$_POST['senddata'][0]['cardno'].'","'.$_POST['senddata'][0]['paymoney'].'","'.$_POST['senddata'][0]['giftpoint'].'","'.$_POST['senddata'][0]['memberpoint'].'","'.$_POST['senddata'][0]['membermoney'].'","'.$_POST['data'][0]['message'].'","'.$_POST['senddata'][0]['datetime'].'")';
	}
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
?>