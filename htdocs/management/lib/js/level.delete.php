<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
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
$list='';
for($i=0;$i<sizeof($_POST['pg']);$i++){
	if(strlen($list)==0){
		$list=$_POST['pg'][$i];
	}
	else{
		$list=$list.','.$_POST['pg'][$i];
	}
}
$sql="UPDATE powergroup SET state=0 WHERE pno IN (".$list.")";
sqlnoresponse($conn,$sql,$dbtype);
sqlclose($conn,$dbtype);
?>