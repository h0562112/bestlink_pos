<?php
include_once '../../../tool/dbTool.inc.php';
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT * FROM itemsdata WHERE inumber="'.$_POST['item'].'"';
$item=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$menu=parse_ini_file('../../../database/'.$_POST['company'].'-menu.ini',true);
$data=array();
$data=$menu[$_POST['item']];
$data['isgroup']=$item[0]['isgroup'];
if(!isset($data['itemdis'])){
	$data['itemdis']="1";
}
else{
}
if(!isset($data['listdis'])){
	$data['listdis']="1";
}
else{
}
if(!isset($data['bothdis'])){
	$data['bothdis']="1";
}
else{
}
if(!isset($data['mempoint'])){
	$data['usemempoint']="1";
}
else{
	$data['usemempoint']=$data['mempoint'];
}
echo json_encode($data);
?>