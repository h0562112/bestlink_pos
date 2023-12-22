<?php
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database/person','data.db','','','','sqlite');
$sql='SELECT * FROM personnel WHERE percard="'.$_POST['mancode'].'" AND state=1';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>