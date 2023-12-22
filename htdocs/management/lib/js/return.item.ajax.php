<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
if(!file_exists('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/timeout.ini')){
	date_default_timezone_set('Asia/Taipei');
}
else{
	$timeout=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/timeout.ini',true);
	if(!isset($timeout['time']['name'])){
		date_default_timezone_set('Asia/Taipei');
	}
	else{
		date_default_timezone_set($timeout['time']['name']);
	}
}
$menu=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'],'menu.db','','','','sqlite');
for($i=0;$i<sizeof($_POST['num']);$i++){
	$sql='UPDATE itemsdata SET state="1" WHERE inumber="'.$_POST['num'][$i].'"';
	sqlnoresponse($conn,$sql,'sqlite');
	if(isset($menu[$_POST['num'][$i]]['state'])){
		$menu[$_POST['num'][$i]]['state']="1";
	}
	else{
	}
}
sqlclose($conn,'sqlite');
write_ini_file($menu, '../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini');

$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>