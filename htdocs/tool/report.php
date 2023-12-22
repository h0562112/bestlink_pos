<?php
include './dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$company=$_GET['company'];
$pcname=$_GET['pc'];
$mac=$_GET['mac'];
$wanip=$_GET['wan'];
$lanip=$_GET['lan'];
$id=$_GET['id'];
$psw=$_GET['psw'];
$version=$_GET['version'];
$lasttime=date('Y/m/d H:i:s');
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='SELECT pcname FROM report WHERE company="'.$company.'" AND mac="'.$mac.'" AND pcname="'.$pcname.'"';
$check=sqlquery($conn,$sql,'mysql');
if(sizeof($check)==0){
	$sql='INSERT INTO report (type,company,pcname,mac,wanip,lanip,id,psw,version,lasttime) VALUES ("pos","'.$company.'","'.$pcname.'","'.$mac.'","'.$wanip.'","'.$lanip.'","'.$id.'","'.$psw.'","'.$version.'","'.$lasttime.'")';
	$table=sqlnoresponse($conn,$sql,'mysql');
	echo "finish";
}
else if($check[0]=='SQL語法錯誤'||$check[0]=='連線失敗'){
	echo $check[0];
}
else{
	$sql='UPDATE report SET lasttime="'.$lasttime.'",wanip="'.$wanip.'",lanip="'.$lanip.'",id="'.$id.'",psw="'.$psw.'",version="'.$version.'" WHERE company="'.$company.'" AND pcname="'.$pcname.'" AND mac="'.$mac.'"';
	$table=sqlnoresponse($conn,$sql,'mysql');
	echo "finish";
}
sqlclose($conn,'mysql');
?>