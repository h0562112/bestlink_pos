<?php
session_start();
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$sql='SELECT person.*,powergroup.seq AS seq,powergroup.name AS pname FROM person JOIN powergroup ON powergroup.pno=person.power AND seq>=(SELECT powergroup.seq FROM person JOIN powergroup ON person.power=powergroup.pno WHERE id="'.$_SESSION['ID'].'") WHERE person.state="1"';
$data=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
for($i=0;$i<sizeof($data);$i++){
	if(strlen($data[$i]['lastdate'])==0||strtotime($data[$i]['firstdate'])>strtotime($data[$i]['lastdate'])){
		$data[$i]['out']='0';
	}
	else{
		$data[$i]['out']='1';
	}
}
echo json_encode($data);
?>