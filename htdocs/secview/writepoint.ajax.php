<?php
include_once '../tool/myerrorlog.php';
include_once '../tool/dbTool.inc.php';
$conn=sqlconnect('../database/sale','temp'.$_POST['machinetype'].'.db','','','','sqlite');
$sql='DELETE FROM phone WHERE TERMINALNUMBER="'.$_POST['machinetype'].'";INSERT INTO phone VALUES ("'.$_POST['tel'].'",'.$_POST['point'].',"'.$_POST['machinetype'].'");';
//echo $sql;
sqlnoresponse($conn,$sql,'sqliteexec');
sqlclose($conn,'sqlite');
?>