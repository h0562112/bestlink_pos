<?php
include_once './nidin_api_inc.php';
include_once '../../../../tool/dbTool.inc.php';

$initsetting=parse_ini_file('../../../../database/initsetting.ini',true);
$setup=parse_ini_file('../../../../database/setup.ini',true);
date_default_timezone_set($initsetting['init']['settime']);

$date=$_POST['bizdate'];
$listno=intval($_POST['consecnumber']);
$conn=sqlconnect('../../../../database/sale','SALES_'.substr($date,0,6).'.db','','','','sqlite');
$sql="SELECT * FROM tempCST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
$saledata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

$temp1=preg_split('/-/',$saledata[0]['nidin']);
$temp2=preg_split('/:/',$temp1[2]);
if($temp2[0]=='10'){//2021/8/18 �w�I��
	$_POST['payment_status']='';
}
else{
}
$Ymd=date('Y-m-d');
$His=date('H:i:s');

return Finish($setup['nidin']['url'],'post',$_POST['Token'],$_POST['User'],$saledata[0]['CLKCODE'],$_POST['payment_status'],$_POST['payment_method'],$Ymd,$His);
?>