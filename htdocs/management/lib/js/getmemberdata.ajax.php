<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
if($initsetting['init']['onlinemember']=='1'){//網路會員
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$dbtype='mysql';
	$sql="SELECT member.*,powergroup.name AS pname,powergroup.seq AS seq FROM member JOIN powergroup ON powergroup.pno=member.power WHERE memno='".$_POST['focus']."' ORDER BY powergroup.seq";
}
else{
	$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$dbtype='sqlite';
	$sql="SELECT person.*,powergroup.name AS pname,powergroup.seq AS seq FROM person JOIN powergroup ON powergroup.pno=person.power WHERE memno='".$_POST['focus']."' ORDER BY powergroup.seq";
}

$data=sqlquery($conn,$sql,$dbtype);
sqlclose($conn,'sqlite');
echo json_encode($data);
?>