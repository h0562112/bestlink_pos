<?php
include_once '../../../tool/inilib.php';
$straw=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/straw.ini',true);
for($i=0;$i<sizeof($_POST['no']);$i++){
	$straw['straw'][$_POST['no'][$i]]=$_POST['newstraw'][$i];
}
write_ini_file($straw,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/straw.ini');
?>