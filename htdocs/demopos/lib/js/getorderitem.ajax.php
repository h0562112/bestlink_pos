<?php
include '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$filename='SALES_'.date('Ym');
if(file_exists("../../../database/sale/".$filename.".DB")){
	$conn=sqlconnect("../../../database/sale",$filename.".db","","","","sqlite");
	$sql='SELECT * FROM tempCST012 WHERE TERMINALNUMBER="'.$_POST['number'].'" ORDER BY CREATEDATETIME';
	$data=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	echo json_encode($data);
}
else{
	echo json_encode('db_is_not_exist');
}
?>