<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$diff=date_diff(date_create(substr($_POST['lastdate'],0,4).'-'.substr($_POST['lastdate'],4,2).'-01'),date_create(date('Y-m-01')));
$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'menu.db','','','','sqlite');
$sql='SELECT inumber FROM itemsdata';
$allitems=sqlquery($conn,$sql,'sqlite');
sqlclose($conn,'sqlite');
$itemname=parse_ini_file('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
$stock=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini',true);
$temp=array();
$stockno='';
foreach($allitems as $at){
	if($itemname[$at['inumber']]['state']=='1'&&intval($itemname[$at['inumber']]['counter'])=='1'){
		if(isset($stock[$at['inumber']])){
			$temp[$at['inumber']]=$stock[$at['inumber']]['stock'];
		}
		else{
			$temp[$at['inumber']]=0;
		}
		if(strlen($stockno)>0){
			$stockno=$stockno.'","'.str_pad($at['inumber'], 16, "0", STR_PAD_LEFT);
		}
		else{
			$stockno=str_pad($at['inumber'], 16, "0", STR_PAD_LEFT);
		}
	}
	else{
	}
}
if(sizeof($temp)==0){
	echo 'fail';
}
else{
	for($i=0;$i<=intval($diff->format("%m"));$i++){
		$tempstart=date("Ym",strtotime($_POST['lastdate'].' +'.$i.' month'));
		$conn=sqlconnect('../../../ourpos/'.$_POST['company'].'/'.$_POST['dep'],'SALES_'.$tempstart.'.db','','','','sqlite');
		$sql='SELECT ITEMCODE,SUM(QTY) AS QTY,CST012.CREATEDATETIME FROM CST012 JOIN CST011 ON CST011.CONSECNUMBER=CST012.CONSECNUMBER AND CST011.BIZDATE=CST012.BIZDATE AND CST011.ZCOUNTER=CST012.ZCOUNTER AND CST011.NBCHKNUMBER IS NULL WHERE CST012.ITEMCODE IN ("'.$stockno.'") AND CST012.CREATEDATETIME>"'.$stock['lastupdate']['time'].'" GROUP BY ITEMCODE ORDER BY CST012.CREATEDATETIME ASC';
		$qty=sqlquery($conn,$sql,'sqlite');
		sqlclose($conn,'sqlite');
		if(sizeof($qty)==0){
		}
		else{
			$stock['lastupdate']['time']=$qty[sizeof($qty)-1]['CREATEDATETIME'];
			foreach($qty as $q){
				$temp[intval($q['ITEMCODE'])]=floatval($temp[intval($q['ITEMCODE'])])-floatval($q['QTY']);
			}
		}
	}
	foreach($temp as $k=>$t){
		$stock[$k]['stock']=$t;
	}
	write_ini_file($stock,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini');
}
?>