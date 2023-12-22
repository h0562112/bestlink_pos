<?php
if(isset($_POST['story'])&&isset($_POST['dep'])&&$_POST['story']!=''&&$_POST['dep']!=''){
	if(file_exists('../../../database/unit.ini')){
		$unit=parse_ini_file('../../../database/unit.ini',true);
		if(isset($unit['webunit'])){
			echo json_encode(['message'=>'success','data'=>$unit['webunit']]);
		}
		else{
			echo json_encode(['message'=>'section is not exists','data'=>['qtyunit'=>'項','moneypreunit'=>'＄','moneysufunit'=>'元']]);
		}
	}
	else{
		echo json_encode(['message'=>'file is not exists','data'=>['qtyunit'=>'項','moneypreunit'=>'＄','moneysufunit'=>'元']]);
	}
}
else{
	echo json_encode(['message'=>'basic data not complete','data'=>['qtyunit'=>'項','moneypreunit'=>'＄','moneysufunit'=>'元']]);
}
?>