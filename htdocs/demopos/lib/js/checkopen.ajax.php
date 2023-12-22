<?php
include_once '../../../tool/myerrorlog.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
$map=parse_ini_file('../../../database/mapping.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($_POST['machinetype'])&&isset($map['map'][$_POST['machinetype']])){//帳務以每台分機為個別主體計算
	$content=parse_ini_file('../../../database/time'.$map['map'][$_POST['machinetype']].'.ini',true);
}
else{//帳務以主機為主體計算
	$content=parse_ini_file('../../../database/timem1.ini',true);
}
if($content['time']['isopen']=='0'){
	echo 'success';
}
else{
	echo 'error';
}
?>