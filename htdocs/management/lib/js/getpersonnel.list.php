<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql='SELECT * FROM personnel WHERE state=1 ORDER BY credatetime ASC';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>