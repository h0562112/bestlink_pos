<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='SELECT REMARKS FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
$remarks=sqlquery($conn,$sql,'sqlite');
if(sizeof($remarks)>0&&isset($remarks[0]['REMARKS'])){
	$sql='UPDATE tempCST011 SET REMARKS="'.substr($remarks[0]['REMARKS'],0,1).'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'";';
}
else{
	$sql='';
}
$sql=$sql.'UPDATE tempCST011 SET REMARKS=(SELECT REMARKS FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'")||"-'.$_POST['datetime'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'";';
$sql=$sql.'UPDATE tempCST012 SET REMARKS=(SELECT REMARKS FROM tempCST011 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'") WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'";';
sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
?>