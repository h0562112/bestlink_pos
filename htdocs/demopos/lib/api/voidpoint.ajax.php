<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$timeini=parse_ini_file('../../../database/timem1.ini',true);
$r=hash('sha1',$_POST['bizdate'].'voidtableplus'.$_POST['consecnumber']);
//echo $r;

$filename='point_'.substr($timeini['time']['bizdate'],0,6);
if(file_exists("../../../database/sale/".$filename.".db")){
}
else{
	if(file_exists("../../../database/sale/EMpoint.db")){
	}
	else{
		include_once '../js/create.emptyDB.php';
		create('EMpoint');
	}
	copy("../../../database/sale/EMpoint.db","../../../database/sale/".$filename.".db");
}

$conn=sqlconnect('../../../database/sale',$filename.'.db','','','','sqlite');
$sql='SELECT * FROM pointtree WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'" AND state=1';
$data=sqlquery($conn,$sql,'sqlite');
if(isset($data[0]['total'])){
	$sql='UPDATE pointtree SET state=0,voidrandom="'.$r.'" WHERE bizdate="'.$_POST['bizdate'].'" AND consecnumber="'.$_POST['consecnumber'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
	echo $data[0]['token'].';PHP;'.$data[0]['mobilephone'].';PHP;'.$r.';PHP;'.$data[0]['total'].';PHP;'.$machinedata['pointtree']['depname'];
}
else{
}
sqlclose($conn,'sqlite');


?>