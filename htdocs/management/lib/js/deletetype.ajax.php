<?php
include '../../../tool/inilib.php';
$temp=preg_split('/,/',$_POST['numbergroup']);
$front=parse_ini_file('../../../menudata/'.$temp[0].'/'.$temp[1].'/'.$temp[0].'-front.ini',true);
for($i=2;$i<sizeof($temp);$i++){
	$front[$temp[$i]]['state']='0';
}
write_ini_file($front,'../../../menudata/'.$temp[0].'/'.$temp[1].'/'.$temp[0].'-front.ini');
date_default_timezone_set('Asia/Taipei');
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>