<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql="SELECT * FROM powergroup WHERE seq>=(SELECT powergroup.seq FROM powergroup JOIN person ON person.id='".$_SESSION['ID']."' AND person.power=powergroup.pno) AND powergroup.`delete`=0 ORDER BY seq";
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>