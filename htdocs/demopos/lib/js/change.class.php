<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/inilib.php';
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
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
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$content=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$content=parse_ini_file('../../../database/timem1.ini',true);
}
//$content=parse_ini_file('../../../database/time.ini',true);
if(isset($_POST['view'])){//手機POS用來檢查營業日與班別
	echo $content['time']['bizdate'].'-'.$content['time']['zcounter'];
}
else{//POS流程
	if(isset($_POST['type'])&&$content['time'][$_POST['type']]=='1'){
		$content['time'][$_POST['type']]='0';
	}
	else if(isset($_POST['type'])){
		$content['time'][$_POST['type']]='1';
	}
	else{
	}
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		write_ini_file($content,'../../../database/time'.$invmachine.'.ini');
	}
	else{//帳務以主機為主體計算
		write_ini_file($content,'../../../database/timem1.ini');
	}
	//write_ini_file($content,'../../../database/time.ini');
	echo $content['time']['bizdate'].'-'.$content['time']['zcounter'];
}
?>