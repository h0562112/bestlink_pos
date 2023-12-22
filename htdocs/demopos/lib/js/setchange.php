<?php
include_once '../../../tool/inilib.php';
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$content=parse_ini_file('../../../database/machinedata.ini',true);
$content['basic']['change']=$_POST['change'];
write_ini_file($content,'../../../database/machinedata.ini');
$conn=sqlconnect('../../../database/sale','SALES_'.substr($content['basic']['bizdate'],0,6).'.db','','','','sqlite');
$sql='INSERT INTO CST012 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,LINENUMBER,CLKCODE,CLKNAME,DTLMODE,DTLTYPE,DTLFUNC,ITEMCODE,AMT,ZCOUNTER,CREATEDATETIME) VALUES ("'.$_POST['machinetype'].'","'.$content['basic']['bizdate'].'"," ","change","'.$_POST['usercode'].'","'.$_POST['username'].'","9","9","99","change",'.$_POST['change'].',"'.$content['basic']['zcounter'].'","'.date('YmdHis').'")';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>