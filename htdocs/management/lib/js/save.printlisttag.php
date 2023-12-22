<?php
include_once '../../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$printlisttag=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/printlisttag.ini',true);
foreach($_POST as $section=>$data){
	if($section!='company'&&$section!='dep'){
		parse_str(urldecode($data),$temp);
		//print_r($temp);
		foreach($temp as $name=>$value){
			$printlisttag[$section][$name]=$value;
		}
	}
	else{
	}
}
write_ini_file($printlisttag,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/printlisttag.ini');
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>