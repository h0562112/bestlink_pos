<?php
include '../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$content=parse_ini_file('./setup.ini',true);
$conn=sqlconnect("localhost","ban","banuser","1qaz2wsx","utf-8","mysql");
$year=intval(date('Y'))-1911;
if(intval(date('m'))%2){
	$month=intval(date('m'))+1;
}
else{
	$month=intval(date('m'));
}
if(strlen($month)<2){
	$month='0'.$month;
}
$sql='INSERT number (company,story,banno,datetime,state) VALUES ("'.$content['basic']['company'].'","'.$content['basic']['story'].'","'.$_GET['num'].'","'.$year.$month.'",1)';
$table=sqlnoresponse($conn,$sql,'mysql');
sqlclose($conn,'mysql');
echo "finish";
?>