<?php
include_once '../../../tool/dbTool.inc.php';
$init=parse_ini_file('../../../database/initsetting.ini',true);
//date_default_timezone_set('Asia/Taipei');
date_default_timezone_set($init['init']['settime']);
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$timeini=parse_ini_file('../../../database/timem1.ini',true);

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
if(isset($_POST['total'])){
	$r=hash('sha1',date('YmdHis').$_POST['bizdate'].'tableplus'.$_POST['consecnumber']);
	echo $r.';PHP;'.$machinedata['pointtree']['depname'];
	$sql='INSERT INTO pointtree VALUES ("'.$_POST['bizdate'].'","'.$_POST['consecnumber'].'","'.$_POST['token'].'","'.$_POST['mobilephone'].'","'.$r.'",'.$_POST['total'].',1,"'.date('YmdHis').'",NULL,NULL,"gift",0)';
}
else{
	$r=hash('sha1',$_POST['bizdate'].'tableplus'.$_POST['consecnumber'].date('YmdHis'));
	echo $r.';PHP;'.$machinedata['pointtree']['depname'];
	$sql='INSERT INTO pointtree VALUES ("'.$_POST['bizdate'].'","'.$_POST['consecnumber'].'","'.$_POST['token'].'","'.$_POST['mobilephone'].'","'.$r.'",0,1,"'.date('YmdHis').'",NULL,NULL,"exchange",'.$_POST['point'].')';
}
sqlnoresponse($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
?>