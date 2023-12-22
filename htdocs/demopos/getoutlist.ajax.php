<?php
//include_once '../tool/dbTool.inc.php';
if(file_exists('../database/mapping.ini')){
	$dbmapping=parse_ini_file('../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$init=parse_ini_file('../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../database/machinedata.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$dir='./table/outside';
$filelist=scandir($dir);
//$result='';
foreach($filelist as $fl){
	$tempfl=preg_split('/;/',$fl);
	if($tempfl[0]==$timeini['time']['bizdate']&&$tempfl[1]==$timeini['time']['zcounter']){
		$result[substr($tempfl[2],0,-4)]=$tempfl[0];
	}
	else{
	}
}
$result['time']=date('H:i:s');
echo json_encode($result);
?>