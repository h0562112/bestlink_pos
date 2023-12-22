<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/inilib.php';
include_once '../../../tool/dbTool.inc.php';
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
//date_default_timezone_set('Asia/Taipei');
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
if($content['time']['isclose']=='1'){
	$content['time']['closedate']=$y.'/'.$m.'/'.$d;
	$content['time']['closetime']=date('H:i:s');
	$content['time']['isclose']='0';
	/*if(isset($machinedata['basic']['strsaleno'])){
		$machinedata['basic']['saleno']=$machinedata['basic']['strsaleno'];
	}
	else{
		$machinedata['basic']['saleno']='0';
	}*/
	$conn=sqlconnect('../../../database/sale','SALES_'.substr($content['time']['bizdate'],0,6).'.db','','','','sqlite');
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machinetype'].'","'.$content['time']['bizdate'].'"," ","close","'.$usercode.'","'.$username.'","9","9","99","'.$content['time']['zcounter'].'","'.date('YmdHis').'")';
	sqlnoresponse($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
		write_ini_file($content,'../../../database/time'.$invmachine.'.ini');
	}
	else{//帳務以主機為主體計算
		write_ini_file($content,'../../../database/timem1.ini');
	}
	//write_ini_file($content,'../../../database/time.ini');
	echo 'success';
	if(isset($init['init']['controltable'])&&$init['init']['controltable']=='1'){
		$dir='../../table';
		$filelist=scandir($dir);
		foreach($filelist as $fl){
			if($fl=='.'||$fl=='..'||$fl=='outside'){
				continue;
			}
			else{
				$temp=preg_split('/;/',$fl);
				if($temp[0]==$content['time']['bizdate']){
				}
				else{
					try {
						unlink('../../table/'.$fl);
					}
					catch(Exception $e){
						$header=fopen('../../../printlog.txt','a');
						fwrite($header,date('Y/m/d H:i:s').' -- file is exists(../../table/'.$fl.'), but error'.PHP_EOL.'  '.$e.PHP_EOL);
						fclose($header);
					}
				}
			}
		}
	}
	else{
	}
}
else{
	echo 'error';
}
?>