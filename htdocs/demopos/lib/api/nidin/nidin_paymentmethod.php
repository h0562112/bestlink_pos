<?php
include_once '../../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.DB','','','','sqlite');
$selectsql='PRAGMA table_info(tempCST011)';
$column=sqlquery($conn,$selectsql,'sqlite');
$columnname=array_column($column,'name');
if(in_array('nidin',$columnname)){
}
else{
	$insertsql='ALTER TABLE tempCST011 ADD COLUMN nidin TEXT';
	sqlnoresponse($conn,$insertsql,'sqlite');
}
$selectsql2='PRAGMA table_info(CST011)';
$column2=sqlquery($conn,$selectsql2,'sqlite');
$columnname2=array_column($column2,'name');
if(in_array('nidin',$columnname2)){
}
else{
	$insertsql2='ALTER TABLE CST011 ADD COLUMN nidin TEXT';
	sqlnoresponse($conn,$insertsql2,'sqlite');
}
$sql='UPDATE tempCST011 SET nidin="'.$_POST['payment']['data'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>