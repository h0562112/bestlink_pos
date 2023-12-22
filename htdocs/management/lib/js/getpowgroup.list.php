<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql='SELECT * FROM powergroup WHERE seq>=(SELECT powergroup.seq FROM person JOIN powergroup ON person.power=powergroup.pno WHERE id="'.$_SESSION['ID'].'")';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($data);
?>