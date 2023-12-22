<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';

$conn1=sqlconnect('../database/sale','temp'.$_POST['machinename'].'.db','','','','sqlite');
$sql='DELETE FROM list WHERE TERMINALNUMBER="'.$_POST['machinename'].'";DELETE FROM ban WHERE TERMINALNUMBER="'.$_POST['machinename'].'"';
sqlnoresponse($conn1,$sql,'sqliteexec');
sqlclose($conn1,'sqlite');
?>