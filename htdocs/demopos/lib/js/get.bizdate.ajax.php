<?php
if(file_exists('../../../database/mapping.ini')){
	$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
	if(isset($dbmapping['map'][$_POST['machinetype']])){
		$invmachine=$dbmapping['map'][$_POST['machinetype']];
	}
	else{
		$invmachine='m1';
	}
}
else{
	$invmachine='';
}
$init=parse_ini_file('../../../database/initsetting.ini',true);
if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){//帳務以每台分機為個別主體計算
	$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
}
else{//帳務以主機為主體計算
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}

$inputDate = isset($_POST['inputDate']) ? $_POST['inputDate'] : '';
$lastDate = "";

if(isset($_POST['bizdate'])){
	
		if($_POST['type']=='-'){
			$bizdate=date('Ymd',strtotime($_POST['bizdate'].' -1 day'));
		}
		else{
			$bizdate=date('Ymd',strtotime($_POST['bizdate'].' +1 day'));
		}
	
}
else{
	if($inputDate == "") {
		$bizdate=$timeini['time']['bizdate'];
	}else{
		$bizdate=$inputDate;
	}
	
	
}
echo $bizdate;
?>