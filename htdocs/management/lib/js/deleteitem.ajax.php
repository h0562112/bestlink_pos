<?php
include '../../../tool/inilib.php';
include_once '../../../tool/dbTool.inc.php';
$temp=preg_split('/,/',$_POST['numbergroup']);
$item=parse_ini_file('../../../menudata/'.$temp[0].'/'.$temp[1].'/'.$temp[0].'-menu.ini',true);
$inumber='';
for($i=2;$i<sizeof($temp);$i++){
	$item[$temp[$i]]['state']='0';
	if($i==2){
		$inumber=$temp[$i];
	}
	else{
		$inumber.='","'.$temp[$i];
	}
}
$conn=sqlconnect('../../../menudata/'.$temp[0].'/'.$temp[1],'menu.db','','','','sqlite');
$sql='UPDATE itemsdata SET state="0" WHERE inumber IN ("'.$inumber.'")';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
write_ini_file($item,'../../../menudata/'.$temp[0].'/'.$temp[1].'/'.$temp[0].'-menu.ini');
date_default_timezone_set('Asia/Taipei');
$ver=parse_ini_file('../../../menudata/'.$temp[0].'/'.$temp[1].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$temp[0].'/'.$temp[1].'/ver.ini');
?>