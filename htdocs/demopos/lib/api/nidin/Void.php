<?php
include_once './nidin_api_inc.php';
include_once '../../../../tool/dbTool.inc.php';

$setup=parse_ini_file('../../../../database/setup.ini',true);
$date=$_POST['bizdate'];
$listno=intval($_POST['consecnumber']);
$conn=sqlconnect('../../../../database/sale','SALES_'.substr($date,0,6).'.db','','','','sqlite');
$sql="SELECT * FROM CST011 WHERE BIZDATE='".$date."' AND CONSECNUMBER='".str_pad($listno,6,'0',STR_PAD_LEFT)."'";
$saledata=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');

return Void($setup['nidin']['url'],'post',$_POST['Token'],$_POST['User'],$saledata[0]['CLKCODE']);
?>