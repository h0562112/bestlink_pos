<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
if($initsetting['init']['onlinemember']=='1'){//�����|��
	$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
	$dbtype='mysql';
	$tablename='member';
}
else{
	$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','member.db','','','','sqlite');
	$dbtype='sqlite';
	$tablename='person';
}

if(strlen($_POST['pno'])==0){
	$sql="SELECT COUNT(*) AS num FROM powergroup";
	$num=sqlquery($conn,$sql,$dbtype);
	$sql="INSERT INTO powergroup (seq,pno,name,discount,state) VALUES ('".intval($_POST['seq'])."','".$num[0]['num']."','".$_POST['name']."'";
	if(strlen($_POST['discount'])>0){
		$sql=$sql.','.$_POST['discount'];
	}
	else{
		$sql=$sql.',100';
	}
	if(!isset($_POST['stop'])){
		$sql=$sql.",1)";
	}
	else{
		$sql=$sql.",0)";
	}
	sqlnoresponse($conn,$sql,$dbtype);
}
else{
	if(!isset($_POST['stop'])){
		$sql="UPDATE powergroup SET seq='".intval($_POST['seq'])."',name='".$_POST['name']."',discount=".$_POST['discount'].",state=1 WHERE pno='".$_POST['pno']."'";
	}
	else{
		$sql="UPDATE powergroup SET seq='".intval($_POST['seq'])."',name='".$_POST['name']."',discount=".$_POST['discount'].",state=0 WHERE pno='".$_POST['pno']."'";
	}
	sqlnoresponse($conn,$sql,$dbtype);
}
sqlclose($conn,$dbtype);
?>