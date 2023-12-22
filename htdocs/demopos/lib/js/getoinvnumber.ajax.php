<?php
include_once '../../../tool/dbTool.inc.php';
//date_default_timezone_set('Asia/Taipei');
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
echo $machinedata['basic']['startoinv'];
?>