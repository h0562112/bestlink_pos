<?php
session_start();
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
if($_POST['dep']=='rabbit0001'){
	$sql='SELECT '.$tablename.'.*,powergroup.seq AS seq,powergroup.name AS pname FROM '.$tablename.' JOIN powergroup ON powergroup.pno='.$tablename.'.power WHERE memno LIKE "'.$_POST['dep'].'%" OR memno NOT LIKE "rabbit%" ORDER BY powergroup.seq,'.$tablename.'.firstdate DESC';
}
else{
	$sql='SELECT '.$tablename.'.*,powergroup.seq AS seq,powergroup.name AS pname FROM '.$tablename.' JOIN powergroup ON powergroup.pno='.$tablename.'.power WHERE '.$tablename.'.memno LIKE "'.$_POST['dep'].'%" ORDER BY powergroup.seq,'.$tablename.'.firstdate DESC,member.memno DESC';
}
$data=sqlquery($conn,$sql,$dbtype);
sqlclose($conn,$dbtype);
for($i=0;$i<sizeof($data);$i++){
	if(strlen($data[$i]['lastdate'])==0||strtotime($data[$i]['firstdate'])>strtotime($data[$i]['lastdate'])){
		$data[$i]['out']='0';
	}
	else{
		$data[$i]['out']='1';
	}
	$data[$i]['firstdate']=preg_replace('/-/','/',$data[$i]['firstdate']);
}
echo json_encode($data);
?>