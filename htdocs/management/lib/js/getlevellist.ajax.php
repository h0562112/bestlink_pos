<?php
include_once '../../../tool/dbTool.inc.php';
$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
if($initsetting['init']['onlinemember']=='1'){//網路會員
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$dbtype='mysql';
	$tablename='member';
}
else{
	$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$dbtype='sqlite';
	$tablename='person';
}
$sql="SELECT * FROM powergroup WHERE state='1'";
$data=sqlquery($conn,$sql,$dbtype);
sqlclose($conn,$dbtype);
echo json_encode($data);
?>