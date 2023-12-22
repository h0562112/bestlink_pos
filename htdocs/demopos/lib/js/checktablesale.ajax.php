<?php
//echo $_POST['consecnumber'];
if(!isset($_POST['consecnumber'])||$_POST['consecnumber']==''){
	echo 'exists';
}
else{
	include_once '../../../tool/dbTool.inc.php';
	if(file_exists('../../../database/mapping.ini')){
		$dbmapping=parse_ini_file('../../../database/mapping.ini',true);
		if(isset($dbmapping['map'][$_POST['machine']])){
			$invmachine=$dbmapping['map'][$_POST['machine']];
		}
		else{
			$invmachine='m1';
		}
	}
	else{
		$invmachine='';
	}
	$init=parse_ini_file('../../../database/initsetting.ini',true);
	if(isset($init['init']['accounting'])&&$init['init']['accounting']=='2'&&isset($invmachine)&&$invmachine!=''){
		$timeini=parse_ini_file('../../../database/time'.$invmachine.'.ini',true);
	}
	else{
		$timeini=parse_ini_file('../../../database/timem1.ini',true);
	}
	if(file_exists("../../../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".DB")){
	}
	else{
		if(file_exists("../../../database/sale/empty.DB")){
		}
		else{
			include_once './create.emptyDB.php';
			create('empty');
		}
		copy("../../../database/sale/empty.DB","../../../database/sale/SALES_".substr($timeini['time']['bizdate'],0,6).".DB");
	}
	$tab=preg_split('/,/',$_POST['tablenumber']);
	$conn=sqlconnect('../../../database/sale',"SALES_".substr($timeini['time']['bizdate'],0,6).".DB",'','','','sqlite');
	$sql='SELECT * FROM tempCST011 WHERE CONSECNUMBER="'.$_POST['consecnumber'].'" AND (TABLENUMBER LIKE "%'.$tab[0].'" OR TABLENUMBER LIKE "'.$tab[0].',%" OR TABLENUMBER LIKE "%,'.$tab[0].',%")';
	$res=sqlquery($conn,$sql,'sqlite');
	sqlclose($conn,'sqlite');
	if(isset($res[0]['CONSECNUMBER'])){
		echo 'exists';
	}
	else{
		echo 'empty';
	}
}
?>