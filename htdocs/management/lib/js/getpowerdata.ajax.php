<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT * FROM powergroup WHERE pno='".$_POST['focus']."'";
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>