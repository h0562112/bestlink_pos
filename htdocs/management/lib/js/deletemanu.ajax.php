<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$sql='UPDATE manulist SET state=0 WHERE no IN (';
for($i=0;$i<sizeof($_POST['no']);$i++){
	if($i==0){
		$sql=$sql.$_POST['no'][$i];
	}
	else{
		$sql=$sql.','.$_POST['no'][$i];
	}
}
$sql=$sql.')';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>