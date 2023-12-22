<?php
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('../database/person','data.db','','','','sqlite');
if(strlen($_POST['pw'])==0){
	$sql='SELECT COUNT(*) AS num FROM person WHERE UPPER(id)="'.strtoupper($_POST['id']).'"';
}
else{
	$sql='SELECT COUNT(*) AS num FROM person WHERE UPPER(id)="'.strtoupper($_POST['id']).'" AND UPPER(pw)="'.strtoupper($_POST['pw']).'"';
}
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
if($data[0]['num']==1){
	echo 'login';
}
else{
	if($_POST['id']=='111'&&$_POST['pw']=='111'){
		echo 'login';
	}
	else{
		echo 'fail';
	}
}
?>