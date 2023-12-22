<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';

$conn=sqlconnect('../../../database/sale','SALES_'.substr($_POST['bizdate'],0,6).'.db','','','','sqlite');
$sql='UPDATE tempCST011 SET SALESTTLQTY=CASE WHEN (SELECT SUM(QTY) FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'") IS NULL THEN 0 ELSE (SELECT SUM(QTY) FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'") END,SALESTTLAMT=CASE WHEN (SELECT SUM(AMT) FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'") IS NULL THEN 0 ELSE (SELECT SUM(AMT) FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'") END WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'"';
sqlnoresponse($conn,$sql,'sqlite');
$sql='UPDATE voiditem SET STATE=0 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$_POST['consecnumber'].'" AND TERMINALNUMBER="'.$_POST['machine'].'-'.$_POST['tablenumber'].'" AND STATE=1';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>