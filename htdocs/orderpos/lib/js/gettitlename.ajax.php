<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('localhost','papermanagement','paperadmin','1qaz2wsx','utf-8','mysql');
$sql='SELECT companyname FROM userlogin WHERE company="'.$_POST['story'].'" LIMIT 1';
$depdata=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'sqlite');
echo $depdata[0]['companyname'];
?>