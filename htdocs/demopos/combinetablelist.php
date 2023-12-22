<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
include_once '../tool/inilib.php';
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
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

if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
	$timeini=parse_ini_file('../database/time'.$invmachine.'.ini',true);
}
else{
	$timeini=parse_ini_file('../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../database/machinedata.ini',true);
if(file_exists('../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db')){
}
else{
	copy("../database/sale/empty.DB","../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".db");
}
$conn=sqlconnect('../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');

if(isset($_POST['consecnumber'])&&$_POST['consecnumber']!=''){
	$sql='UPDATE tempCST011 SET TABLENUMBER="'.$_POST['tabnum'].'" WHERE CONSECNUMBER="'.str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT).'"';
	sqlnoresponse($conn,$sql,'sqlite');
	$consecnumber=$_POST['consecnumber'];
	$tablestring=preg_split('/,/',$_POST['tabnum']);
	foreach($tablestring as $ts){
		if(file_exists('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$ts).'.ini')){
			$tabdata=parse_ini_file('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$ts).'.ini',true);
			$tabdata[$ts]['table']=$_POST['tabnum'];
			$tabdata[$ts]['tablestate']="1";
			$tabdata[$ts]['state']="999";
			$tabdata[$ts]['machine']=$_POST['machinetype'];
			write_ini_file($tabdata,'./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$ts).'.ini');
		}
		else{
			$ftable=fopen('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$ts).'.ini','a');
			fwrite($ftable,'['.$ts.']'.PHP_EOL);
			foreach($tabdata as $td){
				foreach($td as $tdindex=>$tdvalue){
					fwrite($ftable,$tdindex.'="'.$tdvalue.'"'.PHP_EOL);
				}
			}
			fclose($ftable);
		}
	}
}
else{
	$sql='SELECT (SELECT CONSECNUMBER FROM CST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS one,(SELECT CONSECNUMBER FROM tempCST011 ORDER BY CAST(CONSECNUMBER AS FLOAT) DESC LIMIT 1) AS two';
	$s=sqlquery($conn,$sql,'sqlite');
	//2021/10/18 查詢網路訂單的編號
	$sql='SELECT (SELECT SUBSTR(CONSECNUMBER,2) FROM CST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS one,(SELECT SUBSTR(CONSECNUMBER,2) FROM tempCST011 WHERE SUBSTR(CONSECNUMBER,1,1)=="w" ORDER BY CAST(SUBSTR(CONSECNUMBER,2) AS FLOAT) DESC LIMIT 1) AS two';
	$w=sqlquery($conn,$sql,'sqlite');
	if($s[0]['one']==null){
		$s[0]['one']=$w[0]['one'];
	}
	else{
		if(floatval($s[0]['one'])<floatval($w[0]['one'])){
			$s[0]['one']=$w[0]['one'];
		}
		else{
		}
	}
	if($s[0]['two']==null){
		$s[0]['two']=$w[0]['two'];
	}
	else{
		if(floatval($s[0]['two'])<floatval($w[0]['two'])){
			$s[0]['two']=$w[0]['two'];
		}
		else{
		}
	}

	if($s[0]['one']==null&&$s[0]['two']==null){
		$machinedata['basic']['consecnumber']='1';
	}
	else if($s[0]['one']==null){
		$machinedata['basic']['consecnumber']=intval($s[0]['two'])+1;
	}
	else if($s[0]['two']==null){
		$machinedata['basic']['consecnumber']=intval($s[0]['one'])+1;
	}
	else{
		if(intval($s[0]['one'])>intval($s[0]['two'])){
			$machinedata['basic']['consecnumber']=intval($s[0]['one'])+1;
		}
		else{
			$machinedata['basic']['consecnumber']=intval($s[0]['two'])+1;
		}
	}
	$machinedata['basic']['saleno']=intval($machinedata['basic']['saleno'])+1;
	$consecnumber=str_pad($machinedata['basic']['consecnumber'],6,"0",STR_PAD_LEFT);
	$saleno=$machinedata['basic']['saleno'];
	if(intval($machinedata['basic']['saleno'])>intval($machinedata['basic']['maxsaleno'])){
		$machinedata['basic']['saleno']=1;
	}
	else{
	}
	write_ini_file($machinedata,'../database/machinedata.ini');
	$sql='INSERT INTO tempCST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,SALESTTLQTY,SALESTTLAMT,INVOICENUMBER,CLKCODE,CLKNAME,TABLENUMBER,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES (';
	if($_POST['machinetype']==''){
		$sql=$sql.'"m1",';
	}
	else{
		$sql=$sql.'"'.$_POST['machinetype'].'",';
	}
	$createdatetime=date('YmdHis');
	$sql=$sql.'"'.$timeini['time']['bizdate'].'","'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'",0,0,"","'.$_POST['usercode'].'","'.$_POST['username'].'","'.$_POST['tabnum'].'","'.$timeini['time']['zcounter'].'","1","'.$createdatetime.'")';
	sqlnoresponse($conn,$sql,'sqlite');
	$salenomap='INSERT INTO salemap VALUES ("'.$timeini['time']['bizdate'].'","'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'","'.$saleno.'")';
	sqlnoresponse($conn,$salenomap,'sqlite');
	$consecnumber=$machinedata['basic']['consecnumber'];
	$tablestring=preg_split('/,/',$_POST['tabnum']);
	foreach($tablestring as $ts){
		$ftable=fopen('./table/'.$timeini['time']['bizdate'].';'.$timeini['time']['zcounter'].';'.iconv('utf-8','big5',$ts).'.ini','a');
		fwrite($ftable,'['.$ts.']'.PHP_EOL);
		fwrite($ftable,'bizdate="'.$timeini['time']['bizdate'].'"'.PHP_EOL);
		fwrite($ftable,'zcounter="'.$timeini['time']['zcounter'].'"'.PHP_EOL);
		fwrite($ftable,'consecnumber="'.str_pad($machinedata['basic']['consecnumber'],6,'0',STR_PAD_LEFT).'"'.PHP_EOL);
		fwrite($ftable,'saleamt="0"'.PHP_EOL);
		fwrite($ftable,'person="0"'.PHP_EOL);
		fwrite($ftable,'createdatetime="'.$createdatetime.'"'.PHP_EOL);
		fwrite($ftable,'table="'.$_POST['tabnum'].'"'.PHP_EOL);
		fwrite($ftable,'tablestate="1"'.PHP_EOL);
		fwrite($ftable,'state="999"'.PHP_EOL);
		fwrite($ftable,'machine="'.$_POST['machinetype'].'"'.PHP_EOL);
		fclose($ftable);
	}
}
sqlclose($conn,'sqlite');
echo $timeini['time']['bizdate'].','.str_pad($consecnumber,6,'0',STR_PAD_LEFT);
?>