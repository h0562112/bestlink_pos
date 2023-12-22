<?php
date_default_timezone_set('Asia/Taipei');
include_once '../../../tool/inilib.php';
$otherpay=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini',true);
$itemno=preg_split('/,/',$_POST['itemno']);
$temppay=array();
for($i=0;$i<sizeof($itemno);$i++){
	unset($otherpay[$itemno[$i]]);
}
foreach($otherpay as $index=>$value){
	if($index=='pay'){
		$temppay[$index]=$value;
	}
	else{
		$temppay['item'.sizeof($temppay)]=$value;
	}
}
write_ini_file($temppay,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/otherpay.ini');
$ver=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini',true);
$ver['ver']['update']=date('YmdHis');
write_ini_file($ver,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/ver.ini');
?>