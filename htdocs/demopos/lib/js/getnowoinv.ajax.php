<?php
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
if(isset($machinedata['basic']['startoinv'])&&$machinedata['basic']['startoinv']!='0'&&strlen($machinedata['basic']['startoinv'])==10){
	echo $machinedata['basic']['startoinv'];
}
else{
	echo 'ERROR';
}
?>