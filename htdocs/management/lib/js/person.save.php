<?php
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'data.db','','','','sqlite');
if($_POST['type']=='new'){
	$sql="SELECT COUNT(*) AS num FROM person WHERE cardno='".strtoupper($_POST['cardno'])."' OR id='".strtolower($_POST['id'])."'";
	$data=sqlquery($conn,$sql,'sqlite');
	if(intval($data[0]['num'])>0){
		echo 'already';
	}
	else{
		$sql='PRAGMA table_info(person)';
		$temp=sqlquery($conn,$sql,'sqlite');
		if(!isset($temp[14])){
			$sql='ALTER TABLE person ADD voidpw TEXT';
			sqlnoresponse($conn,$sql,'sqlite');
		}
		else{
		}
		if(!isset($temp[15])){
			$sql='ALTER TABLE person ADD paperpw TEXT';
			sqlnoresponse($conn,$sql,'sqlite');
		}
		else{
		}
		if(!isset($temp[16])){
			$sql='ALTER TABLE person ADD punchpw TEXT';
			sqlnoresponse($conn,$sql,'sqlite');
		}
		else{
		}
		if(!isset($temp[17])){
			$sql='ALTER TABLE person ADD reprintpw TEXT';
			sqlnoresponse($conn,$sql,'sqlite');
		}
		else{
		}
		$sql="INSERT INTO person (cardno,id,pw,name,sex,birth,tel,address,power,viewdb,quota,firstdate,lastdate,voidpw,paperpw,punchpw,reprintpw) VALUES ('".strtoupper($_POST['cardno'])."','".strtolower($_POST['id'])."'";
		if(isset($_POST['pw'])&&strlen($_POST['pw'])>0){
			$sql=$sql.',"'.strtolower($_POST['pw']).'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		$sql=$sql.',"'.$_POST['name'].'"';
		if(isset($_POST['sex'])&&strlen($_POST['sex'])>0){
			$sql=$sql.','.$_POST['sex'];
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['birth'])&&strlen($_POST['birth'])>0){
			$sql=$sql.',"'.$_POST['birth'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
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
		$sql=$sql.',"'.$_POST['power'].'"';
		if(isset($_POST['viewdb'])&&strlen($_POST['viewdb'])>0){
			$sql=$sql.',"'.$_POST['viewdb'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['quota'])&&strlen($_POST['quota'])>0){
			$sql=$sql.','.$_POST['quota'];
		}
		else{
			$sql=$sql.',0';
		}
		if(isset($_POST['firstdate'])&&strlen($_POST['firstdate'])>0){
			$sql=$sql.',"'.$_POST['firstdate'].'"';
		}
		else{
			$sql=$sql.',"'.date('Y-m-d').'"';
		}
		if(isset($_POST['lastdate'])&&strlen($_POST['lastdate'])>0){
			$sql=$sql.','.$_POST['lastdate'];
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['voidpw'])&&strlen($_POST['voidpw'])>0){
			$sql=$sql.',"'.$_POST['voidpw'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['paperpw'])&&strlen($_POST['paperpw'])>0){
			$sql=$sql.',"'.$_POST['paperpw'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['punchpw'])&&strlen($_POST['punchpw'])>0){
			$sql=$sql.',"'.$_POST['punchpw'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		if(isset($_POST['reprintpw'])&&strlen($_POST['reprintpw'])>0){
			$sql=$sql.',"'.$_POST['reprintpw'].'"';
		}
		else{
			$sql=$sql.',NULL';
		}
		$sql=$sql.")";
		sqlnoresponse($conn,$sql,'sqlite');
		echo 'success';
	}
}
else{//if($_POST['type']=='edit')
	$sql='PRAGMA table_info(person)';
	$temp=sqlquery($conn,$sql,'sqlite');
	$temp=sqlquery($conn,$sql,'sqlite');
	if(!isset($temp[14])){
		$sql='ALTER TABLE person ADD voidpw TEXT';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}
	if(!isset($temp[15])){
		$sql='ALTER TABLE person ADD paperpw TEXT';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}
	if(!isset($temp[16])){
		$sql='ALTER TABLE person ADD punchpw TEXT';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}
	if(!isset($temp[17])){
		$sql='ALTER TABLE person ADD reprintpw TEXT';
		sqlnoresponse($conn,$sql,'sqlite');
	}
	else{
	}
	
	$sql="UPDATE person SET name='".$_POST['name']."',sex=";
	if(isset($_POST['sex'])&&strlen($_POST['sex'])>0){
		$sql=$sql.$_POST['sex'].",";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."id='".strtolower($_POST['id'])."',pw=";
	if(isset($_POST['pw'])&&strlen($_POST['pw'])>0){
		$sql=$sql."'".strtolower($_POST['pw'])."',";
	}
	else{
		$sql=$sql."NULL,";
	}
	$sql=$sql."birth=";
	if(isset($_POST['birth'])&&strlen($_POST['birth'])>0){
		$sql=$sql."'".$_POST['birth']."',";
	}
	else{
		$sql=$sql."NULL,";
	}
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
	$sql=$sql."power='".$_POST['power']."',firstdate='".$_POST['firstdate']."',lastdate=";
	if(isset($_POST['lastdate'])&&strlen($_POST['lastdate'])>0){
		$sql=$sql."'".$_POST['lastdate']."'";
	}
	else{
		$sql=$sql."NULL";
	}
	if(isset($_POST['stories'])&&sizeof($_POST['stories'])>=1){
		$list='';
		foreach($_POST['stories'] as $i){
			if(strlen($list)==0){
				$list=$i;
			}
			else{
				$list=$list.','.$i;
			}
		}
		$sql=$sql.",viewdb='only-".$list."'";
	}
	else{
		$sql=$sql.",viewdb=NULL";
	}
	if(isset($_POST['voidpw'])&&strlen($_POST['voidpw'])>0){
		$sql=$sql.",voidpw='".$_POST['voidpw']."'";
	}
	else{
		$sql=$sql.",voidpw=NULL";
	}
	if(isset($_POST['paperpw'])&&strlen($_POST['paperpw'])>0){
		$sql=$sql.",paperpw='".$_POST['paperpw']."'";
	}
	else{
		$sql=$sql.",paperpw=NULL";
	}
	if(isset($_POST['punchpw'])&&strlen($_POST['punchpw'])>0){
		$sql=$sql.",punchpw='".$_POST['punchpw']."'";
	}
	else{
		$sql=$sql.",punchpw=NULL";
	}
	if(isset($_POST['reprintpw'])&&strlen($_POST['reprintpw'])>0){
		$sql=$sql.",reprintpw='".$_POST['reprintpw']."'";
	}
	else{
		$sql=$sql.",reprintpw=NULL";
	}
	$sql=$sql." WHERE cardno='".$_POST['cardno']."'";
	//echo $sql;
	sqlnoresponse($conn,$sql,'sqlite');
	echo 'success';
}
sqlclose($conn,'sqlite');
?>