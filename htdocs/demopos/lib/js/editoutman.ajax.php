<?php
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='UPDATE tempCST011 SET CUSTGPCODE="'.$_POST['mancode'].'",CUSTGPNAME="'.$_POST['manname'].'" WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>