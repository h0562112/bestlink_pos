<?php
include_once '../../../../tool/dbTool.inc.php';

$conn=sqlconnect('localhost',$_POST['company'],'orderuser','0424732003','utf8','mysql');
$sql='SELECT * FROM member WHERE SUBSTR(memno,1,LENGTH("'.$_POST['dep'].'"))="'.$_POST['dep'].'" AND state=1';
$memlist=sqlquery($conn,$sql,'mysql');
sqlclose($conn,'mysql');

echo json_encode($memlist);
?>