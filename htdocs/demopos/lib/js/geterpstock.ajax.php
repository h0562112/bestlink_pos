<?php
include_once '../api/a1erp/a1_api.inc.php';

$setup=parse_ini_file('../../../database/setup.ini',true);
$url=$setup['a1erp']['url'];
$id=$setup['a1erp']['id'];
$pw=$setup['a1erp']['pw'];
if(isset($setup['a1erp']['warehouse'])){
	$warehouse=$setup['a1erp']['warehouse'];
}
else{
	$warehouse='公司倉';
}

$login=Login($url,"post",$id,$pw);
$key=$login[1]['access_token'];
if($_POST['erpcode']!=''&&$_POST['erpcode']!=null){
	$stock=Stock($url,'get',$key,$_POST['erpcode']);
}
else{
	$stock=Stock($url,'get',$key,$_POST['number']);
}
if(isset($stock[1])){
	$warehouselist=array_column($stock[1],'WarehouseName');
	if(sizeof($warehouselist)>0){
		if(in_array($warehouse,$warehouselist)){
			$stock[1][array_search($warehouse,$warehouselist)]['inumber']=$_POST['number'];
			echo json_encode($stock[1][array_search($warehouse,$warehouselist)]);
		}
		else{
			echo json_encode(['inumber'=>$_POST['number']]);
		}
	}
	else{
		echo json_encode(['inumber'=>$_POST['number']]);
	}
}
else{
	echo json_encode(['inumber'=>$_POST['number']]);
}
?>