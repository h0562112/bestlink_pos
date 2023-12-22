<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$conn=sqlconnect('../../../database/person','punch.db','','','','sqlite');
$sql='UPDATE punchlist SET date="'.$_POST['date'].'",time="'.$_POST['time'].':00",editdatetime="'.date('Y/m/d H:i:s').'" WHERE perno="'.$_POST['perno'].'" AND date="'.$_POST['initdate'].'" AND time="'.$_POST['inittime'].'" AND type="'.$_POST['inittype'].'"';
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>