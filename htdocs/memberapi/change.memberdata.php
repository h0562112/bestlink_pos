<?php
header('Access-Control-Allow-Origin: *');//遠端呼叫權限
include_once '../tool/dbTool.inc.php';
if(file_exists('../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini')){
	//echo print_r($_POST);
	$initsetting=parse_ini_file('../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
	if($initsetting['init']['onlinemember']=='1'){//網路會員
		$conn=sqlconnect('localhost','','tableplus','0424732003','utf8','mysql');
		$sql='SHOW DATABASES LIKE "'.$_POST['company'].'";';
		$res=sqlquery($conn,$sql,'mysql');
		if(isset($res[0])){//DB存在
			sqlclose($conn,'mysql');
			$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
			$sql='UPDATE member SET point="'.$_POST['data'][0]['remainingpoint'].'",money="'.$_POST['data'][0]['remainingmoney'].'" WHERE memno="'.$_POST['data'][0]['memno'].'" AND cardno="'.$_POST['data'][0]['cardno'].'"';
			sqlnoresponse($conn,$sql,'mysql');
		}
		else{
		}
		sqlclose($conn,'mysql');
		/*$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
		$sql='UPDATE person SET point="'.$_POST['data'][0]['remainingpoint'].'",money="'.$_POST['data'][0]['remainingmoney'].'" WHERE memno="'.$_POST['data'][0]['memno'].'" AND cardno="'.$_POST['data'][0]['cardno'].'"';
		sqlnoresponse($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');*/
	}
	else{//本地會員
		$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
		$sql='UPDATE person SET point="'.$_POST['data'][0]['remainingpoint'].'",money="'.$_POST['data'][0]['remainingmoney'].'" WHERE memno="'.$_POST['data'][0]['memno'].'" AND cardno="'.$_POST['data'][0]['cardno'].'"';
		sqlnoresponse($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
	}
}
else{//預設使用本地會員
	$conn=sqlconnect('../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$sql='UPDATE person SET point="'.$_POST['data'][0]['remainingpoint'].'",money="'.$_POST['data'][0]['remainingmoney'].'" WHERE memno="'.$_POST['data'][0]['memno'].'" AND cardno="'.$_POST['data'][0]['cardno'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
?>