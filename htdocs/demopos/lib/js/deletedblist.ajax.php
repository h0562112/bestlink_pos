<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$filename='SALES_'.date('Ym');
if(file_exists("../../../database/sale/".$filename.".DB")){
}
else{
	copy("../../../database/sale/empty.DB","../../../database/sale/".$filename.".DB");
}
$sqliteconn=sqlconnect("../../../database/sale",$filename.".DB","","","","sqlite");
$sql="SELECT BIZDATE FROM CST011 WHERE TERMINALNUMBER='".$machinedata['basic']['terminalnumber']."' AND BIZDATE='".$machinedata['basic']['bizdate']."' AND CONSECNUMBER='".str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '%".$_POST['time']."'";
$result=sqlquery($conn,$sql,'sqlite');
if(isset($result['BIZDATE'])){
	$sql="INSERT INTO CST011 (TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLAMT,CUSTGPCODE,CUSTGPNAME,CREATEDATETIME) VALUES (SELECT TERMINALNUMBER,BIZDATE,CONSECNUMBER,INVOICENUMBER,CLKCODE,CLKNAME,SALESTTLAMT,'99','刪單',CREATEDATETIME FRON CST011 WHERE WHERE TERMINALNUMBER='".$machinedata['basic']['terminalnumber']."' AND BIZDATE='".$machinedata['basic']['bizdate']."' AND CONSECNUMBER='".str_pad($_POST['consecnumber'],6,'0',STR_PAD_LEFT)."' AND CREATEDATETIME LIKE '%".$_POST['time']."')";
}
else{
	echo '條件有誤';
}
sqlclose($sqliteconn,'sqlite');
?>