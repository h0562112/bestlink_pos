<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
$init=parse_ini_file('../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$header=fopen('./outmoney.txt','a');
fwrite($header,date('Y/m/d H:i:s').' -- '.$_POST['usercode'].'-'.$_POST['username'].':'.$_POST['subtype'].','.$_POST['moneytype'].','.$_POST['moneytypename'].','.$_POST['radius'].',-'.$_POST['money'].','.$_POST['zcounter'].','.$_POST['remarks'].PHP_EOL);
fclose($header);
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
	$invmachine='m1';
}
$bizdate=preg_replace('/-/','',$_POST['bizdate']);
$filename='SALES_'.substr($bizdate,0,6);
if(file_exists("../database/sale/".$filename.".DB")){
}
else{
	copy("../database/sale/empty.DB","../database/sale/".$filename.".DB");
}
$conn=sqlconnect('../database/sale',$filename.'.db','','','','sqlite');
if($_POST['type']=='1'){//支出
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ("'.$invmachine.'","'.$bizdate.'","","001","'.$_POST['usercode'].'","'.$_POST['username'].'","4","1","01","'.$_POST['subtype'].'","'.$_POST['moneytype'].'","'.$_POST['moneytypename'].'","'.$_POST['radius'].'",-'.$_POST['money'].',"'.$_POST['zcounter'].'","'.$_POST['remarks'].'","'.date('YmdHis').'")';
}
else{//代收
	$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMNAME,ITEMDEPTCODE,ITEMDEPTNAME,SELECTIVEITEM1,AMT,ZCOUNTER,REMARKS,CREATEDATETIME) VALUES ("'.$invmachine.'","'.$bizdate.'","","001","'.$_POST['usercode'].'","'.$_POST['username'].'","3","1","01","'.$_POST['subtype'].'","'.$_POST['moneytype'].'","'.$_POST['moneytypename'].'","'.$_POST['radius'].'",'.$_POST['money'].',"'.$_POST['zcounter'].'","'.$_POST['remarks'].'","'.date('YmdHis').'")';
}
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>