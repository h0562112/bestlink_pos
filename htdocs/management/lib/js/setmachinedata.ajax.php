<?php
include_once '../../../tool/inilib.php';
$machinedata=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/machinedata.ini',true);
$machinedata['inv']['total']=$_POST['total'];
write_ini_file($machinedata,'../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/machinedata.ini');
echo 'success';
?>