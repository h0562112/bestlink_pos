<?php
include_once '../tool/dbTool.inc.php';

if(file_exists('../database/sale/'.$_GET['date'].'/invdata_'.$_GET['date'].'_'.$_GET['machine'].'.db')){
	$conn=sqlconnect('../database/sale/'.$_GET['date'],'invdata_'.$_GET['date'].'_'.$_GET['machine'].'.db','','','','sqlite');
	$sql='SELECT COUNT(*) AS number FROM number WHERE company="'.$_GET['company'].'" AND story="'.$_GET['dep'].'" AND state="1"';
	//echo $sql;
	$res=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($res[0]['number'])){
		echo $res[0]['number'];
	}
	else{
		echo 'notdata';
	}
}
else{
	echo 'notdb';
}
?>