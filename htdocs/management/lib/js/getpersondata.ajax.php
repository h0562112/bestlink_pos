<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT person.*,powergroup.name AS pname,powergroup.seq AS seq,powergroup.pno AS pno FROM person JOIN powergroup ON powergroup.pno=person.power WHERE cardno='".$_POST['focus']."' ORDER BY powergroup.seq";
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>