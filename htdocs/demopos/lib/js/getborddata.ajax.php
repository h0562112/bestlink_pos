<?php
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
echo json_encode(array($machinedata['bord']['company'],$machinedata['bord']['dep'],$_POST['num'],$machinedata['bord']['ip']));
?>