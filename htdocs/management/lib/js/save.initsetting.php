<?php
include_once '../../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$initsetting=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini',true);
foreach($_POST as $section=>$data){
	if($section!='company'&&$section!='dep'){
		parse_str(urldecode($data),$temp);
		//print_r($temp);
		foreach($temp as $name=>$value){
			$initsetting[$section][$name]=$value;
		}
	}
	else{
	}
}
write_ini_file($initsetting,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/initsetting.ini');
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>