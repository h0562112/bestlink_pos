<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
$poarr='';
if(isset($_POST['rear'])&&sizeof($_POST['rear'])>0){
	for($i=0;$i<sizeof($_POST['rear']);$i++){
		if(strlen($poarr)==0){
			$poarr=$_POST['rear'][$i];
		}
		else{
			$poarr=$poarr.','.$_POST['rear'][$i];
		}
	}
}
else{
}
if(isset($_POST['front'])&&sizeof($_POST['front'])>0){
	for($i=0;$i<sizeof($_POST['front']);$i++){
		if(strlen($poarr)==0){
			$poarr=$_POST['front'][$i];
		}
		else{
			$poarr=$poarr.','.$_POST['front'][$i];
		}
	}
}
else{
}

if(strlen($_POST['pno'])==0){
	$sql="SELECT COUNT(*) AS num FROM powergroup";
	$num=sqlquery($conn,$sql,'sqlite');
	if($num[0]['num']==null){
		if(!isset($_POST['stop'])){
			$sql="INSERT INTO powergroup (seq,pno,name,state) VALUES ('".intval($_POST['seq'])."','0','".$_POST['name']."',1)";
		}
		else{
			$sql="INSERT INTO powergroup (seq,pno,name,state) VALUES ('".intval($_POST['seq'])."','0','".$_POST['name']."',0)";
		}
		sqlnoresponse($conn,$sql,'sqlite');
		$sql="INSERT INTO powerlist (no,type,subtype,name,`group`,state) SELECT no,type,subtype,name,0,state FROM powerlist WHERE no IN (".$poarr.") AND `group`=1";
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
		if(!isset($_POST['stop'])){
			$sql="INSERT INTO powergroup (seq,pno,name,state) VALUES ('".intval($_POST['seq'])."','".$num[0]['num']."','".$_POST['name']."',1)";
		}
		else{
			$sql="INSERT INTO powergroup (seq,pno,name,state) VALUES ('".intval($_POST['seq'])."','".$num[0]['num']."','".$_POST['name']."',0)";
		}
		sqlnoresponse($conn,$sql,'sqlite');
		$sql="INSERT INTO powerlist (no,type,subtype,name,`group`,state) SELECT no,type,subtype,name,".$num[0]['num'].",state FROM powerlist WHERE no IN (".$poarr.") AND `group`=1";
		sqlnoresponse($conn,$sql,'sqlite');
	}
}
else{
	if(!isset($_POST['stop'])){
		$sql="UPDATE powergroup SET seq='".intval($_POST['seq'])."',name='".$_POST['name']."',state=1 WHERE pno=".$_POST['pno'];
	}
	else{
		$sql="UPDATE powergroup SET seq='".intval($_POST['seq'])."',name='".$_POST['name']."',state=0 WHERE pno=".$_POST['pno'];
	}
	sqlnoresponse($conn,$sql,'sqlite');
	$sql="DELETE FROM powerlist WHERE `group`=".$_POST['pno'];
	sqlnoresponse($conn,$sql,'sqlite');
	$sql="INSERT INTO powerlist (no,type,subtype,name,`group`,state) SELECT no,type,subtype,name,".$_POST['pno'].",state FROM powerlist WHERE no IN (".$poarr.") AND `group`=1";
	sqlnoresponse($conn,$sql,'sqlite');
}
sqlclose($conn,'sqlite');
?>