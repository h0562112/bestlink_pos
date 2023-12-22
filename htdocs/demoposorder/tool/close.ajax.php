<?php
include_once '../../tool/inilib.php';
include_once '../../tool/dbTool.inc.php';
$content=parse_ini_file('../../database/time.ini',true);
$machinedata=parse_ini_file('../../database/machinedata.ini',true);
date_default_timezone_set('Asia/Taipei');
$y=date('Y');
$m=date('m');
if(strlen($m)<2){
	$m='0'.$m;
}
$d=date('d');
if(strlen($d)<2){
	$d='0'.$d;
}
if(isset($_POST['usercode'])){
	$usercode=$_POST['usercode'];
	$username=$_POST['username'];
}
else{
	$usercode=' ';
	$username=' ';
}
if($content['time']['isclose']=='1'){
	$content['time']['closedate']=$y.'/'.$m.'/'.$d;
	$content['time']['closetime']=date('H:i:s');
	$content['time']['isclose']='0';
	$machinedata['basic']['saleno']='0';
	$conn=sqlconnect('../../database/sale','SALES_'.$y.$m.'.db','','','','sqlite');
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,CREATEDATETIME) VALUES ("'.$machinedata['basic']['terminalnumber'].'","'.$machinedata['basic']['bizdate'].'"," ","close","'.$usercode.'","'.$username.'","9","9","99","'.date('YmdHis').'")';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	write_ini_file($content,'../../database/time.ini');
	write_ini_file($machinedata,'../../database/machinedata.ini');
	echo 'success';
}
else{
	echo 'error';
}
?>