<?php
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
if($_POST['type']=='new'){
	$sql="SELECT COUNT(*) AS num FROM personnel WHERE percard='".strtoupper($_POST['percard'])."' AND state=1";
	$data=sqlquery($conn,$sql,'sqlite');
	if(intval($data[0]['num'])>0){
		echo 'already';
	}
	else{	
		$sql="INSERT INTO personnel (perno,percard,name,tel,address,sosname,sostel,state,credatetime) SELECT CASE WHEN perno+1>COUNT(*)+1 THEN perno+1 ELSE COUNT(*)+1 END,'".$_POST['percard']."','".$_POST['name']."'";
		if(isset($_POST['tel'])&&strlen($_POST['tel'])>0){
			$sql=$sql.',"'.$_POST['tel'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['address'])&&strlen($_POST['address'])>0){
			$sql=$sql.',"'.$_POST['address'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['sosname'])&&strlen($_POST['sosname'])>0){
			$sql=$sql.',"'.$_POST['sosname'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['sostel'])&&strlen($_POST['sostel'])>0){
			$sql=$sql.',"'.$_POST['sostel'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		$sql=$sql.',1,"'.date('YmdHis').'" FROM personnel ORDER BY CAST(perno AS INT) DESC LIMIT 1';
		echo $sql;
		sqlnoresponse($conn,$sql,'sqlite');
		echo 'success';
	}
}
else{//if($_POST['type']=='edit')
	$sql="UPDATE personnel SET percard='".$_POST['percard']."',name='".$_POST['name']."',";
	$sql=$sql."tel=";
	if(isset($_POST['tel'])&&strlen($_POST['tel'])>0){
		$sql=$sql."'".$_POST['tel']."',";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."address=";
	if(isset($_POST['address'])&&strlen($_POST['address'])>0){
		$sql=$sql."'".$_POST['address']."',";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."sosname=";
	if(isset($_POST['sosname'])&&strlen($_POST['sosname'])>0){
		$sql=$sql."'".$_POST['sosname']."',";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."sostel=";
	if(isset($_POST['sostel'])&&strlen($_POST['sostel'])>0){
		$sql=$sql."'".$_POST['sostel']."',";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."editdatetime='".date('YmdHis')."' WHERE perno='".$_POST['perno']."'";
	sqlnoresponse($conn,$sql,'sqlite');
	echo 'success';
}
sqlclose($conn,'sqlite');
?>