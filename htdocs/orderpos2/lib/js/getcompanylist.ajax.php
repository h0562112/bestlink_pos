<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','webmember','orderuser','0424732003','utf-8','mysql');
$sql='SELECT * FROM companylist WHERE state=1 ORDER BY push ASC,seq ASC,number ASC';
$companydata=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');
foreach($companydata as $k=>$v){
	echo '<div class="company" style="width:calc(100% - 10px);margin:5px;padding:5px 0;text-align:center;font-size:20px;color:#000000;font-weight:bold;background-color:#84feff;"><input type="hidden" name="company" value="'.$v['company'].'"><span id="companyname">'.$v['name'].'</span></div>';
}
?>