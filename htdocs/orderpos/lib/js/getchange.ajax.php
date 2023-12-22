<?php
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$res['money']=$machinedata['basic']['change'];
echo json_encode($res);
?>