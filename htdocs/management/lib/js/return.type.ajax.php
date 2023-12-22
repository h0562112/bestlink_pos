<?php
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
$type=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-type.ini',true);
for($i=0;$i<sizeof($_POST['num']);$i++){
	if(isset($type[$_POST['num'][$i]]['state'])){
		$type[$_POST['num'][$i]]['state']="1";
	}
	else{
	}
}
write_ini_file($type, '../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-type.ini');

$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>