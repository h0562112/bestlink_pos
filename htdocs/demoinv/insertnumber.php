<?php
include '../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$content=parse_ini_file('../database/setup.ini',true);
$conn=sqlconnect("../database","menu.db","","","","sqlite");
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
$invno=preg_split('/(.INV)/',$_GET['num']);
$sql='INSERT INTO number (company,story,banno,datetime,state,createdatetime) VALUES ("'.$content['basic']['company'].'","'.$content['basic']['story'].'","'.$invno[0].'","'.$year.$month.'",1,"'.date('YmdHis').'")';
$table=sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo "finish";
?>