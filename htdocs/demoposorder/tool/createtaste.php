<?php
include '../../tool/dbTool.inc.php';
include '../../tool/inilib.php';
echo "<script src='http://code.jquery.com/jquery-1.12.2.js'></script>";
date_default_timezone_set('Asia/Taipei');
$newtime=date('YmdHis');
if($_POST['money']==0){
	$money=0;
}
else{
	$money=$_POST['money'];
}
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
$sql='INSERT INTO tastemap (company,depnumber,type,taste,createtime) SELECT "'.$_POST['company'].'"," ",'.$_POST['tastetype'].',COUNT(taste),"'.$newtime.'" FROM tastemap WHERE company="'.$_POST['company'].'"';
$sql2='SELECT taste AS number FROM tastemap WHERE company="'.$_POST['company'].'" AND createtime="'.$newtime.'"';
sqlnoresponse($conn,$sql,'mysql');
$type=sqlquery($conn,$sql2,'mysql');
sqlclose($conn,'mysql');
//echo json_encode($type);
if(file_exists('../data/'.$_POST['company'].'-taste.ini')){
	$content=parse_ini_file('../data/'.$_POST['company'].'-taste.ini',true);
	$content[$type[0]['number']]['type']=$_POST['tastetype'];
	$content[$type[0]['number']]['name']=$_POST['name'];
	$content[$type[0]['number']]['money']=$money;
	write_ini_file($content,'../data/'.$_POST['company'].'-taste.ini');
}
else{
	$content=array();
	$content[$type[0]['number']]['type']=$_POST['tastetype'];
	$content[$type[0]['number']]['name']=$_POST['name'];
	$content[$type[0]['number']]['money']=$money;
	write_ini_file($content,'../data/'.$_POST['company'].'-taste.ini');
}
echo "<form method='post' action='../management.php' id='form'>
		<input type='hidden' name='conttype' value='taste'>
	</form>
	<script>
		$('#form').submit();
	</script>";
?>