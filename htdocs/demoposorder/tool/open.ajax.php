<?php
include_once '../../tool/inilib.php';
include_once '../../tool/dbTool.inc.php';
$init=parse_ini_file('../../database/initsetting.ini',true);
$content=parse_ini_file('../../database/time.ini',true);
$machinedata=parse_ini_file('../../database/machinedata.ini',true);
date_default_timezone_set($init['init']['settime']);
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
if($content['time']['isopen']=='1'){
	$content['time']['opendate']=$y.'/'.$m.'/'.$d;
	$content['time']['opentime']=date('H:i:s');
	$content['time']['isopen']='0';
	if($machinedata['basic']['bizdate']!=$y.$m.$d){
		$machinedata['basic']['bizdate']=$y.$m.$d;
		$machinedata['basic']['zcounter']='1';
		if(substr($machinedata['basic']['bizdate'],0,6)!=$y.$m){
			$machinedata['basic']['consecnumber']='0';
		}
		else{
		}
	}
	else{
		$machinedata['basic']['zcounter']=intval($machinedata['basic']['zcounter'])+1;
	}
	if(file_exists('../../database/sale/SALES_'.$y.$m.'.db')){
	}
	else{
		copy("../../database/sale/empty.DB","../../database/sale/SALES_".$y.$m.".DB");
	}
	$conn=sqlconnect('../../database/sale','SALES_'.$y.$m.'.db','','','','sqlite');
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,CREATEDATETIME) VALUES ("'.$machinedata['basic']['terminalnumber'].'","'.$machinedata['basic']['bizdate'].'"," ","open","'.$usercode.'","'.$username.'","9","9","99","'.date('YmdHis').'")';
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