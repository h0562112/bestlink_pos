<?php
//2022/5/30 ������g�bmain.js���A���O�Q���ѡA�ѰO����γ~�A���O�d(�̦����}�@�����u���F�B�znidin�q��A�S����POS�걵�A�i��Φb�o��)
include_once '../../../../tool/inilib.php';

$machinedata=parse_ini_file('../data/machinedata.ini',true);
date_default_timezone_set($machinedata['basic']['settime']);

if(date('Ymd')!==$machinedata['basic']['bizdate']){
	//$machinedata['basic']['bizdate']=date('Ymd');
	//$machinedata['basic']['consecnumber']="0";
	//$machinedata['basic']['saleno']=$machinedata['basic']['strsaleno'];
}
else{
}

$machinedata['basic']['consecnumber']=intval($machinedata['basic']['consecnumber'])+1;
$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;

write_ini_file($machinedata,'../../../../database/machinedata.ini');

echo json_encode($machinedata['basic']);
?>