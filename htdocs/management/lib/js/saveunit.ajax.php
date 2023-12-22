<?php
include_once '../../../tool/inilib.php';
$unit=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/unit.ini',true);
if(isset($_POST['createnew'])){
	$unit['unit'][sizeof($unit['unit'])]=$_POST['createname'];
}
else{
	for($i=0;$i<sizeof($_POST['no']);$i++){
		$unit['unit'][$_POST['no'][$i]]=$_POST['newunit'][$i];
	}
}
write_ini_file($unit,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/unit.ini');
?>