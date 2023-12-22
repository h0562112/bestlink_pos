<?php
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
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

$list='';
for($i=0;$i<sizeof($_POST['no']);$i++){
	if(strlen($list)==0){
		$list='"'.$_POST['no'][$i].'"';
	}
	else{
		$list=$list.',"'.$_POST['no'][$i].'"';
	}
}
if(sizeof($_POST['no'])==1){
	$sql="UPDATE ".$tablename." SET state=0,lastdate='".date("Y-m-d")."' WHERE memno=".$list;
}
else{
	$sql="UPDATE ".$tablename." SET state=0,lastdate='".date("Y-m-d")."' WHERE memno IN (".$list.")";
}
sqlnoresponse($conn,$sql,$dbtype);
sqlclose($conn,$dbtype);
?>