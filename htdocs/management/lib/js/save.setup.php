<?php
include_once '../../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$setup=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/setup.ini',true);
if($_POST['company']!='tableplusinv'&&$_POST['zdninv']['id']=='60353288'){//避免公司發票被誤用
}
else{
	foreach($_POST as $section=>$data){
		if($section!='company'&&$section!='dep'){
			parse_str(urldecode($data),$temp);
			//print_r($temp);
			foreach($temp as $name=>$value){
				$setup[$section][$name]=$value;
			}
		}
		else{
		}
	}
	write_ini_file($setup,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/setup.ini');
	$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
	$ver['ver']['update']=date('YmdHis');
	write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
}
?>