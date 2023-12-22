<?php
include_once '../../../tool/dbTool.inc.php';
date_default_timezone_set('Asia/Taipei');
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$sql="SELECT COUNT(*) AS num FROM pushlist WHERE createdatetime LIKE '".date('Ymd')."%'";
$list=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo date('ymd').str_pad(intval($list[0]['num'])+1, 3, "0", STR_PAD_LEFT);
?>