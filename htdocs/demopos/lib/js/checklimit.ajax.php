<?php
include_once '../../../tool/myerrorlog.php';
include_once '../../../tool/dbTool.inc.php';
$setup=parse_ini_file('../../../database/setup.ini',true);
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
else{
	$timeini=parse_ini_file('../../../database/timem1.ini',true);
}
$machinedata=parse_ini_file('../../../database/machinedata.ini',true);
$itemname=parse_ini_file('../../../database/'.$setup['basic']['company'].'-menu.ini',true);
if(file_exists('../../../database/stock.ini')){
	$stock=parse_ini_file('../../../database/stock.ini',true);
}
else{
	$stock='-1';
}
$conn=sqlconnect('../../../database','menu.db','','','','sqlite');
$sql='SELECT inumber FROM itemsdata WHERE quickorder="'.$_POST['inumber'].'" AND (state!="0" OR state IS NULL)';
$inumber=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
//echo $sql;

//if(in_array($_POST['inumber'], array_column($itemname, 'qrnumber'))){//檢查輸入代碼是否存在
if(sizeof($inumber)>0){	
	//$itemno=array_keys(array_combine(array_keys($itemname), array_column($itemname, 'qrnumber')),$_POST['inumber'])[0];

	if(!isset($itemname[$inumber[0]['inumber']]['counter'])||$itemname[$inumber[0]['inumber']]['counter']=='1'){//非限量或庫存
		$limit=0;
		$listlimit=0;
	}
	else if(isset($itemname[$inumber[0]['inumber']]['counter'])&&($itemname[$inumber[0]['inumber']]['counter']=='2'||$itemname[$inumber[0]['inumber']]['counter']=='3')){
		$limit=$stock[$inumber[0]['inumber']]['stock'];
		$listlimit=0;
	}
	else{//例外狀況
		$limit=0;
		$listlimit=0;
	}

	if($limit==0&&$listlimit==0){
		echo $itemname[$inumber[0]['inumber']]['counter'].'*'.$inumber[0]['inumber'].'*-1*0';
	}
	else{
		if(file_exists('../../../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db')){
		}
		else{
			copy("../../../database/sale/empty.DB",'../../../database/sale/SALES_'.substr($timeini['time']['bizdate'],0,6).'.db');
		}
		$conn=sqlconnect('../../../database/sale','SALES_'.substr($timeini['time']['bizdate'],0,6).'.db','','','','sqlite');
		if($itemname[$inumber[0]['inumber']]['counter']=='3'){
			$sql='SELECT COUNT(tempCST011.CONSECNUMBER) AS QTY FROM tempCST011 JOIN tempCST012 ON tempCST012.ITEMCODE="'.str_pad($inumber[0]['inumber'], 16, "0", STR_PAD_LEFT).'" AND tempCST011.CONSECNUMBER=tempCST012.CONSECNUMBER AND tempCST011.BIZDATE=tempCST012.BIZDATE AND tempCST011.ZCOUNTER=tempCST012.ZCOUNTER WHERE tempCST011.BIZDATE="'.$timeini['time']['bizdate'].'" AND tempCST011.ZCOUNTER="'.$timeini['time']['zcounter'].'" GROUP BY tempCST011.BIZDATE,tempCST011.ZCOUNTER';
		}
		else if($itemname[$inumber[0]['inumber']]['counter']=='2'){
			$sql='SELECT SUM(QTY) AS QTY FROM tempCST012 WHERE ITEMCODE="'.str_pad($inumber[0]['inumber'], 16, "0", STR_PAD_LEFT).'" AND BIZDATE="'.$timeini['time']['bizdate'].'" AND ZCOUNTER="'.$timeini['time']['zcounter'].'" GROUP BY ITEMCODE';
			$sql2='SELECT SUM(QTY) AS QTY FROM CST012 WHERE ITEMCODE="'.str_pad($inumber[0]['inumber'], 16, "0", STR_PAD_LEFT).'" AND BIZDATE="'.$timeini['time']['bizdate'].'" AND ZCOUNTER="'.$timeini['time']['zcounter'].'" GROUP BY ITEMCODE';
		}
		$qty=sqlquery($conn,$sql,'sqlite');
		if(sizeof($qty)==0){
			$qty[0]['QTY']=0;
		}
		else{
		}
		if(isset($sql2)){
			$qty2=sqlquery($conn,$sql2,'sqlite');
			if(sizeof($qty>0)&&isset($qty[0]['QTY'])){
				if(sizeof($qty2)>0&&isset($qty[0]['QTY'])){
					$qty[0]['QTY']=intval($qty[0]['QTY'])+intval($qty2[0]['QTY']);
				}
				else{
					$qty[0]['QTY']=intval($qty[0]['QTY']);
				}
			}
			else{
				if(sizeof($qty2)>0&&isset($qty[0]['QTY'])){
					$qty[0]['QTY']=intval($qty2[0]['QTY']);
				}
				else{
					$qty[0]['QTY']=0;
				}
			}
		}
		else{
		}
		sqlclose($conn,'sqlite');
		if(intval($qty[0]['QTY'])<intval($limit)){//檢查是否未滿限量額度
			echo $itemname[$inumber[0]['inumber']]['counter'].'*'.$inumber[0]['inumber'].'*'.intval($limit).'*'.$qty[0]['QTY'];
		}
		else{
			echo 'fail';
		}
	}
}
else{
	echo 'error';
}
?>