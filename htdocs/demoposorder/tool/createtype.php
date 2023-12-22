<?php
include '../../tool/dbTool.inc.php';
include '../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$newtime=date('YmdHis');
$filename='../data/'.$_POST['company'].'-type.ini';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
if($_POST['type']=='front'){
	$sql='INSERT INTO frtypemap (company,fronttype,createtime) SELECT "'.$_POST['company'].'",COUNT(fronttype),"'.$newtime.'" FROM frtypemap WHERE company="'.$_POST['company'].'"';
	$sql2='SELECT fronttype AS number FROM frtypemap WHERE company="'.$_POST['company'].'" AND createtime="'.$newtime.'"';
	
}
else{//if($_POST['type']='rear')
	$sql='INSERT INTO retypemap (company,reartype,createtime) SELECT "'.$_POST['company'].'",COUNT(reartype),"'.$newtime.'" FROM retypemap WHERE company="'.$_POST['company'].'"';
	$sql2='SELECT reartype AS number FROM retypemap WHERE company="'.$_POST['company'].'" AND createtime="'.$newtime.'"';
}
sqlnoresponse($conn,$sql,'mysql');
$type=sqlquery($conn,$sql2,'mysql');
sqlclose($conn,'mysql');
if($_POST['type']=='front'){
	if(file_exists($filename)){
		$content=parse_ini_file($filename,true);
		$content['front'.$type[0]['number']]['name']=$_POST['name'];
		write_ini_file($content,$filename);
	}
	else{
		$content=array();
		$content['front'.$type[0]['number']]['name']=$_POST['name'];
		write_ini_file($content,$filename);
	}
}
else{//if($_POST['type']='rear')
	if(file_exists($filename)){
		$content=parse_ini_file($filename,true);
		$content['rear'.$type[0]['number']]['name']=$_POST['name'];
		write_ini_file($content,$filename);
	}
	else{
		$content=array();
		$content['rear'.$type[0]['number']]['name']=$_POST['name'];
		write_ini_file($content,$filename);
	}
}
echo "<form method='post' action='../management.php' id='form'>
		<input type='hidden' name='conttype' value='type'>
	</form>
	<script>
		document.getElementById('form').submit();
	</script>";
?>