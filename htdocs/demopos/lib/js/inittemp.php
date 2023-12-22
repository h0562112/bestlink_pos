<?php
if(file_exists('../../../database/sale/temp.db')){
	include_once '../../../tool/dbTool.inc.php';
	$conn=sqlconnect('../../../database/sale','temp.db','','','','sqlite');
	$sql='DELETE FROM list';
	sqlnoresponse($conn,$sql,'sqlite');
	$sql='DELETE FROM ban';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
}
else{
}
?>