<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$content=parse_ini_file('../../../database/setup.ini',true);
$Y=date('Y');
$year=(intval($Y)-1911);
$month=date('m');
if(intval($month)%2==0){
	$m=$month;
}
else{
	$m=intval($month)+1;
}
if(strlen($m)<2){
	$m='0'.$m;
}
if(file_exists('../../../database/mapping.ini')&&isset($init['init']['accounting'])&&$init['init']['accounting']=='2'){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinename']])){
		$invmachine=$dbmapping['map'][$_POST['machinename']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='m1';
}
if(file_exists("../../../database/sale/".$Y.$m."/invdata_".$Y.$m."_".$invmachine.".db")){
}
else{
	if(file_exists("../../../database/sale/EMinvdata.DB")){
	}
	else{
		include_once './create.emptyDB.php';
		create('EMinvdata');
	}
	copy("../../../database/sale/EMinvdata.db","../../../database/sale/".$Y.$m."/invdata_".$Y.$m."_".$invmachine.".db");
}
$conn=sqlconnect("../../../database/sale/".$Y.$m,"invdata_".$Y.$m."_".$invmachine.".db","","","","sqlite");
$sql='SELECT COUNT(*) AS num FROM number WHERE state=1 AND company="'.$content['basic']['company'].'" AND story="'.$content['basic']['story'].'" AND datetime="'.$year.$m.'"';
$checkadd=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

if(file_exists('../../../trnx/Number/'.$invmachine.'/'.$year.$m.'/07')){
	$dirfile=array_diff(scandir('../../../trnx/Number/'.$invmachine.'/'.$year.$m.'/07'), array('..', '.'));
}
else{
	$dirfile=array();
}
if(!isset($checkadd[0]['num'])&&sizeof($dirfile)<intval($content['basic']['safe'])){
	if(isset($content['basic']['sendinvlocation'])&&$content['basic']['sendinvlocation']=='3'){//�����o��
		$res['zdn']='1';
	}
	else{
	}
}
else if(intval($checkadd[0]['num'])<intval($content['basic']['safe'])&&sizeof($dirfile)<intval($content['basic']['safe'])){
	if(isset($content['basic']['sendinvlocation'])&&$content['basic']['sendinvlocation']=='3'){//�����o��
		$res['zdn']='1';
	}
	else{
	}
	/*$f=fopen("../../../print/zdninv/add.txt","w");
	fclose($f);
	$f=fopen("../../../print/noread/add.txt","w");
	fclose($f);*/
}
else{
}
if(sizeof($checkadd)>0&&intval($checkadd[0]['num'])>0){
	$res['remaining']=intval($checkadd[0]['num']);
}
else{
	$res['remaining']='0';
}
echo json_encode($res);
?>