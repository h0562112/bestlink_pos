<?php
header('Access-Control-Allow-Origin: *');//╗╖║▌йIеs┼vнн
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database/person','member.db','','','','sqlite');
$sql='select discount,type,needbuy,cangift,times from powergroup where pno=(select power from person where memno="'.$_POST['memno'].'")';
$dis=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
echo json_encode($dis);
?>