<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT * FROM personnel WHERE percard="'.$_POST['punchno'].'" AND state=1';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$datetime=preg_split('/ /',$_POST['time']);
if(sizeof($data)>0&&isset($data[0]['perno'])){
	$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
	$sql='INSERT INTO punchlist (perno,percard,type,date,time,firstdatetime,inputmobile,state) VALUES ("'.$data[0]['perno'].'","'.$data[0]['percard'].'","'.$_POST['type'].'","'.$datetime[0].'","'.substr($datetime[1],0,6).'00","'.$datetime[0].' '.$datetime[1].'","'.$_POST['machine'].'",1)';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	echo 'success';
}
else{
	echo 'dataempty';
}
?>