<?php
include_once '../../../tool/dbTool.inc.php';
include_once '../../../tool/inilib.php';
date_default_timezone_set('Asia/Taipei');
$itemname=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini',true);
$stock=parse_ini_file('../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini',true);
$conn=sqlconnect('../../../menudata/'.$_POST['company'].'/person','manufact.db','','','','sqlite');
$time=date('YmdHis');
$sql='SELECT (SELECT COUNT(*) AS num FROM pushlist WHERE createdatetime LIKE "'.date('Ymd').'%") AS num,(SELECT COUNT(*) AS i FROM pushlist WHERE listno="'.$_POST['listno'].'") AS i';
$num=sqlquery($conn,$sql,'sqlite');
if($_POST['type']=='new'){
	if(intval($num[0]['i'])!=0){
		echo 'already';
	}
	else{
		if($num[0]['num']==0){
			$sql='INSERT INTO pushlist (no,listno,manuno,ttmoney,paystate,paydate,invnumber,remark,createdatetime,state) SELECT 1,"'.$_POST['listno'].'","'.$_POST['manufact'].'",'.$_POST['ttmoney'].','.$_POST['paystate'].',';
		}
		else if($num[0]['num']>0){
			$sql='INSERT INTO pushlist (no,listno,manuno,ttmoney,paystate,paydate,invnumber,remark,createdatetime,state) SELECT (SELECT no FROM pushlist ORDER BY substr("0000000000"||no, -10) DESC LIMIT 1)+1,"'.$_POST['listno'].'","'.$_POST['manufact'].'",'.$_POST['ttmoney'].','.$_POST['paystate'].',';
		}
		else{
		}
		if(isset($_POST['paydate'])&&strlen($_POST['paydate'])>0){
			$sql=$sql.'"'.$_POST['paydate'].'",';
		}
		else{
			$sql=$sql.'NULL,';
		}
		if(isset($_POST['invnumber'])&&strlen($_POST['invnumber'])>0){
			$sql=$sql.'"'.$_POST['invnumber'].'",';
		}
		else{
			$sql=$sql.'NULL,';
		}
		if(isset($_POST['remark'])&&strlen($_POST['remark'])>0){
			$sql=$sql.'"'.$_POST['remark'].'",';
		}
		else{
			$sql=$sql.'NULL,';
		}
		$sql=$sql.'"'.$time.'",1';
		sqlnoresponse($conn,$sql,'sqlite');

		$sql="INSERT INTO pushlistdetail (listno,itemno,qty,subtotal,createdatetime) VALUES ";
		for($i=0;$i<sizeof($_POST['pushitem'])-1;$i++){
			if($i==0){
			}
			else{
				$sql=$sql.',';
			}
			$sql=$sql.'("'.$_POST['listno'].'","'.$_POST['pushitem'][$i].'",'.$_POST['qty'][$i].','.$_POST['subtotal'][$i].',"'.$time.'")';
			if($itemname[$_POST['pushitem'][$i]]['counter']=='-999'||$itemname[$_POST['pushitem'][$i]]['counter']=='0'){
				$itemname[$_POST['pushitem'][$i]]['counter']='1';
				$stock[$_POST['pushitem'][$i]]['stock']=$_POST['qty'][$i];
			}
			else{
				$stock[$_POST['pushitem'][$i]]['stock']=floatval($stock[$_POST['pushitem'][$i]]['stock'])+floatval($_POST['qty'][$i]);
			}
		}
		sqlnoresponse($conn,$sql,'sqlite');
		write_ini_file($itemname,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/'.$_POST['company'].'-menu.ini');
		write_ini_file($stock,'../../../menudata/'.$_POST['company'].'/'.$_POST['dep'].'/stock.ini');
	}
}
else{
	$sql='UPDATE pushlist SET paystate='.$_POST['paystate'].',paydate="'.$_POST['paydate'].'",invnumber="'.$_POST['invnumber'].'",remark="'.$_POST['remark'].'" WHERE listno="'.$_POST['listno'].'"';
	sqlnoresponse($conn,$sql,'sqlite');
}
sqlclose($conn,'sqlite');
?>