<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','data.db','','','','sqlite');
if(preg_match('/(boss-)/',$_POST['type'])){
	$sql='SELECT no FROM dblist WHERE `group`=0 AND state=1';
	$data=sqlquery($conn,$sql,'sqlite');
}
else if(preg_match('/(group-)/',$_POST['type'])){
	$list='';
	$t=preg_split('/,/',substr($_POST['type'],0,6));
	foreach($t as $i){
		if(strlen($list)==0){
			$list='"'.$i['no'].'"';
		}
		else{
			$list=$list.',"'.$i['no'].'"';
		}
	}
	$sql='SELECT no,name FROM dblist WHERE `group` IN ('.$list.')';
	$data=sqlquery($conn,$sql,'sqlite');
}
else{
}
sqlclose($conn,'sqlite');
echo json_encode($data);
?>