<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
$consecnumber=$_POST['consecnumber'];

$conn1=sqlconnect('../database/sale','temp'.$_POST['machinename'].'.db','','','','sqlite');
$sql='DELETE FROM list WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad($_POST['linenumber'], 3, "0", STR_PAD_LEFT).'"';
sqlnoresponse($conn1,$sql,'sqlite');
//$sql='SELECT ITEMCODE FROM tempCST012 WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'"';
//$y=sqlquery($conn,$sql,'sqlite');
$sql='DELETE FROM list WHERE BIZDATE="'.$_POST['bizdate'].'" AND CONSECNUMBER="'.$consecnumber.'" AND LINENUMBER ="'.str_pad(intval($_POST['linenumber'])+1, 3, "0", STR_PAD_LEFT).'";';
sqlnoresponse($conn1,$sql,'sqlite');
sqlclose($conn1,'sqlite');
?>