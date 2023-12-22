<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
//date_default_timezone_set('Asia/Taipei');
$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT * FROM personnel WHERE perno="'.$_POST['perno'].'"';
$perdata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
$sql='SELECT * FROM punchlist WHERE perno="'.$_POST['perno'].'" AND date="'.$_POST['date'].'" AND time="'.$_POST['time'].'" AND type="'.$_POST['type'].'"';
$res=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$res[0]['name']=$perdata[0]['percard'].$perdata[0]['name'];
echo json_encode($res);
?>