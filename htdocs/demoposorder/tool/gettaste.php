<?php
include '../../tool/dbTool.inc.php';
$conn=sqlconnect("localhost","papermanagement","paperadmin","1qaz2wsx","utf-8",'mysql');
if($_POST['depnumber']==''){
	$sql='SELECT * FROM tastemap WHERE company="'.$_POST['company'].'" ORDER BY type,taste';
	$type=sqlquery($conn,$sql,'mysql');
	if(file_exists('../data/'.$_POST['company'].'-taste.ini')){
		$content=parse_ini_file('../data/'.$_POST['company'].'-taste.ini',true);
	}
	else{
		$content=0;
	}
}
else{
	$sql='SELECT * FROM tastemap WHERE company="'.$_POST['company'].'" AND depnumber="'.$_POST['depnumber'].'" ORDER BY type,taste';
	$type=sqlquery($conn,$sql,'mysql');
	if(file_exists('../data/'.$_POST['depnumber'].'-taste.ini')){
		$content=parse_ini_file('../data/'.$_POST['depnumber'].'-taste.ini',true);
	}
	else{
		$content=0;
	}
}
sqlclose($conn,'mysql');
for($i=0;$i<sizeof($type);$i++){
	$type[$i]['name']=$content[$type[$i]['taste']]['name'];
	$type[$i]['money']=$content[$type[$i]['taste']]['money'];
}
echo json_encode($type);
?>