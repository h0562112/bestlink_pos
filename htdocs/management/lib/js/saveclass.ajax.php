<?php
include_once '../../../tool/inilib.php';
$type=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/type.ini',true);
if(isset($_POST['createnew'])){
	$type['type'][sizeof($type['type'])]=$_POST['createname'];
}
else{
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$type['type'][$_POST['no'][$i]]=$_POST['newclass'][$i];
	}
}
write_ini_file($type,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/type.ini');
?>