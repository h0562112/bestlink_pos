<?php
include_once '../../tool/inilib.php';
$content=parse_ini_file('../../database/time.ini',true);
$machinedata=parse_ini_file('../../database/machinedata.ini',true);
if($content['time'][$_POST['type']]=='1'){
	$content['time'][$_POST['type']]='0';
}
else{
	$content['time'][$_POST['type']]='1';
}
write_ini_file($content,'../../database/time.ini');
echo $machinedata['basic']['zcounter'];
?>